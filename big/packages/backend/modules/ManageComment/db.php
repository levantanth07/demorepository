<?php
class ManageCommentDB
{
	function get_total_item($cond)
	{
		return DB::fetch(
			'select
				count(*) as acount
			from
				comment
			where
				'.$cond.'
				'
			,'acount');
	}
	function get_items($cond,$order_by,$item_per_page)
	{
		return DB::fetch_all('
			SELECT
				comment.*
			FROM
				comment
			WHERE
				'.$cond.'
			ORDER BY
				'.$order_by.'
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
	function get_user()
	{
		return DB::fetch_all('
			SELECT
				distinct user_id as name
				,user_id as id
			FROM
				comment
			WHERE
				user_id!=""
		');
	}
}
?>
