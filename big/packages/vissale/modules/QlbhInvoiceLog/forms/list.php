<?php
class ListQlbhInvoiceLogForm extends Form
{
	function __construct()
	{
		Form::Form('ListQlbhInvoiceLogForm');
		$this->link_css('packages/hotel/packages/warehousing/skins/default/css/invoice.css');
		$this->link_css('packages/hotel/packages/warehousing/skins/default/css/style.css');
		$this->link_css(Portal::template('core').'/css/jquery/datepicker.css');
		$this->link_js('packages/core/includes/js/jquery/datepicker.js');
	}
	function draw()
	{
		$this->map = array();
		$item_per_page = 1000;
			$cond = '1=1			
			'.(Url::get('bill_number')?' AND qlbh_stock_invoice.bill_number LIKE \'%'.Url::sget('bill_number').'%\'':'').'
			'.(Url::get('note')?' AND UPPER(qlbh_stock_invoice.note) LIKE \'%'.strtoupper(Url::sget('note')).'%\'':'').'
			'.(Url::get('receiver_name')?' AND qlbh_stock_invoice.receiver_name LIKE \'%'.Url::sget('receiver_name').'%\'':'').'
			'.(Url::get('create_date_from')?' AND qlbh_stock_invoice.create_date >= \''.Date_Time::to_sql_date(Url::sget('create_date_from')).'\'':'').'
			'.(Url::get('create_date_to')?' AND qlbh_stock_invoice.create_date <= \''.Date_Time::to_sql_date(Url::sget('create_date_to')).'\'':'').'
			'.(Url::get('warehouse_id')?' AND qlbh_stock_invoice_detail.warehouse_id = '.Url::iget('warehouse_id').'':'').'
			'.(Url::get('supplier_id')?' AND qlbh_stock_invoice.supplier_id = '.Url::iget('supplier_id').'':'').'
			';
		if(!User::can_admin(false,ANY_CATEGORY)){
			if(Session::get('qlkv')){
				$cond .= ' AND zone.id IN ('.Session::get('qlkv_zone_ids').')';
			}else{
				$cond .= ((Session::is_set('warehouse_id'))?' AND qlbh_stock_invoice_detail.warehouse_id = \''.Session::get('warehouse_id').'\'':'');
			}
		}
		require_once 'packages/core/includes/utils/paging.php';	
		$items = $this->get_items('IMPORT',$cond,20,page_no('pn_pn'));
		$i = 1;
		$suppliers = DB::select_all('qlbh_supplier');
		$customers = DB::select_all('qlbh_warehouse',IDStructure::child_cond(ID_ROOT));
		$shops = QlbhInvoiceLogDB::get_shop(Session::get('user_id'));
		foreach($items as $key=>$value){
			$items[$key]['i'] = $i++;
			$items[$key]['create_date'] = Date_Time::to_common_date($value['create_date']);
			if(isset($suppliers[$value['supplier_id']])){
				$items[$key]['supplier_name'] = $suppliers[$value['supplier_id']]['name'];
			}else{
				$items[$key]['supplier_name'] = '';
			}
			if(isset($shops[$value['shop_id']])){
				$items[$key]['shop_name'] = $shops[$value['shop_id']]['name'];
			}else{
				$items[$key]['shop_name'] = '';
			}
			if(isset($customers[$value['warehouse_id']])){
				$items[$key]['warehouse_name'] = $customers[$value['warehouse_id']]['name'];
			}else{
				$items[$key]['warehouse_name'] = '';
			}
		}
		$this->map['items'] = $items;
		$this->map['total'] =  $this->get_total_items('IMPORT',$cond);
		$this->map['paging'] =  paging($this->map['total'],$item_per_page,10,$smart=false,$page_name='pn_pn',array('type','create_date_from','create_date_to'));
		
		$ex_items = $this->get_items('EXPORT',$cond,20,page_no('px_pn'));
		$i = 1;
		foreach($ex_items as $key=>$value){
			$ex_items[$key]['i'] = $i++;
			$ex_items[$key]['create_date'] = Date_Time::to_common_date($value['create_date']);
			if(isset($suppliers[$value['supplier_id']])){
				$ex_items[$key]['supplier_name'] = $suppliers[$value['supplier_id']]['name'];
			}else{
				$ex_items[$key]['supplier_name'] = '';
			}
			if(isset($shops[$value['shop_id']])){
				$ex_items[$key]['shop_name'] = $shops[$value['shop_id']]['name'];
			}else{
				$ex_items[$key]['shop_name'] = '';
			}
			if(isset($customers[$value['warehouse_id']])){
				$ex_items[$key]['warehouse_name'] = $customers[$value['warehouse_id']]['name'];
			}else{
				$ex_items[$key]['warehouse_name'] = '';
			}
		}
		$this->map['ex_items'] = $ex_items;
		$this->map['ex_total'] =  $this->get_total_items('EXPORT',$cond);
		$this->map['ex_paging'] =  paging($this->map['ex_total'],$item_per_page,10,$smart=false,$page_name='px_pn',array('type','create_date_from','create_date_to'));
		
		$this->map['shop_id_list'] = array(''=>'Chọn')+MiString::get_list($shops);
		$this->map['supplier_id_list'] = array(''=>Portal::language('All'))+MiString::get_list($suppliers);
		$this->map['warehouse_id_list'] = array(''=>Portal::language('All'))+MiString::get_list($customers);
		$layout = ((Url::get('page')=='qlbh_dai_ly_xuat_ban')?'m_':'').'list';
		if(Session::get('qlkv') == 1){
			$layout = 'qlkv_list';
		}
		$this->parse_layout($layout,$this->map);
	}
	function get_items($type,$cond,$item_per_page,$page_no){
		$sql = '
				SELECT
					qlbh_stock_invoice.*
				FROM
					qlbh_stock_invoice
					INNER JOIN qlbh_stock_invoice_detail ON qlbh_stock_invoice_detail.invoice_id = qlbh_stock_invoice.id
					LEFT OUTER JOIN hrm_employee ON hrm_employee.user_id = qlbh_stock_invoice.user_id
					LEFT OUTER JOIN zone ON zone.id = hrm_employee.zone_id
				WHERE
					qlbh_stock_invoice.type = "'.$type.'"
					AND 
					'.$cond.'
				ORDER BY
					qlbh_stock_invoice.create_date DESC,qlbh_stock_invoice.bill_number
				LIMIT
					'.((page_no($page_no)-1)*$item_per_page).','.$item_per_page.'
		';
		$items = DB::fetch_all($sql);
		return $items;
	}
	function get_total_items($type,$cond){
		$sql = '
			SELECT
				count(*) AS acount
			FROM
				qlbh_stock_invoice
				INNER JOIN qlbh_stock_invoice_detail ON qlbh_stock_invoice_detail.invoice_id = qlbh_stock_invoice.id
				INNER JOIN hrm_employee ON hrm_employee.user_id = qlbh_stock_invoice.user_id
				INNER JOIN zone ON zone.id = hrm_employee.zone_id
			WHERE
				qlbh_stock_invoice.type = "'.$type.'"
					AND 
					'.$cond.'
		';	
		return DB::fetch($sql,'acount');	
	}
}
?>