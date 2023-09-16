<?php
class ReportByDateForm extends Form
{
	function __construct()
	{
		Form::Form('ReportByDateForm');
	}	
	function draw()
	{
		if(!checkPermissionAccess() && !Dashboard::$xem_khoi_bc_chung){
            Url::access_denied();
        }
		$this->map = array();
		$this->map['total'] = 0;
		//////////////////////////////////////////////////
		if(!Url::get('month')){
			$_REQUEST['month'] = date('m');
		}
		$dates = array();
		$month = Url::get('month')?Url::get('month'):date('m');
		$year = Url::get('year')?Url::get('year'):date('Y');
		$start_time =strtotime($year.'-'.$month.'-01');
		$end_time = strtotime($year.'-'.$month.'-'.Date_Time::get_last_day_of_month($month,$year));
		if(!Url::get('date_from')){
			$_REQUEST['date_from'] = date('d/m/Y',$start_time);
		}
		if(!Url::get('date_to')){
			$_REQUEST['date_to'] = date('d/m/Y',$end_time);
		}
		$status = DashboardDB::get_statuses();
		$cond = '
			orders.group_id = '.Session::get('group_id').'
			 and orders.mobile is not null
		';//AND ((orders.status_id = 7 or orders.status_id=9) ...)
		if(Url::get('date_from')){
			$cond .= ' AND orders.created >="'.Date_Time::to_sql_date(Url::get('date_from')).' 00:00:00"';
		}
		if(Url::get('date_to')){
			$cond .= ' AND orders.created <="'.Date_Time::to_sql_date(Url::get('date_to')).' 23:59:59"';
		}
		if(Url::get('post_id')){
			$cond .= ' AND orders.fb_post_id LIKE "%'.DB::escape(Url::get('post_id')).'%"';
		}
		if(Url::get('page_id')){
			$cond .= ' AND orders.fb_page_id = "'.DB::escape(Url::get('page_id')).'"';
		}
		//echo $cond;die;
		$orders = DashboardDB::get_orders($cond);
		$reports = array();
		if(Url::get('view_report')){
			$reports['label']['id'] = 'label';
			$reports['label']['post_id'] = 'Post ID';
			//$reports['label']['delivered'] = 'Lịch hẹn';
			$reports['label']['page_name'] = 'Page';
			$reports['label']['mobile'] = 'Điện thoại';
			$reports['label']['customer_name'] = 'Khách hàng';
			$reports['label']['san_pham'] = 'Sản phẩm';
			$reports['label']['phi_tu_van'] = 'Phí tư vấn';
			$reports['label']['chi_phi_chot'] = 'Chi phí chốt';
			$reports['label']['lieu_trinh'] = 'Liệu trình';
			$reports['label']['xac_nhan'] = 'Xác nhận';
			$reports['label']['huy'] = 'Hủy';
			$reports['label']['note'] = 'Ghi chú';
			foreach($orders as $key=>$value){
				foreach($status as $k=>$v){
					$reports['label'][$k] = $v['name'];
				}
			}
			$length = get_group_options('hide_phone_number');
			foreach($orders as $key=>$value){
				if($key!='label'){
					$reports[$key]['id'] = $key;
					$reports[$key]['fb_post_id'] = $value['fb_post_id'];
					$reports[$key]['delivered'] = $value['delivered']?date('d/m/Y',strtotime($value['delivered'])):'';
					//$reports[$key]['page_name'] = $value['page_name'];
                    $reports[$key]['fb_customer_id'] = $value['fb_customer_id'];
					$reports[$key]['customer_name'] = $value['customer_name'];
					$reports[$key]['mobile'] = ModifyPhoneNumber::hidePhoneNumber($value['mobile'], $length);
					$reports[$key]['san_pham'] = '';//$value['san_pham'];
					$reports[$key]['phi_tu_van'] = '';//System::display_number($value['phi_tu_van']);
					$reports[$key]['chi_phi_chot'] = '';//System::display_number($value['chi_phi_chot']);
					$reports[$key]['lieu_trinh'] = '';//$value['lieu_trinh'];
					$reports[$key]['xac_nhan'] = ($value['status_id']==7)?'Đã XN':' ';
					$reports[$key]['huy'] = ($value['status_id']==9)?'Đã hủy':' ';
					$reports[$key]['note'] = $value['note1'];
					$reports[$key]['total'] = System::display_number($value['total_price']);
					$this->map['total'] +=  $value['total_price'];
				}
			}
		}
		$this->map['total'] = System::display_number($this->map['total']);
		///////////////////////////////////////////

		$this->map['reports'] = $reports;
		$this->map['status'] = $status;
		$months = array();
		for($i=1;$i<=12;$i++){
			$months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
		}
		$this->map['month_list'] = $months;
		$this->map['year_list'] = array(2016=>2016,2017=>2017);
		$this->map += DashboardDB::get_report_info();
		require_once('packages/vissale/modules/FbSetting/db.php');
		$pages = MiString::get_list(FbSettingDB::get_friendpages('group_id='.Session::get('group_id')),'page_name');
		$this->map['page_id_list'] = array(''=>'Chọn Page',0=>'Tất cả các page') + $pages;
		$this->parse_layout('report_by_date',$this->map);
	}

}
?>
