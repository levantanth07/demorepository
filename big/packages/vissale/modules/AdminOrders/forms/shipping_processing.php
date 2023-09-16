<?php

class ShippingProcessingForm extends Form
{

    function __construct(){
        Form::Form('ShippingProcessingForm');
    }

    function draw()
    {
        $data = [];
        $title = 'Trạng thái kết nối giữa QLBH và đơn vị Vận chuyển';
        $cond = [];
        require_once 'packages/core/includes/utils/paging.php';
        $item_per_page = Url::get('number_page') ? Url::get('number_page') : 30;

        $groups = [];
        $group_id = '';
        if (User::is_admin()) {
            $is_admin = 1;
            $groups = AdminOrdersDB::getGroups();
            if (DB::escape(Url::post('group_id'))) {
                $group_id = DB::escape(Url::post('group_id'));
            }
        } else {
            $group_id = AdminOrders::$group_id;
        }

        if (!empty($group_id)) {
            $cond[] = "AND jobs.group_id = $group_id";
        }

        if (Url::get('carrier_id')) {
            $cond[] = "AND jobs.carrier_id = '".DB::escape( Url::get('carrier_id'))."'";
        }

        if (Url::get('search_text')) {
            $search_text = DB::escape(Url::get('search_text'));
            $search_text = explode(',', $search_text);
            $search_text = implode("','",$search_text);
            $cond[] = "AND (jobs.lading_code IN ('$search_text') OR jobs.order_id IN ('$search_text'))";
        }

        if (Url::get('start_date')) {
            $start_date = date('Y-m-d', Date_Time::to_time(Url::get('start_date')));
            $cond[] = "AND DATE_FORMAT(jobs.created_at, '%Y-%m-%d') >= '$start_date'";
        }

        if (Url::get('end_date')) {
            $end_date = date('Y-m-d', Date_Time::to_time(Url::get('end_date')));
            $cond[] = "AND DATE_FORMAT(jobs.created_at, '%Y-%m-%d') <= '$end_date'";
        }

        // if (Url::get('shipping_option_id')) {
            // $cond[] = "AND so.id = '". Url::get('shipping_option_id') ."'";
        // }
        if (Url::get('status_id')) {
            $cond[] = "AND jobs.status_id = " . DB::escape(Url::get('status_id'));
        }

        $order_shippings = AdminOrdersDB::getOrderShippingProcessings($cond, $item_per_page);
        $total = AdminOrdersDB::getTotalOrderShippingsProcessing($cond);
        $paging = paging($total, $item_per_page,10,false,'page_no',
            array('group_id','cmd','item_per_page','search_text' ,'carrier_id', 'id', 'start_date', 'end_date')
        );

        $status_jobs_config = AdminOrdersConfig::config_jobs_status_text();
        $status_id_list = ['' => 'Chọn trạng thái'];
        foreach ($status_jobs_config as $k => $config) {
            $status_id_list[$k] = $config['name'];
        }

        $carrier_id_list = ['' => 'Hãng vận chuyển'];
        $shipping_config_costs = AdminOrdersConfig::get_list_shipping_costs();
        foreach ($shipping_config_costs as $key => $value) {
            $carrier_id_list[$key] = $value['name'];
        }

        $shipping_options = AdminOrdersDB::getShippingOptionsActive();
        $shipping_option_id_list = ['' => 'Tài khoản vận chuyển'];
        if (!empty($shipping_options)) {
            foreach ($shipping_options as $key => $value) {
                $shipping_option_id_list[$key] = $shipping_config_costs[$value['carrier_id']]['name'] . '-' . $value['name'];
            }
        }
        
        $this->parse_layout('shipping_process', [
            'groups' => $groups,
            'title' => $title,
            'carrier_id_list' => $carrier_id_list,
            'shipping_option_id_list' => $shipping_option_id_list,
            'order_shippings' => $order_shippings,
            'status_jobs_config' => $status_jobs_config,
            'paging' => $paging,
            'total_current' => count($order_shippings),
            'total' => $total,
            'status_id_list' => $status_id_list,
        ]);
    }
}