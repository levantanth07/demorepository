<?php
class PostForm extends Form
{
    protected $map;
	function __construct()
	{
		Form::Form('PostForm');
	}	
	function draw()
	{		
		if(!checkPermissionAccess(['MARKETING','ADMIN_MARKETING','BC_DOANH_THU_MKT']) && !Dashboard::$xem_khoi_bc_marketing){
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
		if(!Url::get('date_from')){
			$_REQUEST['date_from'] = ('01/'.$month.'/'.$year);
		}
		if(!Url::get('date_to')){
			$_REQUEST['date_to'] = date('d').'/'.$month.'/'.$year;//Date_Time::get_last_day_of_month($month,$year)
		}
		$status = DashboardDB::get_statuses();
		$cond = '
			orders.fb_post_id and orders.group_id = '.Dashboard::$group_id.'
		';
		if(Url::get('date_from')){
			$cond .= ' AND orders.created >="'.Date_Time::to_sql_date(Url::get('date_from')).'"';
		}
		if(Url::get('date_to')){
			$cond .= ' AND orders.created <="'.Date_Time::to_sql_date(Url::get('date_to')).' 23:59:59"';
		}
		if(Url::get('post_id')){
			$cond .= ' AND orders.fb_post_id LIKE "%'.DB::escape(Url::get('post_id')).'%"';
		}
		if(Url::get('search_page_id')){
			$cond .= ' AND orders.fb_page_id = "'.DB::escape(Url::get('search_page_id')).'"';
		}
		$orders = DashboardDB::get_orders_by_post($cond);
		$reports = array();
		if(Url::get('view_report')){
			$reports['label']['id'] = 'label';
			$reports['label']['post_id'] = 'Post ID';
			$reports['label']['page_name'] = 'Page';
			foreach($orders as $key=>$value){
				foreach($status as $k=>$v){
					$reports['label'][$k] = $v['name'];
				}
			}
			$reports['label']['total'] = 'Tổng';
			foreach($orders as $key=>$value){
				if($key!='label'){
					$reports[$key]['id'] = $key;
					$reports[$key]['post_id'] = $value['fb_post_id'];
					$reports[$key]['page_name'] = $value['page_name'];
					$total_ = 0;
					foreach($status as $k=>$v){
						$cond = '
						orders.group_id='.Dashboard::$group_id.'
						and orders.fb_post_id="'.$value['fb_post_id'].'"
						and status_id='.$k.'';
						if(Url::get('date_from')){
							$cond .= ' AND orders.created >="'.Date_Time::to_sql_date(Url::get('date_from')).'"';
						}
						if(Url::get('date_to')){
							$cond .= ' AND orders.created <="'.Date_Time::to_sql_date(Url::get('date_to')).' 23:59:59"';
						}
						if(Url::get('post_id')){
							$cond .= ' AND orders.fb_post_id LIKE "%'.DB::escape(Url::get('post_id')).'%"';
						}
						if(Url::get('page_id')){
							$cond .= ' AND orders.fb_page_id LIKE "%'.DB::escape(Url::get('page_id')).'%"';
						}
						$total_price = DB::fetch('select count(*) as total from orders where '.$cond.' group by fb_post_id','total');
						$reports[$key][$k] = $total_price;
						$total_ += $total_price;
					}
					$this->map['total'] += $total_;
					$reports[$key]['total'] = $total_;
				}
			}
		}
		$this->map['total'] = System::display_number($this->map['total']);
		///////////////////////////////////////////
		if(sizeof($reports)>2){
			//System::sksort($reports, 'total',false);
		}
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
		$pages = MiString::get_list(FbSettingDB::get_friendpages('fb_pages.group_id='.Dashboard::$group_id),'page_name');

		$this->map['search_page_id_list'] = array(''=>'Tất cả các page') + $pages;
		$this->parse_layout('post',$this->map);
	}

}
?>
