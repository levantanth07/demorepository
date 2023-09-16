<?php

class ProcessSendFileEmailToWarehouse extends Form{
    protected $map;
    function __construct(){
        Form::Form('ProcessSendFileEmailToWarehouse');
        require_once 'packages/core/includes/utils/mailer/ConfigMailer.php';
        require_once "packages/vissale/modules/PrintTemplates/config.php";
        require_once "packages/vissale/lib/php/simple_html_dom.php";
    }
    function draw(){
        $groupId = Session::get('group_id');

        $orderIds = parse_id(Url::get('ids'));
        if (count($orderIds) > 100) {
            die('FALSE');
        }

        $orders = DB::fetch('SELECT count(*) as num_order FROM orders WHERE id IN (' . implode($orderIds, ',') . ') AND group_id = '. AdminOrders::$group_id);
        if($orders['num_order'] != count($orderIds)){
            die('FALSE');
        }

        $orderIds = implode($orderIds, ',');

        $checkPhone = 0;
        if (is_group_owner() || AdminOrders::$quyen_indon) {
            $checkPhone = Url::get('checkPhone');
        }
        $paperSize = DB::escape(Url::get('paperSize'));
        $print_types = prints_type();
        $type_print_id = $print_types['DON_HANG']['id'];
        $cond_print_system = [];
        $cond_print_system[] = "type = $type_print_id";
        $cond_print_system['paper_size'] = "AND paper_size = '$paperSize'";
        $cond_print = $cond_print_system;
        $cond_print[] = "AND group_id = $groupId";
        $template_obj = AdminOrdersDB::getPrintTemplate(implode(" ", $cond_print));
        $isSettingMedidoc = AdminOrdersDB::isSettingMedidoc();
        $arrReturn = array();

        if (!empty($template_obj)) {
            $template = $template_obj['data'];
        } else {
            $template_system_obj = AdminOrdersDB::getPrintTemplate(implode(" ", $cond_print_system));
            if (empty($template_system_obj)) {
                unset($cond_print_system['paper_size']);
                $template_system_obj = AdminOrdersDB::getPrintTemplate(implode(" ", $cond_print_system));
            }
            $template = $template_system_obj['data'];
        }
        $print_constants = prints_constant();
        $constants = [];
        foreach ($print_constants as $key => $value) {
            if (in_array('DON_HANG', $value['types'])) {
                $constants[$key] = $value;
            }
        }
        $data_request = array();
        $info_senders = array();
        $info_senders_data = array();
        $info_receiver_data = array();
        $ordersShippingType = array();
        if ($paperSize !== 'A4-A5-NGANG-NEW') {
            $this->map['print_constants'] = $constants;
            $info_senders = AdminOrdersDB::getInfoSenderByOrderId();
            $this->map['info_senders'] = $info_senders;
        } else {
            $data_request = array('order_arr' => $orderIds);
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/get', $data_request);
            if ($dataRes['status_code'] === 200 && $dataRes['success']) {
                if (count($dataRes['data']) > 0) {
                    foreach ($dataRes['data'] as $orderDetail) {
                        $order_id = $orderDetail['order']['order_id'];
                        $info_senders[$order_id] = array();
                        if ($orderDetail['carrier']['alias'] === 'api_jt') {
                            $info_senders[$order_id]['bar_code'] = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['bar_code_qr'] = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['postal_bar_code'] = '';
                        } elseif ($orderDetail['carrier']['alias'] === 'api_ghn' || $orderDetail['carrier']['alias'] === 'api_ghn_v2') {
                            $info_senders[$order_id]['bar_code'] = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['bar_code_qr'] = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['postal_bar_code'] = '';
                        } elseif ($orderDetail['carrier']['alias'] === 'api_best') {
                            $info_senders[$order_id]['bar_code'] = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['postal_bar_code'] = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$orderDetail['order']['routecode'].'"><br /> <center>'.$orderDetail['order']['routecode'].'</center></div>';
                            $info_senders[$order_id]['bar_code_qr'] = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$orderDetail['order']['billcode'].'"><br /> <center>'.$orderDetail['order']['billcode'].'</center></div>';
                            $info_senders[$order_id]['postal_bar_code_qr'] = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$orderDetail['order']['routecode'].'"><br /> <center>'.$orderDetail['order']['routecode'].'</center></div>';
                        } else {
                            $info_senders[$order_id]['postal_bar_code'] = '';
                            $info_senders[$order_id]['postal_code'] = '';
                        }

                        $itemsvalue = ($orderDetail['order']['ordertype'] === 2) ? 0 : $orderDetail['detail']['itemsvalue'];
                        $inquiryFee = ($orderDetail['order']['paytype'] === 2) ? 0 : $orderDetail['detail']['inquiryFee'];
                        $info_senders[$order_id]['price_not_shipping'] = $itemsvalue;
                        $info_senders[$order_id]['total_price'] = number_format($itemsvalue + $inquiryFee);
                        if ($inquiryFee > 0){
                            $info_senders[$order_id]['shipping_price'] = number_format($inquiryFee);
                        }else{
                            $info_senders[$order_id]['shipping_price'] = 0;
                        }

                        $arrNote = array('CHOTHUHANG' => 'Cho thử hàng', 'CHOXEMHANGKHONGTHU' => 'Cho xem hàng không thử', 'KHONGCHOXEMHANG' => 'Không cho xem hàng');
                        $info_senders[$order_id]['shipping_note'] = (!isset($arrNote[$orderDetail['detail']['note']])) ? null : $arrNote[$orderDetail['detail']['note']];
                        $info_senders[$order_id]['delivery_time'] = (!isset($orderDetail['order']['delivery_time'])) ? null : date('d/m/Y H:i', strtotime($orderDetail['order']['delivery_time']));
                        $info_senders[$order_id]['sort_code'] = (!isset($orderDetail['order']['sort_code'])) ? null : "Sort code: " . $orderDetail['order']['sort_code'];
                        $info_senders[$order_id]['image_url'] = '/assets/standard/images/tuha_logo.png?v=03122021';
                        $info_senders_data[$order_id] = $orderDetail['detail']['sender'];
                        $info_receiver_data[$order_id] = $orderDetail['detail']['receiver'];
                        $ordersShippingType[$order_id] = $orderDetail['carrier']['alias'];
                    }
                }
            }
            if (count($info_senders_data) > 0) {
                foreach ($info_senders_data as $order_id => $sender) {
                    $info_senders[$order_id]['name_sender'] = $sender['name'];
                    $info_senders[$order_id]['phone_sender'] = $sender['phone'];
                    $info_senders[$order_id]['address_sender'] = $sender['address'];
                }
            }
            if (count($info_receiver_data) > 0) {
                foreach ($info_receiver_data as $order_id => $receiver) {
                    $info_senders[$order_id]['name'] = $receiver['name'];
                    $info_senders[$order_id]['phone'] = $receiver['phone'];
                    $info_senders[$order_id]['address'] = $receiver['address'];
                }
            }
        }
        $items = AdminOrdersDB::getItemSendEmail($groupId, $orderIds, false, true);
        if (count($items) > 0) {
            foreach ($items as $key => $value) {
                $postal_bar_code = $value['postal_code'];
                $postal_bar_code_qr = $value['postal_code'];
                $postal_bar_code_sub = $postal_bar_code;
                $postal_bar_code_sub_qr = $postal_bar_code_qr;
                if (strrpos($postal_bar_code, '.') > 0) {
                    $postal_bar_code_arr = explode('.', $postal_bar_code);
                    $postal_bar_code_sub = $postal_bar_code_arr[count($postal_bar_code_arr) - 1];
                    $postal_bar_code_sub = '<div style="text-align: center; padding-top: 10px"><img src="assets/lib/php-barcode-master/barcode.php?text='.$postal_bar_code_sub.'"><br /> <center>'.$postal_bar_code_sub.'</center></div>';
                    $postal_bar_code_sub_qr = $postal_bar_code_arr[count($postal_bar_code_arr) - 1];
                    $postal_bar_code_sub_qr = '<div style="text-align: center; padding-top: 10px"><img src="generate-qr.php?text='.$postal_bar_code_sub_qr.'"><br /> <center>'.$postal_bar_code_sub_qr.'</center></div>';
                }
                $items[$key]['postal_bar_code_sub'] = $postal_bar_code_sub;
                $items[$key]['postal_bar_code_sub_qr'] = $postal_bar_code_sub_qr;
                $items[$key]['bar_code_mediadoc_qr'] = '';

                $items[$key]['bar_code_qr'] = isset($value['bar_code']) ? $value['bar_code'] :"";
                $items[$key]['bar_code_id_qr'] = isset($value['bar_code_id']) ? $value['bar_code_id'] :"";;
                $items[$key]['bar_code_large_qr'] = isset($value['bar_code_large']) ? $value['bar_code_large']:"";;
                $items[$key]['postal_bar_code_qr'] = isset($value['postal_bar_code']) ?$value['postal_bar_code'] :"";;
                $items[$key]['postal_bar_code_large_qr'] = isset($value['postal_bar_code_large']) ?$value['postal_bar_code_large'] :"";;
            }
        }
        $bar_code_mediadoc = '';
        if($isSettingMedidoc){
            $bar_code_mediadoc = $this->getLinkMediadoc();
        }
        $this->map['bar_code_mediadoc'] = $bar_code_mediadoc;
        $this->map['template'] = $template;
        $this->map['paper_size'] = $paperSize;
        $this->map['ordersShippingType'] = $ordersShippingType;
        $this->map['getFullPhoneNumberPrintOrder'] = $checkPhone;
        $this->map['items'] = $items;
        return $this->parse_layout('content_send_file_warehouse',$this->map);
    }
    public function getLinkMediadoc()
    {
        $links = QRCODE_MEDIDOC_ENDPOINT.$this->getKeyMedidoc();
        return $links;
    }
    public function getKeyMedidoc()
    {
        $keyMediadoc = AdminOrdersDB::encode(['provider' => API_PROVIDER, 'group_id' => Session::get('group_id')]);
        return $keyMediadoc;
    }
}
?>
