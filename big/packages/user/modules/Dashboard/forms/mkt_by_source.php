<?php
class ReportForm extends Form
{
    protected $group_id;
    protected $isObd;
    function __construct()
    {
        $this->group_id = Session::get('group_id');
        $this->isObd = isObd();
        Form::Form('ReportForm');
    }

    function draw()
    {
        if(!checkPermissionAccess(['MARKETING','ADMIN_MARKETING','BC_DOANH_THU_MKT']) && !Dashboard::$xem_khoi_bc_marketing){
            Url::access_denied();
        }
        $data = [];
        $data['title'] = 'Báo cáo Marketing theo nguồn';
        $users = DashboardDB::get_users('MARKETING',false,true);
        $data['account_id_list'] = array(''=>'Tất cả Marketing') + MiString::get_list($users,'full_name');
        $data['view_id_list'] = array(''=>'Xem theo', 1 => 'Ngày xác nhận', 2 => 'Ngày chuyển đóng hàng');
        $items = [];
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('01/m/Y');
        }

        if(!Url::get('date_to')) {
            $_REQUEST['date_to'] = date('d/m/Y');
        }
        $total_date = Date_Time::count_day(Date_Time::to_time(Url::get('date_from')),Date_Time::to_time(Url::get('date_to')));
        if($total_date>31){
            die('<div class="alert alert-danger">Bạn vui lòng chọn khoảng thời gian trong tối đa một tháng.</div>');
        }
        if (!Url::get('view_id')) {
            $_REQUEST['view_id'] = 1;
        }

        $sources = MiString::get_list(DashboardDB::get_source(),'name');
        $group_id = $this->group_id;
        $master_group_id = Session::get('master_group_id');
        $account_type = Session::get('account_type');
        if ($this->isObd) {
            $bundles = DashboardDB::getBundles();
        } else {
            if ($account_type ==TONG_CONG_TY or $master_group_id) {
                if ( $account_type ==TONG_CONG_TY ) {
                    $bundles = DashboardDB::get_bundles($group_id);
                } else {
                    $bundles = DashboardDB::get_bundles($master_group_id);
                }
            } else {
                $bundles = DashboardDB::get_bundles();
            }
        }

        $bundle_id_list = array("" => "Phân loại") + MiString::get_list($bundles);
        $data['bundle_id_list'] = $bundle_id_list;
        $no_revenue_status = DashboardDB::get_no_revenue_status();
        $cond_common = []; $chart_items = [];
        $chart_items[1]['name'] = 'Chi phí QC';
        $chart_items[2]['name'] = 'Doanh thu';
        $chart_cate = [];
        $view_id = $_REQUEST['view_id'];
        if ($account_type) {
            $cond_common[] = '(o.group_id='.$group_id.' or o.master_group_id='. $group_id .')';
        } else if ($master_group_id = Session::get('master_group_id')) {
            $cond_common[] = '(o.group_id='. $group_id .' or o.master_group_id='. $master_group_id .')';
        }else {
            $cond_common[] = '(o.group_id='. $group_id .')';
        }

        

