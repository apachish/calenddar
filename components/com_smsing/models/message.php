<?php

/**
 * @version     1.0.0
 * @package     com_smsing
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      shahriar <apachish@gmail.com> - http://www.bmsystem.ir
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');
echo JURI::base();
include_once(JPATH_ROOT.'/media/webservice/nusoap/nusoap.php');

/**
 * Smsing model.
 */
class SmsingModelMessage extends JModelItem {

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_smsing');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_smsing.edit.message.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_smsing.edit.message.id', $id);
        }
        $this->setState('message.id', $id);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('message.id', $params_array['item_id']);
        }
        $this->setState('params', $params);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('message.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Check published state.
                if ($published = $this->getState('filter.published')) {
                    if ($table->state != $published) {
                        return $this->_item;
                    }
                }

                // Convert the JTable to a clean JObject.
                $properties = $table->getProperties(1);
                $this->_item = JArrayHelper::toObject($properties, 'JObject');
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        
		if ( isset($this->_item->created_by) ) {
			$this->_item->created_by_name = JFactory::getUser($this->_item->created_by)->name;
		}

        return $this->_item;
    }

    public function getTable($type = 'Message', $prefix = 'SmsingTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to check in an item.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkin($id = null) {
        // Get the id.
        $id = (!empty($id)) ? $id : (int) $this->getState('message.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to check out an item for editing.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkout($id = null) {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int) $this->getState('message.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = JFactory::getUser();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    public function getCategoryName($id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
                ->select('title')
                ->from('#__categories')
                ->where('id = ' . $id);
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function publish($id, $state) {
        $table = $this->getTable();
        $table->load($id);
        $table->state = $state;
        return $table->store();
    }

    public function delete($id) {
        $table = $this->getTable();
        return $table->delete($id);
    }
    public function getsendsms($telephon,$message,$signature,$number_sms){
        $db = JFactory::getDBO();
        $user = JFactory::getUser();
        $webServiceURL  = "http://login.parsgreen.com/Api/SendSMS.asmx?WSDL";
        $webServiceSignature = $signature;
        $webServiceNumber   = $number_sms;
        $Mobiles      = array ($telephon); // all mobile add in this array => support one or more
        $isFlash = false; // falsh sms => open quick in phone and after close message , cleare from phone ;
        mb_internal_encoding("utf-8");
        $textMessage=$message; // sms text// the text or body for sending
        $textMessage= $textMessage; // encoding to utf-8
        // OR
        //$textMessage=iconv($encoding, 'UTF-8//TRANSLIT', $textMessage); // encoding to utf-8
        // OR
        //$textMessage =  utf8_encode( $str); // encoding to utf-8

        $parameters['signature'] = $webServiceSignature;
        $parameters['from' ]= $webServiceNumber;
        $parameters['to' ]  = $Mobiles;
        $parameters['text' ]=$textMessage;
        $parameters[ 'isFlash'] = $isFlash;
        $parameters['udh' ]= ""; // this is string  empty
        $parameters['success'] = 0x0; // return refrence success count // success count is number of send sms  success
        $parameters[ 'retStr'] = array( 0  ); // return refrence send status and mobile and report code for delivery
        try
        {
            $con = new SoapClient($webServiceURL);

            $responseSTD = (array) $con ->SendGroupSMS($parameters);
            echo  $responseSTD['SendGroupSMSResult'];  /// print status of request // difrent between SendGroupSMSResult and success count
            // maybe you can send request success but success count and retStr be diferent ;

            echo '#';
            echo $responseSTD['success'];

            echo '#dd';
            $responseSTD['retStr'] = (array) $responseSTD['retStr'];
            var_dump($responseSTD['retStr']);
            $re=explode(';',$responseSTD['retStr']["string"]);var_dump($re);

            if($responseSTD['success']==1){
            echo  $query="INSERT INTO #__sms_message(state,checked_out,checked_out_time,created_by,message,state_message,telephon)
                    VALUES (1,".time().",'".date("Y-m-d H:i:s")."',".$user->id.",'".$message."','".$re[2]."','".$re[0]."')";
                $db->setQuery($query);
              echo  $db->query();
            }
            if       ( $responseSTD['success']>1)
            {


                foreach ($responseSTD['retStr']['string'] as $key => $val)

                {
                    echo '@';
                    echo $val;
                    // pattern => mobile;sendstatus;reportId
                    //@09331001391;0;124172151191542323
                    //@09331001391;0;115161825942015958
                    $re=explode(';',$val);var_dump($re);
                    echo  $query="INSERT INTO #__sms_message(state,checked_out,checked_out_time,created_by,message,state_message,telephon)
                    VALUES (1,".time().",'".date("Y-m-d H:i:s")."',".$user->id.",'".$message."','".$re[2]."','".$re[1]."')";
                    $db->setQuery($query);
                    $db->query();
                }
            }
            else if ( $responseSTD['success']==1)
            {
                echo  $responseSTD['retStr']['string'];
            }
            else
            {
                echo 'dont any send';
            }
        }
        catch (SoapFault $ex)
        {
            echo $ex->faultstring;
        }

    }

}
