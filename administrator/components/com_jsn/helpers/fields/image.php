<?php
/**
* @copyright	Copyright (C) 2013 Jsn Project company. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @package		Easy Profile
* website		www.easy-profile.com
* Technical Support : Forum -	http://www.easy-profile.com/support.html
*/

defined('_JEXEC') or die;


global $_FIELDTYPES;
if(isset($_GET['id'])) $_FIELDTYPES['image']='COM_JSN_FIELDTYPE_IMAGE';

class JsnImageFieldHelper
{
	public static function create($alias)
	{
		$db = JFactory::getDbo();
		$query = "ALTER TABLE #__jsn_users ADD ".$db->quoteName($alias)." VARCHAR(255)";
		$db->setQuery($query);
		$db->query();
	}
	
	public static function delete($alias)
	{
		$db = JFactory::getDbo();
		$query = "ALTER TABLE #__jsn_users DROP COLUMN ".$db->quoteName($alias);
		$db->setQuery($query);
		$db->query();
	}
	
	public static function getXml($item)
	{
		require_once(JPATH_SITE.'/components/com_jsn/helpers/helper.php');
		$hideTitleInProfile=$item->params->get('hidetitle',0);//(isset($item->params['hidetitle']) && $item->params['hidetitle'] && JRequest::getVar('option')=='com_jsn' ? true : false);
		$defaultvalue=($item->params->get('image_defaultvalue','')!='' ? 'default="'.$item->params->get('image_defaultvalue','').'"' : '');//(isset($item->params['image_defaultvalue']) && $item->params['image_defaultvalue']!='' ? 'default="'.$item->params['image_defaultvalue'].'"' : '');
		$xml='
			<field
				name="'.$item->alias.'"
				type="image"
				id="'.$item->alias.'"
				imageclass="'.$item->alias.' '.$item->params->get('image_class','')/*$item->params['image_class']*/.'"
				description="'.JsnHelper::xmlentities(($item->description)).'"
				accept="image/*"
				label="'.($hideTitleInProfile ? '' : JsnHelper::xmlentities($item->title)).'"
				alt="'.$item->params->get('image_alt','')/*$item->params['image_alt']*/.'"
				'.$defaultvalue.'
				width="'.$item->params->get('image_width','200')/*$item->params['image_width']*/.'"
				height="'.$item->params->get('image_height','200')/*$item->params['image_height']*/.'"
				width_thumb="'.$item->params->get('image_thumbwidth','75')/*$item->params['image_width']*/.'"
				height_thumb="'.$item->params->get('image_thumbheight','75')/*$item->params['image_height']*/.'"
				cropwebcam="'.$item->params->get('image_cropwebcam','0')/*$item->params['image_height']*/.'"
				required="'.($item->required && JRequest::getVar('jform',null)==null ? 'true' : 'false' ).'"
				requiredfile="'.($item->required ? 'true' : 'false' ).'"
				validate="image"
			/>
		';
		return $xml;
	}
	
	public static function loadData($field, $user, &$data)
	{
		$alias=$field->alias;
		$alias_mini=$field->alias.'_mini';
		if(isset($user->$alias) && $user->$alias!='' && file_exists(JPATH_SITE.'/'.$user->$alias))
		{
			$data->$alias=$user->$alias;
			if(strpos($user->$alias,'/profiler/')) $data->$alias_mini=str_replace('_', 'mini_', $user->$alias);
			else $data->$alias_mini=$user->$alias;
		}
		elseif($field->params->get('image_defaultvalue','')!=''/*isset($field->params['image_defaultvalue']) && $field->params['image_defaultvalue']!=''*/)
		{
			$data->$alias=$field->params->get('image_defaultvalue','');//$field->params['image_defaultvalue'];
			if(isset($user->$alias) && strpos($user->$alias,'/profiler/')) $data->$alias_mini=str_replace('_', 'mini_', $field->params->get('image_defaultvalue','')/*$field->params['image_defaultvalue']*/);
			else $data->$alias_mini=$field->params->get('image_defaultvalue','');
		}
		elseif($alias == 'avatar')
		{
			$data->$alias='components/com_jsn/assets/img/default.jpg';
			$data->$alias_mini='components/com_jsn/assets/img/default.jpg';
		}
	}
	
