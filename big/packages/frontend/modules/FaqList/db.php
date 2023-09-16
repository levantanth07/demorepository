<?php
class FaqListDB{
	static function get_faq($cond,$item_per_page){
		return DB::fetch_all('
			SELECT
				news.id,
				news.brief_'.Portal::language().' as name,
				news.description_'.Portal::language().' as description
			FROM
				news
			WHERE
				'.$cond.'
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
				inner join category on news.category_id = category.id
			WHERE
				'.$cond.'
		');
	}
}
?>