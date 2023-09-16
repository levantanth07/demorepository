<?php
class ListQlbhInventoryForm extends Form
{
	function __construct()
	{
		Form::Form('ListQlbhInventoryForm');
		$this->link_css(Portal::template('warehousing').'/css/style.css');
	}
	function draw()
	{
		$this->map = array();
		$item_per_page = 1000;
		$cond = '
			(qlbh_inventory.group_id='.Session::get('group_id').')
			'.(Url::get('warehouse_id')?' AND qlbh_inventory.warehouse_id = '.Url::iget('warehouse_id').'':'').'
			';
		$this->map['title'] = 'Khai báo tồn đầu kỳ';
		$sql = '
			SELECT
				count(*) AS acount
			FROM
				qlbh_inventory
				INNER JOIN qlbh_warehouse ON qlbh_warehouse.id = qlbh_inventory.warehouse_id
				INNER JOIN products ON products.code = qlbh_inventory.product_code
			WHERE
				'.$cond.'
		';
		require_once 'packages/core/includes/utils/paging.php';
		$this->map['total'] =  DB::fetch($sql,'acount');
		$this->map['paging'] =  paging($this->map['total'],$item_per_page,10);
		$sql = '
			SELECT
					qlbh_inventory.*,qlbh_warehouse.name as qlbh_warehouse_name,products.name as product_name
				FROM
					qlbh_inventory
					INNER JOIN qlbh_warehouse ON qlbh_warehouse.id = qlbh_inventory.warehouse_id
					INNER JOIN products ON products.code = qlbh_inventory.product_code
				WHERE
					'.$cond.'
				ORDER BY
					qlbh_warehouse.structure_id,qlbh_inventory.product_code
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		';
		$items = DB::fetch_all($sql);
		$i = 1;
		$customers = DB::select_all('qlbh_warehouse','group_id = 0 or group_id='.Session::get('group_id'),'structure_id');
		foreach($items as $key=>$value){
			$items[$key]['i'] = $i++;
			$items[$key]['opening_stock'] = round($value['opening_stock'],2);
		}
		$this->map['items'] = $items;
		$this->map['warehouse_id_list'] = array(''=>'Tất cả')+MiString::get_list($customers);
		$this->parse_layout('list',$this->map);
	}	
}
?>