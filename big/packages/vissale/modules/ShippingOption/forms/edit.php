<?php

class ShippingOptionsForm extends Form
{
    function __construct()
    {
        Form::Form('ShippingOptionsForm');
        require_once ROOT_PATH.'packages/vissale/modules/AdminOrders/config.php';
        $this->link_css('assets/default/css/cms.css');
        // $this->add('products.name',new TextType(true,'Chưa nhập tên sản phẩm',0,255));
    }

    function on_submit()
    {
        //send setting to api
        if (isset($_REQUEST['mi_product'])) {
            $data = new \stdClass;
            $group_id = Session::get('group_id');
            $data->shop_id = $group_id;
            $data->settings = array();
            foreach ($_REQUEST['mi_product'] as $key => $record) {
                $item = new \stdClass;
                $item->carrier_id = $record['carrier_id'];
                $item->carrier_email = $record['carrier_email'];
                $item->shop_name = $record['shop_name'];
                $item->name = $record['name'];
                $item->client_id = $record['client_id'];
                $item->token = $record['token'];
                $item->barcode_prefix = $record['barcode_prefix'];
                $item->is_default = (isset($record['is_default']) && $record['is_default']) ? 1 : 0;
                $del = (isset($record['del']) && $record['del']) ? 1 : 0;
                if ($del === 0) {
                    array_push($data->settings, $item);
                }
            }
        }

        // get from api
        $data_request = $data;
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/update', $data_request);
        //

        //
//        try {
//            $config_costs = AdminOrdersConfig::get_list_shipping_costs();
//            $config_ghn = $config_costs['api_ghn'];
//            if (isset($_REQUEST['mi_product'])) {
//                foreach ($_REQUEST['mi_product'] as $key=>$record) {
//                    if ($record['id']=='(auto)') {
//                        $record['id'] = false;
//                    }
//
//                    $record['carrier_id'] = (isset($record['carrier_id']) and $record['carrier_id']) ? $record['carrier_id'] : '';
//                    $record['client_id'] = (isset($record['client_id']) and $record['client_id']) ? $record['client_id'] : '';
//                    $record['token'] = (isset($record['token']) and $record['token']) ? $record['token'] : '';
//                    $record['is_default'] = (isset($record['is_default']) and $record['is_default']) ? 1 : '0';
//                    $record['del'] = (isset($record['del']) and $record['del']) ? 1 : '0';
//                    if ($record['id'] and DB::exists_id('shipping_options',$record['id'])) {
//                        if ($record['carrier_id'] == "api_ghn") {
//                            if ($this->excute_set_config_client_ghn($config_ghn['token'], $record['token'])) {
//                                DB::update('shipping_options', $record, 'id='.$record['id']);
//                            }
//                        } else {
//                            DB::update('shipping_options', $record, 'id='.$record['id']);
//                        }
//                    } else {
//                        unset($record['id']);
//                        $record['group_id'] = Session::get('group_id');
//                        $record['user_id'] = DB::fetch('select id FROM users WHERE username="'. Session::get('user_id') .'"','id');
//                        if ($record['carrier_id'] == "api_ghn") {
//                            if ($this->excute_set_config_client_ghn($config_ghn['token'], $record['token'])) {
//                                $record['id'] = DB::insert('shipping_options', $record);
//                            }
//                        } else {
//                            // System::debug($record); die();
//                            $record['id'] = DB::insert('shipping_options', $record);
//                        }
//                    }
//                }
//            }
//
//            if (URL::get('deleted_ids')) {
//                $ids = explode(',',URL::get('deleted_ids'));
//                foreach($ids as $id){
//                    DB::delete_id('shipping_options', $id);
//                }
//            }
//
//            Url::js_redirect(true);
//        } catch (\Exception $e) {
//            // System::debug($e->getMessage());
//        }
    }

    function excute_set_config_client_ghn($token, $tokenClient) {
        $config_costs = AdminOrdersConfig::get_list_shipping_costs();
        $config_ghn = $config_costs['api_ghn'];
        $params = array (
            'token' => $token,
            'TokenClient' => [
                $tokenClient
            ],
            'ConfigCod' => true,
            'ConfigReturnData' => true,
            'URLCallback' => 'https://big.shopal.vn/work-auth/shipping.php?token=85a7ada570bc8d6cd9fa01f2248f5574&cmd=ghn_callback',
            'ConfigField' => [
                'CoDAmount' => true,
                'CurrentWarehouseName' => true,
                'CustomerID' => true,
                'CustomerName' => true,
                'CustomerPhone' => true,
                'Note' => true,
                'OrderCode' => true,
                'ServiceName' => true,
                'ShippingOrderCosts' => true,
                'Weight' => true,
                'ExternalCode' => true,
                'ReturnInfo' => true
            ],
            'ConfigStatus' => [
                'ReadyToPick' => true,
                'Picking' => true,
                'Storing' => true,
                'Delivering' => true,
                'Delivered' => true,
                'WaitingToFinish' => true,
                'Return' => true,
                'Returned' => true,
                'Finish' => true,
                'LostOrder' => true,
                'Cancel' => true
            ]
        );
        $response_api = AdminOrdersConfig::execute_curl($config_ghn['api_set_config_client'], json_encode($params));
        $response_api = json_decode($response_api, true);
        if ($response_api['code']) {
            return true;
        }

        return false;
    }

    function draw()
    {
        // get from api
        $group_id = Session::get('group_id');
        $data_request = array('shop_id' => $group_id);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/get', $data_request);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            if (isset($dataRes['data']['settings']['settings'])) {
                $_REQUEST['mi_product'] = $dataRes['data']['settings']['settings'];
            } else {
                $_REQUEST['mi_product'] = array();
            }

            $carrier_options = '<option value="">Chọn hãng vận chuyển</option>';
            foreach ($dataRes['data']['brand'] as $brand) {
                $carrier_options .= '<option value="'. $brand['alias'] .'">'. $brand['name'] .'</option>';
            }
            $data['carrier_options'] = $carrier_options;
            $data['is_owner'] = is_group_owner();
            $this->parse_layout('edit', $data);
        }
        //

        //
//        $data = array();
//        $group_id = Session::get('group_id');
//        $mi_product = DB::fetch_all("
//            SELECT id, carrier_id, client_id, token, is_default, name, barcode_prefix, del
//            FROM shipping_options
//            WHERE group_id = $group_id
//        ");
//        if (!empty($mi_product)) {
//            $_REQUEST['mi_product'] = $mi_product;
//        }
//
//        $config_costs = AdminOrdersConfig::get_list_shipping_costs();
//        $carrier_options = '<option value="">Chọn hãng vận chuyển</option>';
//        foreach ($config_costs as $key => $config) {
//            $carrier_options .= '<option value="'. $key .'">'. $config['name'] .'</option>';
//        }
//
//        $data['carrier_options'] = $carrier_options;
//        $this->parse_layout('edit', $data);
    }
}
