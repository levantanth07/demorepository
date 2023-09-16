<?php
function get_remain_products($warehouse_id)
{
		return 0;
		$cond = $invoice_cond = '1=1';
		
		if(Url::get('cmd')=='edit' and Url::get('id'))
		{
			$invoice_cond .= ' and qlbh_stock_invoice.group_id='.Session::get('group_id').' and qlbh_stock_invoice.id<>'.intval(Url::sget('id'));
		}
		
		if(Url::get('warehouse_id'))
		{
			$invoice_cond.=' and qlbh_stock_invoice.group_id='.Session::get('group_id').' and qlbh_stock_invoice.warehouse_id='.$warehouse_id.'';
			$cond.='  and qlbh_stock_invoice.group_id='.Session::get('group_id').'  and qlbh_inventory.warehouse_id='.$warehouse_id;
		}
		$sql = '
				select
					products.id,
					((CASE WHEN qlbh_inventory.opening_stock>0 THEN qlbh_inventory.opening_stock ELSE 0 END) + (CASE WHEN invoice.remain_invoice>0 THEN invoice.remain_invoice ELSE 0 END)) as remain_number
				from
					products
					left outer join (
						select 
							qlbh_stock_invoice_detail.id,
							qlbh_stock_invoice_detail.product_code,
							sum(CASE WHEN qlbh_stock_invoice.type=\'IMPORT\' THEN qlbh_stock_invoice_detail.quantity ELSE -qlbh_stock_invoice_detail.quantity END) as remain_invoice
						from
							qlbh_stock_invoice_detail
							inner join qlbh_stock_invoice on qlbh_stock_invoice.id = qlbh_stock_invoice_detail.invoice_id
							inner join products on products.id = qlbh_stock_invoice_detail.product_code
						where
							'.$invoice_cond.'
						group by
							qlbh_stock_invoice_detail.id,qlbh_stock_invoice_detail.product_code
					) invoice on invoice.product_code = products.code
					left outer join qlbh_inventory on qlbh_inventory.product_code = products.code
				where
					'.$cond.'
		';
		$items = DB::fetch_all($sql);
		return $items;
}
function get_store_card($code,$date_from,$date_to,&$map){
	if($code and $row = DB::select('products','code = "'.trim($code).'"')){
		$map['have_item'] =  true;
		$map['code'] = $row['id'];
		$map['name'] = $row['name'];
		$old_cond = 'qlbh_stock_invoice_detail.product_code = \''.$row['code'].'\' AND
				(
					(qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') OR 
					(qlbh_stock_invoice.type=\'EXPORT\' AND (qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.' OR qlbh_stock_invoice_detail.to_warehouse_id='.$warehouse_id.'))
					)
				'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date <\''.Date_Time::to_sql_date($date_from).'\'':'').'
		';		
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.id,qlbh_stock_invoice_detail.product_code,qlbh_stock_invoice_detail.quantity,qlbh_stock_invoice.type,
				qlbh_stock_invoice_detail.to_warehouse_id,qlbh_stock_invoice_detail.warehouse_id
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id= qlbh_stock_invoice_detail.invoice_id
			WHERE
				'.$old_cond.'
			ORDER BY
				qlbh_stock_invoice.create_date,qlbh_stock_invoice.time
		';
		$items = DB::fetch_all($sql);
		$old_items = array();
		foreach($items as $key=>$value){
			$product_code = $value['product_code'];
			if($value['type']=='IMPORT' or $value['to_warehouse_id'] == $warehouse_id){
				if(isset($old_items[$product_code]['import_number'])){
					$old_items[$product_code]['import_number'] += $value['quantity'];
				}else{
					$old_items[$product_code]['import_number'] = $value['quantity'];
				}
			}elseif($value['type']=='EXPORT' and $value['to_warehouse_id'] != $warehouse_id){
				if(isset($old_items[$product_code]['export_number'])){
					$old_items[$product_code]['export_number'] += $value['quantity'];
				}else{
					$old_items[$product_code]['export_number'] = $value['quantity'];
				}
			}
		}
		$sql = '
			SELECT
				qlbh_inventory.id,qlbh_inventory.warehouse_id,
				qlbh_inventory.product_code,qlbh_inventory.opening_stock
			FROM
				qlbh_inventory
			WHERE	
				qlbh_inventory.product_code = \''.$row['code'].'\' AND warehouse_id='.$warehouse_id.'
		';
		if($product = DB::fetch($sql)){
			$map['start_remain'] = $product['opening_stock'];	
		}else{
			$map['start_remain'] = 0;	
		}
		$cond = 'qlbh_stock_invoice_detail.product_code = \''.$row['code'].'\' AND
			(
				(qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') OR 
				(qlbh_stock_invoice.type=\'EXPORT\' AND (qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.' OR qlbh_stock_invoice_detail.to_warehouse_id='.$warehouse_id.'))
			)
			'.($date_from?' AND qlbh_stock_invoice.create_date >=\''.Date_Time::to_sql_date($date_from).'\'':'').'
			'.($date_to?' AND qlbh_stock_invoice.create_date <=\''.Date_Time::to_sql_date($date_to).'\'':'').'
			
		';
		$sql = '
			SELECT
				qlbh_stock_invoice.*,
				IF(qlbh_stock_invoice.type=\'IMPORT\',qlbh_stock_invoice.bill_number,\'\') AS import_invoice_code,
				IF(qlbh_stock_invoice.type=\'EXPORT\',qlbh_stock_invoice.bill_number,\'\') AS export_invoice_code,
				qlbh_stock_invoice_detail.quantity,qlbh_stock_invoice_detail.warehouse_id,qlbh_stock_invoice_detail.to_warehouse_id
			FROM
				qlbh_stock_invoice
				INNER JOIN qlbh_stock_invoice_detail ON qlbh_stock_invoice_detail.invoice_id = qlbh_stock_invoice.id
			WHERE
				'.$cond.'
			ORDER BY
				qlbh_stock_invoice.create_date,qlbh_stock_invoice.time
		';
		$items = DB::fetch_all($sql);
		if(isset($old_items[$row['id']])){
			if(isset($old_items[$row['id']]['import_number'])){
				$map['start_remain'] += $old_items[$row['id']]['import_number'];
			}
			if(isset($old_items[$row['id']]['export_number'])){
				$map['start_remain'] -= $old_items[$row['id']]['export_number'];
			}				
		}
		$remain = $map['start_remain'];
		foreach($items as $key=>$value){
			$items[$key]['create_date'] = Date_Time::to_common_date($value['create_date']);
			if($value['type']=='IMPORT' or ($value['to_warehouse_id'] == $warehouse_id)){
				$items[$key]['import_number'] = $value['quantity'];
			}else{
				$items[$key]['import_number'] = 0;
			}
			if($value['type']=='EXPORT' and ($value['to_warehouse_id'] != $warehouse_id)){
				$items[$key]['export_number'] = $value['quantity'];
			}else{
				$items[$key]['export_number'] = 0;		
			}
			$map['end_remain'] += $items[$key]['import_number'] - $items[$key]['export_number'];
			$remain = $remain + $items[$key]['import_number'] - $items[$key]['export_number'];
			$items[$key]['remain'] = $remain;
			$map['import_total'] += $items[$key]['import_number'];
			$map['export_total'] += $items[$key]['export_number'];
		}
		$map['end_remain'] += $map['start_remain'];
		return $items;
	}else{
		return false;
	}
}
function get_product_inventory($code,$date_from,$date_to,$warehouse_id){
	$map = array();
	$map['end_remain'] = 0;
	$map['import_total'] = 0;
	$map['export_total'] = 0; 
	if($code and $row = DB::select('products','code = "'.trim($code).'"')){
		$old_cond = 'qlbh_stock_invoice_detail.product_code = \''.$row['code'].'\' AND
				(
					(qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') OR 
					(qlbh_stock_invoice.type=\'EXPORT\' AND (qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.' OR qlbh_stock_invoice_detail.to_warehouse_id='.$warehouse_id.'))
					)
				'.(Url::get('date_from')?' AND qlbh_stock_invoice.create_date <\''.Date_Time::to_sql_date($date_from).'\'':'').'
		';		
		$sql = '
			SELECT
				qlbh_stock_invoice_detail.id,qlbh_stock_invoice_detail.product_code,qlbh_stock_invoice_detail.quantity,qlbh_stock_invoice.type,
				qlbh_stock_invoice_detail.to_warehouse_id,qlbh_stock_invoice_detail.warehouse_id
			FROM
				qlbh_stock_invoice_detail
				INNER JOIN qlbh_stock_invoice ON qlbh_stock_invoice.id= qlbh_stock_invoice_detail.invoice_id
			WHERE
				'.$old_cond.'
			ORDER BY
				qlbh_stock_invoice.create_date,qlbh_stock_invoice.time
		';
		$items = DB::fetch_all($sql);
		$old_items = array();
		foreach($items as $key=>$value){
			$product_code = $value['product_code'];
			if($value['type']=='IMPORT' or $value['to_warehouse_id'] == $warehouse_id){
				if(isset($old_items[$product_code]['import_number'])){
					$old_items[$product_code]['import_number'] += $value['quantity'];
				}else{
					$old_items[$product_code]['import_number'] = $value['quantity'];
				}
			}elseif($value['type']=='EXPORT' and $value['to_warehouse_id'] != $warehouse_id){
				if(isset($old_items[$product_code]['export_number'])){
					$old_items[$product_code]['export_number'] += $value['quantity'];
				}else{
					$old_items[$product_code]['export_number'] = $value['quantity'];
				}
			}
		}
		$sql = '
			SELECT
				qlbh_inventory.id,qlbh_inventory.warehouse_id,
				qlbh_inventory.product_code,qlbh_inventory.opening_stock
			FROM
				qlbh_inventory
			WHERE	
				qlbh_inventory.product_code = \''.$row['code'].'\' AND warehouse_id='.$warehouse_id.'
		';
		if($product = DB::fetch($sql)){
			$map['start_remain'] = $product['opening_stock'];	
		}else{
			$map['start_remain'] = 0;	
		}
		$cond = 'qlbh_stock_invoice_detail.product_code = \''.$row['code'].'\' AND
			(
				(qlbh_stock_invoice.type=\'IMPORT\' AND qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.') OR 
				(qlbh_stock_invoice.type=\'EXPORT\' AND (qlbh_stock_invoice_detail.warehouse_id='.$warehouse_id.' OR qlbh_stock_invoice_detail.to_warehouse_id='.$warehouse_id.'))
			)
			'.($date_from?' AND qlbh_stock_invoice.create_date >=\''.Date_Time::to_sql_date($date_from).'\'':'').'
			'.($date_to?' AND qlbh_stock_invoice.create_date <=\''.Date_Time::to_sql_date($date_to).'\'':'').'
			
		';
		$sql = '
			SELECT
				qlbh_stock_invoice.*,
				IF(qlbh_stock_invoice.type=\'IMPORT\',qlbh_stock_invoice.bill_number,\'\') AS import_invoice_code,
				IF(qlbh_stock_invoice.type=\'EXPORT\',qlbh_stock_invoice.bill_number,\'\') AS export_invoice_code,
				qlbh_stock_invoice_detail.quantity,qlbh_stock_invoice_detail.warehouse_id,qlbh_stock_invoice_detail.to_warehouse_id
			FROM
				qlbh_stock_invoice
				INNER JOIN qlbh_stock_invoice_detail ON qlbh_stock_invoice_detail.invoice_id = qlbh_stock_invoice.id
			WHERE
				'.$cond.'
			ORDER BY
				qlbh_stock_invoice.create_date,qlbh_stock_invoice.time
		';
		$items = DB::fetch_all($sql);
		if(isset($old_items[$row['id']])){
			if(isset($old_items[$row['id']]['import_number'])){
				$map['start_remain'] += $old_items[$row['id']]['import_number'];
			}
			if(isset($old_items[$row['id']]['export_number'])){
				$map['start_remain'] -= $old_items[$row['id']]['export_number'];
			}				
		}
		$remain = $map['start_remain'];
		foreach($items as $key=>$value){
			$items[$key]['create_date'] = Date_Time::to_common_date($value['create_date']);
			if($value['type']=='IMPORT' or ($value['to_warehouse_id'] == $warehouse_id)){
				$items[$key]['import_number'] = $value['quantity'];
			}else{
				$items[$key]['import_number'] = 0;
			}
			if($value['type']=='EXPORT' and ($value['to_warehouse_id'] != $warehouse_id)){
				$items[$key]['export_number'] = $value['quantity'];
			}else{
				$items[$key]['export_number'] = 0;		
			}
			$map['end_remain'] += $items[$key]['import_number'] - $items[$key]['export_number'];
			$remain = $remain + $items[$key]['import_number'] - $items[$key]['export_number'];
			$items[$key]['remain'] = $remain;
			$map['import_total'] += $items[$key]['import_number'];
			$map['export_total'] += $items[$key]['export_number'];
		}
		$map['end_remain'] += $map['start_remain'];
	}
	return $map;
}
?>