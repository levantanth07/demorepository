<?php

class IntroPageForm extends Form
{

    function __construct()
    {
        Form::Form('IntroPageForm');
        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
    }

    function draw()
    {
        $data = [];
        $data['title'] = 'Quy trình vận hành kinh doanh online qua phần mềm QLBH';
        $data['quyen_admin_marketing'] = check_user_privilege('ADMIN_MARKETING');
        $data['quyen_marketing'] = check_user_privilege('MARKETING');
        $data['quyen_gandon'] = check_user_privilege('GANDON');
        $data['quyen_chia_don'] = check_user_privilege('CHIADON');
        $data['quyen_bc_doanh_thu_mkt'] = check_user_privilege('BC_DOANH_THU_MKT');
        $data['quyen_bc_doanh_thu_nv'] = check_user_privilege('BC_DOANH_THU_NV');
        $data['is_account_group_manager'] = is_account_group_manager();
        $this->parse_layout('list', $data);
    }
}