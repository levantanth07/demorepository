<?php
class QlbhNewsDB
{
	function get_news($cond)
	{
		$items = DB::fetch_all('
			SELECT
				news.id
        ,news.name_id
				,news.publish
				,news.front_page
				,news.status
				,news.position
				,news.user_id
				,news.image_url
				,news.small_thumb_url
				,news.time
				,news.hitcount
				,news.name_'.Portal::language().' as name
				,news.brief_'.Portal::language().' as brief
				,news.description_'.Portal::language().' as description
				,category.name_'.Portal::language().' as category_name
				,category.structure_id
			FROM
				news
				left outer join category on category.id = news.category_id
			WHERE
				'.$cond.'
				AND news.portal_id="'.PORTAL_ID.'"
			ORDER BY 
				news.position DESC,news.id DESC
			LIMIT 
				0,10
		');
		foreach($items as $key=>$value){
			$items[$key]['brief'] = $value['brief']?$value['brief']:MiString::display_sort_title($value['description'],50);
		}
		return ($items);
	}
	function get_total_news($cond,$item_per_page)
	{
		$items = DB::fetch_all('
			SELECT
				news.id
                ,news.name_id
				,news.publish
				,news.front_page
				,news.status
				,news.position
				,news.user_id
				,news.image_url
				,news.small_thumb_url
				,news.time
				,news.hitcount
				,news.name_'.Portal::language().' as name
                ,news.brief_'.Portal::language().' as brief
                ,news.description_'.Portal::language().' as description
				,category.name_'.Portal::language().' as category_name
				,category.structure_id
			FROM
				news
				left outer join category on category.id = news.category_id
			WHERE
				'.$cond.'
				and news.portal_id="'.PORTAL_ID.'"
                ORDER BY news.position DESC,news.id DESC
                LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		return ($items);
	}
	static function get_total_item($cond)
	{
		return DB::fetch('
			SELECT
				count(*) as acount
			FROM
				news
				inner join category on news.category_id = category.id
			WHERE
				'.$cond.'
		');
	}
	function get_categories($cond)
	{
		$sql='
			SELECT
				id,
				name_'.Portal::language().' as name,
				name_id,
				url,
				structure_id
			FROM
				category
			WHERE
				'.$cond.'
			ORDER BY
				structure_id
			';
		$items = DB::fetch_all($sql);
		return $items;
	}
}
?>