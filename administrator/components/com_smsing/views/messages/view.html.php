<?php

/**
 * @version     1.0.0
 * @package     com_smsing
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      shahriar <apachish@gmail.com> - http://www.bmsystem.ir
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Smsing.
 */
class SmsingViewMessages extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        SmsingHelper::addSubmenu('messages');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/smsing.php';

        $state = $this->get('State');
        $canDo = SmsingHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_SMSING_TITLE_MESSAGES'), 'messages.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/message';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
               // JToolBarHelper::addNew('message.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('message.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('messages.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('messages.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'messages.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('messages.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('messages.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'messages.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('messages.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_smsing');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_smsing&view=messages');

        $this->extra_sidebar = '';
        
		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);

    }

	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
		'a.state' => JText::_('JSTATUS'),
		'a.message' => JText::_('COM_SMSING_MESSAGES_MESSAGE'),
		'a.state_message' => JText::_('COM_SMSING_MESSAGES_STATE_MESSAGE'),
		);
	}
    public function checksms($recid){
        $app = JFactory::getApplication();
        $params =  JComponentHelper::getParams('com_smsing');
        $signature=$params->get('signature');
        $webServiceURL       = "http://login.parsgreen.com/Api/SendSMS.asmx?WSDL"; //آدرس وب سرویس را در این قسمت وارد کنید
        $webServiceSignature = $signature; // امضای دیجیتالی خود را در این قسمت وارد کنید
        $webServiceRecID = 20456503;

        $parameters  = array( // در این قسمت گزینه های مورد نظر ساخته می شوند برای ارسال
            'signature' => $webServiceSignature,
            'RecID' => $webServiceRecID,
        );


        try {
            $connectionS = new SoapClient($webServiceURL); // ایجاد یک ارتباط اولیه با وب سرویس
            $responseSTD = (array) $connectionS->GetDelivery($parameters); // ارسال درخواست و گرفتن نتیجه آن ها
            if ( $responseSTD['GetDeliveryResult'] == -1 ) {
               return " امضای وارد شده معتبر نیست";
                //بررسی حابت ها موحود بر روی مقدار خروجی تابع
            } else if ( $responseSTD['GetDeliveryResult'] == 40 ) {
                return " پیامک مورد نظر منتظر تحویل می باشد";
            } else if ( $responseSTD['GetDeliveryResult'] == 41 ) {
                return "پیامک مورد نظر تحویل شد";
            } else if ( $responseSTD['GetDeliveryResult'] == 42 ) {
                return "پیامک مورد نظر تحویل نشد";
            } else if ( $responseSTD['GetDeliveryResult'] == 43 ) {
                return "حطا در مخابرات";
            } else if ( $responseSTD['GetDeliveryResult'] == 44 ) {
                return "پیامک ارسال نشد";
            } else if ( $responseSTD['GetDeliveryResult'] == 45 ) {
                return "خطا";
            } else if ( $responseSTD['GetDeliveryResult'] == 46 ) {
                return "کد رهگیری وارد شده یافت نشد";
            } else if ( $responseSTD['GetDeliveryResult'] == 47 ) {
                return "کدرهگیری وارد شده معتبر نیست";
            }

        }
        catch (SoapFault $ex) {
            echo $ex->faultstring; //زمانی که خطایی رخ دهد این قسمت خطا را چاپ می کند

        }
    }
    public function paysms(){
        $app = JFactory::getApplication();
$params =  JComponentHelper::getParams('com_smsing');
$signature=$params->get('signature');
$webServiceURL       = "http://login.parsgreen.com/Api/ProfileService.asmx?WSDL"; //آدرس وب سرویس را در این قسمت وارد کنید
$webServiceSignature = $signature; // امضای دیجیتالی خود را در این قسمت وارد کنید


$parameters  = array( // در این قسمت گزینه های مورد نظر ساخته می شوند برای ارسال
'signature' => $webServiceSignature,
);


try {
$connectionS = new SoapClient($webServiceURL); // ایجاد یک ارتباط اولیه با وب سرویس
$responseSTD = (array) $connectionS->GetCredit($parameters); // انتقال اطلاعات و دریافت موجودی شما
    //بررسی حالت های خروجی تابع که جواب های مختلف می تواند داشته باشد.

if ( $responseSTD['GetCreditResult'] == -64 ){
echo " خطا ";
} else {
    echo "مقدار اعتبار شما " . $responseSTD['GetCreditResult'] . "ریال است";
}

} catch (SoapFault $ex) {
    echo $ex->faultstring; //زمانی که خطایی رخ دهد این قسمت خطا را چاپ می کند

}
}

}
