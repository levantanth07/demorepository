<?php

class ShippingHistoryForm extends Form
{

    function __construct(){
        Form::Form('ShippingHistoryForm');
    }

    function draw()
    {
        $data = [];
        $data['title'] = 'Lịch sử đơn hàng';
        $shipping_statuses = [];
        $shipping_info = [];
        $order_carrier_logs = [];
        $products = [];
        $prepaid = 0;
        if (Url::get('id')) {
            $order_id = Url::get('id');
            $orderInfo = AdminOrdersDB::getOrdersInfoById($order_id);
            $products = AdminOrdersDB::get_order_product($order_id, true, false);
            $prepaidData = AdminOrdersDB::getTotalPrepaid($order_id);
            if($prepaidData) $prepaid = $prepaidData['total_prepaid'];

            // get from api
            $data_request = array("order_id" => $order_id);
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/carrier-log', $data_request);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                $order_carrier_logs = $dataRes['data']['logs'];
                $shipping_info = $dataRes['data']['transport_detail'];
            }
        } else {
            echo '';die;
        }
        $data['orderInfo'] = $orderInfo;
        $data['prepaid'] = $prepaid;
        $data['products'] = $products;
        $data['shipping_statuses'] = $shipping_statuses;
        $data['shipping_info'] = $shipping_info;
        $data['order_carrier_logs'] = $order_carrier_logs;
        $data['shipping_costs_config'] = null;

        $this->parse_layout('shipping_history', $data);
    }
}