<?php

/**
 * @version     1.0.0
 * @package     com_trn
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      shahriar pahlevansadegh <apachish@gmail.com> - http://bmsystem.ir
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class TrnController extends JControllerLegacy {

    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/trn.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'projects');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }
    function typeproject(){
        $model = &$this->getModel('projects');
        $result = $model->typeproject();
            if($result) {
                echo json_encode($result);
                exit();
            }else{
                exit;
            }
        
    }
    public function searched(){
        $search = JRequest::getVar('search_tel');
        $model = &$this->getModel('projects');
        $result = $model->searched();
        if($search) {
            if(!$result) {
                echo '<h1 style="color: #353535" >';
                echo 'یافت نشد';
                echo '</h1>';
                echo '<p>'.JText::_('MESSAGE_ADD').'</p>';
                echo '<a href="index.php?option=com_adduserfrontend&view=adduserfrontend&telephon='.$search.'">'.JText::_('BHUTTON_ADD').'</a>';
                exit();

            }else{
                $j=1;
echo '<table class="rwd-table">';
    echo '<tr>';
                echo '<th>'.JText::_('LIST_ROW').'</th>';
                echo '<th>'.JText::_('LIST_NAME').'</th>';
echo '<th>'.JText::_('LIST_TELEPHON').'</th>';
echo '<th>'.JText::_('LIST_EDIT').'</th></tr>';
               foreach($result as $res){
                            echo '<tr>';
        echo '<td data-th="'.JText::_('LIST_ROW').'"><span>'.$j.'</span></td>';
        echo '<td data-th="'.JText::_('LIST_NAME').'"><span>'.$res->name.'</span></td>';
        echo '<td data-th="'.JText::_('LIST_TELEPHON').'"><span></span></td>';
        echo '<td data-th="'.JText::_('LIST_EDIT').'"><span><input type="radio" name="edit"></span></td>';
        echo '</tr>';
                   $j++;
               }
                echo '</table>';
                exit();


            }
        }
    }
    public function send_email_alert(){
$app = JFactory::getApplication('site');
$componentParams = $app->getParams('com_trn');
$email1 = $componentParams->get('email1');  
$email2 = $componentParams->get('email2');  
$email3 = $componentParams->get('email3');  
$email4 = $componentParams->get('email4');  
$text = $componentParams->get('textemail');  
echo$month=date("m");echo '|';
echo $day=date("d");echo '|';
echo $year=date("Y");echo '|';
if($day<=26){
    $day=$day+3;
}else{
    if($month < 12){
        $month++;
    }else{
        $month=1;
        $year++;
    }
    $day=5-(31-$day);
}


 $date=$this->hijricalender($year,$month,$day);
       $db = JFactory::getDBO();
       $headers = 'From: info@trn.ir' . "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
        $query_group="SELECT * FROM #__trn_domain";
        $db->setQuery($query_group);
        $db->query();
        $rows = $db->getNumRows();
        if($rows)
        $list_domain=$db->loadObjectlist();
            $query_group="SELECT * FROM #__trn_host";
        $db->setQuery($query_group);
        $db->query();
        $rows = $db->getNumRows();
        if($rows)
        $list_host=$db->loadObjectlist();
            $query_group="SELECT * FROM #__trn_project";
        $db->setQuery($query_group);
        $db->query();
        $rows = $db->getNumRows();
        if($rows)
        $list_project=$db->loadObjectlist();
        foreach ($list_project as $key => $value) {
            if(strcmp($date,$value->expiration_project)<=0){
                if($email1){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
                                if($email2){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
                                if($email3){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
                                if($email4){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
            }
        }
        foreach ($list_domain as $key => $value) {
            if(strcmp($date,$value->expiration_domain)<=0){
                        if($email1){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
                                if($email2){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
                                if($email3){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
                                if($email4){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
            }
        }
        foreach ($list_host as $key => $value) {
            if(strcmp($date,$value->expiration_host)<=0){
                if($email1){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
                if($email2){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
                if($email3){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
               if($email4){
                    $to= 'nobody@example.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    mail($to, $subject, $message, $headers);
                }
            }
        }
       exit;
    }
    public function BaseFromMiladiDate($mldyear , $mldmonth , $mldday )
    {
        $PrevMonthDayMld = array(0,31,59,90,120,151,181,212,243,273,304,334);
        $iDaySum = 0 ;
        $iNewDateElapsed =0 ;
        $iBaseYear = 1996 ;
        $iBaseDateElapsed = 79 ;
        $iNewDateElapsed = ($mldday -1 ) + $PrevMonthDayMld[$mldmonth-1];
        if ( (($mldyear % 4 ) == 0 ) && ( $mldmonth > 2))
            $iNewDateElapsed++;

        $iDaySum = $iNewDateElapsed - $iBaseDateElapsed +($mldyear-$iBaseYear) *365 +(int)(($mldyear -$iBaseYear ) / 4 );

        if ((($mldyear - $iBaseYear) % 4 ) != 0 )
            $iDaySum = $iDaySum+1;
        return $iDaySum;
    }
//---------------------------------------------------------------------
    public function hijricalender ( $year , $month , $day )
    {       

        $PrevMonthDayHjr = array(0,31,62,93,124,155,186,216,246,276,306,336);
        if ( $year < 1995 || $month < 1 || $month > 12 || $day > 31 || $day < 1 )
            return 0;
        $daysum = $this->BaseFromMiladiDate($year , $month , $day );
        $iaddyear=0;
        while ($daysum >0 )
        {
            $daysum = $daysum -365;
            if (($iaddyear % 4 ) == 0 )
                $daysum--;
            $iaddyear++;
        }
        if ( $daysum <0 )
        {
            $iaddyear--;
            $daysum = $daysum+365;
            if (($iaddyear % 4 ) == 0 )
                $daysum++;
        }
        $itodayyear = 1375+$iaddyear;
        $itodaymonth=1;
        while ( $daysum >= $PrevMonthDayHjr[$itodaymonth])
        {
            $itodaymonth++;
            if( $itodaymonth ==12 )
                break;
        }
        $daysum=$daysum - $PrevMonthDayHjr[$itodaymonth-1];
        $itodayday = 1 + $daysum;
        $isodate = sprintf("%04d/% 02d/% 02d",$itodayyear ,$itodaymonth, $itodayday);
        return  $isodate;
    }


}
