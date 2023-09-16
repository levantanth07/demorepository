<?php

class ManagerShippingForm extends Form
{

    function __construct(){
        Form::Form('ManagerShippingForm');

        if (Url::get('action') == 'export-excel') {
            $objPHPExcel = new PHPExcel(); // Khởi tạo thư viện
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()
                         ->setCellValue('A1','STT')
                         ->setCellValue('B1','Mã đơn hàng')
                         ->setCellValue('C1','Mã vận đơn')
                         ->setCellValue('D1','Phí vận chuyển')
                         ->setCellValue('E1','Tổng tiền')
                         ->setCellValue('F1','Đơn vị vận chuyển')
                         ->setCellValue('G1','Ngày hoàn thành')
                         ;
            $cond = [];

            // $cond[] = "AND "
            if (Url::get('carrier_id')) {
                $cond[] = "AND os.carrier_id = '". DB::escape(Url::get('carrier_id')) ."'";
            }

            if (Url::get('shipping_status')) {
                $cond[] = "AND os.shipping_status = '". DB::escape(Url::get('shipping_status')) ."'";
            }

            if (Url::get('search_text')) {
                $cond[] = "AND (os.shipping_order_code LIKE '%". DB::escape(Url::get('search_text')) ."%' OR os.order_id LIKE '%". DB::escape(Url::get('search_text')) ."%')";
            }

            if (Url::get('start_date')) {
                $start_date = date('Y-m-d', Date_Time::to_time(DB::escape(Url::get('start_date'))));
                $cond[] = "AND DATE_FORMAT(os.completed_at, '%Y-%m-%d') >= '$start_date'";
            }

            if (Url::get('end_date')) {
                $end_date = date('Y-m-d', Date_Time::to_time(DB::escape(Url::get('end_date'))));
                $cond[] = "AND DATE_FORMAT(os.completed_at, '%Y-%m-%d') <= '$end_date'";
            }

            $order_shippings = AdminOrdersDB::getOrderShippings($cond);
            if (!empty($order_shippings)) {
                $i = 2;
                $shipping_config_costs = AdminOrdersConfig::get_list_shipping_costs();
                $total_service_fee = 0; $total_fee = 0;
                foreach ($order_shippings as $key => $shipping) {
                    $shipping_unit = $shipping_config_costs[$shipping['carrier_id']]['name'];
                    $date_complete = !empty($shipping['completed_at']) ? date('d-m-Y H:i:s', strtotime($shipping['completed_at'])) : "";
                    $total_fee = !empty($shipping['total_fee']) ? $total_fee : 0;
                    $objPHPExcel->setActiveSheetIndex(0)
                              ->setCellValue('A'.$i,$i-1)
                              ->setCellValue('B'.$i, $shipping['order_id'])
                              ->setCellValue('C'.$i, $shipping['shipping_order_code'])
                              ->setCellValue('D'.$i, $shipping['total_service_fee'])
                              ->setCellValue('E'.$i, $total_fee)
                              ->setCellValue('F'.$i, $shipping_unit)
                              ->setCellValue('G'.$i, $date_complete)
                              ;

                    $total_service_fee += $shipping['total_service_fee'];
                    $total_fee += $total_fee;

                    $i++;
                }

                $lastrow = count($order_shippings) + 2;
                $objPHPExcel->setActiveSheetIndex(0)
                              ->setCellValue('A'.$lastrow, 'Tổng')
                              ->setCellValue('D'.$lastrow, $total_service_fee)
                              ->setCellValue('E'.$lastrow, $total_fee)
                         ;
            }

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
            ob_end_clean();

            header('Content-Type: application/vnd.ms-excel');
            header("Content-Disposition: attachment;filename=doi_soat_". date('dmY') .".xlsx");

            $objWriter->save('php://output');
            return;
        }
    }

