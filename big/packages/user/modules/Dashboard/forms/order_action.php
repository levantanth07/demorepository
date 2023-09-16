<?php
class ReportForm extends Form
{
	function __construct()
	{
		Form::Form('ReportForm');
	}	
	function draw()
	{		
		if(!checkPermissionAccess(['CHIADON']) && !Dashboard::$xem_khoi_bc_truc_page){
            Url::access_denied();
        }
		$this->map = array();
		$this->map['total'] = 0;
		//////////////////////////////////////////////////
		if(!Url::get('date_from')){
			$_REQUEST['date_from'] = date('01/m/Y');
		}
		if(!Url::get('date_to')){
			$_REQUEST['date_to'] = date('d/m/Y');//Date_Time::get_last_day_of_month($month,$year)
		}
		$start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
		$end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
		$status = DashboardDB::get_statuses_by_revision();
		$users = DashboardDB::getUserNew('GANDON',false);
		$reports = array();
		if(Url::get('view_report')){
			$reports['label']['id'] = 'label';
			$reports['label']['username'] = 'Tên tài khoản';
			$reports['label']['name'] = 'Nhân viên';
			foreach($users as $key=>$value){
				foreach($status as $k=>$v){
					$reports['label'][$k] = $v['name'];
				}
			}
			$reports['label']['total'] = 1000000000000000000000000;
			$no_revenue_status = DashboardDB::get_no_revenue_status();
			foreach($users as $key=>$value){
				$reports[$key]['id'] = $key;
				$reports[$key]['username'] = $value['username'];
				$reports[$key]['name'] = $value['full_name'];
				$total_ = 0;
				$total_qty = 0;
                $cond = 'orders.group_id='.Dashboard::$group_id.'';
                $cond .= ' 
                        and order_revisions.order_status_id
                        and order_revisions.created>="'.$start_time.' 00:00:00" 
                        and order_revisions.created<="'.$end_time.' 23:59:59" 
                        and order_revisions.user_created='.$value['user_id'].'';

                $sql = '
                      select
                        order_revisions.order_id as id,
                        orders.status_id
                      from 
                        order_revisions
                        join orders on orders.id = order_revisions.order_id
                      where
                        '.$cond.'
                      GROUP BY
                        order_revisions.order_id
                      order by
                        order_revisions.id DESC
                      ';
                $orders = DB::fetch_all($sql);
				foreach($status as $k=>$v){
                    $total_order = 0;
                    foreach($orders as $k_=>$v_){
                        if(isset($v_['status_id']) and $v_['status_id']==$k){
                            $total_order++;
                        }
                    }
                    if(isset($orders[$k])){
                        $total_order ++;
                    }
					$reports[$key][$k] = System::display_number($total_order);
					{
						$total_qty += $total_order;
					}
					$total_ += $total_order;
					$status[$k]['total'] += $total_order;
					$status[$k]['qty'] += $total_order;
					////
				}
				$this->map['total'] += $total_;
				$reports[$key]['total'] = $total_;
			}
		}
		$this->map['total'] = System::display_number($this->map['total']);
		///////////////////////////////////////////
		if(sizeof($reports)>2){
			System::sksort($reports, 'total','DESC');
		}
		$this->map['reports'] = $reports;
		$this->map['status'] = $status;
		$months = array();
		for($i=1;$i<=12;$i++){
			$months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
		}
		$this->map['month_list'] = $months;
		$this->map += DashboardDB::get_report_info();
		$this->parse_layout('order_action',$this->map);
	}
}
?>
