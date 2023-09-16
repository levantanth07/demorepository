<style>
    .date-control {
        max-width: 100px;
    }
</style>
<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<fieldset id="toolbar" style="background:#FFF;margin: 5px;padding:5px;">
    <div>
        <form name="ReportForm" method="post" class="form-inline">
            
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <label>Thời gian: </label>
                    </div>    
                    <div class="form-group">    
                        <input name="date_from" type="text" id="date_from" class="form-control date-control" placeholder="Từ ngày">
                    </div>
                    <div class="form-group">    
                        <input name="date_to" type="text" id="date_to" class="form-control date-control" placeholder="đến ngày">
                    </div>
                    <div class="form-group">
                        <select name="bundle_id" id="bundle_id" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <select name="account_id" id="account_id" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <select name="view_id" id="view_id" class="form-control"></select>
                    </div>
                    <!--IF:cond(Session::get('account_type')==3)-->
                    <div class="form-group">
                        <select name="group_id" id="group_id" class="form-control"></select>
                    </div>
                    <!--/IF:cond-->
                    <div class="form-group">
                        <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    <a href="index062019.php?page=adv_money" target="_blank" class="btn btn-link">Chi phí quảng cáo</a>
                    <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default btn-lg"><i class="fa fa-print"></i> IN</a>
                </div>
            </div>
        </form>
    </div>
</fieldset>
<?php
    $items = [[=items=]];
?>
<div class="col-md-12">
    <div id="reportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;">
        <!--IF:report_cond(Url::get('view_report'))-->
        <table width="100%" border="0">
            <tr>
                <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                    <div>Điện thoại: [[|phone|]]</div>
                    <div>Địa chỉ: [[|address|]]</div></th>
                <th width="40%" style="text-align: center;"><h2><?= [[=title=]] ?> <?php echo Url::iget('type')?((Url::iget('type')==1)?'SALE':((Url::iget('type')==2)?'CSKH':'Đặt lại')):''?></h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
                <th width="30%" style="text-align: right;">
                    <div>Ngày in: <?php echo date('d/m/Y')?></div>
                    <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                </th>
            </tr>
        </table>
        <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th></th>
                    <th style="text-align: center">Số điện thoại</th>
                    <th style="text-align: center">Chi phí quảng cáo</th>
                    <th style="text-align: center">Giá số</th>
                    <th style="text-align: center">Đơn</th>
                    <th style="text-align: center">Doanh thu</th>
                    <th style="text-align: center">CPQC/DT</th>
                    <th style="text-align: center">Tỷ lệ chốt</th>
                    <th style="text-align: center">Click</th>
                    <th style="text-align: center">CVR</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $k => $item): ?>
                        <tr>
                            <td><b><?= $item['name'] ?></b></td>
                            <td style="text-align: center"><?= $item['sdt'] ?></td>
                            <td style="text-align: center"><?= $item['chi_phi_qc'] ?></td>
                            <td style="text-align: center"><?= $item['gia_so'] ?></td>
                            <td style="text-align: center"><?= $item['so_don'] ?></td>
                            <td style="text-align: center"><?= $item['doanh_thu'] ?></td>
                            <td style="text-align: center"><?= $item['chi_phi_doanh_thu'] ?></td>
                            <td style="text-align: center"><?= $item['ty_le_chot'] ?></td>
                            <td style="text-align: center"><?= $item['click'] ?></td>
                            <td style="text-align: center"><?= $item['cvr'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                        <tr>
                            <td><b>TỔNG</b></td>
                            <td style="text-align: center"><b><?= [[=total_sdt=]] ?></b></td>
                            <td style="text-align: center"><b><?= number_format([[=total_chi_phi_qc=]]) ?></b></td>
                            <td style="text-align: center"><b><?= number_format([[=total_gia_so=]]) ?></b></td>
                            <td style="text-align: center"><b><?= [[=total_order=]] ?></b></td>
                            <td style="text-align: center"><b><?= number_format([[=total_doanh_thu=]]) ?></b></td>
                            <td style="text-align: center"><b><?=[[=total_chi_phi_doanh_thu=]]?>%</b></td>
                            <td style="text-align: center"><b><?= [[=avg_ty_le_chot=]] ?></b></td>
                            <td style="text-align: center"><b><?= number_format([[=total_click=]]) ?></b></td>
                            <td style="text-align: center"></td>
                        </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Chưa có dữ liệu!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <!--IF:report_cond_chart(!empty([[=chart_items=]]))-->
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
                    text: 'Biểu đồ báo cáo Marketing theo nguồn'
                  },
                  xAxis: {
                    categories: <?= json_encode([[=chart_cate=]], JSON_UNESCAPED_UNICODE) ?>,
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
                  series: <?= json_encode([[=chart_items=]], JSON_UNESCAPED_UNICODE) ?>
                });
            </script>
        </div>
      <!--/IF:report_cond_chart-->
        <!--ELSE-->
        <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
        <!--/IF:report_cond-->
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
    });
</script>