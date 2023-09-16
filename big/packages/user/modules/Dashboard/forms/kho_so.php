<?php
require_once ROOT_PATH . 'packages/core/includes/common/Arr.php';
class ReportForm extends Form
{
    protected $map;
    protected $kho_so_months;

    private $bundleIDs;
    private $allShopBundles = [];
    protected $isObd;

    function __construct()
    {
        $this->kho_so_months = [];
        Form::Form('ReportForm');
        $this->link_js('assets/standard/js/multi.select.js');
        $this->link_css('assets/standard/css/multi-select.css?v=18102021');

        $this->link_js('/packages/core/includes/js/helper.js?v=101020201');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
        $this->isObd = isObd();

        if ($this->isObd) {
            $this->allShopBundles = DashboardDB::getBundles();
        } else {
            $this->allShopBundles = $this->getAllShopBundles();
        }//end if

        // $this->allShopBundles = $this->getAllShopBundles();
        $this->bundleIDs = Arr::of(URL::getArray('bundle_id'))
                    ->map(function($bundleID){
                        return intval($bundleID);
                    })
                    ->filter(function($bundleID){
                        return $bundleID === 0 || isset($this->allShopBundles[$bundleID]);
                    });
    }

    function draw(){
        if(Url::get('act') == 'chart'){
            if(!Dashboard::$quyen_marketing && !Dashboard::$quyen_admin_marketing && !Dashboard::$quyen_bc_doanh_thu_mkt && !Dashboard::$xem_khoi_bc_marketing){
                Url::js_redirect(true, 'Bạn không có quyền truy cập!');
            }
        } else {
            if(!Dashboard::$xem_khoi_bc_truc_page){
                Url::access_denied();
            }
        }
        $this->map = array();
        for($i=2018;$i<=date('Y');$i++){
            $this->map['year_list'][$i] =  $i;
        }
        if(!Url::get('year')){
            $_REQUEST['year'] = date('Y');
        }
        $year = Url::iget('year');
        $current_account_id = Session::get('user_id');
        $group_id = Session::get('group_id');
        $account_type = Session::get('account_type');
        $master_group_id = Session::get('master_group_id');
        $admin_group = Session::get('admin_group');
        $is_owner = is_group_owner();
        $this->map['total'] = 0;
        $this->map['so_cap_total'] = 0;
        $this->map['so_cap_total_moi'] = 0;
        $this->map['so_cap_total_cu'] = 0;
        $this->map['so_chia_total'] = 0;
        $this->map['so_tiep_can_total'] = 0;
        $this->map['so_huy_total'] = 0;
        $this->map['so_da_goi_total'] = 0;
        $this->map['so_ton_total'] = 0;
        $this->map['so_chot_total'] = 0;
        $this->map['doanh_thu_total'] = 0;
        $this->map['so_cap_max'] = 0;
        $this->map['total_ty_le_chot'] = 0;
        $this->map['total_ty_le_chot_thuc'] = 0;
        $this->map['total_ty_le_chot_ket_noi'] = 0;
        $total_so_chia_tiep_can_duoc = 0;
        $total_ket_noi = 0;

        //////////////////////////////////////////////////
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('d/m/'.$year);
        }
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('d/m/'.$year);//Date_Time::get_last_day_of_month($month,$year)
        }
        $start_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_from']));
        $end_time = date('Y-m-d',Date_Time::to_time($_REQUEST['date_to']));
        if(strtotime($end_time) - strtotime($start_time) > 31*24*3600){
            die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 1 tháng!</div>');
        }else{
        }
        $is_account_group_manager = is_account_group_manager();
        $is_account_group_department = is_account_group_department();
        $status = DashboardDB::get_statuses();
        $reports = array();
        $this->map['sale'] = $sale = Url::get('sale')?true:false;
        $this->map['users_ids_option'] = '';
        $code = 'MARKETING';
        $users = DashboardDB::getUserNew($code, Url::get('is_active'));
        $userRequest = [];
        $userRequest = Url::get('users_ids');
        if(!empty($userRequest)){
            foreach($users as $key=>$val){
                if(!$is_account_group_manager && !$is_account_group_department){
                    if($sale){
                        if(!Dashboard::$quyen_bc_doanh_thu_nv && !Dashboard::$xem_khoi_bc_truc_page){
                            if($val['username']!=$current_account_id){
                                unset($users[$key]);
                                continue;
                            }
                        }
                    } else {
                        if(!Dashboard::$quyen_admin_marketing and !Dashboard::$quyen_bc_doanh_thu_mkt){
                            if($val['username']!=$current_account_id){
                                unset($users[$key]);
                                continue;
                            }
                        }
                    }
                }
                if (in_array($key,$userRequest)) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }
                $this->map['users_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
        } else {
            foreach($users as $key=>$val){
                if(!$is_account_group_manager && !$is_account_group_department){
                    if($sale){
                        if(!Dashboard::$quyen_bc_doanh_thu_nv && !Dashboard::$xem_khoi_bc_truc_page){
                            if($val['username']!=$current_account_id){
                                unset($users[$key]);
                                continue;
                            }
                        }
                        
                    } else {
                        if(!Dashboard::$quyen_admin_marketing and !Dashboard::$quyen_bc_doanh_thu_mkt){
                            if($val['username']!=$current_account_id){
                                unset($users[$key]);
                                continue;
                            }
                        }
                    }
                }
                $selected = '';
                $this->map['users_ids_option'] .= '<option value="'.$key.'"'.$selected.'>'.$val['username'].'</option>';
            }
            
        }
        if(Url::get('view_report')){
            $this->map['user_id_list'] = [''=>'Tất cả các tài khoản marketing'] + MiString::get_list($users,'full_name');
            $reports['label']['id'] = 'label';
            $reports['label']['username'] = 'Tên tài khoản';
            $reports['label']['name'] = 'Họ và tên';
            $reports['label']['so_cap'] = 'Tổng số cấp';
            $reports['label']['so_cap_cu'] = 'Số cũ <span title="Số CSKH, Tối ưu, ..." class="fa fa-question-circle"></span>';
            $reports['label']['so_cap_moi'] = 'Số mới';
            $reports['label']['so_huy'] = 'Số hủy';
            $reports['label']['so_chia'] = 'Số chia';
            $reports['label']['so_tiep_can'] = 'Số tiếp cận';
            $reports['label']['ty_le_chot'] = '% chốt <span title="số chốt/ số được chia" class="fa fa-question-circle"></span>';
            $reports['label']['ty_le_chot_thuc'] = '% chốt thực <span title="số chốt / số được chia tiếp cận được" class="fa fa-question-circle"></span>';
            $reports['label']['ty_le_ket_noi'] = '% kết nối <span title="số tiếp cận được / số được chia" class="fa fa-question-circle"></span>';
            $reports['label']['so_da_goi'] = 'Đã gọi';
            $reports['label']['so_ton'] = 'Tồn';
            $reports['label']['so_chot'] = 'Chốt';
            $reports['label']['doanh_thu'] = 'Doanh thu';
            $reports['label']['total'] = 1000000000000000000000000;
            $reports['label']['ty_le_chot_color'] = '';
            $reports['label']['ty_le_chot_thuc_color'] = '';

            if(!empty($userRequest)){
                $request = [];
                foreach ($users as $key => $value) {
                    foreach ($userRequest as $k => $v) {
                        if($key == $v){
                            $request[$key] = $value;
                        }
                    }
                }
                $users = $request;
            }
            foreach($users as $key=>$value){
                $total_ = 0;
                if(!$is_account_group_manager && !$is_account_group_department){
                    if($sale){
                        if(!Dashboard::$quyen_bc_doanh_thu_nv && !Dashboard::$xem_khoi_bc_truc_page){
                            if($value['username']!=$current_account_id){
                                unset($users[$key]);
                                continue;
                            }
                        }
                    }else{
                        if(!Dashboard::$quyen_admin_marketing and !Dashboard::$quyen_bc_doanh_thu_mkt){
                            if($value['username']!=$current_account_id){
                                unset($users[$key]);
                                continue;
                            }
                        }
                    }
                }
                if($master_group_id){
                    $cond = '(orders.master_group_id='.$master_group_id.')';
                }else{
                    $cond = '(orders.group_id='.$group_id.' '.($account_type?' or orders.master_group_id='.$group_id.'':'').')';
                }
                if($type = Url::iget('type')){
                    $cond .= ' AND orders.type = '.$type;
                }
                // điều kiện khi chọn phân loại
                if($this->bundleIDs->count() && $this->bundleIDs->indexOf(0) === false){    
                    if ($this->isObd) {
                        $_strBundleRequets = $this->bundleIDs->join(',');
                        $_bundleIds = DashboardDB::getIncludeBundleIds($_strBundleRequets, $group_id);
                        $cond .= " AND orders.bundle_id IN ($_bundleIds)";
                    } else {
                        $cond .= ' AND orders.bundle_id IN (' . $this->bundleIDs->join(',') . ')';
                    }//end if
                }

                $reports[$key]['id'] = $key;
                $reports[$key]['username'] = $value['username'];
                $reports[$key]['name'] = $value['full_name'];
                $kho_so_mkt_users[] = $reports[$key]['name'];
                $reports[$key]['so_tiep_can'] = 0;
                $reports[$key]['so_huy'] = 0;
                $reports[$key]['ty_le_chot'] = 0;
                $reports[$key]['ty_le_chot_thuc'] = 0;
                $reports[$key]['ty_le_ket_noi'] = 0;
                if($sale){
                    $reports[$key]['so_cap'] = 0;
                    $reports[$key]['so_chia'] = 0;
                    $cond1 = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_assigned='.$value['user_id'].'';
                    $this->map['so_chia_total'] += $reports[$key]['so_chia'] = DB::fetch('select count(id) as total from orders where '.$cond1.' group by user_assigned','total');
                    $cond2 = $cond.' and orders.assigned>="'.$start_time.' 00:00:00" and  orders.assigned<="'.$end_time.' 23:59:59" and orders.user_assigned='.$value['user_id'].' and IFNULL(s.not_reach,0) <> 1';
                    $this->map['so_tiep_can_total'] += $reports[$key]['so_tiep_can'] = DB::fetch('select count(orders.id) as total from orders join statuses s on orders.status_id = s.id where '.$cond2.' group by orders.user_assigned','total');
                    //
                    $cond3 = $cond.' and orders.assigned>="'.$start_time.' 00:00:00" and  orders.assigned<="'.$end_time.' 23:59:59" and orders.user_assigned='.$value['user_id'].' and orders.status_id='.HUY;
                    $this->map['so_huy_total'] += $reports[$key]['so_huy'] = DB::fetch('select count(orders.id) as total from orders where '.$cond3.' group by orders.user_created','total');
                    // Lấy tổng số xác nhận.
                    $cf_cond = $cond.' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed='.$value['user_id'].' and orders.status_id<>'.HUY;
                    $so_chot =  DB::fetch('select count(id) as total from orders where '.$cf_cond.' group by user_confirmed','total');
                    $doanh_thu = DashboardDB::get_total_amount('select sum(orders.total_price) as total from orders where '.$cf_cond.' group by user_confirmed','total');
                    $ty_le_chot = 0;
                    if($reports[$key]['so_chia']){
                        $reports[$key]['ty_le_chot'] = $ty_le_chot = (round($so_chot/$reports[$key]['so_chia'],2)*100).'';
                    }else{
                        $reports[$key]['ty_le_chot'] = '';
                    }
                    $reports[$key]['so_chot'] = $so_chot;
                    $this->map['so_chot_total'] += $so_chot;
                    $reports[$key]['ty_le_chot_color'] = DashboardDB::get_color_by_rate($ty_le_chot);
                    /////////CHO TTHUC////////
                    $so_chia_tiep_can_duoc = DB::fetch('
                      select 
                        count(orders.id) as total 
                      from 
                        orders
                        join statuses on statuses.id = orders.status_id 
                      where 
                        '.$cond1.'
                        AND IFNULL(statuses.not_reach,0) = 0
                      group by 
                        user_assigned
                     ','total');
                    $ty_le_chot_thuc = 0;
                    if($so_chia_tiep_can_duoc){
                        $total_so_chia_tiep_can_duoc += $so_chia_tiep_can_duoc;
                        $reports[$key]['ty_le_chot_thuc'] = $ty_le_chot_thuc = (round($so_chot/$so_chia_tiep_can_duoc,2)*100).'';
                    }else{
                        $reports[$key]['ty_le_chot_thuc'] = '';
                    }
                    $this->map['total_ty_le_chot_thuc'] = $total_so_chia_tiep_can_duoc != 0 ? (round($this->map['so_chot_total']/$total_so_chia_tiep_can_duoc,2)*100) : 0;
                    $reports[$key]['ty_le_chot_thuc_color'] = DashboardDB::get_color_by_rate($ty_le_chot_thuc);
                    ////////////////////////////
                    if($reports[$key]['so_chia']>0){
                        $reports[$key]['ty_le_ket_noi'] = $ty_le_ket_noi = (round($reports[$key]['so_tiep_can']/$reports[$key]['so_chia'],2)*100).'';
                    }else{
                        $reports[$key]['ty_le_ket_noi'] = '';
                        $ty_le_ket_noi = 0;
                    }
                    $reports[$key]['ty_le_ket_noi_color'] = DashboardDB::get_color_by_rate($ty_le_ket_noi);
                    ////////////////////////////
                    $reports[$key]['so_da_goi'] = 0;
                    $cond2 = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_assigned='.$value['user_id'].' and status_id <> 10';
                    $this->map['so_da_goi_total'] += $reports[$key]['so_da_goi'] = DB::fetch('select count(id) as total from orders where '.$cond2.' group by user_assigned','total');
                    $this->map['so_ton_total'] += $reports[$key]['so_ton'] = $reports[$key]['so_chia'] - $reports[$key]['so_da_goi'];
                    $this->map['total_ty_le_chot'] = $this->map['so_chia_total'] != 0 ? (round($this->map['so_chot_total']/$this->map['so_chia_total'],2)*100) : 0;
                    $this->map['total_ty_le_chot_ket_noi'] = $this->map['so_chia_total'] != 0 ? (round($this->map['so_tiep_can_total']/$this->map['so_chia_total'],2)*100) : 0;
                }else{ //marketing
                    $reports[$key]['so_cap'] = 0;
                    $cond_so_cap = $cond.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_created='.$value['user_id'].'';
                    $cond_so_cap_moi = $cond.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_created='.$value['user_id'].' and IFNULL(orders.type,0)<=1';
                    $total_+= $this->map['so_cap_total'] += $reports[$key]['so_cap'] = DB::fetch('select count(id) as total from orders where '.$cond_so_cap.' group by user_created','total');
                    $this->map['so_cap_total_moi'] += $reports[$key]['so_cap_moi'] = DB::fetch('select count(id) as total from orders where '.$cond_so_cap_moi.' group by user_created','total');
                    $reports[$key]['so_cap_cu'] = $reports[$key]['so_cap'] - $reports[$key]['so_cap_moi'];
                    $this->map['so_cap_total_cu'] +=$reports[$key]['so_cap_cu'];
                    if($this->map['so_cap_max']<=$reports[$key]['so_cap']){
                        $this->map['so_cap_max'] = $reports[$key]['so_cap'];
                    }
                    //
                    $cond2 = $cond.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_created='.$value['user_id'].' and IFNULL(s.not_reach,0) <> 1';
                    $this->map['so_tiep_can_total'] += $reports[$key]['so_tiep_can'] = DB::fetch('select count(orders.id) as total from orders join statuses s on orders.status_id = s.id where '.$cond2.' group by orders.user_created','total');
                    //
                    $cond3 = $cond.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_created='.$value['user_id'].' and orders.status_id='.HUY;
                    $this->map['so_huy_total'] += $reports[$key]['so_huy'] = DB::fetch('select count(orders.id) as total from orders where '.$cond3.' group by orders.user_created','total');
                    //
                    $cf_cond = $cond.' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_created='.$value['user_id'].' and orders.status_id<>'.HUY;
                    $so_chot =  DB::fetch('select count(id) as total from orders where '.$cf_cond.' group by user_created','total');
                    $doanh_thu = DB::fetch('select sum(total_price) as total from orders where '.$cf_cond.' group by user_created','total');
                    //
                    $ty_le_chot = 0;
                    $so_cap = $reports[$key]['so_cap'];
                    if($so_cap>0){
                        $reports[$key]['ty_le_chot'] = $ty_le_chot =  (round($so_chot/$so_cap,2)*100).'';
                    }else{
                        $reports[$key]['ty_le_chot'] = '';
                    }
                    $reports[$key]['ty_le_chot_color'] = DashboardDB::get_color_by_rate($ty_le_chot);
                    $this->map['so_chot_total'] += $so_chot;
                    $reports[$key]['so_chot'] = $so_chot;
                    $cond2 = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_created='.$value['user_id'].'';
                    $this->map['so_chia_total'] += $reports[$key]['so_chia'] = DB::fetch('select count(*) as total from orders where '.$cond2,'total');
                    $cond3 = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_created='.$value['user_id'].' and status_id <> '.CHUA_XAC_NHAN;
                    $this->map['so_da_goi_total'] += $reports[$key]['so_da_goi'] = DB::fetch('select count(*) as total from orders where '.$cond3,'total');
                    $this->map['so_ton_total'] += $reports[$key]['so_ton'] = $reports[$key]['so_cap'] - $reports[$key]['so_chia'];
                    /////////////////////////////////////
                    if($reports[$key]['so_chia']>0){
                        $reports[$key]['ty_le_ket_noi'] = $ty_le_ket_noi = (round($reports[$key]['so_tiep_can']/$reports[$key]['so_chia'],2)*100).'';
                    }else{
                        $reports[$key]['ty_le_ket_noi'] = '';
                        $ty_le_ket_noi = 0;
                    }
                    $reports[$key]['ty_le_ket_noi_color'] = DashboardDB::get_color_by_rate($ty_le_ket_noi);
                    $this->map['total_ty_le_chot'] = $this->map['so_cap_total'] != 0 ? (round($this->map['so_chot_total']/$this->map['so_cap_total'],2)*100) : 0;
                    $this->map['total_ty_le_chot_ket_noi'] = $this->map['so_chia_total'] != 0 ? (round($this->map['so_tiep_can_total']/$this->map['so_chia_total'],2)*100) : 0;
                    /////////////////////////////////////
                }

                $reports[$key]['doanh_thu'] = System::display_number($doanh_thu);
                $this->map['doanh_thu_total'] += $doanh_thu;
                $this->map['total'] += $total_;
                $reports[$key]['total'] = $total_;
            }
        }
        $this->map['doanh_thu_total'] = System::display_number($this->map['doanh_thu_total']);
        $this->map['total'] = System::display_number($this->map['total']);
        if(Url::get('act')=='chart'){
            $kho_so_series = $this->get_value_by_month(true,Url::get('year'));
        }else{
            $kho_so_series = [];
        }
        $this->map['kho_so_series'] = json_encode(array_values($kho_so_series), JSON_UNESCAPED_UNICODE);
        $this->map['kho_so_months'] = json_encode(array_values($this->kho_so_months));
        ///////////////////////////////////////////
        if(sizeof($reports)>2){
            System::sksort($reports, 'doanh_thu','DESC');
        }
        $this->map['reports'] = $reports;
        $this->map['status'] = $status;
        $months = array();
        for($i=1;$i<=12;$i++){
            $months[$i] = str_pad($i,2,"0",STR_PAD_LEFT);
        }
        $this->map['month_list'] = $months;
        $this->map['assigned_type_list'] = array(''=>'Lần chia cuối cùng','1'=>'Lần chia đầu tiên');
        $this->map += DashboardDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_groups());
        $layout = 'kho_so'.((Url::get('act')=='chart')?'_chart':'');
        $this->map['type_list'] = [''=>'Loại đơn','0'=>'Tất cả'] + Dashboard::$type;
        $this->map['bundle_id'] = $this->allShopBundles;
        $this->map['account_group_id_list'] = array(''=>'Xem theo nhóm tài khoản',''=>'Tất cả các nhóm tài khoản') + MiString::get_list(DashboardDB::get_account_groups());
        $this->parse_layout($layout,$this->map);
    }

    /**
     * Gets all shop bundles.
     *
     * @return     <type>  All shop bundles.
     */
    private function getAllShopBundles()
    {   
        if ($this->isObd) {
            $bundles = DashboardDB::getBundles();
        } else {
            $bundles = DB::fetch_all_array('SELECT `id`, `name` FROM `bundles` WHERE `group_id` = ' . Dashboard::$group_id);
        }

        return array_column($bundles, 'name', 'id');
    }

    function get_value_by_month($mkt = true,$year){
        $group_id = Dashboard::$group_id;
        $date_field = $mkt?'created':'assigned';
        $sql = '
            SELECT 
                MONTH('.$date_field.') as id, count(*) AS total
            FROM 
                orders
            WHERE 
                YEAR('.$date_field.') = '.$year.'
                AND group_id = '.$group_id.'
                '.(Url::iget('user_id')?' and orders.user_created='.(Url::iget('user_id')):'AND IFNULL(orders.user_created,0) > 0').'
            GROUP BY MONTH('.$date_field.')
        ';
        $revenue_by_months = DB::fetch_all($sql);
        $r = [
            $year=>[
                'name'=>$year,
                'type'=> 'area',
                'color' => 'rgb(33, 33, 222)'
            ]
        ];
        $sql = '
            SELECT 
                MONTH('.$date_field.') as id, count(*) AS total
            FROM 
                orders
            WHERE 
                YEAR('.$date_field.') = '.($year-1).'
                AND group_id = '.$group_id.'
                '.(Url::iget('user_id')?' and orders.user_created='.(Url::iget('user_id')):'AND IFNULL(orders.user_created,0) > 0').'
            GROUP BY MONTH('.$date_field.')
        ';
        $pre_revenue_by_months = DB::fetch_all($sql);
        $r += [
            ($year-1)=>[
                'name'=>($year-1),
                'type'=> 'area',
                'color' => 'rgb(333, 66, 99)'
            ]
        ];
        //$r['all']['name'] = 'Tổng';
        for($i=1;$i<=12;$i++){
            $this->kho_so_months[$i] = $i;
            if(isset($revenue_by_months[$i])){
                //echo $revenue_by_months[$i]['total'].'<br>';
                $r[$year]['data'][] = intval($revenue_by_months[$i]['total']);
            }else{
                $r[$year]['data'][] = 0;
            }
            if(isset($pre_revenue_by_months[$i])){
                //echo $revenue_by_months[$i]['total'].'<br>';
                $r[$year-1]['data'][] = intval($pre_revenue_by_months[$i]['total']);
            }else{
                $r[$year-1]['data'][] = 0;
            }
        }
        return $r;
    }
}
?>
