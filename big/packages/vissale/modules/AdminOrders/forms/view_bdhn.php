<?php

class ViewBuuDienHNForm extends Form
{

    function __construct() {
        Form::Form('ViewBuuDienHNForm');
    }

    function draw()
    {
        $data = [];
        $data['title'] = 'Lịch sử đơn hàng';
        $products = [];
        $items = [];
        if (Url::get('postal_code')) {
            $postal_code = Url::get('postal_code');
            $cost_config = AdminOrdersConfig::get_list_shipping_costs();
            $bdhn_config = $cost_config['api_bdhn'];
            $soapclient = new \SoapClient($bdhn_config['url_tra_cuu_du_lieu']);
            $params = [
                'mabuugui' => $postal_code
            ];
            $response = $soapclient->TraCuuThongTinTrangThai($params);
            $xml    = str_replace(array("diffgr:","msdata:"),'', $response->TraCuuThongTinTrangThaiResult->any);
            // Wrap into root element to make it standard XML
            $xml    = "<package>".$xml."</package>";
            // Parse with SimpleXML - probably there're much better ways
            $response_data   = simplexml_load_string($xml);
            if ($response_data->diffgram->NewDataSet->Table1) {
                // System::debug($response_data->diffgram->NewDataSet); die();
                $items = $response_data->diffgram->NewDataSet;
            }
        }

        $data['items'] = $items;

        $this->parse_layout('view_bdhn', $data);
    }
}