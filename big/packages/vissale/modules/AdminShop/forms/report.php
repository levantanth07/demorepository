<?php

class ReportForm extends Form
{

    function __construct()
    {
        Form::Form('ReportForm');
    }

    function draw()
    {
        $data = [];
        $title_no_result = 'Vui lòng nhấn nút Xem báo cáo';
        $items = [];
        $cond = [];
        if(!Url::get('date_to')){
            $_REQUEST['date_to'] = date('t/m/Y');
        }

        if(!Url::get('date_from')){
            $_REQUEST['date_from'] = date('01/m/Y');
        }

        $start_time = date('Y-m-d', Date_Time::to_time($_REQUEST['date_from']));
        $cond[] = " AND pg.billing_at >= '$start_time'";

        $end_time = date('Y-m-d', Date_Time::to_time($_REQUEST['date_to']));
        $cond[] = " AND pg.billing_at <= '$end_time'";

        if (Url::get('package_id')) {
            $cond[] = "AND pg.package_id = " . Url::get('package_id');
        }

        $chart_items = [];
        if (Url::get('view_report')) {
            $items = AdminShopDB::getTotalPricePackages($cond);
            if (empty($items)) {
                $title_no_result = 'Không có dữ liệu';
            } else {
                foreach ($items as $item) {
                    $chart_items[] = [
                        'name' => $item['name'],
                        'y' => (int) $item['total_price']
                    ];
                }
            }
        }

        $packages = AdminShopDB::getPackages();
        $package_id_list = ['' => 'Chọn gói cước'];
        if (!empty($packages)) {
            foreach ($packages as $value) {
                $package_id_list[$value['id']] = $value['name'];
            }
        }

        $data['package_id_list'] = $package_id_list;
        $data['title_no_result'] = $title_no_result;
        $data['acc_packages'] = $packages;
        $data['items'] = $items;
        $data['chart_items'] = $chart_items;
        // System::debug($data['package_id_list']); die();

        $this->parse_layout('report', $data);
    }
}