    function draw()
    {
//        $config = array(
//            "api_list_province_url" => "https://partner.viettelpost.vn/v2/categories/listProvince",
//            "api_list_district_url" => "https://partner.viettelpost.vn/v2/categories/listDistrict"
//        );
//        $params = [
//            'from_province_name' => 'Hà Nội',
//            'from_district_name' => 'Huyện Hoài Đức',
//            'to_province_name' => 'Hà Nội',
//            'to_district_name' => 'Quận Hai Bà Trưng'
//        ];
//        $response_params = AdminOrdersConfig::get_zones_viettel_id($params, $config);
//        print_r($response_params);die;

//        echo 'step1-'.time().'<br>';
        $data = [];
        $data['title'] = 'Đơn vận chuyển';
        $cond = [];
        $arrFilter = array();
        require_once 'packages/core/includes/utils/paging.php';
        $item_per_page = Url::get('number_page') ? Url::get('number_page') : 30;
        $page_no = Url::get('page_no') ? Url::get('page_no') : 1;

        $groups = [];
        $group_id = '';
        $is_admin = 0;
        if (User::is_admin()) {
            $is_admin = 1;
            $groups = AdminOrdersDB::getGroups();
            if (Url::get('group_id')) {
                $group_id = Url::get('group_id');
            }
        } else {
            $group_id = AdminOrders::$group_id;
        }

        if (!empty($group_id)) {
            $cond[] = "AND os.group_id = $group_id";
            $arrFilter['shop_id'] = $group_id;
        }

        if (Url::get('carrier_id')) {
            $cond[] = "AND os.carrier_id = '". DB::escape(Url::get('carrier_id')) ."'";
            $arrFilter['carrier_id'] = DB::escape(Url::get('carrier_id'));
        }
        if (Url::get('shipping_option_id')) {
            $cond[] = "AND so.id = '". DB::escape(Url::get('shipping_option_id')) ."'";
            $arrFilter['setting_id'] = DB::escape(Url::get('shipping_option_id'));
        }

        if (Url::get('shipping_status')) {
            $cond[] = "AND os.shipping_status = '". DB::escape(Url::get('shipping_status')) ."'";
            $arrFilter['shipping_status'] = DB::escape(Url::get('shipping_status'));
        }

        if (Url::get('pick_option')) {
            $cond[] = "AND os.pick_option = '". DB::escape(Url::get('pick_option')) ."'";
            $arrFilter['pick_option'] = DB::escape(Url::get('pick_option'));
        }

        if (Url::get('is_freeship')) {
            $cond[] = "AND os.is_freeship = '". DB::escape(Url::get('is_freeship')) ."'";
            $arrFilter['is_freeship'] = DB::escape(Url::get('is_freeship'));
            $arrFilter['is_freeship'] = ($arrFilter['is_freeship'] == 3) ? 0 : $arrFilter['is_freeship'];
        }

        if (Url::get('search_text')) {
            $search_text = DB::escape(Url::get('search_text'));
            $search_text = explode(',', $search_text);
            $search_text = implode("','",$search_text);
            // $cond[] = "AND (os.shipping_order_code LIKE '%". Url::get('search_text') ."%' OR os.order_id LIKE '%". Url::get('search_text') ."%')";
            $cond[] = "AND (os.shipping_order_code IN ('$search_text') OR os.order_id IN ('$search_text'))";
            $arrFilter['search_text'] = DB::escape(Url::get('search_text'));
        }

        if (Url::get('filter_date')) {
            $filter_date =DB::escape(Url::get('filter_date'));
            $arrFilter['filter_date'] =DB::escape(Url::get('filter_date'));
            if (Url::get('start_date')) {
                $start_date = date('Y-m-d', Date_Time::to_time(DB::escape(Url::get('start_date'))));
                $cond[] = "AND DATE_FORMAT(os.$filter_date, '%Y-%m-%d') >= '$start_date'";
                $arrFilter['start_date'] = $start_date;
            }
    
            if (Url::get('end_date')) {
                $end_date = date('Y-m-d', Date_Time::to_time(DB::escape(Url::get('end_date'))));
                $cond[] = "AND DATE_FORMAT(os.$filter_date, '%Y-%m-%d') <= '$end_date'";
                $arrFilter['end_date'] = $end_date;
            }
        }
//print_r($arrFilter);
        $is_freeship_list = [
            '' => 'Loại vận chuyển',
            3 => 'Thanh toán cuối tháng',
            1 => 'Không miễn phí vận chuyển',
            2 => 'Miễn phí vận chuyển'
        ];
        $data['is_freeship_list'] = $is_freeship_list;

        $pick_option_list = [
            '' => 'Lấy hàng tại điểm',
            'cod' => 'COD đến lấy hàng',
            'post' => 'Gửi hàng tại bưu cục'
        ];
        $data['pick_option_list'] = $pick_option_list;

        $data['groups'] = $groups;
        $data['group_id'] = $group_id;
        $data['is_admin'] = $is_admin;
        $data['user_id'] = DB::fetch('select id FROM users WHERE username="'. Session::get('user_id') .'"','id');
        $data['user_name'] = Session::get('user_id');

        $total = 0;
        $order_shippings = array();
        $statistic_orders = array();
//        $order_shippings = AdminOrdersDB::getOrderShippings($cond, $item_per_page);
        //get from api
//        echo 'step2-'.time().'<br>';
//        print_r($arrFilter);
        $data_request = array_merge($arrFilter, array("page" => $page_no, "limit" => $item_per_page, "all" => 1));
//        print_r($data_request);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/list-data', $data_request);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $total = $dataRes['data']['transport']['total'];
            $order_shippings = $dataRes['data']['transport']['data'];
            $statistic_orders = $dataRes['data']['transportStatus']['total'];
        }

//        $data_request = array_merge($arrFilter, array("page" => $page_no, "limit" => $item_per_page, "all" => 1));
//        $dataRes = EleFunc::cUrlPost('https://logistic-adapter.palvietnam.com/api/transport/list-order', $data_request);
//        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
//            $order_shippings = $dataRes['data']['data'];
//            $total = $dataRes['data']['total'];
//        }
        //

//        $statistic_orders = AdminOrdersDB::getStatisticOrderShipping($cond);
        //get from api
//        echo 'step3-'.time().'<br>';
//        $data_request = array_merge($arrFilter, array("all" => 1));
//        $dataRes = EleFunc::cUrlPost('https://logistic-adapter.palvietnam.com/api/transport/list-status', $data_request);
//        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
//            $statistic_orders = $dataRes['data']['total'];
//        }
        //

