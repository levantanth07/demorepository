<?php
class NewsByCategoryDB{
	static function get_item($cond,$item_per_page){
		$sql = '
			SELECT
				distinct category.id,category.structure_id,category.name_1 as name,category.name_id
			FROM
				category
			WHERE
				'.$cond.'
			ORDER BY
				category.structure_id
			LIMIT
				0,'.$item_per_page.'
		';
		$news_categories = DB::fetch_all($sql);
		foreach($news_categories as $k=>$v){
			$items = DB::fetch_all('
				SELECT
					news.id,news.name_id,news.name_'.Portal::language().' as name,news.brief_'.Portal::language().' as brief,news.small_thumb_url,news.status,category.name_id as category_name_id
				FROM
					news
					inner join category on category.id = news.category_id
				WHERE
					news.status <> "HIDE"
					AND '.IDStructure::child_cond($v['structure_id']).'
				ORDER BY
					news.time DESC
				LIMIT
					0,8
			');
			foreach($items as $key=>$value){
				//$items[$key]['thumb_url'] = str_replace('image_url','image_url',$value['image_url']);
				$items[$key]['sort_name'] = String::display_sort_title(strip_tags($value['name']),6);
				$items[$key]['brief'] = String::display_sort_title(strip_tags($value['brief']),40);
				//$items[$key]['content'] = String::display_sort_title(str_replace(array('"','\''),array('',''),$value['content']),20);
			}
			if(!empty($items)){
				$news_categories[$k]['items'] = $items;
			}else{
				unset($news_categories[$k]);
			}
		}
		return $news_categories;
	}
	static function get_comment($item_per_page){
		
	}
	static function update_hitcount($id){
		DB::query('update item set hitcount = hitcount + 1 where id = '.$id.'');
	}
}
?>