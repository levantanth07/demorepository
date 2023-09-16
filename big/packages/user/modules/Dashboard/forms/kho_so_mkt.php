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
        $this->isObd = isObd();
        $this->kho_so_months = [];
        Form::Form('ReportForm');
        $this->link_js('/packages/core/includes/js/helper.js?v=101020201');
        $this->link_js('assets/standard/js/multiple.select.js');
        $this->link_css('assets/standard/css/multiple-select.css');
        $this->group_id = Session::get('group_id');

        if ($this->isObd) {
            $this->allShopBundles = DashboardDB::getBundles();
        } else {
            $this->allShopBundles = $this->getAllShopBundles();
        }//end if

        $this->bundleIDs = Arr::of(URL::getArray('bundle_id'))
            ->map(function($bundleID){
                return intval($bundleID);
            })
            ->filter(function($bundleID){
                return $bundleID === 0 || isset($this->allShopBundles[$bundleID]);
            });
    }

    function draw(){
        if(!checkPermissionAccess(['MARKETING','ADMIN_MARKETING','BC_DOANH_THU_MKT']) && !Dashboard::$xem_khoi_bc_marketing){
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
        $status = DashboardDB::get_statuses();
        $arrRevenueStatus = array();
        foreach ($status as $rowStatus) {
            if (empty($rowStatus['no_revenue'])) {
                $arrRevenueStatus[] = $rowStatus['id'];
            }
        }
        $reports = array();
        $data = [];
        $this->map['sale'] = $sale = false;
        $this->map['so_cap_max'] = 0;
        $this->map['users_ids_option'] = '';
        $code = 'MARKETING';
        $users = DashboardDB::getUserNew($code);
        $userRequest = [];
        $userRequest = Url::get('users_ids');
        if(!empty($userRequest)){
            foreach($users as $key=>$val){
                if(!$is_account_group_manager && !$is_account_group_department && !Dashboard::$quyen_xem_bc_doi_nhom && !Dashboard::$xem_khoi_bc_marketing){
                    if(!Dashboard::$quyen_admin_marketing and !Dashboard::$quyen_bc_doanh_thu_mkt){
                        if($val['username']!=$current_account_id){
                            unset($users[$key]);
                            continue;
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
                if(!$is_account_group_manager && !$is_account_group_department && !Dashboard::$quyen_xem_bc_doi_nhom && !Dashboard::$xem_khoi_bc_marketing){
                    if(!Dashboard::$quyen_admin_marketing and !Dashboard::$quyen_bc_doanh_thu_mkt){
                        if($val['username']!=$current_account_id){
                            unset($users[$key]);
                            continue;
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
                if(!$is_account_group_manager && !$is_account_group_department && !Dashboard::$quyen_xem_bc_doi_nhom && !Dashboard::$xem_khoi_bc_marketing){
                    if(!Dashboard::$quyen_admin_marketing and !Dashboard::$quyen_bc_doanh_thu_mkt){
                        if($value['username']!=$current_account_id){
                            unset($users[$key]);
                            continue;
                        }
                    }
                }
                $users[$key] = $value;
            }
            if(!empty($userRequest)){
                $userIds = $userRequest;
                $request = [];
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
            if($this->bundleIDs->count() && $this->bundleIDs->indexOf(0) === false){
                if ($this->isObd) {
                    $strBundleRequets = $this->bundleIDs->join(',');
                    $_strBundleRequets = DashboardDB::getIncludeBundleIds($strBundleRequets, $this->group_id);
                    $cond .= ' AND orders.bundle_id IN (' . $_strBundleRequets . ')';
                } else {
                    $cond .= ' AND orders.bundle_id IN (' . $this->bundleIDs->join(',') . ')';
                }//end if
            }
            $dataConfirmed = $this->getDataConfirmed($cond, $start_time, $end_time, $strUserIds, $arrRevenueStatus);
            $dataSoTiepCan = $this->getDataSoTiepCan($cond, $start_time, $end_time, $strUserIds);
            $dataSoHuy = $this->getDataSoHuy($cond, $start_time, $end_time, $strUserIds);
            $dataSoChia = $this->getDataSoChia($cond, $start_time, $end_time, $strUserIds);
            $dataSoDaGoi = $this->getDataSoDaGoi($cond, $start_time, $end_time, $strUserIds);
            $dataSoCap = $this->getDataSoCap($cond, $start_time, $end_time, $strUserIds);
            $dataSoCapMoi = $this->getDataSoCapMoi($cond, $start_time, $end_time, $strUserIds);

            foreach($users as $k => $val){
                $data[$k]['id'] = $k;
                $data[$k]['username'] = $val['username'];
                $data[$k]['name'] = $val['full_name'];
                $data[$k]['so_chot'] = 0;
                $data[$k]['doanh_thu'] = 0;
                $data[$k]['so_chia'] = 0;
                $data[$k]['so_huy'] = 0;
                $data[$k]['so_tiep_can'] = 0;
                $data[$k]['so_ton'] = 0;
                $data[$k]['so_cap'] = 0;
                $data[$k]['so_moi'] = 0;
                $data[$k]['so_da_goi'] = 0;
                $data[$k]['phan_tram_chot'] = 0;
                $data[$k]['phan_tram_ket_noi'] = 0;
                $data[$k]['ty_le_chot_color'] = 0;
                $data[$k]['ty_le_ket_noi_color'] = 0;

                foreach($dataConfirmed as $confirmed){
                    if($confirmed['user_created'] == $k){
                        $data[$k]['so_chot'] = $confirmed['total_order'];
                        $data[$k]['doanh_thu'] = $confirmed['total_price'];
                        break;
                    }
                }
                foreach($dataSoChia as $soChia){
                    if($soChia['user_created'] == $k){
                        $data[$k]['so_chia'] = $soChia['total_order'];
                        break;
                    }
                }
                foreach($dataSoHuy as $soHuy){
                    if($soHuy['user_created'] == $k){
                        $data[$k]['so_huy'] = $soHuy['total_order'];
                        break;
                    }
                }
                foreach($dataSoTiepCan as $soTiepCan){
                    if($soTiepCan['user_created'] == $k){
                        $data[$k]['so_tiep_can'] = $soTiepCan['total_order'];
                        break;
                    }
                }
                foreach($dataSoCap as $soCap){
                    if($soCap['user_created'] == $k){
                        $data[$k]['so_cap'] = $soCap['total_order'];
                        if($this->map['so_cap_max']<=$data[$k]['so_cap']){
                            $this->map['so_cap_max'] = $data[$k]['so_cap'];
                        }
                        break;
                    }
                }
                foreach($dataSoCapMoi as $soCapMoi){
                    if($soCapMoi['user_created'] == $k){
                        $data[$k]['so_moi'] = $soCapMoi['total_order'];
                        break;
                    }
                }
                foreach($dataSoDaGoi as $soDaGoi){
                    if($soDaGoi['user_created'] == $k){
                        $data[$k]['so_da_goi'] = $soDaGoi['total_order'];
                        break;
                    }
                }
                $data[$k]['phan_tram_chot'] = $data[$k]['so_cap'] > 0 ? (round($data[$k]['so_chot']/$data[$k]['so_cap'],2)*100) : 0;
                $data[$k]['ty_le_chot_color'] = DashboardDB::get_color_by_rate($data[$k]['phan_tram_chot']);
                $data[$k]['phan_tram_ket_noi'] = $data[$k]['so_chia'] > 0 ? (round($data[$k]['so_tiep_can']/$data[$k]['so_chia'],2)*100) : 0;
                $data[$k]['ty_le_ket_noi_color'] = DashboardDB::get_color_by_rate($data[$k]['phan_tram_ket_noi']);
            }

        }
        if(sizeof($data)>=2){
            System::sksort($data, 'doanh_thu','DESC');
        }
        $this->map['reports'] = $data;
        
        $this->map['assigned_type_list'] = array(''=>'Lần chia cuối cùng','1'=>'Lần chia đầu tiên');
        $this->map += DashboardDB::get_report_info();
        $this->map['group_id_list'] = array('' => 'Chọn công ty','0'=>'Tất cả') + MiString::get_list(DashboardDB::get_groups());
        $this->map['type_list'] = [''=>'Loại đơn','0'=>'Tất cả'] + Dashboard::$type;
        $this->map['bundle_id'] = $this->allShopBundles;
        $this->map['account_group_id_list'] = array(''=>'Xem theo nhóm tài khoản',''=>'Tất cả các nhóm tài khoản') + MiString::get_list(DashboardDB::getAccountGroup());
        $this->parse_layout('kho_so_mkt',$this->map);
    }

    private function getDataConfirmed($cond, $start_time, $end_time, $strUserIds, $arrRevenueStatus){
        $confirmedCond = $cond.' and orders.confirmed>="'.$start_time.' 00:00:00" and  orders.confirmed<="'.$end_time.' 23:59:59" and orders.user_created IN ('.$strUserIds.')';
        if(count($arrRevenueStatus) > 0){
            $confirmedCond .= ' and orders.status_id in ('.implode($arrRevenueStatus, ',').')';
        }else {
            $confirmedCond .= ' and orders.status_id<>'.HUY;
        }
        $sqlConfirmed = 'SELECT 
                            id , user_created, 
                            count(id) as total_order, 
                            SUM(total_price) as total_price 
                        FROM 
                            orders 
                        WHERE 
                            '.$confirmedCond.' 
                        GROUP BY 
                            user_created';
        $dataConfirmed = DB::fetch_all($sqlConfirmed);
        return $dataConfirmed;
    }
    private function getDataSoTiepCan($cond, $start_time, $end_time, $strUserIds){
        $conSoTiepCan = $cond.' 
                                and orders.created>="'.$start_time.' 00:00:00" 
                                and  orders.created<="'.$end_time.' 23:59:59" 
                                and orders.user_created IN ('.$strUserIds.') 
                                and (statuses.not_reach <> 1 OR statuses.not_reach IS NULL)';
        $sqlSoTiepCan = 'SELECT 
                            orders.id, 
                            orders.user_created, 
                            COUNT(orders.id) as total_order 
                        FROM 
                            orders 
                        JOIN 
                            statuses ON orders.status_id = statuses.id 
                        WHERE 
                            '.$conSoTiepCan.' 
                        GROUP BY 
                            orders.user_created';
        $dataSoTiepCan= DB::fetch_all($sqlSoTiepCan);
        return $dataSoTiepCan;
    }
    private function getDataSoHuy($cond, $start_time, $end_time, $strUserIds){
        $conSoHuy = $cond.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_created IN ('.$strUserIds.') and orders.status_id='.HUY;
        $sqlSoHuy = 'SELECT 
                            orders.id, 
                            orders.user_created, 
                            COUNT(orders.id) as total_order 
                    FROM 
                        orders 
                    WHERE 
                        '.$conSoHuy.' 
                    GROUP BY 
                        orders.user_created';
        $dataSoHuy = DB::fetch_all($sqlSoHuy);
        return $dataSoHuy;
    }
    private function getDataSoChia($cond, $start_time, $end_time, $strUserIds){
        $conSoChia = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_created IN ('.$strUserIds.')';
        $sqlSoChia = 'SELECT 
                            orders.id, 
                            orders.user_created,
                            count(id) as total_order 
                        FROM 
                            orders 
                        WHERE 
                            '.$conSoChia.' 
                        GROUP BY 
                            user_created';
        $dataSoChia = DB::fetch_all($sqlSoChia);
        return $dataSoChia;
    }
    private function getDataSoDaGoi($cond, $start_time, $end_time, $strUserIds){
        $conSoDaGoi = $cond.' and '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'>="'.$start_time.' 00:00:00" and  '.(Url::get('assigned_type')?'orders.first_assigned':'orders.assigned').'<="'.$end_time.' 23:59:59" and orders.user_created IN ('.$strUserIds.') and status_id <> '.CHUA_XAC_NHAN;
        $sqlSoDaGoi = 'SELECT 
                            orders.id, 
                            orders.user_created,
                            count(id) as total_order 
                        FROM 
                            orders 
                        WHERE 
                            '.$conSoDaGoi.' 
                        GROUP BY 
                            user_created';
        $dataSoDaGoi = DB::fetch_all($sqlSoDaGoi);
        return $dataSoDaGoi;
    }
    private function getDataSoCap($cond, $start_time, $end_time, $strUserIds){
        $conSoCap = $cond.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_created IN ('.$strUserIds.')';
        $sqlSoCap = 'SELECT 
                            orders.id, 
                            orders.user_created, 
                            count(id) as total_order 
                    FROM 
                        orders 
                    WHERE 
                        '.$conSoCap.' 
                    GROUP BY 
                        user_created';
        $dataSoCap = DB::fetch_all($sqlSoCap);
        return $dataSoCap;
    }
    private function getDataSoCapMoi($cond, $start_time, $end_time, $strUserIds){
        $conSoCapMoi = $cond.' and orders.created>="'.$start_time.' 00:00:00" and  orders.created<="'.$end_time.' 23:59:59" and orders.user_created IN ('.$strUserIds.') and IFNULL(orders.type,0)<=1';
        $sqlSoCapMoi = 'SELECT 
                            orders.id, 
                            orders.user_created, 
                            count(id) as total_order 
                        FROM 
                            orders 
                        WHERE '.$conSoCapMoi.' 
                        GROUP BY 
                            user_created';
        $dataSoCapMoi = DB::fetch_all($sqlSoCapMoi);
        return $dataSoCapMoi;
    }
    private function getAllShopBundles()
    {   
        $bundles = DB::fetch_all_array('SELECT `id`, `name` FROM `bundles` WHERE `group_id` = ' . Dashboard::$group_id);
        return array_column($bundles, 'name', 'id');
    }
}
?>
