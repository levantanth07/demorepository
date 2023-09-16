<?php
class AdminPrintTemplateDB{
	static function get_total_item($cond){
		return DB::fetch(
			'select
				count(distinct order_print_template.id) as acount
			from
				order_print_template
			where
				'.$cond.'
				'
			,'acount');
	}

	static function get_items($cond,$order_by,$item_per_page){
		$sql = '
				SELECT 
					order_print_template.id,order_print_template.print_name,
					order_print_template.print_address,
					order_print_template.set_default,
					IF(order_print_template.template=1,"Kiểu in mặc định","Kiểu in có mã vạch") as template
				FROM 
					order_print_template
				WHERE
					'.$cond.'
				ORDER BY id
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$items = DB::fetch_all($sql);
		return $items;
	}
	static function get_item(){
		return DB::fetch('
			SELECT * FROM order_print_template WHERE id='.DB::escape(Url::get('id')).'
		');
	}
}

?>

