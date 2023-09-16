<?php

class SoQuy extends Module
{

    function __construct($row)
    {
        Module::Module($row);
        require_once 'db.php';
        if(check_user_privilege('ADMIN_KETOAN')) {
            switch (Url::get('do')) {
                case 'report':
                    require_once "forms/report.php";
                    $this->add_form(new ReportModuleForm());
                    break;

                default:
                    require_once "forms/report.php";
                    $this->add_form(new ReportModuleForm());
                    break;
            }
        }else{
            Url::js_redirect(true,'Bạn không có quyền truy cập tính năng này');
        }
    }

    static public function generateCode($bill_number){
        return strlen($bill_number)< 6 ? str_pad(($bill_number),6,"0",STR_PAD_LEFT): ($bill_number);
    }

    static public function generatePrefixType($bill_type)
    {
        if ($bill_type == '1') {
            return 'PT';
        }

        return 'PC';
    }
}