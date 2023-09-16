<?php
class MediaAdminDB{
	static function get_slide_by_cond($ids){
		return DB::fetch_all('
			SELECT
				id
				,image_url
				,name_'.Portal::language().' as name
				,description_'.Portal::language().' as description
			FROM
				media
			WHERE
				id in('.$ids.')
				and media.type="PHOTO"
			ORDER BY
				media.id DESC'
		);
	}
	static function get_html_slide($id){
		return DB::fetch(
			'SELECT
				id,html
			FROM
				slide
			WHERE
				id = '.$id
		);
	}
	static function get_slide(){
		return DB::fetch_all('
			SELECT
				id,name_'.Portal::language().' as name,time,effect,user_id
			FROM
				slide
			WHERE
				1
				and portal_id = "'.PORTAL_ID.'"
			ORDER BY
				slide.id DESC');
	}
	static function get_total_item($cond){
		return DB::fetch(
			'select
				count(*) as acount
			from
				media
				left outer join category on media.category_id = category.id
			where
				'.$cond.'
				and media.portal_id="'.PORTAL_ID.'"
				'
			,'acount');
	}
	static function get_items($cond,$order_by,$item_per_page){
		return DB::fetch_all('
			SELECT
				media.id
				,media.user_id
				,media.time
				,media.name_'.Portal::language().' as name
				,category.name_'.Portal::language().' as category_name
				,media.image_url
				,media.url
				,media.status
			FROM
				media
				left outer join category on media.category_id = category.id
			WHERE
				'.$cond.'
				and media.portal_id="'.PORTAL_ID.'"
			ORDER BY
				'.$order_by.'
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
	static function get_category(){
		return DB::fetch_all('
			SELECT
				id
				,name_'.Portal::language().' as name
				,structure_id
			FROM
				category
			WHERE
				category.type="'.Url::get('type').'"
				and category.portal_id="'.PORTAL_ID.'"
			ORDER BY
				structure_id
		');
	}
	static function get_product(){
		return DB::fetch_all('
			SELECT
				id
				,name_'.Portal::language().' as name
			FROM
				product
			WHERE
				product.portal_id="'.PORTAL_ID.'"
		');
	}
}
?>
