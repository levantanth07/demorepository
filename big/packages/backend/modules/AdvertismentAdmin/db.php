<?php
class AdvertismentAdminDB{
	static function get_total_item($cond){
		return DB::fetch(
			'select
				count(*) as acount
			from
				media
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
				,media.image_url
				,media.url
				,media.status
			FROM
				media
			WHERE
				'.$cond.'
				and media.portal_id="'.PORTAL_ID.'"
			ORDER BY
				'.$order_by.'
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
}
?>
