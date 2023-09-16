<?php
class PortalCategoryDB
{
	static function get_categories($language_id,$structure_id=false,$extra_cond=false)
	{
		return DB::fetch_all('
			SELECT
				id,
				name_'.$language_id.' as name,
				name_id_'.$language_id.' as name_id,
				structure_id,
				status,
				icon_url,
				url,
				type
			FROM
				category
			WHERE
				1=1
				and portal_id="'.PORTAL_ID.'"
				'.($structure_id?'and '.IDStructure::direct_child_cond($structure_id).'':'').'
				'.$extra_cond.'
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
			$new_categories[$id]['childs']=PortalCategoryDB::get_categories(1,$category['structure_id']);
		}
		return $new_categories;
	}
	static function check_categories($categories)
	{
		foreach($categories as $id=>$category)
		{
			if(!User::can_view(false,$category['structure_id']))
			{
				//unset($categories[$id]);
			}
		}
		return $categories;
	}
}
/*
ALTER TABLE `language` CHANGE `code` `code` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `name` `name` VARCHAR(100) CHARACTER SET utf16 COLLATE utf16_general_ci NOT NULL, CHANGE `icon_url` `icon_url` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `convert_url` `convert_url` VARCHAR(250) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL;
*/
?>
