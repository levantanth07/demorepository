<?php
require_once ROOT_PATH . 'packages/core/includes/common/SystemsTree.php';
require_once 'packages/core/includes/utils/paging.php';
class ListHistoryForm extends Form
{
    function __construct()
    {
        Form::Form('ListHistoryForm');
        $this->link_css('assets/default/css/cms.css');
        $this->map = [];

    }
    function on_submit()
    {
        
    }
    function draw()
    {
        if ((Session::get('group_id') == GROUP_ID_AN_NINH_SHOP && (Session::get('admin_group') || is_group_owner())) || check_system_user_permission('tracuunhansu') ) {
            // code...
        } else {
            Url::js_redirect(false,'Bạn không có quyền truy cập');
        }
        $option_user = [
            "0" => "Chọn tài khoản",
        ];
        $option_user = $option_user + $this->arrayUserSearch();

        $option_action = [
            "0" => "Chọn hành động",
            "1" => "Xem danh sách",
            "2" => "Xem chi tiết",
        ];

        $this->map['page_no'] = page_no();
        $this->map['option_action'] = $option_action;
        $this->map['option_user'] = $option_user;
        $this->map['start_date'] = date('Y-m-d');
        $this->map['end_date'] = date('Y-m-d');
        $this->parse_layout('list_history',$this->map);
    }
    function arrayUserSearch()
    {
        $groupId =  GROUP_ID_AN_NINH_SHOP; 
        $sql = "SELECT id,group_id,username FROM users WHERE group_id = $groupId";
        $data = DB::fetch_all($sql);
        $staftIds = [];
        foreach ($data as $value) {
            $staftIds[$value['id']] = $value['username'];
        }
        return $staftIds;
    }
}
?>
