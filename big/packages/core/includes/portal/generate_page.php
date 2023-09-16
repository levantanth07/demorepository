<?php
require_once 'page_layout.php';
class GeneratePage{
	var $regions = array();
	var $modules = array();
	var $blocks = array();
	function __construct($row){
		$this->data = $row;
		require_once 'cache/language_'.Portal::language().'.php';
	}
	function generate($return =false){
		$code = '';
		$code .= $this->generate_text();
		$cache_file=ROOT_PATH.'cache/pages/'.$this->data['name'].($this->data['params']?'.'.$this->data['params']:'').'.cache.php';
		if($fp = @fopen($cache_file, 'w+')){
			fwrite ($fp, $code );
			fclose($fp);
			if($return){
				ob_start();
			}
			require_once ($cache_file);
			if($return){
				$st = ob_get_contents();
				ob_end_clean();
				return $st;
			}
		}else{
			eval('?>'.$code.'<?php ');
		}
	}
	function generate_text(){
		$code='<?php
'.(($this->data['condition'] and 0)?'if(!('.$this->data['condition'].')){
	URL::redirect(Portal::get_setting(\'page_name_home\'));
}
':'').'
Module::invoke_event(\'ONLOAD\',System::$false,System::$false);
global $blocks;
global $plugins;
$plugins = ';
		$this->plugins = DB::select_all('module','`type` = "PLUGIN"');
		$code .= var_export($this->plugins,true);
		$code.=';$blocks = ';
		$this->blocks = DB::select_all('block','`page_id` = '.$this->data['id'],'container_id,position asc');
		foreach($this->blocks as $id=>$block){
			$this->blocks[$id]['settings'] = MiString::get_list(DB::fetch_all('select setting_id as id, value as name from block_setting where block_id="'.$id.'"'),'name');
			$settings = MiString::get_list(DB::fetch_all('select id, default_value as name from module_setting where module_id="'.$block['module_id'].'"'),'name');
			foreach($settings as $setting_id=>$value){
				if(!isset($this->blocks[$id]['settings'][$setting_id])){
					$this->blocks[$id]['settings'][$setting_id] = $value;
				}
			}
			$this->blocks[$id]['module'] = DB::fetch('select id, name, path, `type`, action_module_id, use_dblclick'.(($this->blocks[$id]['container_id'] != 0)?',layout,code':'').',package_id from module where id="'.$block['module_id'].'"');
			if($this->blocks[$id]['module']['type'] == 'WRAPPER'){
				$this->blocks[$id]['wrapper'] = DB::select('module',$this->blocks[$id]['module']['action_module_id']);
			}
		}
		$code .= var_export($this->blocks,true).';
		Portal::$page = '.var_export($this->data,true).';
		$__OVERLOAD_FEATURES__ = Form::get_flash_message(\'__OVERLOAD_FEATURES__\');
		foreach($blocks as $id=>$block){
			if($id === $__OVERLOAD_FEATURES__){
				continue;
			}
			if($block[\'module\'][\'type\'] == \'WRAPPER\'){
				require_once $block[\'wrapper\'][\'path\'].\'class.php\';
				$blocks[$id][\'object\'] = new $block[\'wrapper\'][\'name\']($block);
				if(URL::get(\'form_block_id\')==$id){
					$blocks[$id][\'object\']->submit();
				}
			}
			else
			if($block[\'module\'][\'type\'] != \'HTML\' and $block[\'module\'][\'type\'] != \'content\' and $block[\'module\'][\'name\'] != \'HTML\'){
				require_once $block[\'module\'][\'path\'].\'class.php\';
				$blocks[$id][\'object\'] = new $block[\'module\'][\'name\']($block);
				if(URL::get(\'form_block_id\')==$id){
					$blocks[$id][\'object\']->submit();
				}
			}
		}
		require_once \'packages/core/includes/utils/draw.php\';
		?>';
		$filename = ROOT_PATH.'packages/core/includes/portal/header.php';
		$fp = fopen($filename, 'r');
		$code .= fread($fp,filesize($filename));
		fclose($fp);
		if($this->data['is_use_sapi']){
			$code.= '<script src="packages/core/includes/js/tcv/tcv.js?load=user,url,module,portal,page,form,window,utils" type="text/javascript"></script>
<script type="text/javascript">
TCV.Portal.initialize(<?php
	$portal_options = Session::get(\'portal\');
	$portal_options[\'settings\'] = Portal::$current->settings;
	unset($portal_options[\'cache_privilege\']);
	unset($portal_options[\'cache_setting\']);
	unset($portal_options[\'password\']);
	$portal_options[\'languageID\'] = Portal::language();
	echo MiString::array2js($portal_options);
?>);
TCV.Page.initialize(<?php
	$page_options = Portal::$page;
	echo MiString::array2js($page_options);
?>);
TCV.User.initialize(<?php
	$user_options = User::$current->data;
	$user_options[\'groups\'] = User::$current->groups;
	$user_options[\'settings\'] = User::$current->settings;
	$user_options[\'actions\'] = isset(User::$current->actions[PORTAL_ID])?User::$current->actions[PORTAL_ID]:array();
	unset($user_options[\'cache_privilege\']);
	unset($user_options[\'cache_setting\']);
	unset($user_options[\'password\']);
	echo MiString::array2js($user_options);
?>);
</script>';
		}
		$text = file_get_contents($this->data['layout']);
		//DB::fetch('select content from layout where id="'.$this->data['layout_id'].'"','content');
		while(($pos=strpos($text,'[[|'))!==false){
			$code .= substr($text, 0,  $pos);
			$text = substr($text, $pos+3,  strlen($text)-$pos-3);
			if(preg_match('/([^\|]*)/',$text, $match)){
				if(isset($match[1])){
					$code .= $this->generate_region($match[1]);
				}
				if(($pos = strpos($text,'|]]',0))!==false){
					$text = substr($text, $pos+3,  strlen($text)-$pos-3);
				}
			}else{
				break;
			}
		}
		$code .= $text;
		$filename = ROOT_PATH.'packages/core/includes/portal/footer.php';
		$fp = fopen($filename, 'r');
		$code .= fread($fp,filesize($filename));
		$code .= '
<?php Module::invoke_event(\'ONUNLOAD\',System::$false,System::$false);?>';
		fclose($fp);
		return $code;
	}
	function generate_region($region){
		$code = '';
		foreach($this->blocks as $id=>$block){
			if($block['region']==$region and $block['container_id'] == 0){
				if($block['module']['type'] == 'HTML'){
					$code .= $this->generate_module_html($id,$block['module']);
				}
				else
				if($block['module']['type'] == 'content'){
					$code .= $this->generate_module_content($id,$block['module']);
				}else{
					if($this->data['is_use_sapi']){
						$code.= '<script language="javascript">
TCV.currentModule = new TCV.Module(<?php
	$module_options = array(
		\'id\'=>$blocks['.$id.'][\'id\'],
		\'moduleID\'=>$blocks['.$id.'][\'module_id\'],
		\'pageID\'=>$blocks['.$id.'][\'page_id\'],
		\'name\'=>$blocks['.$id.'][\'module\'][\'name\'],
		\'packageID\'=>$blocks['.$id.'][\'module\'][\'package_id\'],
		\'region\'=>$blocks['.$id.'][\'region\'],
		\'position\'=>$blocks['.$id.'][\'position\'],
		\'settings\'=>$blocks['.$id.'][\'settings\']
	);
	echo MiString::array2js($module_options);
?>);
</script>';
					}
					$code .= '<?php if(isset($blocks['.$id.'][\'object\'])){
						$blocks['.$id.'][\'object\']->on_draw();
					}
					if(Form::has_flash_message(\'__OVERLOAD_FEATURES_TIME__\')){
						echo \'<div class="content-wrapper" style="min-height: 797px;padding: 20px">\';
						Form::draw_message(\'Tính năng đang được bảo trì, bạn vui lòng truy cập lại lúc \' . Form::get_flash_message(\'__OVERLOAD_FEATURES_TIME__\'), 
						[\'bgColor\' => \'#0bab0b\', \'textColor\' => \'#fff\']
						);
						echo \'</div>\';
					}; ?>';
				}
			}
		}
		return $code;
	}
	function generate_module_html($block_id,$module){
		$module_data = DB::select('module',$module['id']);
		$code = '<?php
		$blocks['.$block_id.'][\'object\'] = new Module($blocks['.$block_id.']);
		Module::$current=&$blocks['.$block_id.'][\'object\'];
		Module::invoke_event(\'ONDRAW\',$blocks['.$block_id.'][\'object\'],System::$false);';
		$results = $this->convert_language($module['id'],$module_data['layout']);
		$code .= '?>'.$results.'<?php
		Module::invoke_event(\'ONDRAW\',$blocks['.$block_id.'][\'object\'],System::$false);';
		$code .= 'Module::$current=&System::$false;?>';
		return $code;
	}
	function generate_module_content($block_id, $module){
		$module_data = DB::select('module',$module['id']);
		$code = '<?php
		$blocks['.$block_id.'][\'object\'] = new Module($blocks['.$block_id.']);
		Module::$current = &$blocks['.$block_id.'][\'object\'];';
		require_once 'packages/core/includes/portal/generate_layout.php';
		$generate_layout = new GenerateLayout($module_data['layout']);
		$layout = str_replace('$this->map','$map',$generate_layout->generate_text($generate_layout->synchronize()));
		//if(!$row['is_cached'])
		$results = $this->convert_language($module['id'],$layout);
		echo $block_id. '<br />'; die();
		$code.='$map = array(\'content_name\'=>\''.$module['name'].'\');$ok=true;'.$module_data['code'].'
			if($ok){Module::invoke_event(\'ONDRAW\',$blocks['.$block_id.'][\'object\'],System::$false);';
		$code.='?>'.$results.'<?phpModule::invoke_event(\'ONENDDRAW\',$blocks['.$block_id.'][\'object\'],System::$false);';
		$code.='}';
		$code .= 'Module::$current=&System::$false;?></div>';
		return $code;
	}
	function convert_language($module_id, $layout){
		return preg_replace('/\[\[\.(\w+)\.\]\]/','<?php echo Portal::language(\'\\1\');?>',$layout);
	}
}
?>