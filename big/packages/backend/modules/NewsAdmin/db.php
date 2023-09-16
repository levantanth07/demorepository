<?php
class NewsAdminDB{
	static function get_total_item($cond){
		return DB::fetch(
			'select
				count(distinct news.id) as acount
			from
				news
				left outer join news_category on news_category.news_id=news.id
				left outer join category on news_category.category_id=category.id
			where
				'.$cond.'
				and news.portal_id="'.PORTAL_ID.'"
				'
			,'acount');
	}
    static function get_item($cond)
    {
        return DB::fetch('
			SELECT
				news.id,
				news.name_id_1,
				news.name_id_2,
				news.name_1,
				news.brief_1,
				news.description_1,
				news.image_url,
				news_category.category_id,
				news.file,
				news.time,
				news.keywords,
				news.tags,
				news.small_thumb_url,
				news.image_url,
				news.file,
				news.user_id,
				news.position,
				news.publish,
				news.publisher,
				news.published_time,
				news.seo_title_1,
				news.seo_title_2,
				news.seo_keywords_1,
				news.seo_keywords_2,
				news.seo_description_1,
				news.seo_description_2,
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
	static function get_items($cond,$order_by,$item_per_page){
		if (Url::get('category_id'!= '0')){
			$sql = '
				SELECT
					distinct news.id as id
					,news.name_id_'.Portal::language().' as name_id
					,news.publish
					,news.front_page
					,news.status
					,news.position
					,news.user_id
					,news.small_thumb_url
					,news.time
					,news.hitcount
					,news.name_'.Portal::language().' as name
					,news.published_time
					,news.publisher
					,category.name_'.Portal::language().' as category_name
					,category.structure_id
					,category.name_id_'.Portal::language().' as category_name_id
				FROM
					news
					left outer join news_category on news_category.news_id = news.id
					left outer join category on category.id = news_category.category_id
				WHERE
					'.$cond.'
					and news.portal_id="'.PORTAL_ID.'"
				ORDER BY
					'.$order_by.'
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$items = DB::fetch_all($sql);
		}else{
			$sql = '
				SELECT
					distinct news.id as id
					,news.name_id_'.Portal::language().' as name_id
					,news.publish
					,news.front_page
					,news.status
					,news.position
					,news.user_id
					,news.small_thumb_url
					,news.time
					,news.hitcount
					,news.name_'.Portal::language().' as name
					,news.published_time
					,news.publisher
					,category.name_'.Portal::language().' as category_name
					,category.structure_id
					,category.name_id_'.Portal::language().' as category_name_id				
				FROM
					news
					left outer join news_category on news_category.news_id = news.id
					left outer join category on category.id = news_category.category_id
				WHERE
					'.$cond.'
					and news.portal_id="'.PORTAL_ID.'"
				ORDER BY
					'.$order_by.'
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$items = DB::fetch_all($sql);
			foreach($items as $key=>$val){
				$items[$key]['categories'] = NewsAdminDB::get_categories($val['id']);
			}
		}
		$comments = DB::fetch_all(
			'SELECT
				count(*) as total_comment
				,news.id as id
			 FROM
				comment
				inner join news on comment.name_id = news.id
			WHERE
				1 and comment.type="NEWS"
			GROUP BY
				comment.name_id
			');
		$i = ((page_no()-1)*$item_per_page)+1;
		foreach($items as $key =>$value){
			$value['index'] = $i++;
			if(isset($comments[$key])){
				$value['total_comment'] = $comments[$key]['total_comment'];
			}
			else{
				$value['total_comment'] = 0;
			}
			$items[$key] = $value;
			$items[$key]['published_time'] = date('H:i\' d/m/y',$value['published_time']);
		}
		return ($items);
	}
	static function update_category($news_id,$category_ids){
		if(!empty($category_ids)){
			DB::delete('news_category','news_id='.$news_id.'');
			foreach($category_ids as $key=>$value){
				DB::insert('news_category',array('news_id'=>$news_id,'category_id'=>$value));
			}
		}
		//exit();
	}
	static function get_categories($news_id){
		$str = '';
		$sql = 'select category.id,category.name_'.Portal::language().' as name from category inner join news_category as pc on pc.category_id=category.id where pc.news_id='.$news_id.'';
		$items = DB::fetch_all($sql);
		foreach($items as $key=>$value){
			$str .= ($str?',':'').$value['name'];
		}
		return $str;
	}
	static function get_category($news_id=false){
		$sql = '
			SELECT
				category.id
				,category.name_'.Portal::language().' as name
				,category.structure_id
				'.($news_id?',news_category.news_id':',"" as news_id').'
			FROM
				category
				'.($news_id?'LEFT OUTER JOIN news_category ON news_category.category_id = category.id AND news_category.news_id='.$news_id.'':'').'
			WHERE
				category.type="NEWS"
				and category.structure_id <> '.ID_ROOT.'
			ORDER BY
				category.structure_id
		';
		$categories =  DB::fetch_all($sql);
		return $categories;
	}
	static function get_user(){
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
	/*ALTER TABLE `news` CHANGE `name_id_1` `name_id_1` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `name_id_2` `name_id_2` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `name_1` `name_1` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `name_2` `name_2` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `seo_title_1` `seo_title_1` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `seo_keywords_1` `seo_keywords_1` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `seo_description_1` `seo_description_1` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `seo_title_2` `seo_title_2` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `seo_keywords_2` `seo_keywords_2` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `seo_description_2` `seo_description_2` TEXT CHARACTER SET utf8 COLLATE utf8_[...]*/
}
?>
