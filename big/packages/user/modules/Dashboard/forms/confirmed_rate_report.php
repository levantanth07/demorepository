<?php
class ConfirmedRateReportForm extends Form
{
    protected $map;
	function __construct()
	{
		Form::Form('ConfirmedRateReportForm');
	}
	function get_report($date_from,$date_to,$time){
		$start_time = date('Y-m-d',Date_Time::to_time($date_from));
		$end_time = date('Y-m-d',Date_Time::to_time($date_to));
		$status = array(
            XAC_NHAN =>array(
	            'id' => XAC_NHAN
	            ,'name' => 'Xác Nhận'
	            ,'total' => 0
	            ,'qty' => 0
	            ,'order_by' => XAC_NHAN
	        ),
            HUY =>array(
	            'id' => HUY
	            ,'name' => 'Hủy'
	            ,'total' => 0
	            ,'qty' => 0
	            ,'order_by' => HUY
	        )
		);
		$items = DashboardDB::getUserNew('GANDON');
        $users = [];
        foreach ($items as $key => $value) {
            $users[$value['username']] = $value;
        }
		// 7: đã xác nhận,9: hủy, 6: chuyển hoàn, 5 thành công
		$no_revenue_status = DashboardDB::get_no_revenue_status();
		$ty_le_chot_tb = 0;
		$tong_doanh_thu = 0;
        $tong_so_chot_all = 0;
        $tong_so_chia_all = 0;
        $so_chia_tiep_can_duoc_all = 0;
        $huy_all = 0;

        $sum_ti_le_huy = 0;
        $sum_ti_le_chot = 0;
        $sum_ti_le_chot_that = 0;
		if(Url::get('account_type')!=3 or Url::get('view_report')){
            foreach($users as $key=>$value){
                $so_chot = 0;
                $cond = 'orders.group_id='.Session::get('group_id').'';
                if($type=Url::iget('type')){
                    $cond .= ' AND orders.type='.$type.'';
                }
                $cond_so_chia = $cond;
                foreach($status as $k=>$v){       
                    if($k!=HUY and $no_revenue_status){
                        $cond .= ' and orders.status_id NOT IN ('.$no_revenue_status.')';
                    }
                    if($k==XAC_NHAN or $k==THANH_CONG or $k == CHUYEN_HANG){//xn
                        $cond .= ' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].'';
                        $confirm_price = DB::fetch('select sum(total_price) as total from orders where '.$cond.' group by user_confirmed','total');
                        $so_chot = DB::fetch('select count(*) as total from orders where '.$cond.' group by user_confirmed','total');
                        $confirm_prd = DB::fetch('select sum(qty) as total from orders INNER JOIN orders_products on orders.id = orders_products.order_id where '.$cond.' ','total');
                    }else{//hủy
                        $cond .= ' and orders.created>="'.$start_time.'" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_assigned='.$value['user_id'].' and orders.status_id='.$k.'';
                        $cancel_order = DB::fetch('select count(*) as total from orders where '.$cond.' group by user_assigned','total');
                        $cancel_prd = DB::fetch('select sum(qty) as total from orders INNER JOIN orders_products on orders.id = orders_products.order_id where '.$cond.' ','total');
                    }
                }
                $tong_so_chia = DB::fetch('
                  select 
                    count(id) as total 
                  from 
                    orders 
                  where
                    ' . $cond_so_chia . '
                    AND
                     orders.assigned>="'.$start_time.'" and  orders.assigned<="'.$end_time.' 23:59:59"
                     AND orders.user_assigned='.$value['user_id'].'
                    group by user_assigned','total'
                );
                //
                $ty_le_chot = '0';

                if ($tong_so_chia && $so_chot) {
                    $ty_le_chot  = round(($so_chot/$tong_so_chia)*100,2);
                }

                if (!$tong_so_chia && $so_chot) {
                    $ty_le_chot = '100';
                }

                $so_chia_tiep_can_duoc = DB::fetch('
                      select 
                        count(orders.id) as total 
                      from 
                        orders
                        join statuses on statuses.id = orders.status_id 
                      where
                        ' . $cond_so_chia . '
                        AND
                        orders.assigned>="'.$start_time.' 00:00:00" and  orders.assigned<="'.$end_time.' 23:59:59"
                        AND orders.user_assigned='.$value['user_id'].'
                        AND IFNULL(statuses.not_reach,0) = 0
                      group by 
                        user_assigned
                     ','total');
                if($so_chia_tiep_can_duoc){
                    $ty_le_chot_thuc = round(($so_chot/$so_chia_tiep_can_duoc)*100,2);
                }else{
                    $ty_le_chot_thuc = '0';
                }
                //
                $reports[$key] = array(
                    'ty_le_chot'=>$ty_le_chot > 0 ? $ty_le_chot : 0,
                    'ty_le_chot_thuc'=>$ty_le_chot_thuc > 0 ? $ty_le_chot_thuc : 0,
                    'name'=>$key,
                    'tong_so_chia'=>$tong_so_chia > 0 ? $tong_so_chia : 0,
                    'so_chot'=>$so_chot > 0 ? $so_chot : 0,
                    'sp'=>$confirm_prd + $cancel_prd,
                    'huy'=>$cancel_order > 0 ? $cancel_order : 0,
                );
                $tong_so_chia_all += $tong_so_chia;
                $tong_so_chot_all += $so_chot;
                $so_chia_tiep_can_duoc_all += $so_chia_tiep_can_duoc;
                $huy_all += $cancel_order;
                $reports[$key]['pthuy'] = $so_chot ? round(($cancel_order*100)/($so_chot), 2) : ($cancel_order > 0 ? 100 : 0);
                $tong_doanh_thu +=$confirm_price;
                $reports[$key]['doanh_thu'] = System::display_number($confirm_price).'đ';
                $reports[$key]['confirm_price'] = $confirm_price;
                ///////
                $sum_ti_le_huy += $reports[$key]['pthuy'];
                $sum_ti_le_chot += $reports[$key]['ty_le_chot'];
                $sum_ti_le_chot_that += $reports[$key]['ty_le_chot_thuc'];
            }
        }else{
		    $reports = array();
        }
        $sum_user = count($reports);
        $this->map['tong_doanh_thu_'.$time] = System::display_number($tong_doanh_thu).'đ';
        $this->map['tong_so_chia_'.$time] = $tong_so_chia_all;
        $this->map['tong_so_chot_'.$time] = $tong_so_chot_all;
        $this->map['tong_so_huy_'.$time] = $huy_all;
        $this->map['ty_le_chot_that_'.$time] = round($sum_ti_le_chot_that/$sum_user,2);
        $this->map['ty_le_huy_'.$time] = round($sum_ti_le_huy/$sum_user,2);
        $this->map['ty_le_chot_'.$time] = round($sum_ti_le_chot/$sum_user,2);

        // if (!$tong_so_chia_all && $tong_so_chot_all) {
        //     $this->map['ty_le_chot_'.$time] = 100;
        // }

		///////////////////////////////////////////
		$this->map['reports_'.$time] = $reports;
	}	
	function draw(){
        if(!checkPermissionAccess(['BC_DOANH_THU_NV','CSKH']) && !Dashboard::$xem_khoi_bc_sale){
            Url::access_denied();
        }
		//////////////////////////////////////////////////
		if(!Url::get('date_from') && !Url::get('date_to')){
			$_REQUEST['date_from'] = date('01/m/Y');
			$_REQUEST['date_to'] = date('d/m/Y');
			$startDate = strtotime(date('Y-m-d 00:00:00'));
			$this->get_report(date('d/m/Y'),date('d/m/Y'),'today');
			$this->get_report(date('d/m/Y', strtotime('-1 day', $startDate)),date('d/m/Y', strtotime('-1 day', $startDate)),'ytd');
			$this->get_report(date('01/m/Y'),date('d/m/Y'),'this_month');
			//$this->get_report(date("d/m/Y", strtotime("first day of previous month")),date("d/m/Y", strtotime("last day of previous month")),'last_month');
			$this->get_report(date("d/m/Y", strtotime("first day of previous month")),date("d/m/Y",strtotime("-1 months")),'last_month');

            // sort doanh thu
            $this->sort('reports_today', 'reports_ytd');
            $this->sort('reports_this_month', 'reports_last_month');
		}
		else{
			$this->map['show_reports_all'] = 1;
			$this->get_report(Url::get('date_from'),Url::get('date_to'),'all');
			
            // Sap xep mang goc theo doanh thu giam dan
            uasort($this->map['reports_all'], function($a, $b){
                return $b['confirm_price'] - $a['confirm_price'];
            });
		}
        $this->map['type_list'] = $this->getTypeList();
		$this->map += DashboardDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_groups());
		$this->parse_layout('confirmed_rate_report',$this->map);
	}

