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
                        <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày">
                    </div>
                    <div class="form-group">    
                        <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày">
                    </div>
                    <div class="form-group">
                        <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                    </div>
                </div>
                <div class="col-md-3 text-right">
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
        <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
            <tbody>
                <tr>
                    <th rowspan="2" width="50" style=" vertical-align: middle;">STT</th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Tên sản phẩm</th>
                    <th colspan="3" style="text-align: center">Đơn chăm sóc</th>
                    <th colspan="3" style="text-align: center">Doanh số</th>
                </tr>
                <tr>
                    <td style="text-align: center">Số đơn chăm sóc</td>
                    <td style="text-align: center">Đánh giá của NVCSKH</td>
                    <td style="text-align: center">Đánh giá của KH</td>
                    <td style="text-align: center">Doanh số</td>
                    <td style="text-align: center">Doanh số CSKH</td>
                    <td style="text-align: center">% Doanh số CSKH/Doanh thu</td>
                </tr>
                <?php if (!empty($items)): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($items as $item): ?>
                         <?php
                            /*$phan_tram_doanh_so = 0;
                            $doanhthu = (float) $item['doanhthu'];
                            $doanhthu_cskh = (float) $item['doanhthu_cskh'];
                            if (!empty($doanhthu) && !empty($doanhthu_cskh)) {
                                $phan_tram_doanh_so = round($doanhthu_cskh/$doanhthu, 4) * 100 . ' %'; 
                            }*/
                        ?>
                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= $item['product_name'] ?></td>
                            <td style="text-align: center"><?= $item['total_order'] ?></td>
                            <td style="text-align: center"><?= (float) $item['staff_rate'] ?> %</td>
                            <td style="text-align: center"><?= (float) $item['customer_rate'] ?> %</td>
                            <td style="text-align: center"><?= number_format($item['doanh_thu']) ?></td>
                            <td style="text-align: center"><?= number_format($item['doanh_thu_cskh']) ?></td>
                            <td style="text-align: center"><?= $item['phan_tram_doanh_so'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center">Không có dữ liệu!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
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