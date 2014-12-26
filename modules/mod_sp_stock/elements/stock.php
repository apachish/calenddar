<?php
    /*------------------------------------------------------------------------
	# mod_sp_stock - Yahoo stock module by JoomShaper.com
	# ------------------------------------------------------------------------
	# author    JoomShaper http://www.joomshaper.com
	# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
	# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
	# Websites: http://www.joomshaper.com
    -------------------------------------------------------------------------*/
	
    // Check to ensure this file is included in Joomla!
    defined('_JEXEC') or die('Restricted access');

    jimport('joomla.form.formfield');

    class JFormFieldStock extends JFormField {

        protected $type = 'Stock';

        public function getInput() {

            $this->value = (array) $this->value;

            return 
            '<select id="'.$this->id.'" name="'.$this->name.'[]" " multiple="multiple">'

            .'<option value="" '.(empty($this->value[0])?" selected":"").'>None</option>'
            .'<option value="fs"'.(in_array('fs',$this->value,true)?" selected":"").'>Stochastic</option>'
            .'<option value="m26-12-9" '.(in_array('m26-12-9',$this->value,true)?" selected":"").'>Moving-Average-Convergence-Divergence</option>'
            .'<option value="f14"'.(in_array('f14',$this->value,true)?" selected":"").'>Money Flow Index </option>
            <option value="p12"'.(in_array('p12',$this->value,true)?" selected":"").'>Rate of Change </option>
            <option value="r14"'.(in_array('r14',$this->value,true)?" selected":"").'>Relative Strength Index </option>
            <option value="ss"'.(in_array('ss',$this->value,true)?" selected":"").'>Slow Stochastic </option>
            <option value="v"'.(in_array('v',$this->value,true)?" selected":"").'>Volume (Inside chart)</option>
            <option value="vm"'.(in_array('vm',$this->value,true)?" selected":"").'>Volume with Moving Average </option>
            <option value="w14"'.(in_array('w14',$this->value,true)?" selected":"").'>Williams Percent Range  </option>
            <option value="b"'.(in_array('b',$this->value,true)?" selected":"").'>Bollinger Bands  </option>
            <option value="p"'.(in_array('p',$this->value,true)?" selected":"").'>Parabolic Stop And Reverse </option>
            <option value="s"'.(in_array('s',$this->value,true)?" selected":"").'>Splits </option>'

            .'</select>';
        }


        public function getLabel() {
            return '<span style="text-decoration: underline;">' . parent::getLabel() . '</span>';
        }
}