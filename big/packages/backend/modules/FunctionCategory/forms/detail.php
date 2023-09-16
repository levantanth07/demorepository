<?php
class FunctionCategoryForm extends Form
{
	function FunctionCategoryForm()
	{
		Form::Form("FunctionCategoryForm");
		$this->add('id',new IDType(true,'object_not_exists','function'));
	}
	function on_submit()
	{
		if(Url::get('id') and $category=DB::fetch('select id,structure_id from function where id='.intval(Url::get('id'))) and User::can_edit(false,$category['structure_id']))
		{
			$this->delete($this,$_REQUEST['id']);
			Url::redirect_current();
		}
		else
		{
			Url::redirect_current();
		}
	}
	function draw()
	{
		$this->load_data();
		$languages = DB::select_all('language');
		$this->parse_layout('detail',$this->item_data+array('languages'=>$languages));
	}
	function delete(&$form,$id)
	{
		$this->item_data = DB::select('function',$id);
		 if(file_exists($this->item_data['icon_url']))
		{
			@unlink($this->item_data['icon_url']);
		}
		DB::delete_id('function', $id);
	}
	function load_data()
	{
		DB::query('
			select
				*
			from
			 	`function`
			where
				`function`.id = "'.URL::sget('id').'"');
		if($this->item_data = DB::fetch())
		{
		}
	}
	function load_multiple_items()
	{
	}
}
?>