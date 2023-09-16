<?php
class QlbhStockReportOptionsForm extends Form
{
    protected $map;
	function __construct()
	{
		Form::Form('QlbhStockReportOptionsForm');
		//$this->link_css('skins/admin/css/report.css');
		$this->link_js('assets/standard/js/autocomplete.js');
		$this->link_css('assets/standard/css/autocomplete/autocomplete.css');
		$this->add('date_from',new DateType(true,'invalid_date_from'));
		$this->add('date_to',new DateType(true,'invalid_date_to',0,255));
	}
	function draw()
	{
		$this->map = array();
		$group_id = Session::get('group_id');
		if(Url::get('warehouse_id')){
			$this->map['warehouse'] = DB::fetch('select name from qlbh_warehouse where id = '.Url::iget('warehouse_id').'','name');
		}else{
			$this->map['warehouse'] = 'Tất cả';
		}
		$this->map['total_payment'] = 0;
		$this->map['title'] = 'Tuỳ chọn xem báo cáo kho';
		if(Url::get('date_from')){
			$this->map['date_from'] = Url::get('date_from');
		}else{
            $_REQUEST['date_from'] = '01/'.date('m/Y');
        }
		if(Url::get('date_to')){
			$this->map['date_to'] = Url::get('date_to');
		}else{
            $_REQUEST['date_to'] = date('d/m/Y');
        }
		$this->map['year'] = date('Y',time());
		$this->map['month'] = date('m',time());
		$this->map['day'] = date('d',time());
		$layout = 'options';
		if(Url::get('store_remain')){
			$layout = 'store_remain';
			$this->map['title'] = 'Báo cáo nhập xuất tồn';
			$this->map['products'] = $this->get_store_remain_products($group_id);
		}elseif(Url::get('store_card')){
			$layout = 'store_card';
			$this->map['start_remain'] = 0;
			$this->map['end_remain'] = 0;
			$this->map['import_total'] = 0;
			$this->map['export_total'] = 0;
			$this->map['have_item'] = false;
            $product_id = Url::get('product_id');
			$this->map['products'] = $this->get_store_card($group_id,$product_id);
			if(!$this->map['have_item']){
				echo '<div class=\'alert alert-default\'>Không có kết quả phù hợp</div>';
				exit();
			}
		}elseif(Url::get('export')){
			$layout = 'warehouse_export';
			$this->map['total_amount'] = 0;
			$this->map['total_quantity'] = 0;
			$this->map['title'] = Portal::language('warehouse_export_report');
			$this->map['products'] = $this->get_warehouse_export($group_id);
		}elseif(Url::get('import')){
			$this->map['total_amount'] = 0;
			$this->map['total_quantity'] = 0;
			$layout = 'warehouse_import';
			$this->map['title'] = Portal::language('warehouse_import_report');
			$this->map['back_products'] = array();			
			$this->map['products'] = $this->get_warehouse_import($group_id);
		}
		$this->map['supplier'] = '';
		if(Url::get('supplier_id')){
			$this->map['supplier'] = DB::fetch('SELECT id,name FROM qlbh_supplier WHERE id='.Url::iget('supplier_id').'','name');	
		}
		$this->map['product_id_list'] = [''=>'Chọn sản phẩm'] + MiString::get_list(QlbhStockReportDB::get_products());

		$this->map['total_payment'] = number_format($this->map['total_payment']);
		$this->map['supplier_id_list'] = MiString::get_list(DB::select_all('qlbh_supplier','group_id = '.Session::get('group_id').'','code'));
		$this->map['warehouse_id_list'] = array(''=>'-------- Chọn ---------')+MiString::get_list(DB::select_all('qlbh_warehouse','structure_id='.ID_ROOT.' OR group_id = '.Session::get('group_id').'','structure_id'));
		$this->map += QlbhStockReportDB::get_order_info();
		if((Url::get('date_from') and Url::get('date_to')) or (!Url::get('store_remain') and !Url::get('store_card'))){
			$this->parse_layout($layout,$this->map);
		}else{
			echo '<div class=\'notice\'>'.Portal::language('has_no_date_duration').'</div>';
		}
	}
	function get_store_card($group_id,$product_id){
	    $warehouse_id = Url::iget('warehouse_id');
        $date_from = Date_Time::to_sql_date(Url::get('date_from'));
        $date_to = Date_Time::to_sql_date(Url::get('date_to'));
		if($product_id and $row = DB::select('products','id = '.$product_id.' and group_id='.$group_id.'')){
			$this->map['have_item'] =  true;
			$this->map['code'] = $row['code'];
			$this->map['name'] = $row['name'];
			$old_cond = '
					qlbh_stock_invoice.group_id = '.$group_id.'
					AND qlbh_stock_invoice_detail.product_code = \''.$row['code'].'\' 
					AND
					(
						(qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') 
						OR (
						        (qlbh_stock_invoice.type=\'EXPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') 
						        OR qlbh_stock_invoice_detail.to_warehouse_id='.$warehouse_id.'
						    )
						)
					'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date <\''.$date_from.'\'':'').'
			';
			$sql = '
				SELECT
					qlbh_stock_invoice_detail.id,
					qlbh_stock_invoice_detail.product_code,
					qlbh_stock_invoice_detail.quantity,qlbh_stock_invoice.type,
					qlbh_stock_invoice_detail.to_warehouse_id,
					qlbh_stock_invoice_detail.warehouse_id
				FROM
					qlbh_stock_invoice_detail
					JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id= qlbh_stock_invoice_detail.invoice_id
				WHERE
					'.$old_cond.'
			';
			$items = DB::fetch_all($sql);
			$old_items = array();
			///
            if(is_array($items))
            {
                foreach($items as $key=>$value){
                    $product_code = $value['product_code'];
                    if(isset($old_items[$product_code])){
                        if($value['type']=='IMPORT' or $value['to_warehouse_id'] == $warehouse_id){
                            $old_items[$product_code]['import_number'] += $value['quantity'];
                        }
                        elseif($value['type']=='EXPORT' and $value['to_warehouse_id'] != $warehouse_id){
                            $old_items[$product_code]['export_number'] += $value['quantity'];
                        }
                    }else{
                        $old_items[$product_code]['import_number'] = 0;
                        $old_items[$product_code]['export_number'] = 0;
                        if($value['type']=='IMPORT' or $value['to_warehouse_id'] == $warehouse_id){
                            $old_items[$product_code]['import_number'] = $value['quantity'];
                        }
                        if($value['type']=='EXPORT' and $value['to_warehouse_id'] != $warehouse_id){
                            $old_items[$product_code]['export_number'] = $value['quantity'];
                        }
                    }
                }
            }
            $this->map['start_remain'] = 0;
			$cond = '
                qlbh_stock_invoice.group_id = '.$group_id.'
                AND qlbh_stock_invoice_detail.product_code = \''.$row['code'].'\' AND
                    (
                        (qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') 
						OR (qlbh_stock_invoice.type=\'EXPORT\' AND (qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.' 
						OR qlbh_stock_invoice_detail.to_warehouse_id='.$warehouse_id.'))
                    )
                    '.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.$date_from.'\'':'').'
                    '.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.$date_to.'\'':'').'
			';
			$sql = '
				SELECT
					qlbh_stock_invoice_detail.id,
					IF(qlbh_stock_invoice.type=\'IMPORT\',CONCAT("PN",qlbh_stock_invoice.bill_number),\'\') AS import_invoice_code,
					IF(qlbh_stock_invoice.type=\'EXPORT\',CONCAT("PX",qlbh_stock_invoice.bill_number),\'\') AS export_invoice_code,
					qlbh_stock_invoice_detail.quantity,
					qlbh_stock_invoice_detail.warehouse_id,
					qlbh_stock_invoice_detail.to_warehouse_id,
					qlbh_stock_invoice_detail.product_code,
					qlbh_stock_invoice.create_date,
					qlbh_stock_invoice.type,
					qlbh_stock_invoice.note
				FROM
				    qlbh_stock_invoice_detail
					JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id
				WHERE
					'.$cond.'
				ORDER BY
					qlbh_stock_invoice.create_date,qlbh_stock_invoice.time
			';
			$items = DB::fetch_all($sql);
			if(isset($old_items[$row['code']])){
				if(isset($old_items[$row['code']]['import_number'])){
					$this->map['start_remain'] += $old_items[$row['code']]['import_number'];
				}
				if(isset($old_items[$row['code']]['export_number'])){
					$this->map['start_remain'] -= $old_items[$row['code']]['export_number'];
				}				
			}
			$remain = $this->map['start_remain'];
			foreach($items as $key=>$value){
				$items[$key]['create_date'] = Date_Time::to_common_date($value['create_date']);
				if($value['type']=='IMPORT' or ($value['to_warehouse_id'] == Url::get('warehouse_id'))){
					$items[$key]['import_number'] = $value['quantity'];
				}else{
					$items[$key]['import_number'] = 0;
				}
				if($value['type']=='EXPORT' and ($value['to_warehouse_id'] != Url::get('warehouse_id'))){
					$items[$key]['export_number'] = $value['quantity'];
				}else{
					$items[$key]['export_number'] = 0;		
				}
				$this->map['end_remain'] += $items[$key]['import_number'] - $items[$key]['export_number'];
				$remain = $remain + $items[$key]['import_number'] - $items[$key]['export_number'];
				$items[$key]['remain'] = $remain;
				$this->map['import_total'] += $items[$key]['import_number'];
				$this->map['export_total'] += $items[$key]['export_number'];
			}
			$this->map['end_remain'] += $this->map['start_remain'];
			return $items;
		}else{
			
		}
	}
	function get_store_remain_products($group_id){
        $warehouse_id = Url::iget('warehouse_id');
        $date_from = Date_Time::to_sql_date(Url::get('date_from'));
        $date_to = Date_Time::to_sql_date(Url::get('date_to'));
		$cond = 'qlbh_stock_invoice.group_id = '.$group_id.' AND
				(
					(qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') OR 
					(qlbh_stock_invoice.type=\'EXPORT\' AND ((qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') OR qlbh_stock_invoice_detail.to_warehouse_id='.$warehouse_id.'))
				)
				'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.$date_from.'\'':'').'
				'.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.$date_to.'\'':'').'
		';//'.(Url::get('warehouse_id')?' AND qlbh_stock_invoice.warehouse_id = '.Url::get('warehouse_id'):'').'
		//				'.(Url::get('warehouse_id')?' AND '.IDStructure::child_cond(DB::structure_id('warehouse',$warehouse_id)).'':'').'
		$old_cond = 'qlbh_stock_invoice.group_id = '.$group_id.' AND
				(
					(qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') 
						OR (qlbh_stock_invoice.type=\'EXPORT\' AND ((qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') 
						OR qlbh_stock_invoice_detail.to_warehouse_id='.$warehouse_id.'))
				)
				'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date <\''.$date_from.'\'':'').'
		';
        $sql = '
            SELECT
                qlbh_stock_invoice_detail.id,
                qlbh_stock_invoice_detail.product_code,
                qlbh_stock_invoice_detail.quantity,qlbh_stock_invoice.type,
                qlbh_stock_invoice_detail.to_warehouse_id,
                qlbh_stock_invoice_detail.warehouse_id
            FROM
                qlbh_stock_invoice_detail
                JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id= qlbh_stock_invoice_detail.invoice_id
            WHERE
                '.$old_cond.'
        ';
		$items = DB::fetch_all($sql);
		$old_items = array();
		if(is_array($items))
		{
			foreach($items as $key=>$value){
				$product_code = $value['product_code'];
				if(isset($old_items[$product_code])){
					if($value['type']=='IMPORT' or $value['to_warehouse_id'] == $warehouse_id){
						$old_items[$product_code]['import_number'] += $value['quantity'];
					}
					elseif($value['type']=='EXPORT' and $value['to_warehouse_id'] != $warehouse_id){
						$old_items[$product_code]['export_number'] += $value['quantity'];
					}
				}else{
					$old_items[$product_code]['import_number'] = 0;
					$old_items[$product_code]['export_number'] = 0;
					if($value['type']=='IMPORT' or $value['to_warehouse_id'] == $warehouse_id){
						$old_items[$product_code]['import_number'] = $value['quantity'];
					}
					if($value['type']=='EXPORT' and $value['to_warehouse_id'] != $warehouse_id){
						$old_items[$product_code]['export_number'] = $value['quantity'];
					}
				}
			}
		}
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.id,qlbh_stock_invoice_detail.product_code,
				products.name,units.name as unit,
				qlbh_stock_invoice_detail.quantity,qlbh_stock_invoice.type,qlbh_inventory.opening_stock as start_term_quantity,
				qlbh_stock_invoice_detail.warehouse_id,qlbh_stock_invoice_detail.to_warehouse_id
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN products ON products.code = qlbh_stock_invoice_detail.product_code AND products.group_id = '.$group_id.'
				LEFT OUTER JOIN qlbh_inventory ON qlbh_inventory.product_code = products.code AND products.group_id = '.$group_id.'
				LEFT OUTER JOIN units ON units.id = products.unit_id
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id= qlbh_stock_invoice_detail.invoice_id
			WHERE
				'.$cond.'
			ORDER BY
				qlbh_stock_invoice_detail.product_code	
		';
		$items = DB::fetch_all($sql);
		$sql = '
			SELECT
				products.code as id,products.code as product_code,products.name, units.name unit,qlbh_inventory.opening_stock as start_term_quantity,
				qlbh_inventory.opening_stock as remain_number,
				0 as import_number,0 as export_number
			FROM
				products
				left JOIN qlbh_inventory ON qlbh_inventory.product_code = products.code AND products.group_id = '.$group_id.'
				LEFT JOIN units ON units.id = products.unit_id
			WHERE
				warehouse_id='.$warehouse_id.'
				AND products.group_id = '.$group_id.'
			ORDER BY
				products.id
		';
		$products = DB::fetch_all($sql);
		$i = 1;
		$new_products = $products;//array();
		foreach($items as $key=>$value){
			$product_code = $value['product_code'];
			if(isset($new_products[$product_code]['id'])){
				if($value['type']=='IMPORT' or $value['to_warehouse_id'] == $warehouse_id){
					$new_products[$product_code]['import_number'] += $value['quantity'];
				}
				if($value['type']=='EXPORT' and $value['to_warehouse_id'] != $warehouse_id){
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
				if($value['type']=='IMPORT' or $value['to_warehouse_id'] == $warehouse_id){
					$new_products[$product_code]['import_number'] = $value['quantity'];
				}
				if($value['type']=='EXPORT' and $value['to_warehouse_id'] != $warehouse_id){
					$new_products[$product_code]['export_number'] = $value['quantity'];
				}
				$new_products[$product_code]['remain_number'] = $new_products[$product_code]['import_number'] - $new_products[$product_code]['export_number'];
			}
		}
		foreach($new_products as $key=>$value){
			$product_code = $value['product_code'];
			$stt = $i;
			if(isset($old_items[$product_code]['import_number'])){
				$new_products[$product_code]['start_term_quantity'] += System::calculate_number($old_items[$product_code]['import_number']) - System::calculate_number($old_items[$product_code]['export_number']);
				$new_products[$product_code]['remain_number'] +=  System::calculate_number($old_items[$product_code]['import_number']) - System::calculate_number($old_items[$product_code]['export_number']);
			}
			if(!isset($value['category_id'])){
				$new_products[$product_code]['category_id'] = '...';
			}
			$new_products[$product_code]['stt'] = $i++;
		}
		return $new_products;
	}	
	function get_warehouse_export($group_id){
        $warehouse_id = Url::get('warehouse_id');
        $date_from = Date_Time::to_sql_date(Url::get('date_from'));
        $date_to = Date_Time::to_sql_date(Url::get('date_to'));
		$cond = '
		qlbh_stock_invoice.group_id = '.$group_id.'
		AND qlbh_stock_invoice.type = \'EXPORT\' AND qlbh_stock_invoice_detail.warehouse_id = '.$warehouse_id.'
			'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.$date_from.'\'':'').'
			'.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.$date_to.'\'':'').'
		';
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.product_code as id,
				qlbh_stock_invoice_detail.product_code,
				products.name,
				qlbh_stock_invoice_detail.id,
				qlbh_stock_invoice_detail.price, units.name as unit_name,
				SUM(qlbh_stock_invoice_detail.quantity) as quantity
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id
				INNER JOIN products ON products.code = qlbh_stock_invoice_detail.product_code
				LEFT OUTER JOIN units ON units.id = products.unit_id
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
	function get_warehouse_import($group_id){
		$cond = '
		qlbh_stock_invoice.group_id = '.$group_id.'
		AND qlbh_stock_invoice.type = \'IMPORT\'
			'.(Url::get('supplier_id')?' AND qlbh_stock_invoice.supplier_id = '.Url::iget('supplier_id'):'').'
			'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.Date_Time::to_sql_date(Url::get('date_from')).'\'':'').'
			'.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.Date_Time::to_sql_date(Url::get('date_to')).'\'':'').'
		';
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.product_code,
				products.name,
				qlbh_stock_invoice_detail.id,
				qlbh_stock_invoice_detail.price, units.name as unit_name,qlbh_stock_invoice_detail.quantity as quantity,
				qlbh_stock_invoice.create_date,
				qlbh_stock_invoice.bill_number
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id
				INNER JOIN products ON products.code = qlbh_stock_invoice_detail.product_code
				LEFT OUTER JOIN units ON units.id = products.unit_id
			WHERE
				'.$cond.'
			ORDER BY
				qlbh_stock_invoice.create_date,qlbh_stock_invoice_detail.id
		';
		/*
			GROUP BY
				qlbh_stock_invoice_detail.product_code,products.name_'.Portal::language().',
				qlbh_stock_invoice_detail.id, units.name_'.Portal::language().',
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
			'.(Url::get('supplier_id')?' AND qlbh_stock_invoice.supplier_id = '.Url::iget('supplier_id'):'').'
			'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date >=\''.Date_Time::to_sql_date(Url::get('date_from')).'\'':'').'
			'.(Url::get('date_to')?' AND qlbh_stock_invoice.create_date <=\''.Date_Time::to_sql_date(Url::get('date_to')).'\'':'').'
		';
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.id,
				products.name,
				qlbh_stock_invoice_detail.product_code,
				qlbh_stock_invoice_detail.price, units.name as unit_name,
				qlbh_stock_invoice_detail.quantity as quantity,
				qlbh_stock_invoice.create_date,
				qlbh_stock_invoice.bill_number
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id
				INNER JOIN products ON products.code = qlbh_stock_invoice_detail.product_code
				LEFT OUTER JOIN units ON units.id = products.unit_id
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
			'.(Url::get('supplier_id')?' AND qlbh_stock_invoice.supplier_id = '.Url::iget('supplier_id'):'').'
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