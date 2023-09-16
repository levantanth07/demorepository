<?php
require_once ROOT_PATH . 'packages/core/includes/common/SystemsTree.php';
require_once 'packages/core/includes/utils/paging.php';
class ListUserAdminInforForm extends Form
{
    function __construct()
    {
        Form::Form('ListUserAdminInforForm');
        $this->link_css('assets/default/css/cms.css');
        $this->map = [];

    }
    function on_submit()
    {
        
    }
    function draw()
    {
        if (Session::get('group_id') == GROUP_ID_AN_NINH_SHOP || check_system_user_permission('tracuunhansu')) {
            
        } else {
            Url::js_redirect(false,'Bạn không có quyền truy cập');
        }

        $action['content'] = 'Xem danh sách';
        $action['type'] = 1;
        if ( defined('LOG_V2') && !empty(LOG_V2)) {
            addLogMongoSecurity($action);
        } else {
            addLogSecurityMysql($action);
        }
        $option_status = [
            "0" => "Chọn trạng thái",
            "1" => "Hoạt động",
            "2" => "Không hoạt động",
        ];
        $option_trung_nhan_su = [
            "" => "Nhân sự",
            "1" => "Không trùng nhân sự",
            "2" => "Trùng nhân sự",
        ];
        $option_cmnd = [
            "" => "CMTND/Căn cước",
            "1" => "Ảnh mặt trước",
            "2" => "Ảnh mặt sau",
            "3" => "Chưa nhập",
        ];
        $option_shk = [
            "" => "Sổ hộ khẩu",
            "1" => "Đã nhập",
            "2" => "Chưa nhập",
        ];
        $option_hosoxinviec = [
            "" => "Hồ sơ xin việc",
            "1" => "Đã nhập",
            "2" => "Chưa nhập",
        ];
        $option_hopdonghoptac = [
            "" => "Hợp đồng",
            "1" => "Đã nhập",
            "2" => "Chưa nhập",
        ];
        $option_bancamket = [
            "" => "Bản cam kết QC/Tư vấn",
            "1" => "Đã nhập",
            "2" => "Chưa nhập",
        ];
        $option_bangcap = [
            "" => "Bằng cấp",
            "1" => "Đã nhập",
            "2" => "Chưa nhập",
        ];
        $option_giaykhaisinh = [
            "" => "Giấy khai sinh",
            "1" => "Đã nhập",
            "2" => "Chưa nhập",
        ];
        $option_camketbaomat = [
            "" => "Cam kết bảo mật thông tin",
            "1" => "Đã nhập",
            "2" => "Chưa nhập",
        ];
        $option_giaykhamsuckhoe = [
            "" => "Giấy khám SK A3",
            "1" => "Đã nhập",
            "2" => "Chưa nhập",
        ];
        if(Session::get('group_id') == GROUP_ID_AN_NINH_SHOP){
            $ID = Systems::getOBDStructureID();
        } else if (check_system_user_permission('tracuunhansu')){
            $permission = 'tracuunhansu';
            $user_id = Session::get('user_data')['user_id'];
            $systemGroupId = DB::fetch('SELECT * FROM groups_system_account WHERE user_id = '.$user_id.' LIMIT 1');    
            $ID = Systems::getOBDStructureIDNew($systemGroupId['system_group_id']);
        } 
        $rootSystem = Systems::getByIDStructure($ID);
        $SystemsOptions = SystemsTree::buildSelectBox([$rootSystem]);
        $all = [
            '' => '<option value="0">Tất cả</option>'
        ];
        $date = new DateTime(date("Y-m-d"));
        $date->modify('-93 day');
        $tomorrowDATE = $date->format('Y-m-d');
        $selectBox = $all + $SystemsOptions;
        $this->map['page_no'] = page_no();
        $this->map['option_status'] = $option_status;
        $this->map['option_cmnd'] = $option_cmnd;
        $this->map['option_shk'] = $option_shk;
        $this->map['option_hosoxinviec'] = $option_hosoxinviec;
        $this->map['option_hopdonghoptac'] = $option_hopdonghoptac;
        $this->map['option_bancamket'] = $option_bancamket;
        $this->map['option_system_group'] = $selectBox;
        $this->map['end_date'] = date("Y-m-d");
        $this->map['start_date'] = $tomorrowDATE;
        $this->map['option_bangcap'] = $option_bangcap;
        $this->map['option_giaykhaisinh'] = $option_giaykhaisinh;
        $this->map['option_camketbaomat'] = $option_camketbaomat;
        $this->map['option_giaykhamsuckhoe'] = $option_giaykhamsuckhoe;
        $this->map['option_trung_nhan_su'] = $option_trung_nhan_su;
        $this->map['current_date'] = date('Y-m-d');
        $this->parse_layout('list',$this->map);
    }
}
?>
