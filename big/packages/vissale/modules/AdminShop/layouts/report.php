<?php
    $packages = [[=acc_packages=]];
    $items = [[=items=]];
?>
<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<fieldset id="toolbar">
    <div style="background:#FFF;margin: 10px;padding:10px;">
        <form name="OrderProductForm" method="post" class="form-inline" autocomplete="off">
            <div class="row">
                <div class="col-xs-8">
                    <div class="row">
                        <div class="form-group" style="border:1px solid #CCC;padding:2px;background-color: #EFEFEF;">
                            <label>Thời gian: </label>
                            <div class="form-group">
                                <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày">
                            </div>
                            <div class="form-group">
                                <input name="date_to" type="text" id="date_to" class="form-control" placeholder="Đến ngày">
                            </div>
                            <div class="form-group">
                                <select name="package_id" id="package_id" class="form-control"></select>
                            </div>
                        </div>
                        <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                    </div>
                </div>
                <div class="col-xs-4 text-right">
                    <div class="input-group pull-right">
                        <a href="index062019.php?page=admin-shop&cmd=report-chart" class="btn btn-success" target="_blank" style="margin-right: 5px;">Xem biểu đồ</a>
                        <input type="button" value="In báo cáo" class="btn btn-default" onclick=" ClickHereToPrint('ifrmPrint', 'reportForm');">
                    </div>
                </div>
            </div>
        </form>
    </div>
</fieldset>
<div class="panel">
    <div class="panel-body" id="reportForm">
        <style type="text/css">
            .table td.tb-middle {
                vertical-align: middle;
            }

            @media print {
                .table td.tb-middle {
                    vertical-align: middle;
                }
                .text-center {
                    text-align: center
                }
                .row {
                    display: block;
                    clear: both;
                }
                .row:before, .row:after {
                    display: table;
                    content: " ";
                    clear: both;
                }
                .row:before, .row:after {
                    display: table;
                    content: " ";
                }
                .text-right {
                    text-align: right
                }
                .col-md-4 {
                    position: relative;
                    min-height: 1px;
                    width: 33%;
                    float: left;
                }
                h3 {
                    margin-top: 0px;
                    margin-bottom: 0px;
                }
                td {
                    padding-top: 10px;
                    padding-bottom: 10px;
                }
                #hidden-print {
                    display: none
                }
            }
        </style>
    <!--IF:report_cond(!empty([[=items=]]))-->
        <?php
            $title = '';
            if (!empty(Url::get('date_from'))) {
                $title .= 'Từ ngày '. date('d-m-Y', Date_Time::to_time($_REQUEST['date_from']));
            }

            if (!empty(Url::get('date_to'))) {
                $title .= ' Đến ngày '. date('d-m-Y', Date_Time::to_time($_REQUEST['date_to']));
            }
            // System::debug($items);
        ?>
        <div class="row" style="margin-bottom: 40px;">
            <div class="col-md-4">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div class="col-md-4 text-center">
                <h3>Báo cáo doanh thu phần mềm</h3>
                <div><?= $title ?></div>
            </div>
            <div class="col-md-4 text-right">
                <div>Ban hành theo QĐ số 114TC/QĐ <br> ngày 01-01-1995 của Bộ Tài Chính</div>
            </div>
        </div>
        <table class="table table-bordered" width="100%" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse" bordercolor="#999">
            <thead>
                <tr>
                    <th width="50">STT</th>
                <?php
                    foreach ($packages as $package):
                ?>
                    <th class="text-center"><?= $package['name'] ?></th>
                <?php
                    endforeach;
                ?>
                    <th class="text-center">Tổng</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                        $i = 1;
                    ?>
                    <td><?= $i++ ?></td>
                    <?php
                        $total_price = 0;
                        foreach ($packages as $package):
                            $price = 0;
                            foreach ($items as $item) {
                                if ($item['id'] == $package['id']) {
                                    $price = $item['total_price'];
                                }
                            }

                            $total_price += $price;
                    ?>
                        <td class="text-right"><?= number_format($price) ?></td>
                    <?php
                        endforeach;
                    ?>
                        <td class="text-right"><b><?= number_format($total_price) ?></b></td>
                </tr>
            </tbody>
        </table>

        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/data.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <!--IF:cond(Session::get('admin_group') or check_user_privilege('BC_DOANH_THU_NV'))-->
        <div style="padding:20px;" id="hidden-print">
            <div id="saleReportContainer" style="min-width: 310px; min-height: 400px; margin: 0 auto"></div>
            <script>
                Highcharts.chart('saleReportContainer', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: 'Biểu đồ',
                        useHTML: true
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true
                        }
                    },
                    series: [{
                        name: 'Tổng tiền',
                        colorByPoint: true,
                        data: <?= json_encode([[=chart_items=]], JSON_UNESCAPED_UNICODE) ?>
                    }]
                });
            </script>
        </div>
    <!--ELSE-->
  <div class="alert text-center"><?= [[=title_no_result=]] ?></div>
  <!--/IF:report_cond-->
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        $.fn.datepicker.defaults.format = "dd/mm/yyyy";
        jQuery('#date_from').datepicker();
        jQuery('#date_to').datepicker();
    });
</script>