        $statistic_order = [
            'total' => $total,
            'total_fee' => 0,
            'total_value' => 0,
            'total_service_fee' => 0,
        ];

        if (count($statistic_orders) > 0)  {
            foreach ($statistic_orders as $statistic) {
                $statistic_order['total_fee'] += $statistic['total_fee'];
                $statistic_order['total_value'] += $statistic['total_value'];
            }
        }

//        if (!empty($statistic_orders)) {
//            foreach ($statistic_orders as $item) {
//                $statistic_order['total'] += (int)$item['total'];
//                $statistic_order['total_fee'] += (int)$item['total_fee'];
//                $statistic_order['total_service_fee'] += (int)$item['total_service_fee'];
//            }
//        }
        $total = $statistic_order['total'];
        $paging = paging($total, $item_per_page,10,false,'page_no',
            array('is_freeship','pick_option','group_id','shipping_option_id','filter_date','cmd','item_per_page','search_text' ,'carrier_id', 'shipping_status','id', 'start_date', 'end_date')
        );
        $excel_filter_array = ['search_text' ,'carrier_id', 'shipping_status', 'start_date', 'end_date'];
        $data['paging'] = $paging;
        $data['excel_filter_array'] = $excel_filter_array;

        $data['total'] = $statistic_order['total'];
        $data['total_current'] = count($order_shippings);
        $data['total_fee'] = $statistic_order['total_fee'];
        $data['total_value'] = $statistic_order['total_value'];
        $data['total_service_fee'] = $statistic_order['total_service_fee'];
        $data['statistic_orders'] = $statistic_orders;
        $data['filter_date_list'] = [
            '' => 'Lọc theo ngày',
            'completed_at' => 'Ngày hoàn thành',
            'created_at' => 'Ngày tạo',
            'picked_at' => 'Ngày lấy hàng',
            'delivered_at' => 'Ngày giao hàng',
            'return_at' => 'Ngày trả hàng',
            'cancel_at' => 'Ngày hủy',
        ];
        $data['number_page_list'] = [
            '' => 'Hiển thị',
            20 => 20,
            50 => 50,
            100 => 100,
            200 => 200
        ];

//        $shipping_config_status = AdminOrdersConfig::get_status_shipping();
//        $shipping_config_costs = AdminOrdersConfig::get_list_shipping_costs();
//        $shipping_status_list = ['' => 'Trạng thái vận chuyển'];
//        foreach ($shipping_config_status as $key => $value) {
//            $shipping_status_list[$key] = $value['name'];
//        }
//        $carrier_id_list = ['' => 'Hãng vận chuyển'];
//        foreach ($shipping_config_costs as $key => $value) {
//            $carrier_id_list[$key] = $value['name'];
//        }

