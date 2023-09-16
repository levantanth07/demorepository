<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item">Báo cáo xử lý đơn hàng của nhân viên</li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <form name="ReportForm" method="post" class="form-inline">
                <div class="box-title">
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
                <div class="box-tools pull-right">
                    <a href="#" onclick="printWebPart('reportForm');return false;" class="btn btn-default btn-lg"><i class="fa fa-print"></i> IN</a>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div class="row" style="overflow: auto;width: 100%;">
                <div class="col-md-12" id="reportForm" style="background:#FFF;margin: 10px;padding:10px;width:100%;float:left;overflow:auto">
                    <!--IF:report_cond(!empty([[=reports=]]))-->
                    <table width="100%" border="0">
                        <tr>
                            <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                <div>Điện thoại: [[|phone|]]</div>
                                <div>Địa chỉ: [[|address|]]</div></th>
                            <th width="40%" style="text-align: center;"><h2>Báo cáo xử lý đơn hàng của nhân viên</h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
                            <th width="30%" style="text-align: right;">
                                <div>Ngày in: <?php echo date('d/m/Y')?></div>
                                <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                            </th>
                        </tr>
                    </table>
                    <table width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                        <tbody>
                        <!--LIST:reports-->
                        <tr <?php echo ([[=reports.id=]]=='label')?'style="font-weight:bold;background:#DDD;"':'';?>>
                            <td>[[|reports.username|]]</td>
                            <td>[[|reports.name|]]</td>
                            <!--LIST:status-->
                            <td align="right">
                                <!--IF:cond(isset($this->map['reports']['current'][[[=status.id=]]]))-->
                                <?php echo $this->map['reports']['current'][[[=status.id=]]];?>
                                <!--/IF:cond-->
                            </td>
                            <!--/LIST:status-->
                            <td align="right"><?php echo ([[=reports.id=]]!='label')?System::display_number([[=reports.total=]]):'Tổng';?></td>
                        </tr>
                        <!--/LIST:reports-->
                        </tbody>
                    </table>
                    <div style="padding:5px;text-align:right;">Tổng: <strong>[[|total|]]</strong></div>
                    <br>
                    <!--ELSE-->
                    <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
                    <!--/IF:report_cond-->
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
</div>