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
        $this->reminder_project();
                $this->reminder_host();
        $this->reminder_domain();

        $view = JFactory::getApplication()->input->getCmd('view', 'projects');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }
    public function reminder_project(){
        $db =& JFactory::getDBO();
        $user = JFactory::getUser();
        $year=date("Y");
        $month=date("m");
        $day=date("d");
        $shamsi_now=$this->hijricalender ( $year , $month , $day );
        $query_type="SELECT * FROM #__trn_project";
        $db->setQuery($query_type);
        $type=$db->loadObjectlist();
        foreach ($type as $key => $value) {
                        $reminde_project=$this->dif_date($value->expiration_project,$shamsi_now);
                        if($reminde_project){
                        $query_rem="UPDATE #__trn_project SET reminde=".$reminde_project." WHERE id=".$value->id;
                        $db->setQuery($query_rem);
                         $db->query();
                        }
        }
    }
    public function reminder_host(){
        $db =& JFactory::getDBO();
        $user = JFactory::getUser();
        $year=date("Y");
        $month=date("m");
        $day=date("d");
        $shamsi_now=$this->hijricalender ( $year , $month , $day );
        $query_type="SELECT * FROM #__trn_host";
        $db->setQuery($query_type);
        $type=$db->loadObjectlist();
        foreach ($type as $key => $value) {
                        $reminde_host=$this->dif_date($value->expiration_host,$shamsi_now);
                        if($reminde_host){
                            $query_rem="UPDATE #__trn_host SET reminde=".$reminde_host." WHERE id=".$value->id;
                            $db->setQuery($query_rem);
                            $db->query();
                        }
                        
        }
    }
    public function reminder_domain(){
        $db =& JFactory::getDBO();
        $user = JFactory::getUser();
        $year=date("Y");
        $month=date("m");
        $day=date("d");
        $shamsi_now=$this->hijricalender ( $year , $month , $day );
        $query_type="SELECT * FROM #__trn_domain";
        $db->setQuery($query_type);
        $type=$db->loadObjectlist();
        foreach ($type as $key => $value) {
                        $reminde_domain=$this->dif_date($value->expiration_domain,$shamsi_now);
                        if($reminde_domain){
                        $query_rem="UPDATE #__trn_domain SET reminde=".$reminde_domain." WHERE id=".$value->id;
                        $db->setQuery($query_rem);
                            $db->query();
                    }
        }
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
function hijricalender ( $year , $month , $day )
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

function dif_date($day1,$day2){
    $years1=substr($day1,0,4);
    $month1=substr($day1,5,2);
    $day1=substr($day1,8,2);
    $years2=substr($day2,0,4);
    $month2=substr($day2,5,2);
    $day2=substr($day2,8,2);
    $years_kabise1=$this->kabise($years1);
    $years_kabise2=$this->kabise($years2);


    $dif=0;
    if($years1-$years2==0){
        if(($month1-$month2)>1 ){
            $dif=$month1-$month2;
            $dif-=1;
            if($month2<=6){
                $reminde=31-$day2;
                $reminde+=$day1;

            }elseif($month2>=7  && $month2<=11 ){
                $reminde=30-$day2;
                $reminde+=$day1;
            }elseif($month2==12){
                if($years_kabise2){
                    $reminde=30-$day2;
                    $reminde+=$day1;
                }else{
                    $reminde=29-$day2;
                    $reminde+=$day1;
                }
            }
            $month=$month2;
            for($i=1;$i<=$dif;$i++){
                $day=$this->day_dif(++$month,$years_kabise2);
                $reminde+=$day;
            }
        }
        elseif(($month1-$month2)==1 ){
            if($month2<=6){
                $reminde=31-$day2;
                $reminde+=$day1;
            }elseif($month2 >=7  && $month2 <=11 ){
                $reminde=30-$day2;
                $reminde+=$day1;
            }elseif($month2==12){
                if($years_kabise2){
                    $reminde=30-$day2;
                    $reminde+=$day1;
                }else{
                    $reminde=29-$day2;
                    $reminde+=$day1;
                }
            }
        }elseif(($month1-$month2)==0){
        $reminde=$day1-$day2;
        }
    }

    if($years1-$years2 >0){

        $dif=0;

            $dif2=12-$month2;
            $dif1=$month1-1;
            if($month2<=6){
                $reminde=31-$day2;
                $reminde+=$day1;

            }elseif($month2>=7  && $month2<=11 ){
                $reminde=30-$day2;
                $reminde+=$day1;
            }elseif($month2==12){
                if($years_kabise2){
                    $reminde=30-$day2;
                    $reminde+=$day1;
                }else{
                    $reminde=29-$day2;
                    $reminde+=$day1;
                }
            }
                    $month=$month2;

            for($i=1;$i<=$dif2;$i++){
                ++$month;
                $day=$this->day_dif($month,$years_kabise2);
                $reminde+=$day;
            }

            for($i=1;$i<=$dif1;$i++){

              $day=$this->day_dif($i,$years_kabise1);
                $reminde+=$day;
            }
            if($years1-$years2 >1){
                $years=$years1-$years2;
                $years--;
                for($i=1;$i<=$years;$i++){
                    $year=++$years2;
                    kabise($year);
                    if($year){
                                $reminde+=366;
                    }else{
                                $reminde+=365;
                    }
                }
            }


    }

        return $reminde;
}
function day_dif($month,$kab){
    switch($month){
        case 1:
            return 31;
            break;
        case 2:
            return 31;
            break;
        case 3:
            return 31;
            break;
        case 4:
            return 31;
            break;
        case 5:
            return 31;
            break;
        case 6:
            return 31;
            break;
        case 7:
            return 30;
            break;
        case 8:
            return 30;
            break;
        case 9:
            return 30;
            break;
        case 10:
            return 30;
            break;
        case 11:
            return 30;
            break;
        case 12:
            if($kab){
                return 30;}else{
                return 29;
                }
            break;
    }
}
 function kabise($year){
     $years_kabise=false;
     $kabise=$year%33;
     if($kabise==1 || $kabise==5 || $kabise==9 || $kabise==13 || $kabise==17 || $kabise==22 || $kabise==26 || $kabise==30){
         $years_kabise=true;
     }
     return $years_kabise;
 }

 function archiveporoject(){
    $model = &$this->getModel('projects');
        $result = $model->archiveproject();
           if(!$result) {
                echo $result;
                exit();
            }else{
                echo $result;
                exit();
            }
     
 }
 function deleteporoject(){
    $model = &$this->getModel('projects');
        $result = $model->deleteproject();
           if(!$result) {
                echo $result;
                exit();
            }else{
                echo $result;
                exit();
            }
     
 }
 function archivehost(){
    $model = &$this->getModel('hosts');
        $result = $model->archivehost();
           if(!$result) {
                echo $result;
                exit();
            }else{
                echo $result;
                exit();
            }
     
 }
 function deletehost(){
    $model = &$this->getModel('hosts');
        $result = $model->deletehost();
           if(!$result) {
                echo $result;
                exit();
            }else{
                echo $result;
                exit();
            }
     
 }
 function archivedomain(){
    $model = &$this->getModel('domains');
        $result = $model->archivedomain();
           if(!$result) {
                echo $result;
                exit();
            }else{
                echo $result;
                exit();
            }
     
 }
 function deletedomain(){
    $model = &$this->getModel('domains');
        $result = $model->deletedomain();
           if(!$result) {
                echo $result;
                exit();
            }else{
                echo $result;
                exit();
            }
     
 }

}
