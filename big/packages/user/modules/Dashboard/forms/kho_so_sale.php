<?php
require_once ROOT_PATH . 'packages/core/includes/common/Arr.php';
class ReportForm extends Form
{
    protected $map;
    protected $kho_so_months;

    private $bundleIDs;
    private $allShopBundles = [];

    function __construct()
    {
        $this->kho_so_months = [];
        Form::Form('ReportForm');
        $this->link_js('/packages/core/includes/js/helper.js?v=101020201');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');

        $this->allShopBundles = $this->getAllShopBundles();
        $this->bundleIDs = Arr::of(URL::getArray('bundle_id'))
                            ->map(function($bundleID){
                                return intval($bundleID);
                            })
                            ->filter(function($bundleID){
                                return $bundleID === 0 || isset($this->allShopBundles[$bundleID]);
                            });
    }

    function draw(){
        if(!checkPermissionAccess(['BC_DOANH_THU_NV','CSKH','BUNGDON2','BUNGDON_NHOM','CHIADON','cs','CUSTOMER','ADMIN_MARKETING','BC_BXH_VINH_DANH','admin_ketoan','XUAT_EXCEL','KE_TOAN','BUNGDON','HCNS','ADMIN_CS','ADMIN_KHO','QUYEN_GIAM_SAT','VAN_DON','BC_DOANH_THU_MKT','BC_DOANH_THU_NV','XUATKHO','GANDON']) && !Dashboard::$xem_khoi_bc_sale){
            Url::access_denied();
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
        }
        $is_account_group_manager = is_account_group_manager();
        $is_account_group_department = is_account_group_department();
        $quyen_xem_bc_doi_nhom = Dashboard::$quyen_xem_bc_doi_nhom;
        $status = DashboardDB::get_statuses();
        $arrRevenueStatus = array();
        foreach ($status as $rowStatus) {
            if (empty($rowStatus['no_revenue'])) {
                $arrRevenueStatus[] = $rowStatus['id'];
            }
        }
        $reports = array();
        $data = [];
        $this->map['sale'] = $sale = true;
        $this->map['users_ids_option'] = '';
        $users = DashboardDB::getUserNew('GANDON',DB::escape(Url::get('is_active')));

        $account_groups = DashboardDB::getAccountGroup();
        if(!get_account_id() && DashboardDB::checkUserOnlyRole('QUYEN_XEM_BC_DOI_NHOM') && !is_account_group_manager() && !is_account_group_department() && !Session::get('admin_group') && !is_group_owner()){
            $users = [];
            $account_groups = [];
        }
        if(!is_group_owner() && !Dashboard::$admin_group && is_account_group_department() && $account_group_ids = get_account_group_ids() && (Dashboard::$quyen_admin_marketing || Dashboard::$quyen_bc_doanh_thu_mkt)){
            $users = DashboardDB::getUserSale('GANDON',DB::escape(Url::get('is_active')));
            $account_groups = DashboardDB::getAccountSale();
        }
        $getUser = [];

        if(Dashboard::$quyen_xem_bc_doi_nhom && !Session::get('admin_group') && !is_group_owner() && DashboardDB::checkMarketing()){
            if(!get_account_id()){
                $users = [];
                $account_groups = [];
            } else {
                $account_groups = DashboardDB::getGroup();
                $getUser = DashboardDB::getUser();
            }
        }

        $userRequest = [];
        $userRequest = Url::get('users_ids');
        if(!empty($userRequest)){
            foreach($users as $key=>$val){
                if(!empty($getUser)&&!in_array($key, $getUser)){
                    unset($users[$key]);
                    continue;
                }
                if(!$is_account_group_department && !$is_account_group_manager && !Dashboard::$quyen_xem_bc_doi_nhom){
                    if($sale){
                        if(!Dashboard::$quyen_bc_doanh_thu_nv){
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
                if(!empty($getUser)&&!in_array($key, $getUser)){
                    unset($users[$key]);
                    continue;
                }
                if(!$is_account_group_department && !$is_account_group_manager && !Dashboard::$quyen_xem_bc_doi_nhom && !Dashboard::$xem_khoi_bc_sale){
                    if($sale){
                        if(!Dashboard::$quyen_bc_doanh_thu_nv){
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
            foreach($users as $key=>$value){
                if(!$is_account_group_department && !$is_account_group_manager && !Dashboard::$quyen_xem_bc_doi_nhom && !Dashboard::$xem_khoi_bc_sale){
                    if(!Dashboard::$quyen_bc_doanh_thu_nv){
                        if($value['username']!=$current_account_id){
                            unset($users[$key]);
                            continue;
                        }
                    }
                }
                $users[$key] = $value;
            }

            if(!empty($userRequest)){
                $request = [];
                $userIds = $userRequest;
                foreach ($users as $key => $value) {
                    foreach ($userRequest as $k => $v) {
                        if($key == $v){
                            $request[$key] = $value;
                        }
                    }
                }
                $users = $request;
            } else {
                $userIds = array_keys($users);
            }

            $strUserIds = implode(',', $userIds);

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
                $cond .= ' AND orders.bundle_id IN (' . $this->bundleIDs->join(',') . ')';
            }

            $dataConfirmed = $this->getDataConfirmed($cond, $start_time, $end_time, $strUserIds, $arrRevenueStatus);
            $dataSoChia = $this->getDataSoChia($cond, $start_time, $end_time, $strUserIds);
            $dataSoTiepCan = $this->getDataSoTiepCan($cond, $start_time, $end_time, $strUserIds);
            $dataSoHuy = $this->getDataSoHuy($cond, $start_time, $end_time, $strUserIds);
            $dataSoDaGoi = $this->getDataSoDaGoi($cond, $start_time, $end_time, $strUserIds);
            $dataSoTiepCanDuoc = $this->getDataSoTiepCanDuoc($cond, $start_time, $end_time, $strUserIds);

            foreach ($users as $k => $val) {
                $data[$k]['id'] = $k;
                $data[$k]['username'] = $val['username'];
                $data[$k]['name'] = $val['full_name'];
                $data[$k]['so_chot'] = 0;
                $data[$k]['doanh_thu'] = 0;
                $data[$k]['ty_le_chot_thuc'] = 0;
                $data[$k]['so_chia_tiep_can_duoc'] = 0;
                $data[$k]['ty_le_chot_thuc_color'] = '';
                $data[$k]['ty_le_ket_noi'] = 0;
                $data[$k]['ty_le_ket_noi_color'] = '';
                $data[$k]['so_tiep_can'] = 0;
                $data[$k]['so_chia'] = 0;
                $data[$k]['so_huy'] = 0;
                $data[$k]['so_da_goi'] = 0;
                $data[$k]['so_ton'] = 0;
                $data[$k]['ty_le_chot'] = 0;
                $data[$k]['ty_le_chot_color'] = '';

                foreach($dataConfirmed as $confirmed){
                    if($confirmed['user_confirmed'] == $k){
                        $data[$k]['so_chot'] = $confirmed['total_order'];
                        $data[$k]['doanh_thu'] = $confirmed['total_price'];
                        break;
                    }
                }
                foreach($dataSoTiepCanDuoc as $tiepCanDuoc){
                    if($tiepCanDuoc['user_assigned'] == $k){
                        $data[$k]['so_chia_tiep_can_duoc'] = $tiepCanDuoc['total_order'];
                        break;
                    }
                }
                foreach($dataSoTiepCan as $soTiepCan){
                    if($soTiepCan['user_assigned'] == $k){
                        $data[$k]['so_tiep_can'] = $soTiepCan['total_order'];
                        break;
                    }
                }
                foreach($dataSoChia as $soChia){
                    if($soChia['user_assigned'] == $k){
                        $data[$k]['so_chia'] = $soChia['total_order'];
                        break;
                    }
                }
                foreach($dataSoHuy as $soHuy){
                    if($soHuy['user_assigned'] == $k){
                        $data[$k]['so_huy'] = $soHuy['total_order'];
                        break;
                    }
                }
                foreach($dataSoDaGoi as $soDaGoi){
                    if($soDaGoi['user_assigned'] == $k){
                        $data[$k]['so_da_goi'] = $soDaGoi['total_order'];
                        break;
                    }
                }
                $data[$k]['ty_le_chot_thuc'] = $data[$k]['so_chia_tiep_can_duoc'] > 0 ? (round($data[$k]['so_chot']/$data[$k]['so_chia_tiep_can_duoc'],2)*100) : 0;
                $data[$k]['ty_le_chot_thuc_color'] = DashboardDB::get_color_by_rate($data[$k]['ty_le_chot_thuc']);

                $data[$k]['ty_le_ket_noi'] =  $data[$k]['so_chia'] > 0 ? (round($data[$k]['so_tiep_can']/$data[$k]['so_chia'],2)*100) : 0;
                $data[$k]['ty_le_ket_noi_color'] = DashboardDB::get_color_by_rate($data[$k]['ty_le_ket_noi']);
                $data[$k]['so_ton'] = $data[$k]['so_chia'] - $data[$k]['so_da_goi'];

                $data[$k]['ty_le_chot'] = $data[$k]['so_chia'] > 0 ? (round($data[$k]['so_chot']/$data[$k]['so_chia'],2)*100) : 0;
                $data[$k]['ty_le_chot_color'] = DashboardDB::get_color_by_rate($data[$k]['ty_le_chot']);
            }
        }

        if(sizeof($data) >= 2){
            System::sksort($data, 'doanh_thu','DESC');
        }

        $this->map['reports'] = $data;
        $this->map['assigned_type_list'] = array(''=>'Lần chia cuối cùng','1'=>'Lần chia đầu tiên');
        $this->map += DashboardDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_groups());
        $this->map['type_list'] = [''=>'Loại đơn','0'=>'Tất cả'] + Dashboard::$type;
        $this->map['bundle_id'] = $this->allShopBundles;
        $this->map['account_group_id_list'] = array(''=>'Xem theo nhóm tài khoản',''=>'Tất cả các nhóm tài khoản') + MiString::get_list($account_groups);
        $this->parse_layout('kho_so_sale',$this->map);
    }

    private function getDataConfirmed($cond, $start_time, $end_time, $strUserIds, $arrRevenueStatus){
        $confirmedCond = $cond.' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_confirmed IN ('.$strUserIds.')';
        if(count($arrRevenueStatus) > 0){
            $confirmedCond .= ' and orders.status_id in ('.implode($arrRevenueStatus, ',').')';
        }else {
            $confirmedCond .= ' and orders.status_id<>'.HUY;
        }
        $sqlConfirmed = 'SELECT 
                            id , user_confirmed, 
                            count(id) as total_order, 
                            SUM(total_price) as total_price 
                        FROM 
                            orders 
                        WHERE 
                            '.$confirmedCond.' 
                        GROUP BY 
                            user_confirmed';
        $dataConfirmed = DB::fetch_all($sqlConfirmed);
        return $dataConfirmed;
    }
    private function getDataSoChia($cond, $start_time, $end_time, $strUserIds){
        $conSoChia = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_assigned IN ('.$strUserIds.')';
        $sqlSoChia = 'SELECT 
                            orders.id, 
                            orders.user_assigned,
                            count(id) as total_order 
                        FROM 
                            orders 
                        WHERE 
                            '.$conSoChia.' 
                        GROUP BY 
                            user_assigned';
        $dataSoChia = DB::fetch_all($sqlSoChia);
        return $dataSoChia;
    }
    private function getDataSoTiepCan($cond, $start_time, $end_time, $strUserIds){
        $conSoTiepCan = $cond.' and orders.assigned>="'.$start_time.' 00:00:00" and  orders.assigned<="'.$end_time.' 23:59:59" and orders.user_assigned IN ('.$strUserIds.') and (statuses.not_reach <> 1 OR statuses.not_reach IS NULL)';
        $sqlSoTiepCan = 'SELECT 
                            orders.id, 
                            orders.user_assigned, 
                            COUNT(orders.id) as total_order 
                        FROM 
                            orders 
                        JOIN 
                            statuses ON orders.status_id = statuses.id 
                        WHERE 
                            '.$conSoTiepCan.' 
                        GROUP BY 
                            orders.user_assigned';
        $dataSoTiepCan= DB::fetch_all($sqlSoTiepCan);
        return $dataSoTiepCan;
    }
    private function getDataSoHuy($cond, $start_time, $end_time, $strUserIds){
        $conSoHuy = $cond.' and orders.assigned>="'.$start_time.' 00:00:00" and  orders.assigned<="'.$end_time.' 23:59:59" and orders.user_assigned IN ('.$strUserIds.') and orders.status_id='.HUY;
        $sqlSoHuy = 'SELECT 
                            orders.id, 
                            orders.user_assigned, 
                            COUNT(orders.id) as total_order 
                    FROM 
                        orders 
                    WHERE 
                        '.$conSoHuy.' 
                    GROUP BY 
                        orders.user_assigned';
        $dataSoHuy = DB::fetch_all($sqlSoHuy);
        return $dataSoHuy;
    }
    private function getDataSoDaGoi($cond, $start_time, $end_time, $strUserIds){
        $conSoDaGoi = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_assigned IN ('.$strUserIds.') and status_id <> '.CHUA_XAC_NHAN;
        $sqlSoDaGoi = 'SELECT 
                            orders.id, 
                            orders.user_assigned,
                            count(id) as total_order 
                        FROM 
                            orders 
                        WHERE 
                            '.$conSoDaGoi.' 
                        GROUP BY 
                            user_assigned';
        $dataSoDaGoi = DB::fetch_all($sqlSoDaGoi);
        return $dataSoDaGoi;
    }
    private function getDataSoTiepCanDuoc($cond, $start_time, $end_time, $strUserIds){
        $conSoTiepCanDuoc = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_assigned IN ('.$strUserIds.') AND (statuses.not_reach = 0 OR statuses.not_reach IS NULL)';
        $dataSoTiepCanDuoc = DB::fetch_all('
                      select 
                        orders.id,
                        orders.user_assigned,
                        count(orders.id) as total_order 
                      from 
                        orders
                        join statuses on statuses.id = orders.status_id 
                      where 
                        '.$conSoTiepCanDuoc.' 
                      group by 
                        user_assigned
                     ');
        return $dataSoTiepCanDuoc;
    }
    private function getAllShopBundles()
    {   
        $bundles = DB::fetch_all_array('SELECT `id`, `name` FROM `bundles` WHERE `group_id` = ' . Dashboard::$group_id);
        return array_column($bundles, 'name', 'id');
    }

}
?>