    /**
     * Sap xep mang goc giam dan theo doanh thu, sap xep mang phu thuoc theo mang key mang goc
     *
     * @param      <type>  $originKey      The origin key
     * @param      <type>  $dependencyKey  The dependency key
     */
    private function sort($originKey, $dependencyKey){
        // Sap xep mang goc theo doanh thu giam dan
        uasort($this->map[$originKey], function($a, $b){
            return $b['confirm_price'] - $a['confirm_price'];
        });

        // sap xep lai mang phu thuoc theo key(username) mang goc
        $tmp = [];
        foreach ($this->map[$originKey] as $userName => $report) {
            $tmp[$userName] = $this->map[$dependencyKey][$userName];
        }

        $this->map[$dependencyKey] = $tmp;
    }
    private function getTypeList(){
        return $array = [
            ''=>'Tất cả số mới + số cũ',
            1=>'SALE (Số mới)',
            2=>'CSKH',
            9=>'Tối ưu',
            3=>'Đặt lại',
            4=>'Đặt lại lần 1',
            5=>'Đặt lại lần 2',
            6=>'Đặt lại lần 3',
            7=>'Đặt lại lần 4',
            8=>'Đặt lại lần 5',
            10=>'Đặt lại lần 6',
            11=>'Đặt lại lần 7',
            12=>'Đặt lại lần 8',
            13=>'Đặt lại lần 9',
            14=>'Đặt lại lần 10',
            15=>'Đặt lại lần 11',
            16=>'Đặt lại lần 12',
            17=>'Đặt lại lần 13',
            18=>'Đặt lại lần 14',
            19=>'Đặt lại lần 15',
            20=>'Đặt lại lần 16',
            21=>'Đặt lại lần 17',
            22=>'Đặt lại lần 18',
            23=>'Đặt lại lần 19',
            24=>'Đặt lại lần 20',
            25=>'Đặt lại trên lần 20',
            26 => 'Cross sale',
            27 => 'Afiliate',
        ];
    }
}
?>