	public static function storeData($field, $data, &$storeData)
	{
		// Set Upload Dir
		$upload_dir=JPATH_SITE.'/images/profiler/';
		if(!file_exists($upload_dir)) 
		{ 
			mkdir($upload_dir); 
		}

		// Get Alias
		$alias=$field->alias;
		if(isset($data[$alias])) $storeData[$alias]=$data[$alias];

		// Delete Image
		if(isset($_POST['jform'][$field->alias.'_delete']))
		{
			// Delete old file
			foreach (glob($upload_dir.$alias.$data['id'].'*') as $deletefile)
			{
				unlink($deletefile);
			}
			
			$storeData[$alias]='';
			return;
		}

		// Single file array
		if(isset($_FILES['jform'])) 
			$fileArray=array(
				'name' => $_FILES['jform']['name'][$alias],
				'type' => $_FILES['jform']['type'][$alias],
				'tmp_name' => $_FILES['jform']['tmp_name'][$alias],
				'error' => $_FILES['jform']['error'][$alias],
				'size' => $_FILES['jform']['size'][$alias],
			);
		else
			$fileArray=array();

		if(file_exists(JPATH_ADMINISTRATOR.'/components/com_k2/lib/class.upload.php')) require_once(JPATH_ADMINISTRATOR.'/components/com_k2/lib/class.upload.php');
		else require_once(JPATH_ADMINISTRATOR.'/components/com_jsn/assets/class.upload.php');
		$foo = new Upload($fileArray);
		if ($foo->uploaded)
		{
			// Delete old file
			foreach (glob($upload_dir.$alias.$data['id'].'*') as $deletefile)
			{
				unlink($deletefile);
			}
			
			$md5=md5(time().rand());
			// Store & Resize Image Thumbs
			$filename=$alias.$data['id'].'mini_'.$md5;
			$foo->file_new_name_body = $filename;
			$foo->image_resize = true;
			$foo->image_ratio_crop = true;
			$foo->image_convert = 'jpg';
			if($field->params->get('image_thumbwidth',75)/*['image_thumbwidth']*/>0) $foo->image_x = $field->params->get('image_thumbwidth',75);//$field->params['image_thumbwidth'];
			if($field->params->get('image_thumbheight',75)/*['image_thumbheight']*/>0) $foo->image_y = $field->params->get('image_thumbheight',75);//$field->params['image_thumbheight'];
			//die($foo->image_x);
			$foo->Process($upload_dir);
			// Store & Resize Image
			$filename=$alias.$data['id'].'_'.$md5;
			$foo->file_new_name_body = $filename;
			$foo->image_resize = true;
			$foo->image_ratio_crop = true;
			$foo->image_convert = 'jpg';
			if($field->params->get('image_width',75)/*['image_width']*/>0) $foo->image_x = $field->params->get('image_width',75);//$field->params['image_width'];
			if($field->params->get('image_height',75)/*['image_height']*/>0) $foo->image_y = $field->params->get('image_height',75);//$field->params['image_height'];
			$foo->Process($upload_dir);
			if ($foo->processed)
			{
				$storeData[$alias]='images/profiler/'.$foo->file_dst_name;
				$foo->Clean();
			}
		}
		elseif(isset($_SESSION['_tmp_img'.$alias])){
			$md5=md5(time().rand());
			$filename=$alias.$data['id'].'_'.$md5.'.jpg';
			$filename_mini=$alias.$data['id'].'mini_'.$md5.'.jpg';
			$path = JPATH_SITE.'/tmp/';
	    	$file = $_SESSION['_tmp_img'.$alias];
			if(file_exists($path . str_replace('.jpg','_big.jpg',$file)) && file_exists($path . str_replace('.jpg','_big.jpg',$file))){
				rename($path . str_replace('.jpg','_big.jpg',$file),JPATH_SITE.'/images/profiler/'.$filename);
				rename($path . str_replace('.jpg','_small.jpg',$file),JPATH_SITE.'/images/profiler/'.$filename_mini);
				$storeData[$alias]='images/profiler/'.$filename;
			} 
			unset($_SESSION['_tmp_img'.$alias]);
		}
	}
	
	public static function image($field)
	{
		$value=$field->__get('value');
		if (empty($value))
		{
			return JHtml::_('users.value', $value);
		}
		else
		{
			return $field->getImage();
		}
		
	}
	
