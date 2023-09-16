<?php
class PartnerAdminDB
{
	static function get_total_item($cond)
	{
		return DB::fetch(
			'select
				count(*) as acount
			from
				`partner`
			where
				'.$cond.'
				'
			,'acount');
	}
	static function get_items($cond,$order_by,$item_per_page)
	{
		$items = DB::fetch_all('
			SELECT
				*
			FROM
				`partner`
			WHERE
				'.$cond.'
			ORDER BY
				'.$order_by.'
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		return $items;
	}

	static function get_user()
	{
//		return DB::fetch_all('
//			SELECT
//				distinct user_id as name
//				,user_id as id
//			FROM
//				partner
//			WHERE
//				user_id!=""
//		');
		return array();
	}
}
?>
