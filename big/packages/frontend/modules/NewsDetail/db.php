<?php
class NewsDetailDB{
	static function get_field_category($cond=' 1',$field='id'){
			return DB::fetch('SELECT * FROM category WHERE '.$cond,$field);
	}
	static function get_item($cond)
	{
		return DB::fetch('
			SELECT
				news.id,
				news.name_id_'.Portal::language().' as name_id,
				news.name_'.Portal::language().' as name,
				news.brief_'.Portal::language().' as brief,
				news.description_'.Portal::language().' as description,
				news.image_url,
				news_category.category_id,
				news.file,
				news.time,
				news.keywords,
				news.tags,
				news.small_thumb_url,
				news.user_id,
				news.seo_title_'.Portal::language().' as seo_title,
				news.seo_keywords_'.Portal::language().' as seo_keywords,
				news.seo_description_'.Portal::language().' as seo_description,
				category.name_'.Portal::language().' as category_name,
				category.name_id_'.Portal::language().' as category_name_id
			FROM
				news
				INNER JOIN news_category on news_category.news_id = news.id
				INNER JOIN category on category.id = news_category.category_id
			WHERE
				'.$cond.'
		');
	}
	static function get_items($cond='1=1',$item_per_page=10)
	{
		$sql = '
			SELECT
				news.id,
				news.name_id_'.Portal::language().' as name_id,
				news.name_'.Portal::language().' as name,
				news.brief_'.Portal::language().' as brief,
				news.description_'.Portal::language().' as description,
				news.image_url,
				news.small_thumb_url,
				news_category.category_id,
				news.time,
				news.file,
				news.published_time,
				news.small_thumb_url,				
				category.name_1 as category_name,
				category.id as category_id,
				category.name_id_1 as category_name_id,
				party.id as party_id,
				party.user_id as party_user_id,
				party.full_name as party_full_name
			FROM
				news
				INNER JOIN news_category on news_category.news_id = news.id
				INNER JOIN category on category.id = news_category.category_id
				LEFT JOIN party on party.user_id = news.user_id
			WHERE
				'.$cond.'
			ORDER BY
				news.position desc,news.id desc
			LIMIT
				0,'.$item_per_page.'
		';
		$items = DB::fetch_all($sql);
		foreach($items as $key=>$val){
			$items[$key]['image_url'] = $val['image_url']?$val['image_url']:$val['small_thumb_url'];
			$items[$key]['small_thumb_url'] = $val['image_url'];//$val['small_thumb_url']?$val['small_thumb_url']:
			//$items[$key]['brief'] = MiString::display_sort_title(trim(strip_tags($val['description'])),30);
			//$items[$key]['star'] = draw_star_v2($val['film_point']);
		}
		return $items;
	}
	static function get_category($cond){
		return DB::fetch('
			SELECT
				news.id,news.name_id_'.Portal::language().' as name_id,category.id as category_id,category.name_'.Portal::language().' as category_name,category.name_id_'.Portal::language().' as category_name_id
			FROM
				news
				INNER JOIN news_category on news_category.news_id = news.id
				INNER JOIN category on category.id = news_category.category_id
			WHERE
				'.$cond.'
		');
	}
	static function update_news_comment($news_id){
		$arr = array(
			'news_id'=>$news_id,
			'full_name',
			'email',
			'content',
			'time'=>time(),
		);
		DB::insert('news_comment',$arr) and $news =  DB::select('news','id='.$news_id);
	}
	static function get_category_parent($cond){
		$sql='
			SELECT
				id,
				name_'.Portal::language().' as name,
				name_id_'.Portal::language().' as name_id,
				url,
				structure_id,
        image_url
			FROM
				category
			WHERE
				'.$cond.'
			ORDER BY
				structure_id
			';
		$items = DB::fetch_all($sql);
		foreach($items as $key=>$value){
			$items[$key]['items'] = DB::fetch_all('
				SELECT
					id,
					name_'.Portal::language().' as name,
					name_id_'.Portal::language().' as name_id,
					url,
					structure_id
				FROM
					category
				WHERE
					 '.IDStructure::child_cond($value['structure_id']).' and id!='.$value['id'].'
				ORDER BY
					position DESC, id ASC
			');
		}
		return $items;
	}
	//24/11/2017
	static function get_stars($cond='1=1',$item_per_page=10){
		$sql = '
			SELECT 
				hay_star.id
				,hay_star.name
				,hay_star.nationality_name
				,hay_star.name_id
				,hay_star.image_url
				,hay_star.position
				,hay_star.checked
				,hay_star.loved
				,hay_star.birth_date
				,hay_star.height
				,hay_star.weight
				,hay_star.intro
				,hay_star.marital_status
				,hay_star.horoscope
				,hay_star.background
				,hay_star.awards
				,hay_star.nationality_name
				,hay_star.element
				,hay_star.loved
			FROM
				hay_star
				LEFT JOIN hay_star_news on hay_star_news.star_id = hay_star.id
			WHERE
				'.$cond.'
			ORDER BY
				hay_star.id
			LIMIT
				0,'.$item_per_page.'
		';
		$items = DB::fetch_all($sql);
		return $items;
	}
	//27/11/2017
	static function get_user_follow(){
		return 1;
	}
	static function get_user_total_article(){
		return 1;
	}

}
?>
