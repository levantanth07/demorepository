<?php
class PieForm extends Form{
	function __construct(){
		Form::Form('PieForm');
	}
	function draw(){
		if(!checkPermissionAccess() && !Dashboard::$xem_khoi_bc_chung){
            Url::access_denied();
        }
		$this->map = array();
		$months = array(
			 '01'=>'Tháng 01'
			,'02'=>'Tháng 02'
			,'03'=>'Tháng 03'
			,'04'=>'Tháng 04'
			,'05'=>'Tháng 05'
			,'06'=>'Tháng 06'
			,'07'=>'Tháng 07'
			,'08'=>'Tháng 08'
			,'09'=>'Tháng 09'
			,'10'=>'Tháng 10'
			,'11'=>'Tháng 11'
			,'12'=>'Tháng 12'
			);
		$this->map['month_list'] = array(''=>'Chọn tháng') + $months;
		$years = array();
		for($i=2017;$i<(date('Y')+10);$i++){
			$years[$i] = $i;
		}
		$this->map['year_list'] = $years;
		if(!Url::get('month')){
			$_REQUEST['month'] = date('m');
		}
		$this->map['month'] = Url::get('month');
		if(!Url::get('year')) {
			$_REQUEST['year'] = date('Y');
		}
		if(!empty($_REQUEST['form_block_id'])){
			$start_date = Url::get('year').'-'.Url::get('month').'-01';
			$end_date = Url::get('year').'-'.Url::get('month').'-'.Date_Time::get_last_day_of_month(Url::get('month'),Url::get('year'));
			$status = DashboardDB::get_statuses();
			$dataPoints = array();
			$total = 0;
			foreach($status as $key=>$val){
				$total_status = DB::fetch('SELECT count(*) as total FROM orders WHERE orders.group_id='.Session::get('group_id').' and orders.status_id = '.$key.' and orders.created >= "'.$start_date.'" and orders.created<="'.$end_date.'"','total');
	            $total += $total_status;
	            $status[$key]['qty'] = $total_status;
			}
	        $this->map['total'] = $total;
	        $this->map['status'] = $status;
	    }
		$this->parse_layout('list',$this->map);
	}
}
?>
