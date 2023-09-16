<?php
class HomeNewsDB
{
	static function get_news($cond)
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
				,category.name_id as category_name_id
			FROM
				news
				left outer join category on category.id = news.category_id
			WHERE
				'.$cond.'
     ORDER BY 
		 		news.position DESC,news.id DESC
        LIMIT 0,6
		');
		return ($items);
	}
	static function get_item($cond)
	{
		$items = DB::fetch('
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
				,category.name_id as category_name_id
			FROM
				news
				left outer join category on category.id = news.category_id
			WHERE
				'.$cond.'
      ORDER BY
				news.position DESC,news.id DESC
		');
		return ($items);
	}


	static  function get_thuonghieu()
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
				,category.name_id as category_name_id
			FROM
				news
				left outer join category on category.id = news.category_id
			WHERE
				news.category_id = 273
      ORDER BY
				news.position DESC,news.id DESC
		');
		return ($items);
	}
}
?>