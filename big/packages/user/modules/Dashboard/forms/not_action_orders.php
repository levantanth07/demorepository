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
		$status = DashboardDB::get_statuses();
		$reports = array();
		$group_id = Dashboard::$group_id;
		$master_group_id = Session::get('master_group_id');
		if(Url::get('view_report')){
			if(Dashboard::$account_type==3){//khoand edited in 30/09/2018
				$cond = ' (orders.group_id='.$group_id.' or orders.master_group_id = '.$group_id.')';
			}elseif($master_group_id){
				$cond = ' (orders.group_id='.$group_id.' or (orders.master_group_id = '.$master_group_id.' and UA.group_id = '.$group_id.'))';
			}else{
				$cond = ' orders.group_id='.$group_id.'';
			}
			$cond .= ' and orders.status_id = 10';
			$cond .= ' and orders.created>="'.$start_time.'" and  orders.created<="'.$end_time.' 23:59:59"';
			require_once('packages/vissale/modules/AdminOrders/db.php');
			$reports = DashboardDB::get_orders($cond,'orders.id DESC',false);
			if(count($reports) > 0){
			    $length = get_group_options('hide_phone_number');
                foreach($reports as $key => $row){
                    $reports[$key]['mobile'] = ModifyPhoneNumber::hidePhoneNumber($row['mobile'], $length);
                }
            }
		}
		$this->map['total'] = System::display_number(sizeof($reports));
		///////////////////////////////////////////
		
		$this->map['reports'] = $reports;
		$this->map['status'] = $status;
		$months = array();
		for($i=1;$i<=12;$i++){
			$months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
		}
		$this->map['month_list'] = $months;
		$this->map += DashboardDB::get_report_info();
		$this->parse_layout('not_action_orders',$this->map);
	}
}
?>
