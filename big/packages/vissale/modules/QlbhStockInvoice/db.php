<?php
class QlbhStockInvoiceDB{
	static function get_shop($accout_id){
		return DB::fetch_all('SELECT id,CONCAT(name,CONCAT(\' - \',address)) AS name FROM qlbh_shop where account_id="'.$accout_id.'" ORDER BY name');
	}
	static function get_order_info(){
		$party = DB::fetch('select full_name,note1,note2,address,phone,website from party WHERE user_id="'.Session::get('user_id').'" ');
		return $party;
	}
    static function nhap_kho($ids,$warehouse_id=false, $group_id = false){
        if (!$group_id) {
            $group_id = Session::get('group_id');
        }
        $auto = false;
        if($warehouse_id==false){
            $auto = true;
        }
        $warehouse_id = get_default_warehouse($group_id);
        $invoice_id = false;
        if($ids and $warehouse_id){
            $idsArr = explode(',',$ids);
            $total_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $str_ids = implode(',', $total_ids);

            // $totalIds = sizeof($total_ids);
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
						orders_products.order_id IN ('.$str_ids.')
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
                if($auto == true){
                    $data ='Đã tạo phiếu nhập kho tự động. Mã phiếu '.'PN'.$bill_number;
                }

                AdminOrdersDB::update_revision($orderId,false,false,$data);
            }
            return $invoice_id;
        }else{
            return false;
        }
    }
    static function xuat_kho($ids,$warehouse_id=false,$group_id=false,$receiver_name=false){
        $auto = false;
        if($warehouse_id==false){
            $auto = true;
        }
        $group_id = $group_id?$group_id:Session::get('group_id');
        $master_group_id = Session::get('master_group_id')?Session::get('master_group_id'):false;
        $default_warehouse_id = DB::fetch('SELECT id FROM `qlbh_warehouse` WHERE `qlbh_warehouse`.`is_default` = 1 and group_id='.$group_id,'id');
        $default_warehouse_id = $default_warehouse_id?$default_warehouse_id:1;
        $warehouse_id = $warehouse_id?$warehouse_id:$default_warehouse_id;
        $invoice_id = false;
        $user_data = Session::get('user_data');
        $receiver_name = $receiver_name?$receiver_name:'...';
        
        if($ids and $warehouse_id){
            $warehouse = DB::fetch('SELECT id,name FROM qlbh_warehouse WHERE id = '.$warehouse_id);
            if($warehouse){
                $name = $warehouse['name'];
            } else {
                $name = '';
            }
            $idsArr = explode(',',$ids);
            $escape_ids = array_map(function($id){
                return DB::escape($id);
            }, $idsArr);

            $ids = implode(',', $escape_ids);
            
            $total_ids = explode(',',$ids);
            $totalIds = sizeof($total_ids);
            $str_ids = implode(', ', $total_ids);
            $lastest_item = DB::fetch('SELECT id,bill_number FROM qlbh_stock_invoice where type="EXPORT"  and qlbh_stock_invoice.group_id='.$group_id.' ORDER BY bill_number DESC');
            $bill_number = $lastest_item['bill_number'] + 1;
            $total_amount = '0';
            $array = array(
                'bill_number'=>$bill_number,
                'type'=>'EXPORT',
                'deliver_name'=>$user_data['full_name'],
                'receiver_name'=>$receiver_name,
                'total_amount'=>$total_amount,
                'create_date'=>date('Y-m-d'),
                'order_id'=>$str_ids,
                'note'=>$str_ids?'Từ '.$totalIds.' đơn hàng '.(($totalIds>=1)?' (Mã: '.$str_ids.')':''): DB::escape(Url::get('note'))
            );
            $sql = '
					SELECT
						orders_products.id,
						products.code as product_code,
						products.name as product_name,
						orders_products.product_price as price,
						orders_products.qty as quantity,
						orders_products.product_price*orders_products.qty as payment_price,
						units.id as unit_id,
						products.id as product_id,
						orders_products.warehouse_id
					FROM
						orders_products
						JOIN products ON products.id = orders_products.product_id
						LEFT JOIN units ON units.id = products.unit_id
					WHERE
						orders_products.order_id IN ('.$ids.')
					';
            $products = DB::fetch_all($sql);
            if(sizeof($products)>0){
                $invoice_id = DB::insert('qlbh_stock_invoice',$array+array('group_id'=>$group_id,'user_id'=>Session::get('user_id'),'time'=>time()));
                foreach($products as $key=>$record){
                    $record['unit_id'] = $record['unit_id']?$record['unit_id']:'0';
                    $record['price']=$record['price']?str_replace(',','',$record['price']):'0';
                    $record['payment_price'] = isset($record['payment_price'])?$record['payment_price']:0;
                    $record['warehouse_id'] = $record['warehouse_id']?$record['warehouse_id']:$warehouse_id;
                    $payment_price = System::calculate_number($record['payment_price']);
                    $total_amount += $payment_price;
                    $record['quantity']=$record['quantity']?str_replace(',','',$record['quantity']):'0';
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
                        if($product=DB::fetch('SELECT id FROM products WHERE code=\''.$record['product_code'].'\' AND '.($master_group_id?' (products.group_id = '.$master_group_id.' or products.group_id = '.$group_id.') ':'products.group_id = '.$group_id.''))){
                            if(isset($record['id'])){
                                unset($record['id']);
                            }
                            $record['product_id'] = $product['id'];
                            DB::insert('qlbh_stock_invoice_detail',$record);
                        }
                    }
                }
                DB::update('qlbh_stock_invoice',array('total_amount'=>$total_amount),'id='.$invoice_id);
                //} end IF
            }
            require_once('packages/vissale/modules/AdminOrders/db.php');
            foreach ($total_ids as $orderId) {
                if($auto == true){
                    $data ='Đã tạo phiếu xuất kho tự động. Mã phiếu '.'PX'.$bill_number;
                } else {
                    $data ='Đã tạo phiếu xuất kho cho đơn hàng tại Kho '.$name.'. Mã phiếu '.'PX'.$bill_number;
                }

                AdminOrdersDB::update_revision($orderId,false,false,$data);
            }
            return $invoice_id;
        }else{
            return false;
        }
    }
}