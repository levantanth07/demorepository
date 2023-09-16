<?php 
class ManageCountryDB
{
	static function get_items($cond = '1')
	{
		return DB::fetch_all('
			SELECT
				id,name
			FROM
				country
			WHERE
				'.$cond.'
			ORDER BY
				name
			LIMIT
				0,400
		');
	}	
}
?>