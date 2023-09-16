<?php
class FAQDB
{
	static function get_item($cond,$item_per_page=20)
	{
		return DB::fetch_all('
			SELECT
				news.id,
				news.name_'.Portal::language().' as name,
				news.image_url,
				news.description_'.Portal::language().' as description,
				news.brief_'.Portal::language().' as brief,
				news.category_id,
				news.name_id
			FROM
				news
			WHERE
				'.$cond.'
			ORDER BY
				news.time desc
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
	static function get_total_item($cond)
	{
		return DB::fetch('
			SELECT
				count(*) as acount
			FROM
				news
			WHERE
				'.$cond.'
		');
	}
	static function insert_faq($array){
		DB::insert('news',$array);
	}
}
?>
