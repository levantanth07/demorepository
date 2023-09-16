<?php
class NavigationForm extends Form{
	function NavigationForm(){
		Form::Form('NavigationForm');
		$this->map = array();
		//$this->link_css(Portal::template('cms').'/css/style.css');
		$this->link_css(Portal::template('cms').'/css/item.css');
		if(Module::get_setting('list_skin_template'))
		{
			$this->link_css(Module::get_setting('list_skin_template').'/style.css');
		}
		if(Module::get_setting('frame_skin_template'))
		{
			$this->link_css(Module::get_setting('frame_skin_template').'/style.css');
		}
		if(Module::get_setting('content_css'))
		{
			Portal::$extra_header .= '<style type="text/css"> '.Module::get_setting('content_css').' </style>';
		}
	}
	function submit()
	{
	}
	function draw(){
		if(Portal::get_setting('use_cache') or !($list_layout = Module::get_setting('cache')))
		{
			$no_frame = true;
			if(Module::get_setting('frame_template') and file_exists(Module::get_setting('frame_template').'/layout.php')){
				$frame_code = file_get_contents(Module::get_setting('frame_template').'/layout.php');
				$no_frame = false;
			}
			if($no_frame==true){
				$frame_code = '{{-content-}}';
			}
			if(Module::get_setting('list_layout_template') and file_exists(Module::get_setting('list_layout_template').'/layout.php'))
			{
				$frame_code = str_replace(
					array('{{-content-}}','{{-title-}}'),
					array(
						file_get_contents(Module::get_setting('list_layout_template').'/layout.php'),
						'<?php echo $title;?>',
					),
					$frame_code
				);
				require_once 'packages/core/includes/portal/generate_layout.php';
				$generate_layout = new GenerateLayout($frame_code);
				$list_layout = $generate_layout->generate_text($generate_layout->synchronize());
				if(Module::get_setting('list_skin_template') and preg_match('/packages\/(\w+)\/templates\/Navigation\/skins\/(\w+)/',Module::get_setting('list_skin_template'),$patterns))
				{
					$list_layout = '<div class="'.$patterns[1].'-navigation-'.$patterns[2].'">'.$list_layout.'</div>';
				}
				if(Module::get_setting('frame_skin_template') and preg_match('/packages\/(\w+)\/templates\/Frame\/skins\/(\w+)/',Module::get_setting('frame_skin_template'),$patterns))
				{
					$list_layout = '<div class="'.$patterns[1].'-frame-'.$patterns[2].'">'.$list_layout.'</div>';
				}
				$init_code = '<?php
	$title = Module::get_setting(\'title\')?Portal::language(Module::get_setting(\'title\')):\'\';
	$hide_columns = Module::get_setting(\'hide_columns\')?explode(\',\',Module::get_setting(\'hide_columns\')):array();
	';
				if(Module::get_setting('list_code_template') and file_exists(Module::get_setting('list_code_template').'/code.php'))
				{
					$init_code .= file_get_contents(Module::get_setting('list_code_template').'/code.php');
				}
				//$init_code .= ' System::print_array($this->map[\'items\']);';
				if(Module::get_setting('detail_code_template') and file_exists(Module::get_setting('detail_code_template').'/code.php'))
				{
					$init_code .= file_get_contents(Module::get_setting('detail_code_template').'/code.php');
				}
				$list_layout = $init_code.'?>'.$list_layout;
				Module::set_setting('cache',$list_layout);
			}
			else
			{
				$list_layout = '';
			}
			//System::print_array($list_layout);
		}
		/*if(User::is_admin())
		{
			$list_layout .= '<div><a href="?'.$_SERVER['QUERY_STRING'].'&cmd=delete_cache_'.Module::block_id().'">'.Portal::language('Delete_cache').'</a></div>';
		}*/
		eval('?>'.$list_layout.'<?php ');
	}
}
?>