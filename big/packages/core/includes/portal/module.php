<?php
class Module{
	var $forms = array();
	var $data = false;
	static $current = false;
	static $blocks = array();
	function Module($row){
		Module::$current=&$this;
		$this->data = $row;
		Module::invoke_event('ONLOAD',$this,System::$false);
	}
	static function block_id(){
		return Module::$current->data['id'];
	}
	function add_form($form){
		$this->forms[]=$form;
	}
	function submit(){
		Module::invoke_event('ONSUBMIT',$this,System::$false);
		Module::$current=&$this;
		$submit=$this->on_submit();
		Module::invoke_event('ONENDSUBMIT',$this,System::$false);
		Module::$current=&System::$false;
	}
	function on_submit(){
		if($this->forms){
			for($i=0;$i<sizeof($this->forms);$i++){
				if($this->forms[$i]->on_submit()){
					return true;
				}
			}
		}
	}
	function draw(){
		if($this->forms){
			foreach($this->forms as $form){
				$form->on_draw();
			}
		}
	}
	function on_draw(){
		Module::invoke_event('ONDRAW',$this,System::$false);
		Module::$current=&$this;
		$this->draw();
		Module::invoke_event('ONENDDRAW',$this,System::$false);
		Module::$current=&System::$false;
	}
	static function get_setting($name,$default=false, $block_id = false){
		if($block_id){
			if($block_id<1000){
				if($block=DB::select('block','id="'.intval(URL::sget('block_id')).'"')){
					return DB::fetch('select value from block_setting where block_id="'.intval(URL::sget('block_id')).'" and setting_id="'.$block['module_id'].'_'.$name.'"','value',$default);
				}
			}
			else{
				if($block=DB::select('block','id="'.intval($block_id).'"')){
					return DB::fetch('select value from block_setting where block_id="'.intval($block_id).'" and setting_id="'.$block['module_id'].'_'.$name.'"','value',$default);
				}
			}
		}
		return isset(Module::$current->data['settings'][Module::$current->data['module_id'].'_'.$name])?Module::$current->data['settings'][Module::$current->data['module_id'].'_'.$name]:$default;
	}
	function set_setting($setting_id,$value){
		if(isset($this) and isset($this->data['id'])){
			$block_id = $this->data['id'];
			$module_id = $this->data['module_id'];
			$page_id = $this->data['page_id'];
		}
		else{
			$block_id = Module::block_id();
			$module_id = Module::$current->data['module_id'];
			$page_id = Module::$current->data['page_id'];
		}
		if($setting = DB::select('block_setting','block_id="'.$block_id.'" and setting_id="'.$module_id.'_'.$setting_id.'"')){
			DB::update('block_setting',array('value'=>$value),'id="'.$setting['id'].'"');
		}
		else{
			DB::insert('block_setting',array('setting_id'=>$module_id.'_'.$setting_id,'value'=>$value,'block_id'=>$block_id));
		}
		require_once 'packages/core/includes/portal/update_page.php';
		update_page($page_id);
	}
	function get_help_topic_id(){
		if(isset(Module::$current->data['help_topics'][URL::get('cmd')])){
			return Module::$current->data['help_topics'][URL::get('cmd')];
		}
		else
		if(isset(Module::$current->data['help_topics'][''])){
			return Module::$current->data['help_topics'][''];
		}
		else{
			return 1;
		}
	}
	static function get_sub_regions($region){
		$last_module = &Module::$current;
		$block_id = Module::block_id();
		global $blocks;
		foreach($blocks as $id => &$block){
			if($block['container_id'] == $block_id and $block['region'] == $region){
				if($block['module']['type'] == 'HTML'){
					Module::generate_module_html($block);
				}
				else
				if($block['module']['type'] == 'content'){
					Module::generate_module_content($block);
				}
				else{
					$block['object']->on_draw();
				}
			}
		}
		Module::$current = &$last_module;
	}
	static function generate_module_html(&$block){
	}
	static function generate_module_content(&$block){
	}
	function convert_language($layout){
		eval('?>'. preg_replace('/\[\[\.(\w+)\.\]\]/','<?php echo Portal::language(\'\\1\');?>',$layout).'<?php ');
	}
	function make_ext_region($region, $container_id=0, $baseCls=false){
		global $blocks;
		$first = true;
		$st = '';
		foreach($blocks as $block){
			if($block['region'] == $region and $block['container_id'] == $container_id){
				if(!$first){
					$st .= ',';
				}
				$first = false;
				$st .= '{'.($block['name']?'title: \''.($block['name']?$block['name']:$block['module']['name']).'\',
					tools: tools,
					':'header:false,border:false,bodyBorder:false,frame:false,footer:false,').'
					'.($baseCls?'baseCls:\'simple-panel\',':'').'
					contentEl :\'module_'.$block['id'].'\'
                }';
			}
		}
		if($first){
			$st = 'html:\'\'';
		}
		else{
			$st = 'items:['.$st.']';
		}
		echo $st;
	}
	static function invoke_event($event, &$module, &$params){
		global $plugins;
		if($plugins){
			foreach($plugins as $plugin){
				if($plugin['action'] == $event and (($module === System::$false) or ($module->data['module_id'] == $plugin['action_module_id']))){
					if(!class_exists($plugin['name'])){
						require_once $plugin['path'].'class.php';
						eval($plugin['name'].'::init($module,$params);');
					}
					eval($plugin['name'].'::run($module,$params);');
				}
			}
			if($event == 'ONUNLOAD' and $module === System::$false){
				if(class_exists($plugin['name'])){
					eval($plugin['name'].'::finish($module,$params);');
				}
			}
		}
	}
}
class Plugin{
	static function init(&$module,&$params){
	}
	static function run(&$module,&$params){
	}
	static function finish(&$module,&$params){
	}
}