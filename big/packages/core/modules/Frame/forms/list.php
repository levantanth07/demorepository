<?php
class FrameForm extends Form
{
	function FrameForm()
	{
		Form::Form('FrameForm');
		if(Module::get_setting('frame_skin_template'))
		{
			$this->link_css(Module::get_setting('frame_skin_template').'/style.css');
		}
	}
	function draw()
	{
		if(Module::get_setting('frame_template') and file_exists(Module::get_setting('frame_template').'/layout.php'))
		{
			$frame_code = file_get_contents(Module::get_setting('frame_template').'/layout.php');
		}
		else
		{
			$frame_code = '{{-content-}}';
		}
		if(Module::get_setting('title_category')){
			if(Url::get('name_id') and $category = DB::fetch('select id,name_'.Portal::language().' as name,name_id from category where name_id="'.Url::sget('name_id').'"')){
				$title = $category['name'];
			}else{
				if($title = Module::get_setting('title')){
					$title = Portal::language($title);
				}else{
					$title = '';
				}
			}
		}else{
			if($title = Module::get_setting('title'))
			{
				if(strpos($title,'Portal::'))
				{
					eval('$title="'.$title.'";');
				}
				else
				{
					$title=Portal::language($title);
				}
			}
			else
			{
				$title = '';
			}
		}
		$frame_code = str_replace(
			array('{{-content-}}','{{-title-}}'),
			array(
				'<?php Module::get_sub_regions(\'content\');?>',
				'<?php echo $title;?>'
			),
			$frame_code
		);
		if(Module::get_setting('frame_skin_template') and preg_match('/packages\/(\w+)\/templates\/Frame\/skins\/(\w+)/',Module::get_setting('frame_skin_template'),$patterns))
		{
			$frame_code = '<div class="'.$patterns[1].'-frame-'.$patterns[2].'">'.$frame_code.'</div>';
		}
		eval('?>'.$frame_code.'<?php ');
	}
}
?>