<?php

class RegisterViettelPostForm extends Form
{
    function __construct()
    {
        Form::Form('RegisterViettelPostForm');
    }

    function draw()
    {
        $data = array();
        $zones = ShopShippingAddressDB::getProvinces();
        $zone_id_list = ['' => 'Tỉnh/Thành phố *'];
        if (!empty($zones)) {
            foreach ($zones as $zone) {
                $zone_id_list[$zone['id']] = $zone['name'];
            }
        }

        $data['zone_id_list'] = $zone_id_list;

        $this->parse_layout('register_viettel_post', $data);
    }
}
