<?php
    /*------------------------------------------------------------------------
	# mod_sp_stock - Yahoo stock module by JoomShaper.com
	# ------------------------------------------------------------------------
	# author    JoomShaper http://www.joomshaper.com
	# Copyright (C) 2010 - 2012 JoomShaper.com. All Rights Reserved.
	# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
	# Websites: http://www.joomshaper.com
    -------------------------------------------------------------------------*/

    // no direct access
    defined('_JEXEC') or die('Restricted access');

    /**
    * 
    */
  

    class Mod_SP_Stock
    {	
        private $config;
        private $raw_data;
        private $raw_url;
        private $base_url = 'http://chart.finance.yahoo.com/z?';
        private $symbols = array();

        //Initiate configurations
        public function __construct($params) {
            $this->raw_url = $this->generateURL($params);
            array_push($this->symbols,'"'.$params->get('stock_id').'"');

            if( $params->get('comparing_ids')!='' ) {
                $z = explode(',',$params->get('comparing_ids'));
                foreach($z as $v) array_push($this->symbols,'"'.trim($v).'"');
            }

            return $this;
        }

        private function generateURL($params)
        {
            $p = array();
            $qs = '&amp;p=';

            if( $params->get('moving_average_indicator')!='' )
            {
                $p = explode(',',$params->get('moving_average_indicator'));
                foreach($p as $v) $k[] = 'm'.trim($v);

                $qs .= implode(',',$k);
            }

            if( $params->get('exponential_moving_average_indicator')!='' )
            {
                $p = explode(',',$params->get('exponential_moving_average_indicator'));
                foreach($p as $v) $t[] = 'e'.trim($v);

                $qs .= implode(',',$t);
            }

            if( is_array($params->get('technical_indicator')) )
            {

                $qs .= implode(',', $params->get('technical_indicator') );
            }

            if( $params->get('comparing_ids')!='' )
            {
                $z = explode(',',$params->get('comparing_ids'));
                foreach($z as $v) $c[] = trim($v);
                $qs .= '&amp;c='.implode(',',$c);
            }

            $urls = array(
                's'=>$params->get('stock_id'),
                't'=>$params->get('time_span'),
                'q'=>$params->get('chart_type'),
                'l'=>$params->get('chart_scale'),
                'z'=>$params->get('chart_image_size')
            );

            $url = http_build_query($urls,'', '&amp;');
            $url .= $qs;
            return $url;
        }


        public function getChart() {
            return $this->base_url.$this->raw_url;
        }

        public function getQuote()
        {
            $url  = 'http://query.yahooapis.com/v1/public/yql?q=';
            $trail = ('&format=json&diagnostics=true&env=http%3A%2F%2Fdatatables.org%2Falltables.env');
            $YQLquery = rawurlencode(sprintf('select * from yahoo.finance.quotes where symbol in (%s)', implode(',',$this->symbols) ));
            $url = $url.$YQLquery.$trail;
            // create a new cURL resource
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            $data = curl_exec($curl);
            curl_close($curl);
            return json_decode($data,true);
        }
        public function getgroup(){
            $db = JFactory::getDBO();
            $user = JFactory::getUser();
            $query_select="SELECT * FROM   #__usergroups";
            $db->setQuery($query_select);
            $select=$db->loadObjectList();
            $i=0;
            foreach($select as $sle){
                if($sle->parent_id==2){
                    $array["karguzari"][$i]['title']=$sle->title;
                    $array["karguzari"][$i]['id']=$sle->id;
                    $query_select="SELECT * FROM   #__usergroups where parent_id=".$sle->id;
                    $db->setQuery($query_select);
                    $select_g=$db->loadObjectList();
                    $j=0;
                    foreach($select_g as $gro){
                        $array['group'][$i][$j]['title']=$gro->title;
                        $array['group'][$i][$j]['id']=$gro->id;
                            $query_user="SELECT * FROM   #__user_usergroup_map where group_id=".$gro->id;
                            $db->setQuery($query_user);
                            $select_u=$db->loadObjectList();
                            $m=0;
                            foreach($select_u as $urs){
                                $query_user_name="SELECT * FROM   #__comprofiler where user_id=".$urs->user_id;
                                $db->setQuery($query_user_name);
                                $select_un=$db->loadObject();
                                $array['user'][$i][$j][$m]['first']=$select_un->firstname;
                                $array['user'][$i][$j][$m]['last']=$select_un->lastname;
                                $m++;
                            }
                        $j++;
                    }

                    $i++;
                }
            }
            return $array;
        }
        public function getpermissiongroup(){
            $db = JFactory::getDBO();
            $user = JFactory::getUser();
            $query_select="SELECT group_id FROM   #__user_usergroup_map where user_id=".$user->id;
            $db->setQuery($query_select);
            $spermission_group=$db->loadObjectList();
            $i=0;
            foreach($spermission_group as $val)
                $spermission[$i++]=$val->group_id;
            return $spermission;
        }
        public function getpermissionkargozari(){
            $arr=Mod_SP_Stock::getpermissiongroup();
            $arr=join(',',$arr);
            $db = JFactory::getDBO();
            $user = JFactory::getUser();
            $query_select="SELECT * FROM  #__usergroups where id  in ($arr)";
            $db->setQuery($query_select);
            $spermission_kar=$db->loadObjectList();
            $i=0;
            foreach($spermission_kar as $val)
                $spermission[$i++]=$val->parent_id;
            return $spermission;
        }
    }
