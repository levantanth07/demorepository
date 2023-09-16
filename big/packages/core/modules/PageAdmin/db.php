<?php
class PageAdminDB{
	function get_module_list()
	{
		return DB::fetch_all('
			SELECT
				id, name
			FROM
				module
			ORDER BY
				name
		');
	}
	function get_page_name_list()
	{
		return DB::fetch_all('
			SELECT DISTINCT
				name as id, name
			FROM
				page
			ORDER BY
				name
		');
	}
	function get_container_list()
	{
		return DB::fetch_all('
			SELECT DISTINCT
				module.id, module.name
			FROM
				block b1
				inner join block b2 on b1.container_id = b2.id
				inner join module on b2.module_id = module.id
			ORDER BY
				module.name
		');
	}
	function get_region_list()
	{
		return DB::fetch_all('
			SELECT DISTINCT
				region as id, region as name
			FROM
				block
			ORDER BY
				region
		');
	}
	function get_portal_list()
	{
		return DB::fetch_all('
			SELECT
				id, substring(id,2) as name
			FROM
				account
			WHERE
				type="PORTAL"
			ORDER BY
				name
		');
	}
	function get_copy_setting_id_list($module_id)
	{
		return DB::fetch_all('
			SELECT
				block.id, CONCAT(block.region,"(",block.position,") of ",page.name," ",page.params) as name
			FROM
				block
				inner join page on page_id=page.id
			WHERE
				module_id="'.$module_id.'"
			ORDER BY
				page.params,
				page.name,
				block.region,
				block.position
		');
	}
	function find_pages($portals, $page_name)
	{
		if($portals)
		{
			return DB::fetch_all('
				SELECT
					id
				FROM
					page
				WHERE
					name LIKE "'.$page_name.'"
					and (params LIKE "%portal='.str_replace('#','',join('%" or params LIKE "%portal=',$portals)).'%")
			');
		}
		else
		{
			return array();
		}
	}
	function add_block($module_id,$page_id,$container_id, $region,$position, $block_name)
	{
		if($container_id)
		{
			if($container = DB::select('block','page_id="'.$page_id.'" and module_id="'.$container_id.'"'))
			{
				$container_id=$container['id'];
			}
			else
			{
				return;
			}
		}
		return DB::insert('block',array(
			'page_id'=>$page_id,
			'container_id'=>$container_id,
			'region'=>$region,
			'position'=>$position,
			'name'=>$block_name,
			'module_id'=>$module_id
		));
	}
	function delete_block($module_id,$page_id,$container_id, $region,$position, $block_name)
	{
		if($container_id)
		{
			if($container = DB::select('block','page_id="'.$page_id.'" and module_id="'.$container_id.'"'))
			{
				$container_id=$container['id'];
			}
			else
			{
				return;
			}
		}
		else
		{
			$container_id = 0;
		}
		echo 'page_id='.$page_id.' and container_id="'.$container_id.'" and region="'.$region.'" '.($position?'and position="'.$position.'"':'').' and name LIKE "%'.addslashes($block_name).'%" and module_id="'.$module_id.'"';
		DB::delete('block','page_id='.$page_id.' and container_id="'.$container_id.'" and region="'.$region.'" '.($position?'and position="'.$position.'"':'').' and name LIKE "%'.addslashes($block_name).'%" and module_id="'.$module_id.'"');
	}
	function copy_block_setting($block_id,$copy_setting_id)
	{
		DB::query('insert block_setting(block_id, value, setting_id) select '.$block_id.',value, setting_id from block_setting where block_id='.$copy_setting_id);
	}
}
?>