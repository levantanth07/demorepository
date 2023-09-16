<?php
class QlbhWarehouseDB
{	
	static function check_categories($categories)
	{
		return $categories;
	}
	static function get_categories()
	{
		return DB::fetch_all('select warehouse.* from warehouse where structure_id <>'.ID_ROOT.' order by structure_id');
	}
}
?>