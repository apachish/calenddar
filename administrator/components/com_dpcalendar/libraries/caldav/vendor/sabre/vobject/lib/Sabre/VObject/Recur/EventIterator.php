<?php

namespace Sabre\VObject\Recur;

use InvalidArgumentException;
use DateTime;
use Sabre\VObject\Component;
use Sabre\VObject\Component\VEvent;

/**
 * This class is used to determine new for a recurring event, when the next
 * events occur.
 *
 * This iterator may loop infinitely in the future, therefore it is important
 * that if you use this class, you set hard limits for the amount of iterations
 * you want to handle.
 *
 * Note that currently there is not full support for the entire iCalendar
 * specification, as it's very complex and contains a lot of permutations
 * that's not yet used very often in software.
 *
 * For the focus has been on features as they actually appear in Calendaring
 * software, but this may well get expanded as needed / on demand
 *
 * The following RRULE properties are supported
 *   * UNTIL
 *   * INTERVAL
 *   * COUNT
 *   * FREQ=DAILY
 *     * BYDAY
 *     * BYHOUR
 *     * BYMONTH
 *   * FREQ=WEEKLY
 *     * BYDAY
 *     * BYHOUR
 *     * WKST
 *   * FREQ=MONTHLY
 *     * BYMONTHDAY
 *     * BYDAY
 *     * BYSETPOS
 *   * FREQ=YEARLY
 *     * BYMONTH
 *     * BYMONTHDAY (only if BYMONTH is also set)
 *     * BYDAY (only if BYMONTH is also set)
 *
 * Anything beyond this is 'undefined', which means that it may get ignored, or
 * you may get unexpected results. The effect is that in some applications the
 * specified recurrence may look incorrect, or is missing.
 *
 * The recurrence iterator also does not yet support THISANDFUTURE.
 *
 * @copyright Copyright (C) 2007-2014 fruux GmbH (https://fruux.com/).
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class EventIterator implements \Iterator {

    /**
     * Creates the iterator
     *
     * You should pass a VCALENDAR component, as well as the UID of the event
     * we're going to traverse.
     *
     * @param Component $vcal
     * @param string|null $uid
     */
    public function __construct(Component $vcal, $uid = null) {

        $rrule = null;
        if ($vcal instanceof VEvent) {
            // Single instance mode.
            $events = array($vcal);
        } else {
            $uid = (string)$uid;
            if (!$uid) {
                throw new InvalidArgumentException('The UID argument is required when a VCALENDAR is passed to this constructor');
            }
            if (!isset($vcal->VEVENT)) {
                throw new InvalidArgumentException('No events found in this calendar');
            }
            $events = array();
            foreach($vcal->VEVENT as $event) {
                if ($event->uid->getValue() === $uid) {
                    $events[] = $event;
                }
            }

        }

        foreach($events as $vevent) {

            if (!isset($vevent->{'RECURRENCE-ID'})) {

                $this->masterEvent = $vevent;

            } else {

                $this->exceptions[$vevent->{'RECURRENCE-ID'}->getDateTime()->getTimeStamp()] = true;
                $this->overriddenEvents[] = $vevent;

            }

        }

        if (!$this->masterEvent) {
            // No base event was found. CalDAV does allow cases where only
            // overridden instances are stored.
            //
            // In this particular case, we're just going to grab the first
            // event and use that instead. This may not always give the
            // desired result.
            if (!count($this->overriddenEvents)) {
                throw new InvalidArgumentException('This VCALENDAR did not have an event with UID: ' . $uid);
            }
            $this->masterEvent = array_shift($this->overriddenEvents);
        }

        // master event.
        if (isset($this->masterEvent->RRULE)) {
            $rrule = $this->masterEvent->RRULE->getParts();
        } else {
            // master event has no rrule. We default to something that
            // iterates once.
            $rrule = array(
                'FREQ' => 'DAILY',
                'COUNT' => 1,
            );
        }
        $this->startDate = $this->masterEvent->DTSTART->getDateTime();

        if (isset($this->masterEvent->EXDATE)) {

            foreach($this->masterEvent->EXDATE as $exDate) {

                foreach($exDate->getDateTimes() as $dt) {
                    $this->exceptions[$dt->getTimeStamp()] = true;
                }

            }

        }

        if (isset($this->masterEvent->DTEND)) {
            $this->eventDuration =
                $this->masterEvent->DTEND->getDateTime()->getTimeStamp() -
                $this->startDate->getTimeStamp();
        } elseif (isset($this->masterEvent->DURATION)) {
            $duration = $this->masterEvent->DURATION->getDateInterval();
            $end = clone $this->startDate;
            $end->add($duration);
            $this->eventDuration = $end->getTimeStamp() - $this->startDate->getTimeStamp();
        } elseif ($this->masterEvent->DTSTART->getValueType() === 'DATE') {
            $this->eventDuration = 3600 * 24;
        } else {
            $this->eventDuration = 0;
        }

        if (isset($this->masterEvent->RDATE)) {
            $this->recurIterator = new RDateIterator(
                $this->masterEvent->RDATE->getParts(),
                $this->startDate
            );
        } elseif (isset($this->masterEvent->RRULE)) {
            $this->recurIterator = new RRuleIterator(
                $this->masterEvent->RRULE->getParts(),
                $this->startDate
            );
        } else {
            $this->recurIterator = new RRuleIterator(
                array(
                    'FREQ' => 'DAILY',
                    'COUNT' => 1,
                ),
                $this->startDate
            );
        }

        $this->rewind();

    }

    /**
     * Returns the date for the current position of the iterator.
     *
     * @return DateTime
     */
    public function current() {

        if ($this->currentDate) {
            return clone $this->currentDate;
        }

    }

    /**
     * This method returns the start date for the current iteration of the
     * event.
     *
     * @return DateTime
     */
    public function getDtStart() {

        if ($this->currentDate) {
            return clone $this->currentDate;
        }

    }

    /**
     * This method returns the end date for the current iteration of the
     * event.
     *
     * @return DateTime
     */
    public function getDtEnd() {

        if (!$this->valid()) {
            return null;
        }
        $end = clone $this->currentDate;
        $end->modify('+' . $this->eventDuration . ' seconds');
        return $end;

    }

    /**
     * Returns a VEVENT for the current iterations of the event.
     *
     * This VEVENT will have a recurrence id, and it's DTSTART and DTEND
     * altered.
     *
     * @return VEvent
     */
    public function getEventObject() {

        if ($this->currentOverriddenEvent) {
            return $this->currentOverriddenEvent;
        }

        $event = clone $this->masterEvent;

        // Ignoring the following block, because PHPUnit's code coverage
        // ignores most of these lines, and this messes with our stats.
        //
        // @codeCoverageIgnoreStart
        unset(
            $event->RRULE,
            $event->EXDATE,
            $event->RDATE,
            $event->EXRULE,
            $event->{'RECURRENCE-ID'}
        );
        // @codeCoverageIgnoreEnd

        $event->DTSTART->setDateTime($this->getDtStart());
        if (isset($event->DTEND)) {
            $event->DTEND->setDateTime($this->getDtEnd());
        }
        if ($this->recurIterator->key() > 0) {
            $event->add('RECURRENCE-ID', $event->DTSTART->getDateTime());
        }
        return $event;

    }

    /**
     * Returns the current position of the iterator.
     *
     * This is for us simply a 0-based index.
     *
     * @return int
     */
    public function key() {

        // The counter is always 1 ahead.
        return $this->counter - 1;

    }

    /**
     * This is called after next, to see if the iterator is still at a valid
     * position, or if it's at the end.
     *
     * @return bool
     */
    public function valid() {

        return !!$this->currentDate;

    }

    /**
     * Sets the iterator back to the starting point.
     */
    public function rewind() {

        $this->recurIterator->rewind();
        // re-creating overridden event index.
        $index = array();
        foreach($this->overriddenEvents as $key=>$event) {
            $stamp = $event->DTSTART->getDateTime()->getTimeStamp();
            $index[$stamp] = $key;
        }
        krsort($index);
        $this->counter = 0;
        $this->overriddenEventsIndex = $index;
        $this->currentOverriddenEvent = null;

        $this->nextDate = null;
        $this->currentDate = clone $this->startDate;

        $this->next();

    }

    /**
     * Advances the iterator with one step.
     *
     * @return void
     */
    public function next() {

        $this->currentOverriddenEvent = null;
        $this->counter++;
        if ($this->nextDate) {
            // We had a stored value.
            $nextDate = $this->nextDate;
            $this->nextDate = null;
        } else {
            // We need to ask rruleparser for the next date.
            // We need to do this until we find a date that's not in the
            // exception list.
            do {
                if (!$this->recurIterator->valid()) {
                    $nextDate = null;
                    break;
                }
                $nextDate = $this->recurIterator->current();
                $this->recurIterator->next();
            } while(isset($this->exceptions[$nextDate->getTimeStamp()]));

        }


        // $nextDate now contains what rrule thinks is the next one, but an
        // overridden event may cut ahead.
        if ($this->overriddenEventsIndex) {

            $offset = end($this->overriddenEventsIndex);
            $timestamp = key($this->overriddenEventsIndex);
            if (!$nextDate || $timestamp < $nextDate->getTimeStamp()) {
                // Overridden event comes first.
                $this->currentOverriddenEvent = $this->overriddenEvents[$offset];

                // Putting the rrule next date aside.
                $this->nextDate = $nextDate;
                $this->currentDate = $this->currentOverriddenEvent->DTSTART->getDateTime();

                // Ensuring that this item will only be used once.
                array_pop($this->overriddenEventsIndex);

                // Exit point!
                return;

            }

        }

        $this->currentDate = $nextDate;

    }

    /**
     * Quickly jump to a date in the future.
     *
     * @param DateTime $dateTime
     */
    public function fastForward(DateTime $dateTime) {

        while($this->valid() && $this->getDtEnd() < $dateTime ) {
            $this->next();
        }

    }

    /**
     * Returns true if this recurring event never ends.
     *
     * @return bool
     */
    public function isInfinite() {

        return $this->recurIterator->isInfinite();

    }

    /**
     * RRULE parser
     *
     * @var RRuleIterator
     */
    protected $recurIterator;

    /**
     * The duration, in seconds, of the master event.
     *
     * We use this to calculate the DTEND for subsequent events.
     */
    protected $eventDuration;

    /**
     * A reference to the main (master) event.
     *
     * @var VEVENT
     */
    protected $masterEvent;

    /**
     * List of overridden events.
     *
     * @var array
     */
    protected $overriddenEvents = array();

    /**
     * Overridden event index.
     *
     * Key is timestamp, value is the index of the item in the $overriddenEvent
     * property.
     *
     * @var array
     */
    protected $overriddenEventsIndex;

    /**
     * A list of recurrence-id's that are either part of EXDATE, or are
     * overridden.
     *
     * @var array
     */
    protected $exceptions = array();

    /**
     * Internal event counter
     *
     * @var int
     */
    protected $counter;

    /**
     * The very start of the iteration process.
     *
     * @var DateTime
     */
    protected $startDate;

    /**
     * Where we are currently in the iteration process
     *
     * @var DateTime
     */
    protected $currentDate;

    /**
     * The next date from the rrule parser.
     *
     * Sometimes we need to temporary store the next date, because an
     * overridden event came before.
     *
     * @var DateTime
     */
    protected $nextDate;

}
