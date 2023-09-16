<?php
class QlbhStockReportDB{
    static function get_order_info(){
        $party = DB::fetch('select full_name,note1,note2,address,phone,website from party WHERE user_id="'.Session::get('user_id').'" ');
        return $party;
    }
    static function get_products($cond='1=1'){
        $master_group_id = Session::get('master_group_id');
        $sql = '
				select
					products.id,
					'.($master_group_id?'CONCAT(CONCAT(products.code,"-",products.name)," / ",groups.name) as name':'CONCAT(products.code,"-",products.name) as name').',
					products.color,products.size,products.price
				from
					products
					inner join `groups` on groups.id = products.group_id
				WHERE
					'.$cond.'
					AND (products.group_id='.Session::get('group_id').' '.($master_group_id?' OR groups.id='.$master_group_id:'').')
					AND (products.del is null or products.del = 0)
				order by
					products.name
				LIMIT
					0,1000
			';
        $products = DB::fetch_all($sql);
        foreach($products as $key=>$value){
            $products[$key]['price'] = System::display_number($value['price']);
        }
        return $products;
    }
}