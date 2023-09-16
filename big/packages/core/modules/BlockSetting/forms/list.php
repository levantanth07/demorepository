<?php
class ListBlockSettingForm extends Form{
	function __construct(){
		Form::Form('ListBlockSettingForm');
		$this->add('name',new TextType(true,'Lỗi nhập tên',3,255));
	}
	function on_submit(){
		if($this->check())
		{
			BlockSettingDB::update_block_name();
			$this->get_items($block, $all_items, $item_values);
			if(isset($_REQUEST['delete_images_items']))
			foreach($_REQUEST['delete_images_items'] as $setting_id=>$checked){
				if($checked and isset($item_values[$setting_id]) and $item_values[$setting_id]['value']){
					if(file_exists($item_values[$setting_id]['value'])){
						unlink($item_values[$setting_id]['value']);
					}
					BlockSettingDB::update_block_setting('',$block['id'],$setting_id);
				}
			}
			if(isset($_REQUEST['copy_from_link_items']))
			foreach($_REQUEST['copy_from_link_items'] as $setting_id=>$url){
				if($url){
					BlockSettingDB::update_block_setting($url,$block['id'],$setting_id);
				}
			}
			if(isset($_FILES) and isset($_FILES['items']) and isset($_FILES['items']['name']) and is_array($_FILES['items']['name'])){
				foreach($_FILES['items']['name'] as $setting_id=>$file_name){
					if($file_name and isset($all_items[$setting_id]) and ($all_items[$setting_id]['type']=='FILE' or $all_items[$setting_id]['type']=='IMAGE')){
						if($all_items[$setting_id]['edit_condition']){
							eval('$can_edit = '.$all_items[$setting_id]['edit_condition'].';');
						}else{
							$can_edit = true;
						}
						if($can_edit){
							if($all_items[$setting_id]['type']=='IMAGE' and !@getimagesize($_FILES['items']['tmp_name'][$setting_id])){
								$this->error('items_'.$setting_id,'Only image uploads are allowed');
								break;
							}else{
								if($all_items[$setting_id]['meta']){
									eval('$meta='.$all_items[$setting_id]['meta'].';');
									if(isset($meta['max_file_size']) and filesize($_FILES['items']['tmp_name'][$setting_id])>$meta['max_file_size']){
										$this->error('items_'.$setting_id,'Filesize must less than '.$meta['max_file_size'].' byte');
										break;
									}
								}
								if(isset($item_values[$setting_id]) and $item_values[$setting_id]['value'] and file_exists($item_values[$setting_id]['value']) and !BlockSettingDB::image_url_is_still_use_by_other_setting($item_values[$setting_id]['value'],$block['id'],$setting_id)){
									unlink($item_values[$setting_id]['value']);
								}
								if(!is_dir('upload/'.substr(PORTAL_ID,1))){
									mkdir('upload/'.substr(PORTAL_ID,1));
								}
								if(!is_dir('upload/'.substr(PORTAL_ID,1).'/block_settings')){
									mkdir('upload/'.substr(PORTAL_ID,1).'/block_settings');
								}
								$new_file_name = 'upload/'.substr(PORTAL_ID,1).'/block_settings/'.str_replace('#','_',$block['id']).'_'.time().'_'.$_FILES['items']['name'][$setting_id];
								if(move_uploaded_file($_FILES['items']['tmp_name'][$setting_id], $new_file_name)){
									$_REQUEST['items'][$setting_id] = $new_file_name;
									if(isset($item_values[$setting_id])){
										BlockSettingDB::update_block_setting($new_file_name,$block['id'],$setting_id);
									}else{
										BlockSettingDB::insert_block_setting($new_file_name,$block['id'],$setting_id);
									}
								}
							}
						}
					}
				}
			}
			if(!$this->is_error()){
				if(isset($_REQUEST['items']))
				foreach($_REQUEST['items'] as $name=>$value){
					if(isset($all_items[$name]) and $all_items[$name]['type']!='IMAGE' and $all_items[$name]['type']!='FILE'){
					}
					if(isset($all_items[$name]) and $all_items[$name]['type']!='IMAGE' and $all_items[$name]['type']!='FILE'){
						if($all_items[$name]['edit_condition']){
							eval('$can_edit = '.$all_items[$name]['edit_condition'].';');
						}else{
							$can_edit = true;
						}
						if($can_edit){
							if((!isset($item_values[$name]) and $value!='') or (isset($item_values[$name]) and $item_values[$name]!=$value)){
								if(isset($item_values[$name])){
									BlockSettingDB::update_block_setting($value,$block['id'],$name);
								}else{
									BlockSettingDB::insert_block_setting($value,$block['id'],$name);
								}
							}
						}
					}
				}
				if($module = DB::select('module',$block['module_id'])){
					if($module['update_setting_code']){
						$block['old_settings'] = array();
						foreach($all_items as $item){
							if(isset($item_values[$item['id']])){
								$block['old_settings'][$item['id']] = $item_values[$item['id']]['value'];
							}else{
								$block['old_settings'][$item['id']] = $item['default_value'];
							}
						}
						$block['settings'] = MiString::get_list(DB::fetch_all('
							SELECT
								setting_id as id, value as name
							FROM
								block_setting
							WHERE
								block_id="'.$block['id'].'"
						'));
						$settings = MiString::get_list(DB::fetch_all('
							select
								id, default_value as name
							from
								module_setting
							where
								module_id="'.$block['module_id'].'"
						'));
						foreach($settings as $setting_id=>$value){
							if(!isset($block['settings'][$setting_id])){
								$block['settings'][$setting_id] = $value;
							}
						}
						require_once $module['path'].'class.php';
						$object = new Module($block);
						eval($module['update_setting_code']);
					}
				}
				$page = DB::select('page',$block['page_id']);
				@unlink('cache/page_layouts/'.$page['name'].'.'.($page['params']?$page['params'].'.':'').'cache.php');
				Url::redirect_current(array('block_id','suss'=>true));
			}
		}
	}
	function draw(){
		require_once 'packages/core/includes/utils/custom_input.php';
		if($this->get_items($block, $all_items, $item_values)){
			$groups = array();
			foreach($all_items as $setting_id=>$item){
				if($item['view_condition']){
					eval('$can_view = ('.$item['view_condition'].');');
				}else{
					$can_view = true;
				}
				if($can_view){
					unset($item['view_condition']);
					if($item['edit_condition']){
						eval('$can_edit = ('.$item['edit_condition'].');');
					}else{
						$can_edit = true;
					}
					$item['edit_condition'] = $can_edit;
					if(isset($item_values[$item['id']])){
						$item['value'] = $item_values[$item['id']]['value'];
					}else{
						$item['value'] = $item['default_value'];
					}
					$input = new CustomInput($item,'items['.$item['id'].']','items_'.$item['id'], $item['value']);
					$item['value'] = $input->html_code();
					if(!isset($groups[$item['group_name']])){
						$groups[$item['group_name']] = array('name'=>$item['group_name'],'items'=>array(),'group_column'=>$item['group_column']);
					}
					if($item['group_name'] == $item['name']){
						$item['name'] = '';
					}
					$groups[$item['group_name']]['items'][$setting_id] = $item;
				}
			}
			$_REQUEST['name'] = $block['name'];
			//System::debug(BlockSettingDB::select_similar_blocks($block));
			$this->parse_layout('list',
				DB::select('module',$block['module_id'])+
				array(
					'name'=>$block['name'],
					'groups'=>$groups,
					'region'=>$block['region'],
					'module_id'=>$block['module_id'],
					'page_name'=>DB::fetch('select name from page where id="'.$block['page_id'].'"','name'),
					'style_list'=>array(0=>'Linebreak', 1=>'Inline', 2=>'Very large information'),
					'copy_setting_id_list'=>array(''=>'')+MiString::get_list(BlockSettingDB::select_similar_blocks($block))
				)
			);
		}
	}
	function get_items(&$block, &$all_items, &$item_values){
		$can_view = false;
		$block_id = URL::get('block_id')?addslashes(URL::get('block_id')):0;
		if(substr($block_id,0,10)=='$template_'){
			$template_id = substr($block_id,10);
			if($block = BlockSettingDB::select_template($template_id)){
				$block['id'] = $block_id;
			}else{
				$block_id = '';
			}
		}
		if(!isset($block) or !$block_id){
			if(!$block_id or !($block = DB::select('block',$block_id))){
				$block = DB::select('block',$block_id);
			}
		}
		if($block){
			$all_items = BlockSettingDB::select_all_module_setting($block['module_id']);
			$item_values = BlockSettingDB::select_all_block_setting($block_id);
			return 1;
		}
	}
}
?>