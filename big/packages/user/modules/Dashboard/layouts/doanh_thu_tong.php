<fieldset id="toolbar" style="background:#FFF;margin: 5px;padding:5px;">
    <div>
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
                        <select name="type" id="type" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <select name="is_active" id="is_active" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <select name="account_group_id" id="account_group_id" class="form-control"></select>
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
    </div>
</fieldset>
<div class="col-md-12">
    <div id="reportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;">
        <!--IF:cond(!empty([[=reports=]]))-->
        <table width="100%" border="0">
            <tr>
                <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                    <div>Điện thoại: [[|phone|]]</div>
                    <div>Địa chỉ: [[|address|]]</div></th>
                <th width="40%" style="text-align: center;"><h2>Báo cáo doanh thu tổng <?php echo Url::iget('type')?((Url::iget('type')==1)?'SALE':((Url::iget('type')==2)?'CSKH':'Đặt lại')):''?></h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
                <th width="30%" style="text-align: right;">
                    <div>Ngày in: <?php echo date('d/m/Y')?></div>
                    <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                </th>
            </tr>
        </table>
        <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
            <tbody>
            <!--LIST:reports-->
            <!--IF:cond([[=reports.id=]]=='label')-->
            <tr style="font-weight:bold;background:#DDD;">
                <td rowspan=2>[[|reports.name|]]</td>
                <!--LIST:status-->
                <td colspan=2 align="center"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['name'];?></td>
                <!--/LIST:status-->
            </tr>
            <tr>
                <!--LIST:status-->
                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['qty'];?></td>
                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['total_price'];?></td>
                <!--/LIST:status-->
            </tr>
            <!--ELSE-->
            <tr>
                <td>[[|reports.name|]]</td>
                <!--LIST:status-->
                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['qty'];?></td>
                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['total_price'];?></td>
                <!--/LIST:status-->
            </tr>
            <!--/IF:cond-->
            <!--/LIST:reports-->
            <tr>
                <td>Tổng</td>
                <!--LIST:status-->
                <td align="center" class="col"><strong><?php echo System::display_number([[=status.qty=]]);?></strong></td>
                <td align="center" class="col"><strong><?php echo System::display_number([[=status.total=]]);?></strong></td>
                <!--/LIST:status-->
            </tr>
            </tbody>
        </table>
        <!--ELSE-->
        <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
        <!--/IF:cond-->
        <br>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
    });
</script>