<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 7/30/19
 * Time: 11:03 AM
 */
class CashFlowCategoryDB{
    static function get_total_item($cond){
        $sql = ('
				select 
					count(cash_flow_category.id) as total
				from 
					cash_flow_category
				where 
					'.$cond.'
			');
        return $count = DB::fetch($sql,'total');
    }
    static function get_items($cond,$item_per_page=10){
        $sql = '
				select 
					cash_flow_category.id,
					cash_flow_category.name,
					cash_flow_category.type,
					cash_flow_category.group_id
				from 
					cash_flow_category
				WHERE
					'.$cond.'
				GROUP BY
					cash_flow_category.id
				order by 
					cash_flow_category.id  DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
        return $mi_category = DB::fetch_all($sql);
    }
}