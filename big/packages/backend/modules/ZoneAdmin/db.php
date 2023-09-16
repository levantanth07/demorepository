<?php
class ZoneAdminDB
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
				category
			WHERE
				1 
				and portal_id="'.PORTAL_ID.'"		
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
			$new_categories[$id]['childs']=ZoneAdminDB::get_categories($category['structure_id']);
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
	static function get_regions($cond){
		return DB::fetch_all('
			SELECT
				area.id,area.name,area.zone_id
			FROM
				area
				INNER JOIN zone ON area.zone_id=zone.id
				INNER JOIN area_type ON area.area_type_id=area_type.id
			WHERE
				'.$cond.'
			ORDER BY
				area.name,area.id desc
		');
	}
	static function get_cities($cond){
		return DB::fetch_all('
			SELECT
				zone.id,zone.name,zone.structure_id,zone.lat,zone.long,zone.status,zone.flag
			FROM
				zone
			WHERE
				'.$cond.'
			ORDER BY
				zone.structure_id
		');
	}
}
?>