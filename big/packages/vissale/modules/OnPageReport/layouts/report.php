
<style>
    table, th, td {
        border: 1px solid #eee;
        border-collapse: collapse;
    }
    th, td {
        padding: 5px;
        text-align: left;
    }
    th {
        text-align: center;
    }
    .header {
        background: #ddd;
        margin-top: 10px;
        text-align: center;
    }
</style>
<fieldset id="toolbar" style="background:#FFF;margin: 5px;padding:5px;">
    <form name="ReportForm" method="post" class="form-inline">
        <div class="row">
            <div class="col-md-10">
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
                    <select name="bundle_id" id="bundle_id" class="form-control"></select>
                </div>
                <div class="form-group">
                    <select name="group_id" id="group_id" class="form-control"></select>
                </div>
                <div class="form-group">
                    <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                </div>
            </div>
            <div class="col-md-2 text-right">
                <a href="#" onclick="printWebPart('reportForm');return false;" class="btn btn-default btn-lg"><i class="fa fa-print"></i> IN</a>
            </div>
        </div>
    </form>
</fieldset>
<div class="col-md-12">
    <div id="reportForm" style="background:#FFF;margin: 10px;padding:10px; ">
        <table width="100%" border="0" style=" margin-bottom: 10px; ">
            <tr>
                <div width="15%" style="text-align: left;">
                    <div>Ngày in: <?php echo date('d/m/Y')?></div>
                    <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                </div>
                <div width="85%" style="text-align: center;"><h2>Báo cáo trực page ngày [[|date_report|]]</h2></div>
            </tr>
        </table>

        <table style="width:100%">
            <?php if (!empty($_REQUEST['form_block_id'])): ?>
            <tr class="header">
                <th rowspan=2>STT</th>
                <th rowspan=2 colspan=1 >HỌ VÀ TÊN</th>
                <th rowspan=1 colspan=4 >SĐT MỚI</th>
                <th rowspan=1 colspan=4 >SĐT CŨ</th>
                <th rowspan=2 colspan=1>SĐT từ 22-24h</th>
                <th rowspan=1 colspan=4 >HUỶ </th>
                <th rowspan=2 colspan=1>Tổng</th>
            </tr>
            <tr class="header">
                <th>Ca sáng</th>
                <th>Ca chiều</th>
                <th>Ca tối</th>
                <th>Tổng ngày</th>

                <th>Ca sáng</th>
                <th>Ca chiều</th>
                <th>Ca tối</th>
                <th>Tổng ngày</th>

                <th>Ca sáng</th>
                <th>Ca chiều</th>
                <th>Ca tối</th>
                <th>Tổng huỷ</th>
            </tr>
            
                <!--LIST:reports_today-->
            <tr>
                <td>[[|reports_today.id|]]</td>
                <td>[[|reports_today.name|]]</td>
                <!--LIST:reports_today.shift_1-->
                <td class="text-center">[[|reports_today.shift_1.new_count|]]</td>
                <!--/LIST:reports_today.shift_1-->
                <!--LIST:reports_today.shift_2-->
                <td class="text-center">[[|reports_today.shift_2.new_count|]]</td>
                <!--/LIST:reports_today.shift_2-->
                <!--LIST:reports_today.shift_3-->
                <td class="text-center">[[|reports_today.shift_3.new_count|]]</td>
                <!--/LIST:reports_today.shift_3-->
                <td class="text-center">[[|reports_today.total_new_count|]]</td>
                <!--LIST:reports_today.shift_1-->
                <td class="text-center">[[|reports_today.shift_1.duplicated|]]</td>
                <!--/LIST:reports_today.shift_1-->
                <!--LIST:reports_today.shift_2-->
                <td class="text-center">[[|reports_today.shift_2.duplicated|]]</td>
                <!--/LIST:reports_today.shift_2-->
                <!--LIST:reports_today.shift_3-->
                <td class="text-center">[[|reports_today.shift_3.duplicated|]]</td>
                <!--/LIST:reports_today.shift_3-->
                <td class="text-center">[[|reports_today.total_duplicated|]]</td>
                <!--LIST:reports_today.shift_4-->
                <td class="text-center">[[|reports_today.shift_4.all_number|]]</td>
                <!--/LIST:reports_today.shift_4-->
                <!--LIST:reports_today.shift_1-->
                <td class="text-center">[[|reports_today.shift_1.cancel_count|]]</td>
                <!--/LIST:reports_today.shift_1-->
                <!--LIST:reports_today.shift_2-->
                <td class="text-center">[[|reports_today.shift_2.cancel_count|]]</td>
                <!--/LIST:reports_today.shift_2-->
                <!--LIST:reports_today.shift_3-->
                <td class="text-center">[[|reports_today.shift_3.cancel_count|]]</td>
                <!--/LIST:reports_today.shift_3-->
                <td class="text-center">[[|reports_today.total_cancel_count|]]</td>
                <td class="text-center">[[|reports_today.total_all_number|]]</td>
            </tr>
            <!--/LIST:reports_today-->
        </table>
        <hr>
        <div class="text-right" style="color:#f00;font-size:20px;">Tổng số chưa được chia: <strong>[[|total_not_assigned_yet|]]</strong></div>
        <br>
    <?php else: ?>
        <div style="display: flex; width: 100%; justify-content: center; padding: 40px; ">Vui lòng nhấn nút<strong style="padding: 0 5px;"> Xem báo cáo </strong></div>
    <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        $.fn.datepicker.defaults.format = "dd/mm/yyyy";
        jQuery('#date_from').datepicker();
        jQuery('#date_to').datepicker();
    });
</script>