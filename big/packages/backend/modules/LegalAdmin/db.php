<?php
class NewsAdminDB
{
	function get_total_item($cond)
	{
		return DB::fetch(
			'select
				count(*) as acount
			from
				news
				left outer join category on news.category_id=category.id
			where
				'.$cond.'
				and news.portal_id="'.PORTAL_ID.'"
				'
			,'acount');
	}
	function get_items($cond,$order_by,$item_per_page)
	{
		if (Url::get('category_id'!= '0')){
		$items = DB::fetch_all('
			SELECT
				news.id
				,news.publish
				,news.front_page
				,news.status
				,news.position
				,news.user_id
				,news.image_url
				,news.time
				,news.hitcount
				,news.name_'.Portal::language().' as name
				,category.name_'.Portal::language().' as category_name
				,category.structure_id
			FROM
				news
				left outer join category on category.id = news.category_id
			WHERE
				'.$cond.'
				and news.portal_id="'.PORTAL_ID.'"
			ORDER BY
				'.$order_by.'
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		}else{
			$items = DB::fetch_all('
			SELECT
				news.id
				,news.publish
				,news.front_page
				,news.status
				,news.position
				,news.user_id
				,news.image_url
				,news.time
				,news.hitcount
				,news.name_'.Portal::language().' as name
				,category.name_'.Portal::language().' as category_name
				,category.structure_id
			FROM
				news
				left outer join category on category.id = news.category_id
			WHERE
				'.$cond.'
				and news.portal_id="'.PORTAL_ID.'"
			ORDER BY
				'.$order_by.'
			LIMIT
				'.((1-1)*$item_per_page).','.$item_per_page.'
		');
		}
		$comments = DB::fetch_all(
			'SELECT
				count(*) as total_comment
				,news.id as id
			 FROM
				comment
				inner join news on comment.item_id = news.id
			WHERE
				1 and comment.type="NEWS"
			GROUP BY
				comment.item_id
			');
		$i = ((page_no()-1)*$item_per_page)+1;
		foreach($items as $key =>$value)
		{
			$value['index'] = $i++;
			if(isset($comments[$key]))
			{
				$value['total_comment'] = $comments[$key]['total_comment'];
			}
			else
			{
				$value['total_comment'] = 0;
			}
			$items[$key] = $value;
		}
		return ($items);
	}
	function get_category($cond=' 1 ')
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
                AND '.$cond. '
			ORDER BY
				structure_id
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
	function get_item(){
		return DB::fetch('
			SELECT
				id,name_'.Portal::language().' as name,brief_'.Portal::language().' as brief,description_'.Portal::language().' as description,time,name_id
			FROM
				news
			WHERE
				id='.Url::get('id').' and portal_id="'.PORTAL_ID.'"
		');
	}
}
?>
