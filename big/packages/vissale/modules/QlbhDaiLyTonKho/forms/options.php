<?php
class QlbhDaiLyTonKhoOptionsForm extends Form
{
	function __construct()
	{
		Form::Form('QlbhDaiLyTonKhoOptionsForm');
		$this->link_css('skins/admin/css/report.css');
		$this->add('date_from',new DateType(true,'invalid_date_from'));
		$this->add('date_to',new DateType(true,'invalid_date_to',0,255));
	}
	function draw()
	{
		$this->map = array();
		if(Session::is_set('warehouse_id')){
			$this->map['warehouse'] = DB::fetch('select name from qlbh_warehouse where id = '.Session::get('warehouse_id').'','name');
		}else{
			$this->map['warehouse'] = Portal::language('All');
		}
		$this->map['total_payment'] = 0;
		$this->map['title'] = Portal::language('Report_options');
		if(Url::get('date_from')){
			$this->map['date_from'] = Url::get('date_from');
		}
		if(Url::get('date_to')){
			$this->map['date_to'] = Url::get('date_to');
		}
		$this->map['year'] = date('Y',time());
		$this->map['month'] = date('m',time());
		$this->map['day'] = date('d',time());
		$layout = 'options';
		if(Url::get('store_remain')){
			$layout = 'store_remain';
			$this->map['title'] = 'Báo cáo nhập xuất tồn';
			$this->map['products'] = $this->get_store_remain_products();
		}elseif(Url::get('store_card')){
			$layout = 'store_card';
			$this->map['start_remain'] = 0;
			$this->map['end_remain'] = 0;
			$this->map['import_total'] = 0;
			$this->map['export_total'] = 0;
			$this->map['have_item'] = false;
			$this->map['products'] = get_store_card(Url::get('code'),Url::get('date_from'),Url::get('date_to'),$this->map);
			if(!$this->map['have_item']){
				echo '<div class=\'notice\'>Không tồn tại sản phẩm</div>';
				exit();
			}
		}elseif(Url::get('export')){
			$layout = 'warehouse_export';
			$this->map['total_amount'] = 0;
			$this->map['total_quantity'] = 0;
			$this->map['title'] = Portal::language('warehouse_export_report');
			$this->map['products'] = $this->get_warehouse_export();
		}elseif(Url::get('import')){
			$this->map['total_amount'] = 0;
			$this->map['total_quantity'] = 0;
			$layout = 'warehouse_import';
			$this->map['title'] = Portal::language('warehouse_import_report');
			$this->map['back_products'] = array();			
			$this->map['products'] = $this->get_warehouse_import();
		}
		$this->map['supplier'] = '';
		if(Url::get('supplier_id')){
			$this->map['supplier'] = DB::fetch('SELECT id,name FROM qlbh_supplier WHERE id='.Url::iget('supplier_id').'','name');	
		}
		$this->map['product_arr'] = DB::fetch_all('SELECT id,name FROM qlbh_product');
		$this->map['total_payment'] = number_format($this->map['total_payment']);
		$this->map['supplier_id_list'] = MiString::get_list(DB::select_all('qlbh_supplier',false,'code'));
		$this->map['warehouse_id_list'] = array(''=>'--------'.Portal::language('select').'---------')+MiString::get_list(DB::select_all('qlbh_warehouse',IDStructure::child_cond(ID_ROOT),'structure_id'));
		if((Url::get('date_from') and Url::get('date_to')) or (!Url::get('store_remain') and !Url::get('store_card'))){
			$this->parse_layout($layout,$this->map);
		}else{
			echo '<div class=\'notice\'>'.Portal::language('has_no_date_duration').'</div>';
		}
	}
	function get_store_remain_products(){
		$cond = '1=1 AND
				(
					(qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.Session::get('warehouse_id').') OR 
					(qlbh_stock_invoice.type=\'EXPORT\' AND (qlbh_stock_invoice_detail.warehouse_id='.Session::get('warehouse_id').' OR qlbh_stock_invoice_detail.to_warehouse_id='.Session::get('warehouse_id').'))
				)
				'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.Date_Time::to_sql_date(Url::get('date_from')).'\'':'').'
				'.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.Date_Time::to_sql_date(Url::get('date_to')).'\'':'').'
		';//'.(Session::get('warehouse_id')?' AND qlbh_stock_invoice.warehouse_id = '.Session::get('warehouse_id'):'').'
		//				'.(Session::get('warehouse_id')?' AND '.IDStructure::child_cond(DB::structure_id('warehouse',Session::get('warehouse_id'))).'':'').'
		$old_cond = '1=1 AND
				(
					(qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.Session::get('warehouse_id').') OR 
					(qlbh_stock_invoice.type=\'EXPORT\' AND (qlbh_stock_invoice_detail.warehouse_id='.Session::get('warehouse_id').' OR qlbh_stock_invoice_detail.to_warehouse_id='.Session::get('warehouse_id').'))
				)
				'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date <\''.Date_Time::to_sql_date(Url::get('date_from')).'\'':'').'
		';		
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.*,qlbh_stock_invoice.type
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id= qlbh_stock_invoice_detail.invoice_id
				INNER JOIN qlbh_product ON qlbh_product.code = qlbh_stock_invoice_detail.product_code
				LEFT OUTER JOIN qlbh_unit ON qlbh_unit.id = qlbh_product.unit_id
			WHERE
				'.$old_cond.'
			ORDER BY
				qlbh_stock_invoice_detail.product_code	
		';
		$items = DB::fetch_all($sql);
		$old_items = array();
		if(is_array($items))
		{
			foreach($items as $key=>$value){
				$product_code = $value['product_code'];
				if(isset($old_items[$product_code])){
					if($value['type']=='IMPORT' or $value['to_warehouse_id'] == Session::get('warehouse_id')){
						$old_items[$product_code]['import_number'] += $value['quantity'];
					}
					elseif($value['type']=='EXPORT' and $value['to_warehouse_id'] != Session::get('warehouse_id')){
						$old_items[$product_code]['export_number'] += $value['quantity'];
					}
				}else{
					$old_items[$product_code]['import_number'] = 0;
					$old_items[$product_code]['export_number'] = 0;
					if($value['type']=='IMPORT' or $value['to_warehouse_id'] == Session::get('warehouse_id')){
						$old_items[$product_code]['import_number'] = $value['quantity'];
					}
					if($value['type']=='EXPORT' and $value['to_warehouse_id'] != Session::get('warehouse_id')){
						$old_items[$product_code]['export_number'] = $value['quantity'];
					}
				}
			}
		}
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.id,qlbh_stock_invoice_detail.product_code,
				qlbh_product.name,qlbh_unit.name as unit,
				qlbh_stock_invoice_detail.quantity,qlbh_stock_invoice.type,qlbh_inventory.opening_stock as start_term_quantity,
				qlbh_product.category_id,qlbh_stock_invoice_detail.warehouse_id,qlbh_stock_invoice_detail.to_warehouse_id
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_product ON qlbh_product.code = qlbh_stock_invoice_detail.product_code
				LEFT OUTER JOIN qlbh_inventory ON qlbh_inventory.product_code = qlbh_product.code
				LEFT OUTER JOIN qlbh_unit ON qlbh_unit.id = qlbh_product.unit_id
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id= qlbh_stock_invoice_detail.invoice_id
			WHERE
				'.$cond.'
			ORDER BY
				qlbh_stock_invoice_detail.product_code	
		';
		$items = DB::fetch_all($sql);
		$sql = '
			SELECT
				qlbh_product.code as id,qlbh_product.code as product_code,qlbh_product.name, qlbh_unit.name unit,qlbh_inventory.opening_stock as start_term_quantity,
				qlbh_inventory.opening_stock as remain_number,
				0 as import_number,0 as export_number,qlbh_product_category.name as category_id
			FROM
				qlbh_product
				left outer JOIN qlbh_inventory ON qlbh_inventory.product_code = qlbh_product.code
				LEFT OUTER JOIN qlbh_product_category ON qlbh_product_category.id = qlbh_product.category_id
				LEFT OUTER JOIN qlbh_unit ON qlbh_unit.id = qlbh_product.unit_id	
			WHERE warehouse_id='.Session::get('warehouse_id').'
			ORDER BY
				qlbh_product_category.name,qlbh_product.id
		';
		$products = DB::fetch_all($sql);
		$i = 0;
		$new_products = $products;//array();
		foreach($items as $key=>$value){
			$product_code = $value['product_code'];
			if(isset($new_products[$product_code]['id'])){
				if($value['type']=='IMPORT' or $value['to_warehouse_id'] == Session::get('warehouse_id')){
					$new_products[$product_code]['import_number'] += $value['quantity'];
				}
				if($value['type']=='EXPORT' and $value['to_warehouse_id'] != Session::get('warehouse_id')){
					$new_products[$product_code]['export_number'] += $value['quantity'];
				}
				$new_products[$product_code]['remain_number'] = $new_products[$product_code]['import_number'] - $new_products[$product_code]['export_number'];
				$new_products[$product_code]['remain_number'] += $new_products[$product_code]['start_term_quantity'];
			}else{
				$new_products[$product_code]['start_term_quantity'] = 0;
				$new_products[$product_code]['id'] = $product_code;
				$new_products[$product_code]['product_code'] = $product_code;
				$new_products[$product_code]['unit'] = $value['unit'];
				$new_products[$product_code]['name'] = $value['name'];
				$new_products[$product_code]['import_number'] = 0;
				$new_products[$product_code]['export_number'] = 0;
				if($value['type']=='IMPORT' or $value['to_warehouse_id'] == Session::get('warehouse_id')){
					$new_products[$product_code]['import_number'] = $value['quantity'];
				}
				if($value['type']=='EXPORT' and $value['to_warehouse_id'] != Session::get('warehouse_id')){
					$new_products[$product_code]['export_number'] = $value['quantity'];
				}
				$new_products[$product_code]['remain_number'] = $new_products[$product_code]['import_number'] - $new_products[$product_code]['export_number'];
			}
		}
		foreach($new_products as $key=>$value){
			$product_code = $value['product_code'];
			if(isset($old_items[$product_code]['import_number'])){
				$new_products[$product_code]['start_term_quantity'] += System::calculate_number($old_items[$product_code]['import_number']) - System::calculate_number($old_items[$product_code]['export_number']);
				$new_products[$product_code]['remain_number'] +=  System::calculate_number($old_items[$product_code]['import_number']) - System::calculate_number($old_items[$product_code]['export_number']);
			}
			if(!isset($value['category_id'])){
				$new_products[$product_code]['category_id'] = '...';
			}
		}
		return $new_products;
	}	
	function get_warehouse_export(){
		$cond = ' qlbh_stock_invoice.type = \'EXPORT\' AND qlbh_stock_invoice_detail.warehouse_id = '.Session::get('warehouse_id').'
			'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.Date_Time::to_sql_date(Url::get('date_from')).'\'':'').'
			'.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.Date_Time::to_sql_date(Url::get('date_to')).'\'':'').'
		';
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.product_code as id,
				qlbh_stock_invoice_detail.product_code,
				qlbh_product.name,
				qlbh_stock_invoice_detail.id,
				qlbh_stock_invoice_detail.price, qlbh_unit.name as unit_name,
				SUM(qlbh_stock_invoice_detail.quantity) as quantity
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id
				INNER JOIN qlbh_product ON qlbh_product.code = qlbh_stock_invoice_detail.product_code
				LEFT OUTER JOIN qlbh_unit ON qlbh_unit.id = qlbh_product.unit_id
			WHERE
				'.$cond.'
			GROUP BY
				qlbh_stock_invoice_detail.product_code
			ORDER BY
				qlbh_stock_invoice_detail.product_code
		';
		$items = DB::fetch_all($sql);
		$i = 0;
		foreach($items as $key=>$value){
			$items[$key]['i'] = ++$i;
			$items[$key]['amount'] = System::display_number($value['price']*$value['quantity']);
			$this->map['total_quantity'] += $value['quantity'];
			$this->map['total_amount'] += $value['price']*$value['quantity'];
			$items[$key]['quantity'] = System::display_number($value['quantity']);
			$items[$key]['price'] = System::display_number($value['price']);
		}
		$this->map['total_amount'] = System::display_number($this->map['total_amount']);
		$this->map['total_quantity'] = System::display_number($this->map['total_quantity']);
		return $items;
	}	
	function get_warehouse_import(){
		$cond = ' qlbh_stock_invoice.type = \'IMPORT\'
			'.(Url::get('supplier_id')?' AND qlbh_stock_invoice.supplier_id = '.Url::get('supplier_id'):'').'
			'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.Date_Time::to_sql_date(Url::get('date_from')).'\'':'').'
			'.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.Date_Time::to_sql_date(Url::get('date_to')).'\'':'').'
		';
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.product_code,
				qlbh_product.name,
				qlbh_stock_invoice_detail.id,
				qlbh_stock_invoice_detail.price, qlbh_unit.name as unit_name,qlbh_stock_invoice_detail.quantity as quantity,
				qlbh_stock_invoice.create_date,
				qlbh_stock_invoice.bill_number
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id
				INNER JOIN qlbh_product ON qlbh_product.code = qlbh_stock_invoice_detail.product_code
				LEFT OUTER JOIN qlbh_unit ON qlbh_unit.id = qlbh_product.unit_id
			WHERE
				'.$cond.'
			ORDER BY
				qlbh_stock_invoice.create_date,qlbh_stock_invoice_detail.id
		';
		/*
			GROUP BY
				qlbh_stock_invoice_detail.product_code,qlbh_product.name_'.Portal::language().',
				qlbh_stock_invoice_detail.id, qlbh_unit.name_'.Portal::language().',
				qlbh_stock_invoice_detail.price,qlbh_stock_invoice.type
		*/
		$items = DB::fetch_all($sql);
		$i = 0;
		$arr_by_date = array();
		foreach($items as $key=>$value){
			$items[$key]['i'] = ++$i;
			$items[$key]['amount'] = System::display_number($value['price']*$value['quantity']);
			$this->map['total_quantity'] += $value['quantity'];
			$this->map['total_amount'] += $value['price']*$value['quantity'];
			$items[$key]['quantity'] = System::display_number($value['quantity']);
			$items[$key]['price'] = System::display_number($value['price']);
			$items[$key]['create_date'] = Date_Time::to_common_date($value['create_date']);
			if(!isset($arr_by_date[$items[$key]['create_date']])){
				$arr_by_date[$items[$key]['create_date']] = $value['price']*$value['quantity'];
			}else{
				$arr_by_date[$items[$key]['create_date']] += $value['price']*$value['quantity'];
			}
		}
		$this->map['arr_by_date'] = $arr_by_date;
		$cond = ' qlbh_stock_invoice.type = \'EXPORT\'
			'.(Url::get('supplier_id')?' AND qlbh_stock_invoice.supplier_id = '.Url::get('supplier_id'):'').'
			'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.Date_Time::to_sql_date(Url::get('date_from')).'\'':'').'
			'.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.Date_Time::to_sql_date(Url::get('date_to')).'\'':'').'
		';
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.id,
				qlbh_product.name,
				qlbh_stock_invoice_detail.product_code,
				qlbh_stock_invoice_detail.price, qlbh_unit.name as unit_name,
				qlbh_stock_invoice_detail.quantity as quantity,
				qlbh_stock_invoice.create_date,
				qlbh_stock_invoice.bill_number
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id
				INNER JOIN qlbh_product ON qlbh_product.code = qlbh_stock_invoice_detail.product_code
				LEFT OUTER JOIN qlbh_unit ON qlbh_unit.id = qlbh_product.unit_id
			WHERE
				'.$cond.'
			ORDER BY
				qlbh_stock_invoice.create_date,qlbh_stock_invoice_detail.id
		';
		$back_arr_by_date = array();
		$back_items = DB::fetch_all($sql);
		foreach($back_items as $key=>$value){
			$back_items[$key]['i'] = ++$i;
			$back_items[$key]['amount'] = System::display_number($value['price']*$value['quantity']);
			$this->map['total_quantity'] -= $value['quantity'];
			$this->map['total_amount'] -= $value['price']*$value['quantity'];
			$back_items[$key]['quantity'] = System::display_number($value['quantity']);
			$back_items[$key]['price'] = System::display_number($value['price']);
			$back_items[$key]['create_date'] = Date_Time::to_common_date($value['create_date']);
			if(!isset($back_arr_by_date[$back_items[$key]['create_date']])){
				$back_arr_by_date[$back_items[$key]['create_date']] = $value['price']*$value['quantity'];
			}else{
				$back_arr_by_date[$back_items[$key]['create_date']] += $value['price']*$value['quantity'];
			}
		}
		$this->map['back_products'] = $back_items;
		$this->map['back_arr_by_date'] = $back_arr_by_date;
		$cond = ' qlbh_stock_invoice.type = \'EXPORT\'
			'.(Url::get('supplier_id')?' AND qlbh_stock_invoice.supplier_id = '.Url::get('supplier_id'):'').'
			'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.Date_Time::to_sql_date(Url::get('date_from')).'\'':'').'
			'.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.Date_Time::to_sql_date(Url::get('date_to')).'\'':'').'
		';
		$this->map['total_before_tax'] = $this->map['total_amount']/(1.1);
		$this->map['shipping_fee'] = 0;
		$this->map['grand_total'] = 0;
		if(Url::get('shipping_fee')){
			$this->map['shipping_fee'] = Url::get('shipping_fee');
		}
		$this->map['commission'] = 0;
		$this->map['total_commission'] = 0;
		if(Url::get('commission')){
			$this->map['commission'] = 	Url::get('commission');
			$this->map['total_commission'] = $this->map['total_before_tax']*$this->map['commission']/100;
		}
		$this->map['total_after_commission'] = $this->map['total_before_tax'] - $this->map['total_commission'];
		$this->map['total_before_tax_commission'] = $this->map['total_after_commission']*(1.1);
		$this->map['grand_total'] = $this->map['total_before_tax_commission'] + $this->map['shipping_fee'];
		$this->map['total_commission'] = System::display_number($this->map['total_commission']);
		$this->map['total_before_tax_commission'] = System::display_number($this->map['total_before_tax_commission']);
		$this->map['total_after_commission'] =  System::display_number($this->map['total_after_commission']);
		$this->map['total_before_tax'] = System::display_number($this->map['total_before_tax']);
		$this->map['shipping_fee'] = System::display_number($this->map['shipping_fee']);
		$this->map['total_amount'] = System::display_number($this->map['total_amount']);
		$this->map['total_quantity'] = System::display_number($this->map['total_quantity']);
		$this->map['grand_total'] = System::display_number($this->map['grand_total']);
		return $items;
	}	
}
?>