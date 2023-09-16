<?php

class ShippingProcessingFormV3 extends Form
{

    function __construct(){
        Form::Form('ShippingProcessingFormV3');
    }

    function draw()
    {
//        echo 'step1-'.time().'<br>';
        $data = [];
        $title = 'Trạng thái kết nối giữa TUHA và đơn vị Vận chuyển';
        $cond = [];
        $arrFilter = array();
        require_once 'packages/core/includes/utils/paging.php';
        $item_per_page = Url::get('number_page') ? DB::escape(Url::get('number_page')) : 30;
        $page_no = Url::get('page_no') ? DB::escape(Url::get('page_no')) : 1;

        $groups = [];
        $group_id = '';
        if (User::is_admin()) {
            $is_admin = 1;
//            $groups = AdminOrdersDB::getGroups();
            if (Url::post('group_id')) {
                $group_id = DB::escape(Url::post('group_id'));
            }
        } else {
            $group_id = AdminOrders::$group_id;
        }

        if (!empty($group_id)) {
            $cond[] = "AND jobs.group_id = $group_id";
            $arrFilter['shop_id'] = $group_id;
        }

        if (Url::get('carrier_id')) {
            $cond[] = "AND jobs.carrier_id = '". DB::escape(Url::get('carrier_id')) ."'";
            $arrFilter['carrier_id'] = DB::escape(Url::get('carrier_id'));
        }

        if (Url::get('search_text')) {
            $search_text = DB::escape(Url::get('search_text'));
            $search_text = explode(',', $search_text);
            $search_text = implode("','",$search_text);
            $cond[] = "AND (jobs.lading_code IN ('$search_text') OR jobs.order_id IN ('$search_text'))";
            $arrFilter['search_text'] = DB::escape(Url::get('search_text'));
        }

        if (Url::get('start_date')) {
            $start_date = date('Y-m-d', Date_Time::to_time(DB::escape(Url::get('start_date'))));
            $cond[] = "AND DATE_FORMAT(jobs.created_at, '%Y-%m-%d') >= '$start_date'";
            $arrFilter['createdAt_from'] = Date_Time::to_time(DB::escape(Url::get('start_date')));
        }

        if (Url::get('end_date')) {
            $end_date = date('Y-m-d', Date_Time::to_time(DB::escape(Url::get('end_date'))));
            $cond[] = "AND DATE_FORMAT(jobs.created_at, '%Y-%m-%d') <= '$end_date'";
            $arrFilter['createdAt_to'] = Date_Time::to_time(DB::escape(Url::get('end_date')));
        }

        if (Url::get('shipping_option_id')) {
            // $cond[] = "AND so.id = '". Url::get('shipping_option_id') ."'";
            $arrFilter['setting_id'] = DB::escape(Url::get('shipping_option_id'));
        }
        if (Url::get('status_id')) {
            $cond[] = "AND jobs.status_id = " . DB::escape(Url::get('status_id'));
            $arrFilter['shipping_status'] = DB::escape(Url::get('status_id'));
        }

        $total = 0;
        $order_shippings = array();
        // get from api
        $data_request = array_merge($arrFilter, array("page" => $page_no, "limit" => $item_per_page, "all" => 0));
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/list-transport-logs', $data_request);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $order_shippings = $dataRes['data']['data'];
            $total = $dataRes['data']['total'];
        }
        //

//        $order_shippings = AdminOrdersDB::getOrderShippingProcessings($cond, $item_per_page);
//        $total = AdminOrdersDB::getTotalOrderShippingsProcessing($cond);
        $paging = paging($total, $item_per_page,10,false,'page_no',
            array('group_id','cmd','item_per_page','search_text' ,'carrier_id', 'id', 'start_date', 'end_date')
        );

        $shipping_options = array();
        $shipping_config_costs = array();
        // get from api
//        echo 'step3-'.time().'<br>';
        $data_request = array("shop_id" => ($group_id) ? $group_id : 1135);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/get', $data_request);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $shipping_config_costs = $dataRes['data']['brand'];
            $shipping_options = $dataRes['data']['settings']['settings'];
        }
        if (!$group_id) {
            $shipping_options = array();
        }
        //
//        echo 'step4-'.time().'<br>';
        $status_id_list = array();
        $status_jobs_config = array(1 => 'Đang xử lý', 2 => 'Thành công', 3 => 'Thất bại');
//        $status_jobs_config = AdminOrdersConfig::config_jobs_status_text();
        $status_id_list = ['' => 'Chọn trạng thái'];
        foreach ($status_jobs_config as $k => $v) {
            $status_id_list[$k] = $v;
        }

        $carrier_id_list = ['' => 'Hãng vận chuyển'];
//        $shipping_config_costs = AdminOrdersConfig::get_list_shipping_costs();
        foreach ($shipping_config_costs as $key => $brand) {
            $carrier_id_list[$brand['alias']] = $brand['name'];
        }

//        $shipping_options = AdminOrdersDB::getShippingOptionsActive();
        $shipping_option_list = ['' => 'Tài khoản vận chuyển'];
        if (!empty($shipping_options)) {
            foreach ($shipping_options as $key => $value) {
                $shipping_option_list[$value['_id']] = $value['name'];
            }
        }
//        echo 'step5-'.time().'<br>';
        $this->parse_layout('shipping_process_v3', [
            'groups' => $groups,
            'title' => $title,
            'carrier_id_list' => $carrier_id_list,
            'order_shippings' => $order_shippings,
            'status_jobs_config' => $status_jobs_config,
            'shipping_option_list' => $shipping_option_list,
            'paging' => $paging,
            'total_current' => count($order_shippings),
            'total' => $total,
            'status_id_list' => $status_id_list,
        ]);
    }
}