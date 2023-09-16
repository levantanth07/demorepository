<?php

class AdminShop extends Module
{
    function __construct($row)
    {
        if(User::can_admin(true,ANY_CATEGORY) or User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY)) {
            Module::Module($row);
            require_once('db.php');
            switch(URL::get('cmd'))
            {
                case 'shop':
                    require_once 'forms/shop.php';
                    $this->add_form(new ListShopForm());
                break;

                case "manager-packages":
                    require_once "forms/manager_packages.php";
                    $this->add_form(new ManagerPackageForm());
                break;

                case "report":
                    require_once "forms/report.php";
                    $this->add_form(new ReportForm());
                break;

                case "report-chart":
                    require_once "forms/report_chart.php";
                    $this->add_form(new ReportChartForm());
                break;

                case 'delete_shop':
                    require_once 'forms/shop.php';
                    $this->add_form(new ListShopForm());
                break;

                default:
                    require_once 'forms/shop.php';
                    $this->add_form(new ListShopForm());
                    break;
            }
        }
        else
        {
            URL::access_denied();
        }
    }
}

