<?php
class ManageAdvertismentDB{
	static function get_region($cond='1'){
		if($items = DB::fetch_all('
			SELECT
				distinct block.name as id
				,page.name as page
			FROM
				block inner join page on page.id=page_id
			WHERE
				'.$cond.'
				and module_id=5911
				and page.params LIKE "%portal='.substr(PORTAL_ID,1).'%"
			ORDER
				 by block.name
		')){
			return $items;
		}else{
			return DB::fetch_all('
				SELECT
					distinct block.name as id
					,page.name as page
				FROM
					block inner join page on page.id=page_id
				WHERE
					'.$cond.'
					and module_id=5911
				ORDER
					 by block.name
			');
		}
	}
	static function get_page_name(){
		if($items = DB::fetch_all('
			SELECT
				page.name as id
			FROM
				block inner join page on page.id=page_id
			WHERE
				module_id=5911
				and page.params LIKE "%portal='.substr(PORTAL_ID,1).'%"
			ORDER
				 by block.name
		')){
			return $items;
		}else{
			return DB::fetch_all('
				SELECT
					page.name as id
				FROM
					block inner join page on page.id=page_id
				WHERE
					module_id=5911
				ORDER
					 by block.name
			');
		}
	}
	static function get_item_id_list(){
		if(DB::fetch('
			SELECT
				count(*) as acount
			FROM
				media
			WHERE
				type="ADVERTISMENT"
				and portal_id="'.PORTAL_ID.'"
		','acount')<100){
			return DB::fetch_all('
				SELECT
					media.*
					,media.name_'.Portal::language().' as name
				FROM
					media
				WHERE
					type="ADVERTISMENT"
					and portal_id="'.PORTAL_ID.'"
				ORDER
					 by media.name_'.Portal::language().'
			');
		}else{
			return array();
		}
	}
	static function get_categories(){
		$portal_id=URL::get('portal_id')?addslashes(URL::get('portal_id')):str_replace('#','',PORTAL_ID);
		return String::get_list(DB::fetch_all('
			SELECT
				`category`.id
				,`category`.name_'.Portal::language().' as name
				,`category`.structure_id
			FROM
				`category`
			WHERE
				portal_id="#'.$portal_id.'"
			ORDER BY
				 structure_id'
		));
	}
	static function get_item_count($cond){
		$item=DB::fetch('
			select
				 count(*) as acount
			from
				`advertisment`
				left outer join media on `media`.id=advertisment.item_id
			where '.$cond.'
				limit 0,1
		');
		return $item['acount'];
	}
	static function get_items($cond, $item_per_page){
		return DB::fetch_all('
			SELECT
				advertisment.id
				,advertisment.start_time
				,advertisment.end_time
				,media.name_'.Portal::language().' as name
				,advertisment.position
				,media.image_url
				,category.name_'.Portal::language().' as category_name
				,region
			FROM
				advertisment
				left outer join category on `advertisment`.category_id=category.id
				left outer join media on `media`.id=advertisment.item_id
			WHERE
				'.$cond.'
			'.(URL::get('order_by')?'order by '.URL::get('order_by').(URL::get('order_dir')?' '.URL::get('order_dir'):' order by portal_id'):'').'
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
}
?>