<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<div class="col-md-12">
    <div style="background:#FFF;margin: 10px;padding:10px;">
        <form name="ReportForm" method="post" class="form-inline">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Ngày</span>
                        <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày" aria-describedby="basic-addon1">
                        <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"> Post/page</span>
                        <input name="post_id" type=text id="post_id" class="form-control" placeholder="Post ID">
                        <select name="page_id" id="page_id" class="form-control"></select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group pull-right">
                        <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        <button type="button" class="btn btn-default" onclick=" ClickHereToPrint('ifrmPrint', 'reportForm');"><i class="fa fa-print"></i> In báo cáo</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="reportForm" style="background:#FFF;margin: 10px;padding:10px;">
        <!--IF:report_cond(!empty([[=reports=]]))-->
        <table width="100%" border="0">
            <tr>
                <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                    <div>Điện thoại: [[|phone|]]</div>
                    <div>Địa chỉ: [[|address|]]</div></th>
                <th width="40%" style="text-align: center;">
                    <h2>Báo Cáo Đơn Hàng Theo Ngày</h2>
                    <div style="padding:5px;">
                        <!--IF:cond(Url::get('date_from'))-->
                        Từ ngày <?php echo Url::get('date_from');?>
                        <!--/IF:cond-->
                        <!--IF:cond(Url::get('date_to'))-->
                        Đến ngày <?php echo Url::get('date_to');?>
                        <!--/IF:cond-->
                    </div>
                </th>
                <th width="30%" style="text-align: right;">
                    <div>Ngày: <?php echo date('d/m/Y')?></div>
                    <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                </th>
            </tr>
        </table>
        <table width="100%" class="table table-bordered" bordercolor="#000">
            <tbody>
            <?php $total=0;?>
            <!--IF:cond1(sizeof([[=reports=]]) > 1)-->
            <!--LIST:reports-->
            <tr <?php echo ([[=reports.id=]]=='label')?'style="font-weight:bold;background:#DDD;"':'';?>>
                <td><a href="https://facebook.com/[[|reports.fb_post_id|]]" target="_blank">[[|reports.customer_name|]]</a></td>
                <td>[[|reports.mobile|]]</td>
                <td>[[|reports.san_pham|]]</td>
                <td>[[|reports.phi_tu_van|]]</td>
                <td>[[|reports.chi_phi_chot|]]</td>
                <td>[[|reports.lieu_trinh|]]</td>
                <td>[[|reports.xac_nhan|]]</td>
                <td>[[|reports.huy|]]</td>
                <td>[[|reports.note|]]</td>
            </tr>
            <!--/LIST:reports-->
            <!--/IF:cond1-->
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