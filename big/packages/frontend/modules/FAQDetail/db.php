<?php
class FAQDetailDB{
	static function get_item($cond)
	{
		return DB::fetch('
			SELECT
				news.id,
				news.name_'.Portal::language().' as name,
				news.brief_'.Portal::language().' as brief,
				news.description_'.Portal::language().' as description,
				news.image_url,
				news.category_id,
				news.time,
				news.name_id,
				news.parent_id
			FROM
				news
			WHERE
				'.$cond.'
		');
	}
	static function get_items($cond=1)
	{
		return DB::fetch_all('
			SELECT
				news.id,
				news.name_'.Portal::language().' as name,
				news.brief_'.Portal::language().' as brief,
				news.description_'.Portal::language().' as description,
				news.image_url,
				news.time,
				news.name_id
			FROM
				news
			WHERE
				'.$cond.'
			ORDER BY
				news.position,news.id desc
			LIMIT
				0,10
		');
	}
	static function get_category($cond){
		return DB::fetch('
			SELECT
				news.id,news.name_id,category.id as category_id,category.name_'.Portal::language().' as category_name,category.name_id as category_name_id
			FROM
				news
				INNER JOIN category ON item.category_id=category.id
			WHERE
				'.$cond.'
		');
	}
}
?>
