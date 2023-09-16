<?php
class TransportForm extends Form
{
	function __construct()
	{
		Form::Form('TransportForm');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
	}	
	function draw()
	{		
        if(!checkPermissionAccess() && !Dashboard::$xem_khoi_bc_chung){
            Url::access_denied();
        }
		$this->map = array();
        $group_id = Dashboard::$group_id;
        $account_type = Dashboard::$account_type;
		$this->map['total'] = 0;
		//////////////////////////////////////////////////
		if(!Url::get('date_from')){
			$_REQUEST['date_from'] = date('01/m/Y');
		}
		if(!Url::get('date_to')){
			$_REQUEST['date_to'] = date('d/m/Y');
		}
		$dates = array();
		
		$start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
		$end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
        $status = array(
            1000000000 =>array(
                'id' => 1000000000
                ,'name' => 'Tổng'
                ,'total' => 0
                ,'qty' => 0
                ,'shipping'=>0
                ,'order_by' => 1000000000
            ),
            2 =>array(
                'id' => 2
                ,'name' => 'Đã thu tiền'
                ,'total' => 0
                ,'qty' => 0
                ,'shipping'=>0
                ,'order_by' => 2
            ),
            5 =>array(
                'id' => 5
                ,'name' => 'Thành công'
                ,'total' => 0
                ,'qty' => 0
                ,'shipping'=>0
                ,'order_by' => 5
            ),
            1 =>array(
                'id' => 1
                ,'name' => 'Đã trả hàng về kho'
                ,'total' => 0
                ,'qty' => 0
                ,'shipping'=>0
                ,'order_by' => 1
            ),
            6 =>array(
                'id' => 6
                ,'name' => 'Chuyển hoàn'
                ,'total' => 0
                ,'qty' => 0
                ,'shipping'=>0
                ,'order_by' => 6
            ),
            8 =>array(
                'id' => 8
                ,'name' => 'Chuyển hàng'
                ,'total' => 0
                ,'qty' => 0
                ,'shipping'=>0
                ,'order_by' => 8
            )
        );
        $statusIds = array_keys($status);
        $strStatusIds = implode(',', $statusIds);
		$users = DashboardDB::get_users();
		$reports = array();
        $code = 'MARKETING';
        $userMKT = DashboardDB::getUserNew($code, Url::get('is_active'));
        $this->map['users_ids_option'] = '';
        $usersRequest = [];
        $usersRequest = Url::get('users_ids');
        $strUsersRequest = '';
        if(!empty($usersRequest)){
            $userFormat = DB::escapeArr($usersRequest);
            foreach($userMKT as $key=>$val){
                if (in_array($key,$userFormat)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['users_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
            $strUsersRequest = implode(',', $userFormat);
        } else {
            foreach($userMKT as $key=>$val){
                $selected = '';
                $this->map['users_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
        }
		if(Url::get('view_report')){
            $transport = $_REQUEST['shipping_service_id'];
            $reports['label']['total'] = 1000000000000000000000000;
            $reports['label']['id'] = 'label';
            $reports['label']['name'] = 'Nhân viên';
            $reports['label']['sl'] = 'SL';
            $userIds = [];
            foreach($userMKT as $key=>$value){
                $userIds[] = $value['user_id'];
                foreach($status as $k=>$v){
                    $reports['label'][$k][1] = array('total_price'=>'Doanh thu','qty'=>'Số Lượng','total_shipping'=>'Chi phí','name'=>$v['name']);
                }
            }
            $strUserIds = implode(',', $userIds);
            $condChuyenHang = 'orders.group_id='.$group_id.' 
                                and orders.confirmed>="'.$start_time.' 00:00:00" 
                                and orders.confirmed<="'.$end_time.' 23:59:59" 
                                and orders.user_confirmed IN ('.$strUserIds.') 
                                and orders.user_delivered>0';
            $sqlChuyenHang = 'SELECT orders.user_confirmed as id, count(orders.id) as total FROM orders WHERE '.$condChuyenHang.' GROUP BY user_confirmed';
            $resultChuyenHang = DB::fetch_all($sqlChuyenHang);
            $condStatus = '';
            if($account_type){
                $condStatus = '(orders.group_id='.$group_id.' or orders.master_group_id='.$group_id.')';
            }else{
                if($master_group_id = Dashboard::$master_group_id){
                    $condStatus = '(orders.group_id='.$group_id.' or orders.master_group_id='.$master_group_id.')';
                }else{
                    $condStatus = '(orders.group_id='.$group_id.')';
                }
            }
            if($strUsersRequest){
                $condStatus.= ' and orders.user_created IN ('.$strUsersRequest.')';
            }
            if ($transport != '') {
                $condStatus .= ' and orders.shipping_service_id='.$transport;
            }
            $condStatus .= ' and  orders.confirmed>="'.$start_time.' 00:00:00" 
                             and  orders.confirmed<="'.$end_time.' 23:59:59" 
                             and  orders.user_confirmed IN ('.$strUserIds.') 
                             and orders.status_id IN ('.$strStatusIds.')';
            $sqlStatus = 'SELECT 
                                total_price as total_price, 
                                shipping_price as shipping_price, 
                                orders.id,
                                orders.user_confirmed,
                                orders.status_id
                            FROM 
                                orders 
                            WHERE '.$condStatus.'';
            $resultStatus = DB::fetch_all($sqlStatus);
            foreach ($userMKT as $key => $value) {
                $total_ = 0;
                $i = 1;
                $total_total_price = 0;
                $total_qty = 0;
                $total_total_shipping = 0;
                $reports[$key]['id'] = $key;
                $reports[$key]['name'] = $value['full_name'].' <div class="small" style="color:#999;font-style: italic;"> '.$value['id'].' </div>';
                $reports[$key]['chuyen_hang'] = '0';
                if(isset($resultChuyenHang[$value['user_id']])){
                    $reports[$key]['chuyen_hang'] = $resultChuyenHang[$value['user_id']]['total'];
                }
                foreach($status as $k => $v){
                    $total_price = 0;
                    $total_shipping = 0;
                    $total_order = 0;
                    foreach($resultStatus as $r => $t){
                        if($k == $t['status_id'] && $t['user_confirmed'] == $value['user_id']){
                            $total_price += $t['total_price'];
                            $total_shipping += $t['shipping_price'];
                            $total_order += $i;
                            // break;
                        }

                    }
                    $total_ += $total_price;
                    $total_total_price += $total_price;
                    $total_total_shipping += $total_shipping;
                    $total_qty += $total_order;
                    $status[$k]['total'] += $total_price;
                    $status[$k]['shipping'] += $total_shipping;
                    $status[$k]['qty'] += $total_order;
                    $reports[$key][$k] = array(1=>array('total_price'=>System::display_number($total_price),'qty'=>System::display_number($total_order),'total_shipping'=>System::display_number($total_shipping),'name'=>$v['name']));
                }
                $reports[$key][1000000000] = array(1=>array('total_price'=>System::display_number($total_total_price),'total_shipping'=>System::display_number($total_total_shipping),'qty'=>System::display_number($total_qty),'name'=>'Tổng'));
                $reports[$key]['total'] = $total_total_price;
                $status[1000000000]['total'] += $total_total_price;
                $status[1000000000]['qty'] += $total_qty;
                $status[1000000000]['shipping'] += $total_total_shipping;
            }
            $i++;
            if(sizeof($reports)>2){
                System::kksort($reports, 'total');
            }
        }
		$this->map['reports'] = $reports;
		$this->map['status'] = $status;
		$months = array();
		for($i=1;$i<=12;$i++){
			$months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
		}
		$this->map['shipping_service_id_list'] = array(""=>'--Chọn--')+MiString::get_list(DashboardDB::shipping_services());
		$this->map['month_list'] = $months;
		$this->map['year_list'] = array(2016=>2016,2017=>2017);
		$this->map += DashboardDB::get_report_info();
		if($shipping_service_id = Url::get('shipping_service_id')){
			$items = DB::select_id('shipping_services',$shipping_service_id);
			$this->map['shipping_services_name'] = $items['name'];
		}
		else{
			$this->map['shipping_services_name'] = 'Tất cả';
		}
		$this->parse_layout('transport',$this->map);
	}
}
?>
