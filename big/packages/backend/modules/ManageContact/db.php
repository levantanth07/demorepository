<?php
class ManageContactDB{
	static function get_total($cond = '1'){
		return DB::fetch('
			SELECT
				count(*) as acount
			FROM
				contact
				left outer join zone on contact.zone_id = zone.id
			WHERE
				'.$cond.'
				and contact.portal_id="'.PORTAL_ID.'"
		');
	}
	static function get_items($item_per_page,$cond = '1'){
		return DB::fetch_all('
			SELECT
				contact.*
			FROM
				contact
				left outer join zone on contact.zone_id = zone.id
			WHERE
				'.$cond.'
				and contact.portal_id="'.PORTAL_ID.'"
			ORDER BY
				contact.time desc
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
	static function get_total_email($cond = '1'){
		return DB::fetch('
			SELECT
				count(*) as acount
			FROM
				newsletter
			WHERE
				'.$cond.'
		');
	}
	static function get_email($item_per_page,$cond = '1=1'){
		return DB::fetch_all('
			SELECT
				newsletter.*
			FROM
				newsletter
			WHERE
				'.$cond.'
			ORDER BY
				newsletter.time desc
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
	}
}
?>