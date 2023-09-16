<?php
	if(Module::get_setting('frame_template') and file_exists(Module::get_setting('frame_template').'/layout.php'))
	{
		$frame_code = file_get_contents(Module::get_setting('frame_template').'/layout.php');
	}
	else
	{
		$frame_code = '{{-content-}}';
	}
	if(Module::get_setting('title'))
	{
		eval('$title="'.Module::get_setting('title').'";');
	}
	else
	{
		$title = '';
	}
	$frame_code = str_replace(
		array('{{-content-}}','{{-title-}}'),
		array(
			'[[--content--]]',
			'<?php echo $title;?>'
		),
		$frame_code
	);
	eval('?>'.$frame_code.'<?php ');
?>