<?php

class ShopShippingAddress extends Module
{

    function __construct($row)
    {
        Module::Module($row);
        require_once 'db.php';
        require_once 'packages/vissale/modules/AdminOrders/config.php';

        switch (Url::get('do')) {
            case 'list':
                require_once "forms/list.php";
                $this->add_form(new ShopShippingAddressForm());
                break;
            
            default:
                require_once "forms/list.php";
                $this->add_form(new ShopShippingAddressForm());
                break;
        }
    }
}