<?php
class EditQlbhStockInvoiceForm extends Form{
	function __construct(){
		Form::Form('EditQlbhStockInvoiceForm');
		$this->link_js('packages/core/includes/js/multi_items.js');	
		$this->link_css('assets/admin/css/bootstrapValidator.min.css');
		$this->link_js('assets/standard/js/autocomplete.js');
		$this->link_css('assets/standard/css/autocomplete/autocomplete.css');		
		$this->add('bill_number',new TextType(true,'miss_bill_number',0,255));
		$this->add('create_date',new DateType(true,'invalid_create_date'));
		$this->add('qlbh_stock_invoice_detail.product_code',new TextType(true,'product_id_is_required',0,255));
		$this->add('receiver_name',new TextType(false,'invalid_receiver_name',0,255));
	}
	function on_submit(){
        $master_group_id = Session::get('master_group_id');
        $group_id = Session::get('group_id');
		$product_remain = get_remain_products(Url::iget('warehouse_id'));
		$check = false;
		$data_check = array();	
		if(!Url::get('importExcelBtn')){
            try{
                if(isset($_REQUEST['mi_product']) and Url::get('type')=='EXPORT'){
                    foreach($_REQUEST['mi_product'] as $id=>$product){
                        $quantity = 0;
                        if(!isset($data_check[$product['product_code']])){
                            $data_check[$product['product_code']] = $product['quantity'];
                            $quantity = $product['quantity'];
                        }else{
                            $quantity = $data_check[$product['product_code']]+$product['quantity'];
                        }
                        if($product['product_code']==''){
                            $this->error('product_code_'.$id,'Nhập hàng hóa bán',false);
                            $check = true;
                        }
                        if(isset($product_remain[$product['product_code']]) and ($quantity>$product_remain[$product['product_code']]['remain_number'] or $product['quantity']<=0)){
                            if($product['quantity']<=0){
                                $this->error('quantity_'.$id,'sản phẩm "'.$product['name'].'" '.Portal::language('is_lager_than_0'),false);
                            }elseif($product_remain[$product['product_code']]['remain_number']==0){
                                $this->error('quantity_'.$id,'sản phẩm "'.$product['product_name'].'" '.' đã hết hàng',false);
                            }else{
                                $this->error('quantity_'.$id,'Bạn phải nhập "'.$product['name'].'" '.' nhỏ hơn '.' '.$product_remain[$product['product_code']]['remain_number'],false);
                            }
                            $check = true;
                        }
                    }
                }
            }catch (Exception $e){
                die('Có lỗi xảy ra');
            }
			if($check){
				return false;
			}
			if($this->check()){
				$type = DB::escape(Url::get('type'));
				$error = false;
				$action = 'add';$title = '';$description = '';$id = 0; // For log
				$invoice_id = 0;
				$bill_number = intval(str_replace(array('PN','PX'),'',Url::sget('bill_number')));
				if(Url::get('cmd')=='add' and DB::exists('select id,bill_number from qlbh_stock_invoice where bill_number = \''.$bill_number.'\' and type="'.$type.'" and qlbh_stock_invoice.group_id='.Session::get('group_id').'')){
					$this->error('bill_number','Trùng mã phiếu',false);
					$error = true;
				}
				if(Url::get('cmd')=='edit' and DB::exists('select id,bill_number from qlbh_stock_invoice where bill_number = \''.$bill_number.'\' and type="'.$type.'" and qlbh_stock_invoice.id<>'.Url::iget('id').' and qlbh_stock_invoice.group_id='.Session::get('group_id').'')){
					$this->error('bill_number','Trùng mã phiếu',false);
					$error = true;
				}
				if($error==false){
					$import_id = 0;
					$array = array(
							'bill_number'=>$bill_number,
							'type'=>$type,
							'deliver_name'=> DB::escape(Url::get('deliver_name')),
							'note'=>  DB::escape(Url::get('note')),
							'receiver_name'=>  DB::escape(Url::get('receiver_name')),
							'total_amount'=>Url::get('total_amount') ? str_replace(',','', DB::escape(Url::get('total_amount'))) : 0,
							'create_date'=>Date_Time::to_sql_date(Url::get('create_date')),
							'by_fb'=>Session::get('by_fb')?1:0,
							'deliver_address' => URL::sget('deliver_address'),
							'receiver_address' => URL::sget('receiver_address'),
							'original_documents_number' => URL::sget('original_documents_number'),
					);
					if(Url::get('move_product') and Url::get('type')=='EXPORT'){
						$array += array('move_product'=>1);
					}
					if(Session::get('by_fb')==1){
						$array += array('shop_id'=> DB::escape(Url::get('shop_id')));
					}
					if(Url::get('type')=='IMPORT' or Url::check('get_back_supplier')){
						$array['supplier_id']= Url::get('supplier_id')?  DB::escape(Url::get('supplier_id')):0;
					}
					$description = '
					Bill number: '.Url::get('bill_number').'<br>
					Type: '.Url::get('type').'<br>
					Create date: '.Url::get('create_date').'<br>
					Deliver name: '.Url::get('deliver_name').'<br>
					Receiver name: '.Url::get('receiver_name').'<br>
					Note: '.Url::get('note').'<br>
					Total amount: '.Url::get('total_amount').'<br>
					Warehouse: '.Url::get('warehouse_id').'<br>
					'.((Url::get('move_product') and Url::get('type')=='EXPORT')?'Move product<br>':'').'
				';
				// var_dump($array);die;
					if(Url::get('cmd')=='edit'){
						$id = Url::iget('id');
						$action = 'Edit';
						$title = 'Edit warehouse invoice '.$id.'';
						DB::update('qlbh_stock_invoice',$array+array('last_modified_user_id'=>Session::get('user_id'),'last_modified_time'=>time()),'id='.Url::iget('id'));
					}else{
                        try{
						    $id = DB::insert('qlbh_stock_invoice',$array+array('group_id'=>$group_id,'user_id'=>Session::get('user_id'),'time'=>time()));
                        }catch (Exception $e){
                            die('Có lỗi xảy ra khi tạo phiếu.');
                        }
					}
					if(URl::get('group_deleted_ids')){
						$group_deleted_ids = explode(',',URl::get('group_deleted_ids'));
						$description .= '<hr>';
						foreach($group_deleted_ids as $delete_id){
							$description .= 'Delete product id: '.$delete_id.'<br>';
							DB::delete_id('qlbh_stock_invoice_detail',$delete_id);
						}
					}
					$invoice_id = $id;
					try{
                        if(isset($_REQUEST['mi_product'])){
                            $description .= '<hr>';
                            $productCode = [];
                            foreach ($_REQUEST['mi_product'] as $k => $v) {
                                $productCode[] = DB::escape($v['product_code']);
                            }
                            $strProductCode = "('" . implode("','", $productCode) . "')";
                            $and = '';

                            if($master_group_id){
                                $and .= " AND (products.group_id = $master_group_id OR products.group_id = $group_id)";
                            } else {
                                $and .= " AND products.group_id = $group_id";
                            }
                            $and .= " AND (products.del = 0 OR products.del IS NULL)";
                            $sql = "SELECT 
                            			code as id,
                            			name, 
                            			id as product_id 
                        			FROM 
                        				products 
                    				WHERE 
                    					code IN $strProductCode ".$and;
                            $products = DB::fetch_all($sql);
                            $sqlExit = "SELECT 
                            				id as detail_id, product_code as id, 
                            				invoice_id   
                        				FROM 
                        					qlbh_stock_invoice_detail 
                    					WHERE 
                    						qlbh_stock_invoice_detail.product_code IN $strProductCode 
                						AND  invoice_id = $invoice_id";
                            $invoiceDetails = DB::fetch_all($sqlExit);
                            foreach($_REQUEST['mi_product'] as $key=>$record){
                                if(isset($record['expired_date'])){
                                    $record['expired_date'] = $record['expired_date']?Date_Time::to_sql_date($record['expired_date']):'0000-00-00 00:00:00';
                                }
                                $record['price']= $record['price'] ?  str_replace(',','',$record['price']) : 0;
                                if(isset($record['discount'])){
                                    $record['discount']=str_replace(',','',$record['discount']);
                                }
                                $record['quantity']=str_replace(',','',$record['quantity']);
                                unset($record['payment_price']);
                                // var_dump($record);die;
                                $unit_name = $record['unit'];
                                $record['unit_id'] = $record['unit_id']?$record['unit_id']:'0';
                                unset($record['unit']);
                                if(Session::get('by_fb')){
                                    if(Session::is_set('warehouse_id')){
                                        $record['warehouse_id'] = Session::get('warehouse_id');
                                    }
                                }
                                //$record['free'] = isset($record['free'])?1:0;
                                $empty = true;
                                foreach($record as $record_value){
                                    if($record_value){
                                        $empty = false;
                                    }
                                }
                                if(!$empty){
                                    $record['invoice_id'] = $invoice_id;
                                    if(Url::get('from_order_ids')){
                                        $record['id'] = '';
                                    }

                                    if(isset($products[$record['product_code']])){
                                        $record['product_id'] = $products[$record['product_code']]['product_id'];
                                    }
                                    if($record['id']){
                                        $id = $record['id'];
                                        unset($record['id']);
                                        $description .= 'Edit [Product id: '.$record['product_code'].', Price: '.$record['price'].', Quantity: '.$record['quantity'].', Unit: '.$unit_name.']<br>';
                                        if(isset($invoiceDetails[$record['product_code']])){
                                        	DB::update('qlbh_stock_invoice_detail', $record, 'id=' . DB::escape($id) . '');
                                        } else {
                                        	DB::insert('qlbh_stock_invoice_detail',$record);
                                        }
                                    }else{
                                        if(isset($record['id'])){
                                            unset($record['id']);
                                        }
                                        $description .= 'Add [Product id: '.$record['product_code'].', Price: '.$record['price'].', Quantity: '.$record['quantity'].', Unit: '.$unit_name.']<br>';
                                        DB::insert('qlbh_stock_invoice_detail',$record);
                                        if($import_id!=0)
                                        {
                                            $record['invoice_id'] = $import_id;
                                            DB::insert('qlbh_stock_invoice_detail',$record);
                                        }
                                    }
                                    /////////////////Cap nhat gia san pham//////////////////////////////////////
                                    if($record['product_code'] and Url::get('type')=='IMPORT'){
                                        //DB::update('products',array('import_price'=>$record['import_price']),'code="'.$record['product_code'].'"');
                                    }
                                }
                            }
                        }
                    }catch (Exception $e){
                        die('Có lỗi xảy ra');


                    }

				}else{
					$this->error('product_code','Kiem tra loi nhap/xuat hang hoa (Chu y so luong ton kho khi xuat hang)',false);
					return;
				}
				if(Session::get('user_id')=='dinhkkk'){
					die;
				}
				//die;
				if($error==false){
					//die;
					Url::js_redirect(true,'Cập nhật dữ liệu thành công',array('type','just_edited_id'=>$invoice_id));
				}
			}
		}
	}
	function draw(){
		$this->map = array();
		$item = QlbhStockInvoice::$item;
		$group_id = Session::get('group_id');
		$type = DB::escape(Url::get('type'));
		if($item){
			if($type=='IMPORT'){
				$item['bill_number'] = 'PN'.str_pad($item['bill_number'],2,'0',STR_PAD_LEFT);
			}
			else if($type=='EXPORT'){
				$item['bill_number'] = 'PX'.str_pad($item['bill_number'],2,'0',STR_PAD_LEFT);
			}
			$item['create_date'] = str_replace('-','/',Date_Time::to_common_date($item['create_date']));
			$item['total_amount'] = number_format($item['total_amount']);
			//$item['create_date'] = Date_Time::to_common_date($item['create_date']);
			foreach($item as $key=>$value){
				if(!isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
			if(!isset($_REQUEST['mi_product'])){
				$sql = '
					SELECT
						qlbh_stock_invoice_detail.*,
						(qlbh_stock_invoice_detail.price*qlbh_stock_invoice_detail.quantity) as payment_price,
						products.name as name,
						units.name as unit
					FROM
						qlbh_stock_invoice_detail
						INNER JOIN products ON products.code = qlbh_stock_invoice_detail.product_code
						left join units ON units.id = products.unit_id
					WHERE
						qlbh_stock_invoice_detail.invoice_id=\''.$item['id'].'\'
				';
				$mi_product = DB::fetch_all($sql);
				foreach($mi_product as $k=>$v){
					$mi_product[$k]['price'] = System::display_number($v['price']);
					$mi_product[$k]['expired_date'] = Date_Time::to_common_date($v['expired_date']);
					$mi_product[$k]['number'] = $v['quantity'];
					$mi_product[$k]['payment_price'] = System::display_number($v['payment_price']);
				}
				$_REQUEST['mi_product'] = $mi_product;
			} 
		}else{
			if(!Url::get('create_date')){
				$_REQUEST['create_date'] = date('d/m/Y',time());
			}
			if(!Url::get('bill_number')){
				$lastest_item = DB::fetch('SELECT id,bill_number,receiver_name FROM qlbh_stock_invoice where type=\''.$type.'\'  and qlbh_stock_invoice.group_id='.$group_id.' ORDER BY id DESC');
				//System::debug($lastest_item);
				if($type=='IMPORT'){
					$total = intval(str_replace('PN','',$lastest_item['bill_number']))+1;
					$total = (strlen($total)<2)?'0'.$total:$total;
					$_REQUEST['bill_number'] = 'PN'.$total;
				}
				else if($type=='EXPORT'){
					$total = intval(str_replace('PX','',$lastest_item['bill_number']))+1;
					$total = (strlen($total)<2)?'0'.$total:$total;					
					$_REQUEST['bill_number'] = 'PX'.$total;
				}
                $_REQUEST['receiver_name'] = $lastest_item['receiver_name'];
			}
			$mi_product = array();
		}
		if(Url::get('importExcelBtn') and $excel_file = $_FILES['excel_file'] and $temp_file = $excel_file['tmp_name']){
			$arr = read_excel($temp_file);
			foreach($arr as $key=>$val){
				if($key>1){
					$record['code'] = $val[1];
					$record['name'] = $val[2];
					$record['price'] = $val[3];
					$record['color'] = $val[4];
					$record['size'] = $val[5];
					$record['bundle_id'] = DB::fetch('select id from bundles where name LIKE "'.DB::escape($val[6]).'" and group_id="'.$group_id.'"','id');
					$record['unit_id'] = DB::fetch('select id from units where name LIKE "'.DB::escape($val[7]).'" and group_id="'.$group_id.'"','id');
					$record['group_id'] = $group_id;
					if(!DB::exists('select id from products where code="'.DB::escape($record['code']).'" and group_id="'.$group_id.'"')){
						unset($record['quantity']);
						unset($record['product_code']);
						unset($record['product_name']);
						unset($record['payment_price']);
						unset($record['unit']);
						DB::insert('products',$record);
					}
					$record['quantity'] = DB::escape($val[8]);
					$record['product_code'] = $record['code'];
					$record['product_name'] = $record['name'];
					$record['payment_price'] = System::display_number($record['price']*$record['quantity']);
					$record['price'] = System::display_number($record['price']);
					$record['unit'] = DB::escape($val[7]);
					$mi_product[$record['code']] = $record;
				}
			}
			$_REQUEST['mi_product'] = $mi_product;
		}
		if(!isset($_REQUEST['deliver_name'])){
			//$_REQUEST['deliver_name'] = $_SESSION['user_data']['full_name'];
		}
		if(!isset($_REQUEST['receiver_name'])){
//			$_REQUEST['receiver_name'] = '123';
		}
		$this->map['supplier_id_list'] = array(''=>'Chưa có nhà cung cấp')+MiString::get_list(DB::fetch_all('SELECT id,code,CONCAT(code,CONCAT(\' - \',name)) AS name FROM qlbh_supplier where qlbh_supplier.group_id='.$group_id.' ORDER BY code'));
		$this->map['shop_id_list'] = array(''=>'Chọn')+MiString::get_list(QlbhStockInvoiceDB::get_shop(Session::get('user_id')));
		$warehouses = DB::select_all('qlbh_warehouse','structure_id<>'.ID_ROOT.' and group_id='.$group_id.'','structure_id');
		$this->map['warehouse_options'] = '<option value="1">Kho tổng</option>';
		foreach($warehouses as $value){
			$this->map['warehouse_options'] .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
		}
		$this->map['warehouse_id_list'] = array(''=>Portal::language('select'))+MiString::get_list($warehouses);
		$this->map['title'] = (Url::get('cmd')=='add')?(($type=='IMPORT')?'Nhập kho':'Xuất kho'):(($type=='IMPORT')?'Sửa phiếu nhập':'Sửa phiếu xuất');
		$sql = '
          SELECT 
            products.code as id,products.name,
            '.(($type=='EXPORT')?'products.price':'products.import_price').' AS price,
            units.id as unit_id,units.name as unit 
          FROM 
            products 
            LEFT JOIN units ON units.id = products.unit_id 
          where 
            products.group_id='.$group_id.' AND (products.del = 0 OR products.del IS NULL)';
		$this->map['products'] = DB::fetch_all($sql);
		$layout = 'edit';
		//System::Debug($_SESSION);exit();
		//get_product_inventory('SP01',date('d/m/Y'),date('d/m/Y'),Session::get('warehouse_id'));
		$this->parse_layout($layout,$this->map);
	}
}