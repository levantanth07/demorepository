<?php
class SiteMapDB{
	static function get_category($cond)
	{
		return DB::fetch_all('
			SELECT
				id
				,name_'.Portal::language().' as title
				,name_id as name
				,structure_id
				,type
				,url
			FROM
				category
			WHERE
				category.portal_id="'.PORTAL_ID.'"
				'.$cond.'
			ORDER BY
				structure_id');
	}
	function site_map()
	{
		return DB::fetch_all('
			SELECT
				id
				,title_'.Portal::language().' as title
				,name
			FROM
				page
			WHERE
				params like "%portal='.substr(PORTAL_ID,1).'%"
				and package_id = 331
				and name!="xem-trang-tin" and name!="xem-san-pham" and name!="xem-hoi-dap" and name!="so-do-site" and name!="dang-nhap"
		');
	}
}
?>