<?php
class ContentDB{
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
				news.file,
				news.name_id
			FROM
				news
				INNER JOIN category ON news.category_id=category.id
			WHERE
				'.$cond.'
			ORDER BY
				news.id desc
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
				news.category_id,
				news.time,
				news.name_id
			FROM
				news
				INNER JOIN category ON news.category_id=category.id
			WHERE
				'.$cond.'
			ORDER BY
				news.position desc,news.id desc
			LIMIT
				0,10
		');
	}
	static function get_category($cond){
		return DB::fetch('
			SELECT
				category.id,category.name_'.Portal::language().' as category_name,category.name_id,category.structure_id,category.portal_id
			FROM
				category
			WHERE
				'.$cond.'
		');
	}
}
?>
