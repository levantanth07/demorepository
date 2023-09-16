<?php
class StatisticDB{
	static function get_total_hitcount($cond='type="NEWS"'){
		return DB::fetch('
			SELECT
				sum(hitcount) as total
			FROM
				news
			WHERE
				'.$cond.'
				and news.portal_id="'.PORTAL_ID.'"
		','total');
	}
	static function GetTotal($cond='type="NEWS"'){
		return DB::fetch('
			SELECT
				count(*) as acount
			FROM
				news
			WHERE
				'.$cond.'
				and news.portal_id="'.PORTAL_ID.'"
		');
	}
	static function GetItems($cond='type="NEWS"',$order_by='hitcount DESC',$item_per_page=20){
		return DB::fetch_all('
			SELECT
				news.id,news.name_'.Portal::language().' as name
				,news.name_id_'.Portal::language().' as name_id
				,news.hitcount
				,news.user_id
				,news.time
				,category.name_'.Portal::language().' as category_name
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
		');
	}
	static function GetTotalAdv($cond='1'){
		return DB::fetch('
			SELECT
				count(*) as acount
			FROM
				advertisment
				inner join media on media.id=advertisment.item_id
				inner join block on advertisment.region = block.name
				inner join page on page.id = block.page_id
				left outer join category on category.id = advertisment.category_id
			WHERE
				'.$cond.'
				and module_id=5911
				and media.type="ADVERTISMENT"
				and media.portal_id="'.PORTAL_ID.'"
		');
	}
	static function GetAdvItems($cond='1'){
		return DB::fetch_all('
			SELECT
				advertisment.*
				,media.url
				,media.name_'.Portal::language().' as name
				,page.name as page
				,category.name_'.Portal::language().' as category_name
			FROM
				advertisment
				inner join media on media.id=advertisment.item_id
				inner join block on advertisment.region = block.name
				inner join page on page.id = block.page_id
				left outer join category on category.id = advertisment.category_id
			WHERE
				'.$cond.'
				and module_id=5911
				and media.type="ADVERTISMENT"
				and media.portal_id="'.PORTAL_ID.'"
			ORDER BY
				advertisment.click_count DESC
		');
	}
	static function get_user()
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
}
?>