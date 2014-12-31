<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


JFormHelper::loadFieldClass('hidden');

/**
 * Form Field class for the Joomla Platform.
 * Provides spacer markup to be used in form layouts.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldPrivacy extends JFormFieldHidden
{

	public $type = 'Privacy';
	
	protected function getInput()
	{
		$config = JComponentHelper::getParams('com_jsn');
		$html=array();
		global $JSNSOCIAL;
		if($JSNSOCIAL)
		{
			$html[]='
			  <a id="btn_'.$this->id.'" class="btn btn-default btn-xs btn-mini" data-toggle="dropdown" href="#">
			    <i></i>
			    <span class="caret"></span>
			  </a>
			  <ul class="dropdown-menu pull-right" id="opt_'.$this->id.'">
				'.($config->get('profileACL',2)==2 ? '<li><a href="#" rel="0"><i class="icon icon-eye-open green"></i> '.JText::_('COM_JSN_PUBLIC').'</a></li>': '').'
				<li><a href="#" rel="1"><i class="icon icon-user orange"></i> '.JText::_('COM_JSN_FRIENDSONLY').'</a></li>
				<li><a href="#" rel="99"><i class="icon icon-eye-close red"></i> '.JText::_('COM_JSN_PRIVATE').'</a></li>
			  </ul>
			';
		}
		else
		{
			$html[]='
			  <a id="btn_'.$this->id.'" class="btn btn-default btn-xs btn-mini" data-toggle="dropdown" href="#">
			    <i></i>
			    <span class="caret"></span>
			  </a>
			  <ul class="dropdown-menu pull-right" id="opt_'.$this->id.'">
				'.($config->get('profileACL',2)==2 ? '<li><a href="#" rel="0"><i class="icon icon-eye-open green"></i> '.JText::_('COM_JSN_PUBLIC').'</a></li>': '').'
				<li><a href="#" rel="1"><i class="icon icon-user orange"></i> '.JText::_('COM_JSN_REGISTERED').'</a></li>
				<li><a href="#" rel="99"><i class="icon icon-eye-close red"></i> '.JText::_('COM_JSN_PRIVATE').'</a></li>
			  </ul>
			';
		}
		//'<a href="#'.$this->id.'" class="privacy_btn btn"><i class="icon-locked"></i></a>';
		$html[]=parent::getInput();
		return implode('', $html);
	}

}