        // lay option tu api
        $carrier_id_list = array('' => 'Hãng vận chuyển');
        $shipping_status_list = array('' => 'Trạng thái đơn hàng');
        $shipping_config_status = array();
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $shipping_config_status = $dataRes['data']['transportStatus']['status'];
            $shipping_config_costs = $dataRes['data']['transportStatus']['brand'];
            foreach ($shipping_config_status as $status) {
                if ($status['status'] === 1) continue;
                $shipping_status_list[$status['status']] = $status['name'];
            }
            foreach ($shipping_config_costs as $brand) {
                $carrier_id_list[$brand['alias']] = $brand['name'];
            }
        }

        $data['shipping_status_list'] = $shipping_status_list;
        $data['carrier_id_list'] = $carrier_id_list;

        $shipping_options = array();
        $shipping_config_costs = array();
        // get from api
//        echo 'step3-'.time().'<br>';
        $data_request = array("shop_id" => $group_id);
        $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/get', $data_request);
        if ($dataRes['status_code'] === 200 && $dataRes['success']) {
            $shipping_config_costs = $dataRes['data']['brand'];
            $shipping_options = $dataRes['data']['settings']['settings'];
        }
        //

//        $shipping_options = AdminOrdersDB::getShippingOptionsActive();
//        $shipping_option_id_list = ['' => 'Tài khoản vận chuyển'];
//        if (!empty($shipping_options)) {
//            foreach ($shipping_options as $key => $value) {
//                $shipping_option_id_list[$value['_id']['$oid']] = $value['name'];
//            }
//        }
        $shipping_option_list = ['' => 'Tài khoản vận chuyển'];
        if (!empty($shipping_options)) {
            foreach ($shipping_options as $key => $value) {
                $shipping_option_list[$value['_id']] = $value['name'];
            }
        }
        $data['shipping_option_list'] = $shipping_option_list;
//        $data['shipping_option_id_list'] = $shipping_option_id_list;
        $data['shipping_config_status'] = $shipping_config_status;
        $data['shipping_config_costs'] = AdminOrdersConfig::get_list_shipping_costs();
        $data['order_shippings'] = $order_shippings;
        if(AdminOrders::$quyen_van_don){
            $status_arr = AdminOrdersDB::get_status();
        }else{
            $status_arr = AdminOrdersDB::get_status_from_roles(AdminOrders::$user_id);
        }
        $data['order_status_id_list'] = [''=>'Trạng thái đơn hàng'] + MiString::get_list($status_arr);
//        echo 'step4-'.time().'<br>';
        $this->parse_layout('manager_shipping', $data);
    }
}