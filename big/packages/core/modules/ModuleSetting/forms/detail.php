<?php
class ModuleSettingForm extends Form
{
	function ModuleSettingForm()
	{
		Form::Form("ModuleSettingForm");
		$this->add('id',new IDType(true,'object_not_exists','module_setting'));
		$this->link_css(Portal::template('core').'/css/category.css');
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm'))
		{
			$this->delete($this,$_REQUEST['id']);
			Url::redirect_current(array('module_id'=>isset($_GET['module_id'])?$_GET['module_id']:'',
	));
		}
	}
	function draw()
	{
		DB::query('
			select
				`module_setting`.id
				,`module_setting`.`name` ,`module_setting`.`description` ,`module_setting`.`type` ,`module_setting`.`default_value` ,`module_setting`.`value_list` ,`module_setting`.`edit_condition` ,`module_setting`.`view_condition` ,`module_setting`.`extend` ,`module_setting`.`group_name` ,`module_setting`.`position` ,`module_setting`.`meta` ,`module_setting`.`group_column` ,`module_setting`.`update_code`
				,`module`.`name` as module_id
			from
			 	`module_setting`
				left outer join `module` on `module`.id=`module_setting`.module_id
			where
				`module_setting`.id = "'.URL::sget('id').'"
			order by module.id
			');
		if($row = DB::fetch())
		{
			$defintition = array('TEXT',' INT',' FLOAT',' EMAIL',' COLOR',' FONT_FAMILY','FONT_SIZE','FONT_WEIGHT','TEXTAREA',' TABLE',' SELECT',' DATE',' DATETIME',' CHECKBOX',' RADIO',' FILE',' IMAGE',' YESNO');
			if(isset($defintition[$row['type']]))
			{
				$row['type'] = $defintition[$row['type']];
			}
			else
			{
				$row['type'] = '';
			}
			$row['position'] = System::display_number($row['position']);
			$row['group_column'] = System::display_number($row['group_column']);
		}
		$this->parse_layout('detail',$row);
	}
	function delete(&$form,$id)
	{
		$row = DB::select('module_setting',$id);
		DB::delete_id('module_setting', $id);
		DB::delete('block_setting', 'setting_id="'.$id.'"');
	}
}
?>