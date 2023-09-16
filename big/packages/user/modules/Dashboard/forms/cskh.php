<?php

class CSKHForm extends Form
{

    function __construct()
    {
        Form::Form('CSKHForm');
    }

    function draw()
    {
        if(!checkPermissionAccess(['BC_DOANH_THU_NV','CSKH']) && !Dashboard::$xem_khoi_bc_sale){
            Url::access_denied();
        }
        $data = [];
        $items = [];
        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('01/m/Y');
        }

        if(!Url::get('date_to')) {
            $_REQUEST['date_to'] = date('d/m/Y');
        }
        $total_date = Date_Time::count_day(Date_Time::to_time(Url::get('date_from')),Date_Time::to_time(Url::get('date_to')));
        if($total_date>31){
            die('<div class="alert alert-danger">Bạn vui lòng chọn khoảng thời gian trong tối đa một tháng.</div>');
        }
        if (Url::get('view_report')) {
            $start_time = date('Y-m-d', Date_Time::to_time($_REQUEST['date_from']));
            $end_time = date('Y-m-d', Date_Time::to_time($_REQUEST['date_to']));
            $cond = " AND o.confirmed >= '". $start_time ." 00:00:00' AND  o.confirmed <= '". $end_time ." 23:59:59' ";
            // $items = DashboardDB::getOrderProductsCskh($cond);
            $results = DashboardDB::getOrderProductsCskhOther($cond);
            if (!empty($results)) {
                $records = [];
                foreach ($results as $result) {
                    $records[$result['product_id']][] = $result;
                }
                foreach ($records as $k => $record) {
                    $total_order = 0;
                    $staff_has_value = 0; $customer_has_value = 0;
                    $staff_no_value = 0; $customer_no_value = 0;
                    $total_staff_has_value = 0; $total_customer_has_value = 0;
                    $doanh_thu = 0; $doanh_thu_cskh = 0; $phan_tram_doanh_so = 0;
                    foreach ($record as $item) {
                        if ($item['staff_rate'] > 0) {
                            $staff_has_value++;
                            $total_staff_has_value += $item['staff_rate'];
                        } else {
                            $staff_no_value++;
                        }

                        if ($item['customer_rate'] > 0) {
                            $customer_has_value++;
                            $total_customer_has_value += $item['customer_rate'];
                        } else {
                            $customer_no_value++;
                        }

                        $doanh_thu += (int)$item['doanh_thu'];
                        $doanh_thu_cskh += (int)$item['doanh_thu_cskh'];
                        $total_order++;
                    }

                    $avg_staff_rate = 0;
                    if ($staff_has_value > 0) {
                        $avg_staff_rate = round($total_staff_has_value / $staff_has_value, 4);
                    }

                    if ($staff_no_value > 0) {
                        $avg_staff_rate = round((($avg_staff_rate * $staff_no_value) + $total_staff_has_value) / $total_order, 4);
                    }

                    $avg_customer_rate = 0;
                    if ($customer_has_value > 0) {
                        $avg_customer_rate = round($total_customer_has_value / $customer_has_value, 4);
                    }

                    if ($customer_no_value > 0) {
                        $avg_customer_rate = round((($avg_customer_rate * $customer_no_value) + $total_customer_has_value) / $total_order, 4);
                    }

                    if (!empty($doanh_thu) && !empty($doanh_thu_cskh)) {
                        $phan_tram_doanh_so = round($doanh_thu_cskh/$doanh_thu, 4) * 100 . ' %'; 
                    }

                    $items[$k]['product_name'] = $record[0]['product_name'];
                    $items[$k]['total_order'] = $total_order;
                    $items[$k]['staff_rate'] = $avg_staff_rate;
                    $items[$k]['customer_rate'] = $avg_customer_rate;
                    $items[$k]['doanh_thu'] = $doanh_thu;
                    $items[$k]['doanh_thu_cskh'] = $doanh_thu_cskh;
                    $items[$k]['phan_tram_doanh_so'] = $phan_tram_doanh_so;
                }
            }
        }

        $data['items'] = $items;

        $this->parse_layout('cskh', $data);
    }
}
