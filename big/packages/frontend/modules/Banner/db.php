<?php
class BannerDB
{
	static function get_categories(){
		require_once 'packages/backend/modules/PortalCategory/db.php';
		require_once 'packages/core/includes/utils/category.php';
		$language_id = Portal::language();
		$categories = PortalCategoryDB::get_categories($language_id,ID_ROOT,'and category.status="MENU"');
		$categories = convert_item_cat_to_ul($categories,true,$language_id);
		return $categories;
	}
	static function get_category($cond)
	{
		$sql='
			SELECT
				id,
				name_'.Portal::language().' as name,
				name_id_'.Portal::language().' as name_id,
				url,
				structure_id,
                image_url
			FROM
				category
			WHERE
				'.$cond.'
			ORDER BY
				structure_id
			';
		$items = DB::fetch_all($sql);
		return $items;
	}
}
?>