<?php
class ImportMaBuuDienDB{
	static function get_total_item($cond){
		return DB::fetch(
			'select
				count(distinct orders.id) as acount
			from
				orders
			  	LEFT OUTER JOIN users ON users.id = orders.user_assigned
				LEFT OUTER JOIN party ON party.user_id = users.username
				LEFT OUTER JOIN statuses ON statuses.id = orders.status_id
				LEFT OUTER JOIN shipping_services ON shipping_services.id = orders.shipping_service_id
			where
				'.$cond.'
				
				'
			,'acount');
	}
	static function get_items($cond,$order_by,$item_per_page){
		$items = DB::fetch_all('
			SELECT
				orders.*,statuses.name as status_name,shipping_services.name as shipping_service,
				users.username as user_assigned,party.label,cu.username as user_confirmed
			FROM
				orders
				LEFT OUTER JOIN users ON users.id = orders.user_assigned
				LEFT OUTER JOIN users as cu ON cu.id = orders.user_confirmed
				LEFT OUTER JOIN party ON party.user_id = users.username
				LEFT OUTER JOIN statuses ON statuses.id = orders.status_id
				LEFT OUTER JOIN shipping_services ON shipping_services.id = orders.shipping_service_id
			WHERE
				'.$cond.'
			GROUP BY
				orders.id
			ORDER BY
				'.$order_by.'
			LIMIT
				'.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		$i=1;
		foreach($items as $key =>$value){
			$index = $i + $item_per_page*(page_no()-1);
			$items[$key]['index'] = $index;
			$items[$key]['products'] = '123';
			$i++;
		}
		return ($items);
	}
	static function get_users(){
		return DB::fetch_all('
			SELECT
				account.id,party.full_name,account.group_id
			FROM
				account
				INNER JOIN party ON party.user_id = account.id
			WHERE
				account.group_id = '.Session::get('group_id').'
		');
	}
	static function get_category($order_id=false){
		$categories =  DB::fetch_all('
			SELECT
				category.id
				,category.name_'.Portal::language().' as name
				,category.structure_id
				'.($order_id?',product_category.product_id':'').'
			FROM
				category
				'.($order_id?'LEFT OUTER JOIN product_category ON product_category.category_id = category.id AND product_category.product_id='.$order_id.'':'').'
			WHERE
				category.type="PRODUCT"
				and category.structure_id <> '.ID_ROOT.'
			ORDER BY
				category.structure_id
		');
		return $categories;
	}
	static function get_categories($order_id){
		$str = '';
		$sql = 'select category.id,category.name_'.Portal::language().' as name from category inner join product_category as pc on pc.category_id=category.id where pc.product_id='.$order_id.'';
		$items = DB::fetch_all($sql);
		foreach($items as $key=>$value){
			$str .= ($str?',':'').$value['name'];
		}
		return $str;
	}
	static function get_status(){
		$sql = 'SELECT * FROM `statuses` WHERE group_id='.Session::get('group_id').' OR is_system=1';
		return DB::fetch_all($sql);
	}

	static function update_order_product($order_id){
		$order_id = DB::escape($order_id);
		if(isset($_REQUEST['mi_order_product'])){
			foreach($_REQUEST['mi_order_product'] as $key=>$record){
				if($record['id']=='(auto)'){
					$record['id']=false;
				}
				$record['order_id'] = $order_id;
				$record['product_price'] = System::calculate_number($record['product_price']);
				unset($record['color']);
				unset($record['size']);
				unset($record['total']);
				if($record['id'] and DB::exists_id('orders_products',$record['id'])){
					DB::update('orders_products',$record,'id='.$record['id']);
				}else{
					unset($record['id']);
					$record['id'] = DB::insert('orders_products',$record);
				}
				/////
				//$this->save_item_image('anh_dai_dien_'.$key,$record['id']);
			}
			if (isset($ids) and sizeof($ids)){
				$_REQUEST['selected_ids'].=','.join(',',$ids);
			}
			if(URL::get('deleted_ids')){
				$ids = explode(',',URL::get('deleted_ids'));
				foreach($ids as $id){
					DB::delete_id('orders_products',DB::escape($id));
				}
			}
		}
	}
	static function get_products($cond='1=1'){
		$sql = '
				select
					products.id,products.name,products.color,products.price
				from
					products
				WHERE
					'.$cond.'
					AND products.group_id='.Session::get('group_id').'
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
	static function get_order_product($order_id){
		{
			$order_id = DB::escape($order_id);
			$sql = '
				select
					orders_products.id,orders_products.order_id,
					orders_products.product_id,
					orders_products.product_price,orders_products.qty,
					products.name,products.color,products.size
				from
					orders_products
					INNER JOIN products ON products.id = orders_products.product_id
				WHERE
					orders_products.order_id='.$order_id.'
				order by
					orders_products.product_price DESC
				LIMIT
					0,1000
			';
			$order_product = DB::fetch_all($sql);
			foreach($order_product as $key=>$value){
				$order_product[$key]['product_price'] = System::display_number($value['product_price']);
				$order_product[$key]['total'] = System::display_number($value['product_price']*$value['qty']);
			}
			return $order_product;
		}
	}

	static function get_bundles(){
		$sql = '
				select
					bundles.id,bundles.name
				from
					bundles
				WHERE
					bundles.group_id='.Session::get('group_id').'
				order by
					bundles.name
			';
		$bundles = DB::fetch_all($sql);
		return $bundles;
	}
	static function update_revision($order_id,$old_status_id,$new_status_id){
		if($old_status_id and $new_status_id and $old_status_id != $new_status_id){
			$before_order_status = DB::fetch('select id,name from statuses where id='.$old_status_id.'','name');
			$order_status = DB::fetch('select id,name from statuses where id='.$new_status_id.'','name');
			DB::insert('order_revisions',array(
					'order_id'=>$order_id,
					'before_order_status_id'=>$old_status_id,
					'before_order_status'=>$before_order_status,
					'order_status_id'=>$new_status_id,
					'order_status'=>$order_status,
					'user_created_name'=>Session::get('user_id'),
					'created'=>date('Y-m-d H:i:s')
			));
		}
	}
	static function get_order_revisions(){
		$sql = '
				select
					order_revisions.*
				from
					order_revisions
				WHERE
					order_revisions.order_id='.Url::iget('id').'
				order by
					order_revisions.id desc
			';
		$items = DB::fetch_all($sql);
		return $items;
	}
	static function shipping_services(){
		$sql = '
				select
					shipping_services.*
				from
					shipping_services
				WHERE
					shipping_services.group_id = '.Session::get('group_id').'
				order by
					shipping_services.name
			';
		$items = DB::fetch_all($sql);
		return $items;
	}
	static function get_order_info(){
		$party = DB::fetch('select full_name,note1,note2,full_name,address,phone,website from party WHERE user_id="'.Session::get('user_id').'" ');
		return $party;
	}
	static function nhap_kho($ids,$warehouse_id=false, $group_id = false){
        if (!$group_id) {
            $group_id = Session::get('group_id');
        }
		$idsArr = explode(',',$ids);
		$escape_ids = array_map(function($id){
			return DB::escape($id);
		}, $idsArr);

		$ids = implode(',', $escape_ids);
        $warehouse_id = get_default_warehouse($group_id);
        $invoice_id = false;
        if($ids and $warehouse_id){
            $total_ids = explode(',',$ids);
            $totalIds = sizeof($total_ids);
            $str_ids = implode(', ', $total_ids);
            $lastest_item = DB::fetch('SELECT id,bill_number FROM qlbh_stock_invoice where type="IMPORT"  and qlbh_stock_invoice.group_id='.$group_id.' ORDER BY bill_number DESC');
            $bill_number = $lastest_item['bill_number'] + 1;
            $total_amount = 0;
            $array = array(
                'bill_number'=>$bill_number,
                'type'=>'IMPORT',
                'deliver_name'=>'',
                'note'=>Url::get('note'),
                'receiver_name'=>'',
                'total_amount'=>$total_amount,
                'create_date'=>date('Y-m-d'),
                'order_id'=>$str_ids,
                'note'=>'Từ đơn hàng đã trả hàng về kho (Mã: ' .$str_ids.')'
            );
            $sql = '
					SELECT
						orders_products.id,products.code as product_code,products.name as product_name,
						orders_products.product_price as price,
						SUM(orders_products.qty) as quantity,
						orders_products.product_price*SUM(orders_products.qty) as payment_price,
						units.id as unit_id,
						products.id as product_id
					FROM
						orders_products
						INNER JOIN products ON products.id = orders_products.product_id
						LEFT JOIN units ON units.id = products.unit_id
					WHERE
						orders_products.order_id IN ('.$ids.')
					GROUP BY
						products.id
					';
            $products = DB::fetch_all($sql);
            if(sizeof($products)>0){
                $invoice_id = DB::insert('qlbh_stock_invoice',$array+array('group_id'=>$group_id,'user_id'=>Session::get('user_id'),'time'=>time()));
                foreach($products as $key=>$record){
                    $record['unit_id'] = $record['unit_id']?$record['unit_id']:0;
                    $record['price']=$record['price']?str_replace(',','',$record['price']):0;
                    $record['warehouse_id'] = $warehouse_id;
                    $payment_price = str_replace(',','',$record['payment_price']);
                    $total_amount += $payment_price;
                    $record['quantity']=$record['quantity']?str_replace(',','',$record['quantity']):0;
                    unset($record['payment_price']);
                    unset($record['unit']);
                    unset($record['id']);
                    $empty = true;
                    foreach($record as $record_value){
                        if($record_value){
                            $empty = false;
                        }
                    }
                    if(!$empty){
                        $record['invoice_id'] = $invoice_id;
                        if(DB::exists('SELECT id FROM products WHERE code=\''.$record['product_code'].'\' AND '.(Session::get('master_group_id')?' (products.group_id = '.Session::get('master_group_id').' or products.group_id = '.$group_id.') ':'products.group_id = '.$group_id.'').'')){
                            if(isset($record['id'])){
                                unset($record['id']);
                            }
                            DB::insert('qlbh_stock_invoice_detail',$record);
                        }
                    }
                }
                DB::update('qlbh_stock_invoice',array('total_amount'=>$total_amount),'id='.$invoice_id);
            }
            require_once('packages/vissale/modules/AdminOrders/db.php');
            foreach ($total_ids as $orderId) {
                $data = 'Import mã bưu điện: Tạo phiếu nhập kho tự động. Mã phiếu '.'PN'.$bill_number;
                AdminOrdersDB::update_revision($orderId,false,false,$data);
            }
            return $invoice_id;
        }else{
            return false;
        }
    }
}
?>
