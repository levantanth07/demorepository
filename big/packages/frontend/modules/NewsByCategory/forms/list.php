<?php
class NewsByCategoryForm extends Form
{
	function NewsByCategoryForm()
	{
		Form::Form('NewsByCategoryForm');
	}	
	function draw()
	{
		$layout = 'list';
		$item_per_page = 10;
		$category_id = 585;
		$cond='category.type="NEWS" AND category.status <> "HIDE"  AND '.IDStructure::direct_child_cond( DB::structure_id('category',$category_id));
		$this->map['news_categories'] = NewsByCategoryDB::get_item($cond,$item_per_page);
		$this->parse_layout($layout,$this->map);
	}
}
?>