<?php
class ProductCategoryForm extends Form
{
	function ProductCategoryForm()
	{
		Form::Form('ProductCategoryForm');
	}	
	function draw()
	{	
		$this->map = array();
		$categories = DB::fetch_all('
			select id,name_id,name_'.Portal::language().' as name 
			from 
				category 
			where 
				type="PRODUCT" AND '.IDStructure::direct_child_cond(ID_ROOT).' 
			order by 
				structure_id
		');
		if(Url::get('category_name_id') and $category = DB::select('category','name_id="'.Url::get('category_name_id').'" and type="PRODUCT"')){
			$parent_structure_id = IDStructure::parent($category['structure_id']);
			$level = IDStructure::level($parent_structure_id);
			if($parent = DB::select('category','structure_id='.$parent_structure_id.'') and $level>=1){
				$sub_categories= DB::fetch_all('SELECT id,name_id,name_'.Portal::language().' AS name FROM category WHERE type="PRODUCT" and '.IDStructure::direct_child_cond($parent['structure_id']));
				$categories[$parent['id']]['childs'] = $sub_categories;
			}else{
				$sub_categories= DB::fetch_all('SELECT id,name_id,name_'.Portal::language().' AS name FROM category WHERE type="PRODUCT"  and '.IDStructure::direct_child_cond($category['structure_id']));
				$categories[$category['id']]['childs'] = $sub_categories;
			}
		}
		$this->map['categories'] = $categories;
		$this->parse_layout('list',$this->map);
	}
}
?>