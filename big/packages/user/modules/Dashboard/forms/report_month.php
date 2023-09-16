<?php
class ReportMonthForm extends Form
{
    protected $map;
    function __construct()
    {
        Form::Form('ReportMonthForm');
        Portal::$extra_header .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
        $this->link_css('assets/vissale/BXHHTML/style.css?v=1.11');
        $this->link_css('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
    }
    function draw(){
        if(Dashboard::$group_id!=1279){
            //die('<div class="alert alert-warning">Tính năng đang trong quá trình bảo trì. Qúy khách vui lòng quay lại tính năng này sau. Xin lỗi quý khách vì sự bất tiện!</div>');
        }
        if (DashboardDB::checkPermissionBXHReport() == false) {
                Url::js_redirect(false,'Bạn không có quyền truy cập!');
            }   
        $this->parse_layout('report_month');
    }
}
?>