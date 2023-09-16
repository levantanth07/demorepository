<?php
class ListQlbhSaleReportForm extends Form
{
	function __construct()
	{
		Form::Form('ListQlbhSaleReportForm');
		$this->link_css('skins/default/css/cms.css');
	}
	function draw()
	{
		$this->map = array();
		$item_per_page = 1000;
		$cond = '
			1=1
			'.(Url::get('keyword')?' AND ksb.product_name LIKE "%'.Url::sget('keyword').'%"':'').'
			'.(Url::get('start_time')?' AND (qlbh_stock_invoice.create_date >= '.Date_Time::to_sql_date(Url::get('start_time')).')':'').'
			'.(Url::get('end_time')?' AND (qlbh_stock_invoice.create_date <= '.Date_Time::to_sql_date(Url::get('end_time')).')':'').'
			';
		$sql = '
			SELECT
				count(*) AS acount
			FROM
				qlbh_stock_invoice_detail as ksb
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = ksb.invoice_id
				LEFT OUTER JOIN qlbh_unit ON qlbh_unit.id = ksb.unit_id				
			WHERE
				'.$cond.'
			GROUP BY
				ksb.product_name
		';
		require_once 'packages/core/includes/utils/paging.php';
		$this->map['total'] =  DB::fetch($sql,'acount');
		$this->map['paging'] =  paging($this->map['total'],$item_per_page,10);
		$sql = '
			SELECT
				ksb.id,
				ksb.product_name as name,
				qlbh_unit.name as unit,
				sum(ksb.quantity) as quantity,
				sum(ksb.quantity*ksb.price) as amount,
				sum(ksb.discount) as discount
			FROM
				qlbh_stock_invoice_detail as ksb
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = ksb.invoice_id
				LEFT OUTER JOIN qlbh_unit ON qlbh_unit.id = ksb.unit_id
			WHERE
				'.$cond.'
			GROUP BY
				ksb.product_name
			ORDER BY
				'.(Url::get('order_by')?Url::get('order_by'):'ksb.product_name').' '.(Url::get('dir')?Url::get('dir'):'ASC').'
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		';
		$items = DB::fetch_all($sql);
		$i = 1;
		$this->map['total_amount'] = 0;
		$this->map['total_discount'] = 0;		
		$this->map['total_remain'] = 0;		
		foreach($items as $key=>$value){
			$items[$key]['i'] = $i++;
			$items[$key]['remain'] = $value['amount'] - $value['discount'];
			$this->map['total_amount'] += $value['amount'];
			$this->map['total_discount'] += $value['discount'];			
			$this->map['total_remain'] += $items[$key]['remain'];
		}
		$this->map['items'] = $items;
		$this->parse_layout('list',$this->map);
	}
}
?>