	public static function operations()
	{
		if (empty($_REQUEST['action']))
			$_POST['action'] = 'webcam';
		
		$filename=substr(md5($_SERVER['REMOTE_ADDR'].$_GET['name']),0,10);
		
		if (isset($_POST['action'])) {
			switch ($_POST['action']) {

				//Upload
				case 'upload':
					
					if (isset($_FILES['ajax-uploadimage']['tmp_name'])) {
						//Get uploaded image
						$image = $_FILES['ajax-uploadimage'];

						//Get php.ini upload limit
						$max_post     = (int)(ini_get('post_max_size'));
						$max_upload   = (int)(ini_get('upload_max_filesize'));
						$memory_limit = (int)(ini_get('memory_limit'));
						$upload_limit = min($max_upload, $max_post, $memory_limit);
						
						//Get Extension
						$ext = strtolower($image['name'][strlen($image['name'])-4].$image['name'][strlen($image['name'])-3].$image['name'][strlen($image['name'])-2].$image['name'][strlen($image['name'])-1]);
						if ($ext[0] == '.') $ext = substr($ext, 1, 3);

						//Get some config options
						$upload_tmp  = JPATH_SITE.'/tmp/';
						
						$errors = array(
							0 => "The file is to big. Upload a image under $upload_limit",
							1 => 'This file extension is not allowed !',
							2 => "Error."
						);
						if (!is_uploaded_file($image['tmp_name'])) {
							return false;
						} else if ( $image['size'] > $upload_limit*100*100*100 ) {
							echo json_encode(array('success' => false, 'data' => $errors[0]));
						} else if (!in_array($ext,  explode('|', 'jpg|png|jpeg|gif|bmp') )) {
							echo json_encode(array('success' => false, 'data' => $errors[1]));
						
						} else {
							require_once(JPATH_ADMINISTRATOR.'/components/com_jsn/assets/class.upload.php');
							$foo = new Upload($image);
							if ($foo->uploaded)
							{
								if(file_exists($upload_tmp.$filename.'.jpg')) unlink($upload_tmp.$filename.'.jpg');
								if(file_exists($upload_tmp.$filename.'_original.jpg')) unlink($upload_tmp.$filename.'_original.jpg');
								if(file_exists($upload_tmp.$filename.'_big.jpg')) unlink($upload_tmp.$filename.'_big.jpg');
								if(file_exists($upload_tmp.$filename.'_small.jpg')) unlink($upload_tmp.$filename.'_small.jpg');
								
								$foo->file_new_name_body = $filename.'_original';
								$foo->image_convert = 'jpg';
								$foo->Process($upload_tmp);
								$foo->file_new_name_body = $filename;
								$foo->image_convert = 'jpg';
								$foo->image_x=350;
								$foo->image_resize = true;
								$foo->image_ratio_y = true;
								$foo->Process($upload_tmp);
								$_SESSION['_tmp_img'.$_GET['name']] = $filename.'.jpg';
								$foo->Clean();
								echo json_encode(array('success' => true, 'data' => JURI::root().'/tmp/'.$filename.'.jpg?'.time()));
							}
						}
					} 
					else 
						echo json_encode(array('success' => false, 'data' => $errors[2]));
				break;

				//Webcam upload
				case 'webcam':
					$data = file_get_contents('php://input');
					$path = JPATH_SITE.'/tmp/';
					
					if (!empty($data)) {
						$file = $filename.'.jpg';
						$file2 = $filename.'_original.jpg';
						if (file_put_contents($path . $file, $data ) && file_put_contents($path . $file2, $data )) {
							$_SESSION['_tmp_img'.$_GET['name']] = $file;
							echo JURI::root() . '/tmp/' .  $file . '?'.time();
						} else echo '0';
					} else echo '0';
				break;

				//Save cropped image
				case 'save':
					$path = JPATH_SITE.'/tmp/';
			    	$file = $_SESSION['_tmp_img'.$_GET['name']];
			    	$new_file = str_replace('-', '', $file);

			    	unlink($path . $file);
					if(file_exists($path . str_replace('.jpg','_big.jpg',$file))) unlink($path . str_replace('.jpg','_big.jpg',$file)); 
					if(file_exists($path . str_replace('.jpg','_small.jpg',$file))) unlink($path . str_replace('.jpg','_small.jpg',$file)); 
					require_once(JPATH_ADMINISTRATOR.'/components/com_jsn/assets/class.upload.php');
					$foo = new upload($path.str_replace('.jpg','_original.jpg',$file));
					$foo->file_new_name_body = str_replace('.jpg','_original_crop',$file);
					$rapp=$foo->image_src_x/350;
					$w=round($_POST['w']*$rapp);
					$h=round($_POST['h']*$rapp);
					$x1=round($_POST['x1']*$rapp);
					$y1=round($_POST['y1']*$rapp);
					$x2=$foo->image_src_x-($x1+$w);
					$y2=$foo->image_src_y-($y1+$h);
					$foo->image_crop=array($y1,$x2,$y2,$x1);
					$foo->Process($path);
					
					unlink($path . str_replace('.jpg','_original.jpg',$file)); 
					
					$foo = new upload($path.str_replace('.jpg','_original_crop.jpg',$file));
					$foo->file_new_name_body = str_replace('.jpg','_big',$file);
					$foo->image_x=$_GET['width'];
					$foo->image_resize = true;
					$foo->image_ratio_y = true;
					$foo->Process($path);
					
					$foo = new upload($path.str_replace('.jpg','_original_crop.jpg',$file));
					$foo->file_new_name_body = str_replace('.jpg','_small',$file);
					$foo->image_x=$_GET['thumbwidth'];
					$foo->image_resize = true;
					$foo->image_ratio_y = true;
					$foo->Process($path);
					
					unlink($path . str_replace('.jpg','_original_crop.jpg',$file)); 
					
					$foo->Clean();
			    	
					echo json_encode(array('success' => true, 'data' => JURI::root().'/tmp/'.str_replace('.jpg','_small',$file).'.jpg?'.time()));
					
				break;
			}
		}
		
	}

}
