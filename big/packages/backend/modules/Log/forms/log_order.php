<?php
class LogOrderForm extends Form
{
    function __construct()
    {
        Form::Form('LogOrderForm');
    }
    function on_submit()
    {

    }
    function draw()
    {
        if(!User::is_admin()){
            Url::js_redirect(false,'Bạn không có quyền truy cập!');
        }
        $this->parse_layout('log_order');
    }
}
?>
