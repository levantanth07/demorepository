<?php
class GroupsSystemDB
{
	static function get_groups($system_group_id){
		$sql = '
			select
				groups.id,
				groups.name,
				groups.code,
				groups.email,
				groups.created,
				groups.account_type,
				groups.active,
				groups.expired_date,
				groups.prefix_post_code,
				groups.image_url,
				groups.master_group_id,
				groups.system_group_id,
				groups.user_counter,
				groups.page_counter,
                acc_packages.name package_name,
				(select count(*) as total from orders WHERE group_id=groups.id) AS total_order,
				(select count(*) as total from account WHERE group_id=groups.id) AS total_user,
				groups_system.name as group_system_name
			FROM
                `groups`
            LEFT JOIN acc_packages ON groups.package_id = acc_packages.id
			LEFT JOIN groups_system ON groups_system.id=groups.system_group_id
			where
				'.Systems::getIDStructureChildCondition(DB::structure_id('groups_system',$system_group_id)).'
			order by
			    groups.expired_date desc  	
			';
		$items = DB::fetch_all($sql);
		return $items;
	}
	static function get_categories($language_id,$structure_id=false,$extra_cond=false)
	{
		return DB::fetch_all('
			SELECT
				id,
				name,
				name_id,
				structure_id,
				status,
				icon_url
			FROM
				groups_system
			WHERE
				1=1
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
			$new_categories[$id]['childs']=GroupsSystemDB::get_categories(1,$category['structure_id']);
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
?>