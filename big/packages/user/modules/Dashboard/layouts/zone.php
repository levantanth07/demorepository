<?php $title = 'Biểu đồ thống kê bán hàng theo tỉnh thành';?>
<style type="text/css">
    .input-group{
        margin: 0 5px;
    }
</style>
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
    <form name="DashboardForm" method="post">
        <div class="box box-default">
            <div class="box-header">Tổng số đơn hàng: [[|total|]]</div>
            <div class="box-body" style="position: relative;overflow: hidden;height: 650px;">
                <div class="row">
                    <div class="col-md-6 pull-right">
                        <div class="row">
                            <div class="col-xs-12" style="display: flex">
                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2">Tháng</span>
                                    <select name="month" id="month" class="form-control" aria-describedby="sizing-addon2" onchange="DashboardForm.submit();"></select>
                                </div>

                                <div class="input-group">
                                    <span class="input-group-addon" id="sizing-addon2">Năm</span>
                                    <select name="year" id="year" class="form-control" aria-describedby="sizing-addon2" onchange="DashboardForm.submit();"></select>
                                </div>
                                <div class="input-group">
                                    <button class="btn btn-success" type="submit">Xem báo cáo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <?php if(!empty($_REQUEST['form_block_id'])): ?>
                    <div id="chartContainer"></div>
                <?php else: ?>
                    <div style="display: flex; width: 100%; justify-content: center; padding: 40px; ">Vui lòng nhấn nút <b style="padding: 0 5px;">Xem báo cáo</b></div>
                <?php endif; ?>
                <div style="background:#FFF;height:50px;width:100%;position: absolute;bottom:0px;"></div>
            </div>
        </div>
    </form>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function () {
        Highcharts.chart('chartContainer', {
            chart: {
                height: 500,
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: '<?=$title?>'
            },
            subtitle: {
                text: ''
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}% / {point.y} đơn</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>:<br>{point.percentage:.1f} %<br>Số đơn: {point.y}'
                    },
                }
            },
            series: [{
                name: 'Số đơn',
                colorByPoint: true,
                data: [
                    <!--LIST:items-->
                    {
                        name: '[[|items.name|]]',
                        y: [[|items.qty|]],
                        sliced: true,
                        selected: true
                    },
                    <!--/LIST:items-->
                ]
            }]
        });
	});
</script>