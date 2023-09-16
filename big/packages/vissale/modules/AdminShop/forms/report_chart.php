<?php

class ReportChartForm extends Form
{

    function __construct()
    {
        Form::Form('ReportChartForm');
    }

    function draw()
    {
        $data = [];
        $title_no_result = 'Vui lòng nhấn nút Xem báo cáo';
        $items = [];
        $cond = [];
        if(!Url::get('year')){
            $_REQUEST['year'] = date('Y');
        }

        $cond[] = "AND DATE_FORMAT(pg.billing_at, '%Y') = " . $_REQUEST['year'];
        $packages = AdminShopDB::getPackages();
        $chart_items = [];
        $items = AdminShopDB::getTotalPricePackagesByMonth($cond);
        // System::debug($items); die();
        if (!empty($items)) {
            foreach ($packages as $k => $package) {
                $chart_items[$k]['name'] = $package['name'];
                for ($i = 1; $i <= 12; $i++) {
                    $price = 0;
                    $date = sprintf("%'.02d", $i) . '-' . $_REQUEST['year'];
                    foreach ($items as $item) {
                        if ($item['id'] == $package['id'] && $item['date_at'] == $date) {
                            $price = (int) $item['total_price'];
                        }
                    }

                    $chart_items[$k]['data'][] = $price;
                }
            }
        }

        $year_list = ['' => 'Chọn năm'];
        for ($i = date('Y') - 4; $i <= date('Y'); $i++) { 
            $year_list[$i] = $i;
        }

        $data['year_list'] = $year_list;
        $data['title_no_result'] = $title_no_result;
        $data['items'] = $items;
        $data['chart_items'] = array_values($chart_items);

        $this->parse_layout('report_chart', $data);
    }
}
