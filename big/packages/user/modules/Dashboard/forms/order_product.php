<?php
class OrderProductForm extends Form
{
    protected $isObd;
	function __construct()
	{
		Form::Form('OrderProductForm');
		$this->isObd = isObd();
	}
	function draw()
	{
		if(!checkPermissionAccess() && !Dashboard::$xem_khoi_bc_chung){
            Url::access_denied();
        }
		$this->map = array();
		$this->map['total_qty'] = 0;
		$this->map['total_amount'] = 0;
		if(!Url::get('date_from')){
			$_REQUEST['date_from'] = date('01/m/Y');
		}
		if(!Url::get('date_to')){
			$month = date('m',Date_Time::to_time($_REQUEST['date_from']));
			$year = date('Y',Date_Time::to_time($_REQUEST['date_from']));
			$_REQUEST['date_to'] = date('d/m/Y');
		}
		$start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
		$end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
		$admin_tong = false;
		if(Session::get('user_id')=='admin' or Session::get('user_id')=='likihang' or Session::get('user_id')=='likitrung' or Session::get('user_id')=='PAL.khoand'){
			$cond = '1=1';
			$admin_tong = true;
		}else{
			$cond = 'orders.group_id='.Dashboard::$group_id;
		}
		$this->map['admin_tong'] = $admin_tong;
		if(!Url::iget('status_id')){
			$_REQUEST['status_id'] = 7;
		}
		$status_id = Url::iget('status_id')?Url::iget('status_id'):7;
		$this->map['status_name'] = DB::fetch('select id,name from statuses where id='.$status_id,'name');
		$no_revenue_status = DashboardDB::get_no_revenue_status();
		if( $status_id==7){//xn, 6 chuyen hoan
			$cond .= ' AND orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.status_id <> 9';
			$cond .= ' AND orders.status_id NOT IN ('.$no_revenue_status.')';
		}elseif( $status_id==5){// thành công
			$cond .= ' AND orders.confirmed>="'.$start_time.'" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.status_id='. $status_id.'';
		}elseif( $status_id==8 or $status_id==6){// chuyen hang
			$cond .= ' AND orders.delivered>="'.$start_time.'" and  orders.delivered<="'.$end_time.' 23:59:59" and orders.status_id='. $status_id.'';
		}else{	
			$cond .= ' AND orders.created>="'.$start_time.'" and  orders.created<="'.$end_time.' 23:59:59" and orders.status_id='. $status_id.'';
		}
		if($user_id=DB::escape(Url::get('user_id'))){
            $cond .= ' AND orders.user_confirmed='.$user_id;
        }
		$this->map['bundle_name'] = '';
		if($bundle_id = Url::iget('bundle_id') and $bundle_name = DB::fetch('select id,name from bundles where group_id = 0 and id=' . $bundle_id,'name')){
			$this->map['bundle_name'] = 'Phân loại: '.$bundle_name.'<br>';
			if ($this->isObd) {
				$bundleIds = DashboardDB::getIncludeBundleIds($bundle_id, Dashboard::$group_id);
				$cond .= " AND products.bundle_id IN ($bundleIds)";
			} else {
				$cond .= ' and products.bundle_id = '.$bundle_id;
			}
		}
		$this->map['lable_name'] = '';
		if($label_id = Url::iget('label_id') and $lable_name = DB::fetch('select id,name from labels where id='.$label_id,'name')){
			$this->map['lable_name'] = 'Nhãn: '.$lable_name.'<br>';
			$cond .= ' and products.label_id = '.$label_id;
		}
        $this->map['product_name'] = '';
        if($product_code = DB::escape(Url::get('product_code')) and $product_name = DB::fetch('select id,name from products where group_id='.Dashboard::$group_id.' and code="'.$product_code.'"','name')){
            $this->map['product_name'] = 'Mã hàng: '.$product_code.' - '.$product_name.'<br>';
            $cond .= ' and products.code = "'.$product_code.'"';
        }
        $warehouse_id = false;
		$warehouse_name = '';
		if($warehouse_id = Url::iget('warehouse_id') and $warehouse_name = DB::fetch('select id,name from qlbh_warehouse where id='.$warehouse_id,'name')){
            $warehouse_name = 'Kho: '.$warehouse_name;
            $cond .= ' and orders_products.warehouse_id = '.$warehouse_id;
        }
        $this->map['warehouse_name'] = $warehouse_name;
		$products = DashboardDB::getDataOrderProduct($cond);
		$this->map['total_all_price'] = 0;
		$this->map['total_all_discount'] = 0;
		$this->map['total_all_after_discount'] = 0;
		if(Url::get('view_report')){
			System::sksort($products, 'name');
			$i=0;
			foreach($products as $k=>$v){
				if($v['code']){
					$products[$k] = $v;
                    $products[$k]['i'] = ++$i;
                    $products[$k]['number'] = System::display_number($v['qty']);
                    $products[$k]['price'] = System::display_number($v['price']);
                    $products[$k]['discount_amount'] = System::display_number($v['discount_amount']);
                    $total_price = ($v['product_price'] - $v['discount_amount']) * $v['qty'];
                    $products[$k]['total_price'] = System::display_number($total_price);
                    $products[$k]['after_discount'] = System::display_number($v['after_discount']);
                    $this->map['total_qty'] += $v['qty'];
                    $this->map['total_all_price'] += $v['price'];
                    $this->map['total_all_discount'] += $v['discount_amount'];
                    $this->map['total_all_after_discount'] += $v['after_discount'];
                    $this->map['total_amount'] += $total_price;
				}
				
			}
		} else {
			$products = array();
		}
		
		$this->map['products'] = $products;
		$status = DashboardDB::get_statuses();
		$this->map['status_id_list'] = array(''=>'Chọn trạng')+MiString::get_list($status);
		if ($this->isObd) {
			$bundles = DashboardDB::getBundles();
		} else {
			$bundles = DashboardDB::get_bundles();
		}//end if
		$lable = DashboardDB::get_lable();
		$this->map['bundle_id_list'] = array(''=>'Tất cả phân loại')+MiString::get_list($bundles);
		$this->map['label_id_list'] = array(''=>'Tất cả nhãn')+MiString::get_list($lable);
		$this->map['has_revenue_list'] = array(''=>'Không tính doanh thu','1'=>'Theo doanh thu');
		$this->map['total_qty']  = System::display_number($this->map['total_qty']);
		$this->map['total_amount']  = System::display_number($this->map['total_amount']);
		$this->map['total_all_price']  = System::display_number($this->map['total_all_price']);
		$this->map['total_all_discount']  = System::display_number($this->map['total_all_discount']);
		$this->map['total_all_after_discount']  = System::display_number($this->map['total_all_after_discount']);
		$layout = 'order_product';

        $users = DashboardDB::get_users('GANDON',false,true);
        $this->map['user_id_list'] = array(''=>'Tất cả SALE') + MiString::get_list($users,'full_name');
        $this->map['warehouse_id_list'] = array(''=>'Tất cả kho') + MiString::get_list(DashboardDB::get_warehouses(),'name');

		$this->parse_layout($layout,$this->map);
	}

}
?>
