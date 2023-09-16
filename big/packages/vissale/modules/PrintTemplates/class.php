<?php

class PrintTemplates extends Module
{

    function __construct($row)
    {
        Module::Module($row);
        if (!(Session::get('admin_group') || User::is_admin())) {
            URL::access_denied();
        }
        
        require_once 'db.php';
        require_once 'config.php';

        switch (Url::get('cmd')) {
            case "update_template":
                require_once "forms/update_template.php";
                $this->add_form(new UpdateTemplateForm());
            break;
            
            default:
                require_once "forms/print.php";
                $this->add_form(new PrintForm());
                break;
        }
    }
}