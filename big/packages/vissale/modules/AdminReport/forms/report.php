<?php

class ReportForm extends Form
{

    function __construct()
    {
        Form::Form('ReportForm');
        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
    }

    function draw()
    {
        $data = [];
        $data['title']                       = 'Hệ thống báo cáo QLBH';
        $data['quyen_admin_marketing']       = check_user_privilege('ADMIN_MARKETING');
        $data['quyen_marketing']             = check_user_privilege('MARKETING');
        $data['quyen_gandon']                = check_user_privilege('GANDON');
        $data['quyen_chia_don']              = check_user_privilege('CHIADON');
        $data['quyen_bc_doanh_thu_mkt']      = check_user_privilege('BC_DOANH_THU_MKT');
        $data['quyen_bc_doanh_thu_nv']       = check_user_privilege('BC_DOANH_THU_NV');
        $data['is_account_group_manager']    = is_account_group_manager();
        $data['quyen_xem_bc_bxh_vinh_danh']  = check_user_privilege('BC_BXH_VINH_DANH');
        $data['quyen_xem_bc_doi_nhom']       = check_user_privilege('QUYEN_XEM_BC_DOI_NHOM');
        $data['quyen_ke_toan']               = check_user_privilege('KE_TOAN');
        $data['quyen_van_don']               = check_user_privilege('VAN_DON');
        $data['is_account_group_department'] = is_account_group_department();
        $data['quyen_cskh'] = check_user_privilege('CSKH');
        $data['only_mkt'] = checkUserOnlyRole('MARKETING');
        $data['xem_khoi_bc_marketing']       = check_user_privilege('NHOM_BC_MKT');
        $data['xem_khoi_bc_sale']            = check_user_privilege('NHOM_BC_SALE');
        $data['xem_khoi_bc_chung']           = check_user_privilege('NHOM_BC_CHUNG');
        $data['xem_khoi_bc_truc_page']       = check_user_privilege('NHOM_BC_TRUC_PAGE_TINH_TRANG_DON');
        $this->parse_layout('report', $data);
    }
}
