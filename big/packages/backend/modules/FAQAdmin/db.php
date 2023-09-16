<?php
class FAQAdminDB
{
	function get_total_item($cond)
	{
		return DB::fetch(
			'select
				count(*) as acount
			from
				news
			where
				'.$cond.'
				and news.portal_id="'.PORTAL_ID.'"
				'
			,'acount');
	}
	function get_items($cond,$order_by,$item_per_page)
	{
		return DB::fetch_all('
			SELECT
				news.id
				,news.publish
				,news.front_page
				,news.status
				,news.position
				,news.user_id
				,news.time
				,news.hitcount
				,news.name_'.Portal::language().' as name
				,news.brief_'.Portal::language().' as question
			FROM
				news
			WHERE
				'.$cond.'
				and news.portal_id="'.PORTAL_ID.'"
			ORDER BY
				'.$order_by.'
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
	function get_category()
	{
		return DB::fetch_all('
			SELECT
				id
				,name_'.Portal::language().' as name
				,structure_id
			FROM
				category
			WHERE
				category.type="NEWS"
				and category.portal_id="'.PORTAL_ID.'"
		');
	}
	function get_user()
	{
		return DB::fetch_all('
			SELECT
				distinct user_id as name
				,user_id as id
			FROM
				news
			WHERE
				user_id!=""
		');
	}
}
?>
