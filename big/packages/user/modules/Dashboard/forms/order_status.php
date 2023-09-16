<?php
class ReportForm extends Form
{
    protected $map;
	function __construct()
	{
		Form::Form('ReportForm');
	}	
	function draw()
	{
	    return; // Tam an Bao cao thay doi trang thai
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
		$master_group_id = Dashboard::$master_group_id;
		$paging = '';
		if(Url::get('view_report')){
			if(Session::get('account_type')==3){//khoand edited in 30/09/2018
				$cond = ' (orders.group_id='.$group_id.' or orders.master_group_id = '.$group_id.')';
			}elseif($master_group_id){
				$cond = ' (orders.group_id='.$group_id.' or (orders.master_group_id = '.$master_group_id.' and UA.group_id = '.$group_id.'))';
			}else{
				$cond = ' orders.group_id='.$group_id.'';
			}
			$cond .= ' and orders.created>="'.$start_time.'" and  orders.created<="'.$end_time.' 23:59:59"';
			if($order_id = Url::iget('order_id')){
                $cond .= ' and orders.id = '.$order_id;
            }
			require_once('packages/vissale/modules/AdminOrders/db.php');
            require_once 'packages/core/includes/utils/paging.php';
            $item_per_page = 100;
            $total = DashboardDB::get_total_order($cond);
            $paging = paging($total, $item_per_page,10,false,'page_no',
                array('view_report','do','order_id')
            );
			$reports = DashboardDB::get_orders($cond,'orders.id DESC',$item_per_page);
			if(count($reports) > 0){
                $length = get_group_options('hide_phone_number');
                foreach($reports as $keyReports => $rowReports){
                    $reports[$keyReports]['mobile'] = ModifyPhoneNumber::hidePhoneNumber($rowReports['mobile'], $length);
                    $reports[$keyReports]['mobile2'] = ModifyPhoneNumber::hidePhoneNumber($rowReports['mobile2'], $length);
                }
            }
		}
        $this->map['paging'] = $paging;
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
		$this->parse_layout('order_status',$this->map);
	}
}
?>