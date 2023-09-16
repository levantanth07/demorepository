<?php
class QlbhProductCategoryForm extends Form
{
	function __construct()
	{
		Form::Form("QlbhProductCategoryForm");
		$this->add('id',new IDType(true,'object_not_exists','qlbh_product_category'));
		$this->link_css(Portal::template('core').'/css/category.css');
	}
	function on_submit()
	{
		if(Url::get('id') and $category=DB::fetch('select id,structure_id from qlbh_product_category where id='.intval(Url::get('id'))) and User::can_edit(false,$category['structure_id']))
		{
			$this->delete($this,$_REQUEST['id']);
			Url::redirect_current(Module::$current->redirect_parameters);
		}
		else
		{
			Url::redirect_current(Module::$current->redirect_parameters);
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
		$this->item_data = DB::select('qlbh_product_category',$id);
		 if(file_exists($this->item_data['icon_url']))
		{
			@unlink($this->item_data['icon_url']);
		} 
		DB::delete_id('qlbh_product_category', $id);
	}
	function load_data()
	{
		DB::query('
			select 
				`qlbh_product_category`.id
				,`qlbh_product_category`.structure_id
				,`qlbh_product_category`.`is_visible` ,`qlbh_product_category`.`icon_url` 
				

				,`qlbh_product_category`.name_'.Portal::language().' as name 

				,`qlbh_product_category`.description_'.Portal::language().' as description 
				

				,`type`.`id` as type 
			from 
			 	`qlbh_product_category`
				

				left outer join `type` on `type`.id=`qlbh_product_category`.type 
			where
				`qlbh_product_category`.id = "'.URL::sget('id').'"');
		if($this->item_data = DB::fetch())
		{
		}
	}
	function load_multiple_items()
	{
	}
}
?>