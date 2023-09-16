<?php
class ZoneAdminForm extends Form
{
	function __construct()
	{
		Form::Form("ZoneAdminForm");
		$this->add('id',new IDType(true,'object_not_exists','zone'));
		$this->link_css(Portal::template('core').'/css/category.css');
	}
	function on_submit()
	{
		if(Url::get('id') and $category=DB::fetch('select id,structure_id from zone where id='.intval(Url::get('id'))) and User::can_edit(false,$category['structure_id']))
		{
			$this->delete($this,$_REQUEST['id']);
			Url::redirect_current(array('countries'));
		}
		else
		{
			Url::redirect_current(array('countries'));
		}
	}
	function draw()
	{
		$this->load_data();
		$this->parse_layout('detail',$this->item_data);
	}
	function delete(&$form,$id)
	{
		$this->item_data = DB::select('zone',$id);
		 if(file_exists($this->item_data['image_url']))
		{
			@unlink($this->item_data['image_url']);
		} 
		DB::delete_id('zone', $id);
	}
	function load_data()
	{
		DB::query('
			select 
				`zone`.*
			from 
			 	`zone`
			where
				`zone`.id = "'.URL::sget('id').'"');
		if($this->item_data = DB::fetch())
		{
		}
	}
	function load_multiple_items()
	{
	}
}
?>