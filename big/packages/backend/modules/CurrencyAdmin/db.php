<?php
class CurrencyAdminDB
{
	static function get_items($cond = '1')
	{
		return DB::fetch_all('
			SELECT
				id,id as name,name as brief,exchange,position
			FROM
				currency
			WHERE
				'.$cond.'
			ORDER BY
				position desc,id
			LIMIT
				0,50
		');
	}
}
?>