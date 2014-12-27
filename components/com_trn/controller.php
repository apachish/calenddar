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

}