        $total_chi_phi_qc = 0; $total_doanh_thu = 0; $total_order = 0;
        $total_sdt = 0; $total_gia_so = 0; $total_click = 0;
        $avg_ty_le_chot = 0; $total_ty_le_chot = 0; $total_ty_le_chot_has_value = 0;
        if (Url::get('view_report')) {
            $start_time = date('Y-m-d', Date_Time::to_time($_REQUEST['date_from']));
            $end_time = date('Y-m-d', Date_Time::to_time($_REQUEST['date_to']));

            $data += DashboardDB::get_report_info();
            // System::debug($sources); die();
            if (!empty($sources)) {
                foreach ($sources as $source_id => $source) {
                    $chart_cate[] = $source;
                    $cond = $cond_common;

                    $cond[] = $this->getUserCreatedCondition();
                    $cond[] = $this->getBundleCondition();
                    $cond[] = " AND o.source_id = $source_id";
                    $cond[] = " AND o.created >= '". $start_time ." 00:00:00' AND  o.created <= '". $end_time ." 23:59:59' ";

                    $sdt = DashboardDB::getPhonesBySource(implode(" ", $cond)); // Số đơn

                    $cond_adv = [];
                    $chi_phi_qc = 0;
                    $cond_adv[] = "AND vam.group_id = " . $group_id;
                    $cond_adv[] = "AND vam.source_id = " . $source_id;
                    $cond_adv[] = " AND vam.date >= '". $start_time ."' AND  vam.date <= '". $end_time ."' ";
                    $chi_phi_qc = DashboardDB::getAdvCostBySource(implode(" ", $cond_adv)); // Do các tài khoản tự khai báo
                    $luot_click = DB::fetch("
                        SELECT SUM(IFNULL(vam.clicks, 0)) AS total
                        FROM vs_adv_money AS vam
                        WHERE vam.id > 0 ". implode(" ", $cond_adv) ."
                    ", "total");
                    $chi_phi_qc = !empty($chi_phi_qc) ? $chi_phi_qc : 0;
                    $chart_items[1]['data'][] = (float) $chi_phi_qc;
                    $gia_so = 0; // Chi phí QC/SĐT
                    if ($sdt > 0) {
                        $gia_so = round($chi_phi_qc / $sdt);
                    }

                    $so_don = 0; // Số lượng đơn trường đóng hàng
                    $cond_ketoan = $cond_common;
                    if ($no_revenue_status) {
                        $cond_ketoan[] = ' AND o.status_id NOT IN ('.$no_revenue_status.')';
                    }

                    $cond_ketoan[] = " AND o.source_id = $source_id";
                    $join_ke_toan = "";
                    if ($view_id == 1) {
                        $cond_ketoan[] = ' AND o.confirmed >= "'.$start_time.' 00:00:00" and  o.confirmed <= "'.$end_time.' 23:59:59"';
                    } else {
                        $join_ke_toan = " inner join orders_extra on orders_extra.order_id = o.id ";
                        $cond_ketoan[] = ' AND orders_extra.accounting_confirmed>="'.$start_time.' 00:00:00" and  orders_extra.accounting_confirmed<="'.$end_time.' 23:59:59"';
                    }

                    $cond_ketoan[] = $this->getBundleCondition();
                    $cond_ketoan[] = $this->getUserCreatedCondition();

                    $doanh_thu = DB::fetch('
                        select 
                            sum(o.total_price) as total 
                        from 
                            orders AS o
                            '. $join_ke_toan .'
                        where 
                            '. implode(" ", $cond_ketoan),'total');
                    $so_don = DB::fetch('
                        select 
                            count(o.id) as total 
                        from 
                            orders AS o
                            inner join orders_extra on orders_extra.order_id = o.id
                        where 
                            '. implode(" ", $cond_ketoan), 'total');
                    $doanh_thu = !empty($doanh_thu) ? $doanh_thu : 0;
                    $chart_items[2]['data'][] = (float) $doanh_thu;
                    $chi_phi_doanh_thu = 0;
                    if (!empty($doanh_thu)) {
                        $chi_phi_doanh_thu = round($chi_phi_qc / $doanh_thu, 2) * 100 . ' %';
                    }

                    $ty_le_chot = 0; // Đơn / SĐT
                    if (!empty($sdt)) {
                        $ty_le_chot = round($so_don / $sdt, 4) * 100;
                    }

                    if ($ty_le_chot > 0) {
                        $total_ty_le_chot += $ty_le_chot;
                        $total_ty_le_chot_has_value += 1;
                    }

                    $cvr = 0; // Clic/SDT (%: Làm tròn đến chữ số thập phân thứ 2)
                    if (!empty($luot_click)) {
                        $cvr = round($sdt/$luot_click, 4) * 100 . " %";
                    }

                    $total_chi_phi_qc += $chi_phi_qc;
                    $total_doanh_thu += $doanh_thu;
                    $total_order += $so_don;
                    $total_sdt += $sdt;
                    $total_gia_so += $gia_so;
                    $total_click += $luot_click;
                    if ($total_ty_le_chot_has_value > 0) {
                        $avg_ty_le_chot = round($total_ty_le_chot/$total_ty_le_chot_has_value, 4) . ' %';
                    }

                    $items[$source_id]['name'] = $source;
                    $items[$source_id]['sdt'] = $sdt;
                    $items[$source_id]['chi_phi_qc'] = number_format($chi_phi_qc);
                    $items[$source_id]['gia_so'] = number_format($gia_so);
                    $items[$source_id]['so_don'] = $so_don;
                    $items[$source_id]['doanh_thu'] = number_format($doanh_thu);
                    $items[$source_id]['chi_phi_doanh_thu'] = $chi_phi_doanh_thu;
                    $items[$source_id]['ty_le_chot'] = $ty_le_chot . ' %';
                    $items[$source_id]['click'] = $luot_click;
                    $items[$source_id]['cvr'] = $cvr;
                }
            }
        }

        // System::debug($items); die();

        $data['items'] = $items;
        $data['total_chi_phi_qc'] = $total_chi_phi_qc;
        $data['total_doanh_thu'] = $total_doanh_thu;
        $data['total_order'] = $total_order;
        $data['total_sdt'] = $total_sdt;
        $data['total_gia_so'] = $total_gia_so;
        $data['total_click'] = $total_click;
        $totalChiPhiDoanhThu = $total_doanh_thu ? number_format(round($total_chi_phi_qc * 100/$total_doanh_thu, 2)) : 0;
        $data['total_chi_phi_doanh_thu'] = $totalChiPhiDoanhThu;
        $data['avg_ty_le_chot'] = $avg_ty_le_chot;
        $data['chart_items'] = array_values($chart_items);
        $data['chart_cate'] = $chart_cate;
        $this->parse_layout('mkt_by_source', $data);
    }


    /**
     * Gets the bundle condition.
     *
     * @return     string  The bundle condition.
     */
    private function getBundleCondition()
    {
        if ($bundle = URL::getUInt("bundle_id")) {
            if ($this->isObd) {
                $_strBundleRequets = DashboardDB::getIncludeBundleIds($bundle, $this->group_id);
                return " AND o.bundle_id IN ($_strBundleRequets) ";
            } else {
                return " AND o.bundle_id = " . $bundle;
            }
        }
    }

    /**
     * Gets the user created condition.
     *
     * @return     string  The user created condition.
     */
    private function getUserCreatedCondition()
    {
        if ($userID = URL::get("account_id")) {
            return " AND o.user_created = " . $userID;
        }
    }
}
