<?php
class ReportForm extends Form
{
	function __construct()
	{
		Form::Form('ReportForm');
	}	
	function draw()
	{		
		$zoma_modal = false;
		$account_type = Session::get('account_type');
		if(Session::get('master_group_id') or $account_type==3){
			$zoma_modal = true;
		}
		$this->map = array();
		$this->map['total'] = 0;
		//////////////////////////////////////////////////
		if(!Url::get('date_from')){
			$_REQUEST['date_from'] = date('01/m/Y');
		}
		if(!Url::get('date_to')){
			$month = date('m',Date_Time::to_time($_REQUEST['date_from']));
			$year = date('Y',Date_Time::to_time($_REQUEST['date_from']));
			$_REQUEST['date_to'] = date('d/m/Y');//date(''.Date_Time::get_last_day_of_month($month,$year)
		}
		$dates = array();
		$group_id = Session::get('group_id');
		$account_type = Session::get('account_type');
		$start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
		$end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
		$status = DashboardDB::get_report_statuses();
		$items = DashboardDB::get_groups();
		$reports = array();
		if(Url::get('view_report')){
			$reports['label']['id'] = 'label';
			$reports['label']['name'] = 'SHOP';
			$reports['label']['sl'] = 'SL';
			$status[1000000000] = array('id'=>1000000000,'name'=>'Tổng','total'=>0,'qty'=>0);
			foreach($items as $key=>$value){
				foreach($status as $k=>$v){
					$reports['label'][$k][1] = array('total_price'=>'Doanh thu','qty'=>'Số Lượng','name'=>$v['name']);
				}
			}
			$reports['label']['total'] = 1000000000000000000000000;
			// 7: đã xác nhận,9: hủy, 6: chuyển hoàn, 5 thành công
			$no_revenue_status = DashboardDB::get_no_revenue_status();
			foreach($items as $key=>$value){
				$reports[$key]['id'] = $key;
				$reports[$key]['name'] = $value['name'];
				$total_ = 0;
				$total_total_price = 0;
				$total_qty = 0;
				foreach($status as $k=>$v){
					if($account_type){
						$cond = '(groups.id='.$group_id.' or groups.master_group_id='.$group_id.')';
					}else{
						if($master_group_id = Session::get('master_group_id')){
							$cond = '(groups.id='.$group_id.' or groups.master_group_id='.$master_group_id.')';
						}else{
							$cond = '(groups.id='.$group_id.')';
						}
					}
					if($k!=9 and $no_revenue_status){
						$cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
					}
					if($type=Url::iget('type')){
						$cond .= ' AND orders.type='.$type.'';
					}
					$join_orders_extra = '';
					if($k==XAC_NHAN){//xn
						$cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and users.group_id='.$value['id'].' and orders.status_id <> 9';
                    }elseif($k==KE_TOAN){// đóng hàng
                        $cond .= ' and orders_extra.accounting_confirmed>="'.$start_time.' 00:00:00" and  orders_extra.accounting_confirmed<="'.$end_time.' 23:59:59" and u2.group_id='.$value['id'].'';
                        $join_orders_extra = '
                            inner join orders_extra on orders_extra.order_id = orders.id
                            inner join users as u2 on u2.id = orders_extra.accounting_user_confirmed
                        ';
					}else{
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and users.group_id='.$value['id'].'  and orders.status_id = '.$k;
                    }
					$sql1 = '
						select 
							sum(total_price) as total 
						from 
							orders 
							inner join users on users.id = orders.user_confirmed
							inner join  `groups` on groups.id = users.group_id
							'.$join_orders_extra.'
						where 
							'.$cond.'
						group by 
							groups.id
					';
					$sql2 = '
						select 
							count(*) as total 
						from 
							orders 
							inner join users on users.id = orders.user_confirmed
							inner join  `groups` on groups.id = users.group_id
							'.$join_orders_extra.'
						where 
							'.$cond.'
						group by 
							groups.id
						';
					$total_price = DB::fetch($sql1,'total');
					$total_order = DB::fetch($sql2,'total');
					$reports[$key][$k] = array(1=>array('total_price'=>System::display_number($total_price),'qty'=>System::display_number($total_order),'name'=>$v['name']));
					if($k==7 or $k==9){
						$total_total_price += $total_price;
						$total_qty += $total_order;
					}
					$total_ += $total_price;
					$status[$k]['total'] += $total_price;
					$status[$k]['qty'] += $total_order;
				}
				$status[1000000000]['total'] += $total_total_price;
				$status[1000000000]['qty'] += 0;
				$reports[$key][1000000000] = array(1=>array('total_price'=>System::display_number($total_total_price),'qty'=>System::display_number($total_qty),'name'=>'Tổng'));
				//$status[1000000000]
				$reports[$key]['total'] = $total_;
			}
		}
		///////////////////////////////////////////
		//System::Debug($reports);
		if(sizeof($reports)>2){
			//$this->sort_array_of_array();
			System::sksort($reports, 'total','DESC');
			
		}
		$this->map['reports'] = $reports;
		$this->map['status'] = $status;
		$months = array();
		for($i=1;$i<=12;$i++){
			$months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
		}
		$this->map['month_list'] = $months;
		$this->map['year_list'] = array(2018=>2018,2019=>2019);
		$this->map['type_list'] = array(''=>'Tất cả đơn',1=>'SALE',2=>'CSKH','3'=>'Đặt lại');
		$this->map['is_active_list'] = array(''=>'Tài khoản kích hoạt',1=>'Tài khoản chưa kích hoạt',2=>'Tất cả',3=>'Tất cả 1');
		$this->map['account_group_id_list'] = array(''=>'Xem theo nhóm tài khoản',''=>'Tất cả các nhóm tài khoản') + MiString::get_list(DashboardDB::get_account_groups());
		$this->map += DashboardDB::get_report_info();
		$this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_groups());
		$this->parse_layout('doanh_thu_tong',$this->map);
	}
}
?>
