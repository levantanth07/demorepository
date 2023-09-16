<?php
    $chart_items = [[=chart_items=]];
?>
<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<fieldset id="toolbar">
    <div style="background:#FFF;margin: 10px;padding:10px;">
        <form name="OrderProductForm" method="post" class="form-inline" autocomplete="off">
            <div class="row">
                <div class="col-md-10">
                    <div class="row">
                        <div class="form-group" style="border:1px solid #CCC;padding:2px;background-color: #EFEFEF;">
                            <label>Thời gian: </label>
                            <div class="form-group">
                                <select name="year" id="year" class="form-control"></select>
                            </div>
                        </div>
                        <input name="view_report" type="submit" class="btn btn-primary" value="Xem biểu đồ">
                    </div>
                </div>
                <div class="col-md-2 text-right">
                  <a href="index062019.php?page=admin-shop&cmd=report" class="btn btn-default">Quay lại</a>
                </div>
            </div>
        </form>
    </div>
</fieldset>
<div class="panel">
    <div class="panel-body" id="reportForm">
    <!--IF:report_cond(!empty([[=chart_items=]]))-->
        <?php
            $title = '';
            if (Url::get('year')) {
                $title .= 'Năm '. Url::get('year');
            }
            // System::debug($items);
        ?>

        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/data.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <div style="padding:20px;" id="hidden-print">
            <div id="container" style="min-width: 310px; min-height: 400px; margin: 0 auto"></div>
            <script>
                Highcharts.chart('container', {
                  chart: {
                    type: 'column'
                  },
                  title: {
                    text: 'Báo cáo doanh thu'
                  },
                  subtitle: {
                    text: '<?= $title ?>'
                  },
                  xAxis: {
                    categories: [
                      'T1',
                      'T2',
                      'T3',
                      'T4',
                      'T5',
                      'T6',
                      'T7',
                      'T8',
                      'T9',
                      'T10',
                      'T11',
                      'T12'
                    ],
                    crosshair: true
                  },
                  yAxis: {
                    min: 0,
                    title: {
                      text: 'Doanh thu (VNĐ)'
                    }
                  },
                  tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                      '<td style="padding:0"><b>{point.y:,.0f} VNĐ</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                  },
                  plotOptions: {
                    column: {
                      pointPadding: 0.2,
                      borderWidth: 0
                    }
                  },
                  series: <?= json_encode($chart_items, JSON_UNESCAPED_UNICODE) ?>
                });
            </script>
        </div>
    <!--ELSE-->
  <div class="alert text-center">Không có dữ liệu !</div>
  <!--/IF:report_cond-->
</div>