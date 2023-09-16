<?php
class FileManagerDB
{
	function GetTotal($table='item',$cond='1')
	{
		$inner = ' left outer join category on category.id = '.$table.'.category_id';
		return DB::fetch('
			SELECT
				count(*) as acount
			FROM
				'.$table.$inner.'
			WHERE
				'.$cond.'
				and '.$table.'.portal_id="'.PORTAL_ID.'"
		');
	}
	function GetItems($table='news',$cond=' 1',$order_by='id DESC',$item_per_page=20)
	{
		$status = ','.$table.'.status';
		$inner = ' left outer join category on category.id = '.$table.'.category_id';
		return DB::fetch_all('
			SELECT
				'.$table.'.id,
				'.$table.'.name_'.Portal::language().' as name,
				'.$table.'.time,
				'.$table.'.last_time_update,
				'.$table.'.user_id,
				'.$table.'.status,
				'.$table.'.front_page,
				'.$table.'.publish,
				category.name_'.Portal::language().' as category_name
				'.$status.'
			FROM
				'.$table.'
				'.$inner.'
			WHERE
				'.$cond.'
				and '.$table.'.portal_id="'.PORTAL_ID.'"
			ORDER BY
				'.$order_by.'
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
}
?>