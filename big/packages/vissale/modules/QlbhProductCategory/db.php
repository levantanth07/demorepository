<?php
class QlbhProductCategoryDB
{
	static function get_categories($structure_id='1040000000000000000')
	{		
		return DB::fetch_all('
			SELECT
				id,
				name_'.Portal::language().' as name,
				structure_id,
				status,
				type
			FROM
				qlbh_product_category
			WHERE
				1		
				and '.IDStructure::direct_child_cond($structure_id).'		
			ORDER BY
				structure_id			
		');		
	}
	static function get_all_categories($categories)
	{
		$new_categories=array();
		foreach($categories as $id=>$category)
		{
			$new_categories[$id]=$category;
			$new_categories[$id]['childs']=QlbhProductCategoryDB::get_categories($category['structure_id']);
		}
		return $new_categories;
	}
	static function check_categories($categories)
	{
		foreach($categories as $id=>$category)
		{
			if(!User::can_view(false,$category['structure_id']))
			{
				unset($categories[$id]);
			}
		}
		return $categories;
	}
}
?>