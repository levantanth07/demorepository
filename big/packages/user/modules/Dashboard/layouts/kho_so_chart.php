<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<?php
    $title = 'Biểu đồ kho số '.([[=sale=]]?'sale':'Marketing');
?>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item"><?=$title?></li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box box-default">
        <div class="box-header">
            <form name="ReportForm" method="post" class="form-inline">
                <div class="row">
                    <div class="col-xs-10">
                        <div class="form-group">
                            <label>Năm: </label>
                        </div>
                        <div class="form-group">
                            <select name="year" id="year" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="user_id" id="user_id" class="form-control"></select>
                        </div>
                        <!--IF:cond(Session::get('account_type')==TONG_CONG_TY)-->
                        <div class="form-group">
                            <select name="assigned_type" id="assigned_type" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="group_id" id="group_id" class="form-control" required oninvalid="this.setCustomValidity('Bạn vui lòng chọn công ty.')" oninput="setCustomValidity('')"></select>
                        </div>
                        <!--/IF:cond-->
                        <div class="form-group">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem biểu đồ">
                        </div>
                    </div>
                    <div class="col-xs-2 text-right">
                        <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default btn-lg"><i class="fa fa-print"></i> IN</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12" id="reportForm">
                    <table width="100%" border="0">
                        <tr>
                            <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                <div>Điện thoại: [[|phone|]]</div>
                                <div>Địa chỉ: [[|address|]]</div></th>
                            <th width="40%" style="text-align: center;"><h2><?=$title?></h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
                            <th width="30%" style="text-align: right;">
                                <div>Ngày in: <?php echo date('d/m/Y')?></div>
                                <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                            </th>
                        </tr>
                    </table>
                    <!--IF:cond(!empty([[=reports=]]))-->
                    <script src="https://code.highcharts.com/highcharts.js"></script>
                    <script src="https://code.highcharts.com/modules/data.js"></script>
                    <script src="https://code.highcharts.com/modules/exporting.js"></script>
                    <div class="col-xs-12">
                            <div id="doanhThuReportContainer" style="width: 100%; height: 400px; margin: 0 auto"></div>
                            <?php
                            $subtitle = '';
                            ?>
                            <script>
                                Highcharts.chart('doanhThuReportContainer', {
                                    chart: {
                                        zoomType: 'x'
                                    },
                                    title: {
                                        text: 'Biểu đồ số về'
                                    },
                                    subtitle: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories: <?= [[=kho_so_months=]] ?>
                                    },
                                    yAxis: {
                                        title: {
                                            text: 'Số lượng'
                                        }
                                    },
                                    legend: {
                                        enabled: true
                                    },
                                    plotOptions: {
                                        area: {
                                            fillColor: {
                                                linearGradient: {
                                                    x1: 0,
                                                    y1: 0,
                                                    x2: 0,
                                                    y2: 1
                                                },
                                                stops: [
                                                    [0, Highcharts.getOptions().colors[0]],
                                                    [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                                ]
                                            },
                                            marker: {
                                                radius: 2
                                            },
                                            lineWidth: 1,
                                            states: {
                                                hover: {
                                                    lineWidth: 1
                                                }
                                            },
                                            threshold: null
                                        }
                                    },
                                    series: <?=[[=kho_so_series=]];?>
                                });

                            </script>
                        </div>
                    <!--ELSE-->
                    <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
                    <!--/IF:cond-->
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
    });
</script>