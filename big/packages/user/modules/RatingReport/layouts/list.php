<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<?php
    $title = 'Báo cáo bộ phận Dịch vụ khách hàng';
?>
<div class="container full report">
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
                            <label>Thời gian: </label>
                        </div>
                        <div class="form-group">
                            <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <select name="account_group_id" id="account_group_id" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
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
                    <table width="100%" class="table table-bordered" bordercolor="#000" border="1" cellpadding="5" cellspacing="0">
                        <tbody>
                        <!--LIST:reports-->
                        <tr align="center" <?php echo ([[=reports.id=]]=='label')?'style="font-weight:bold;background:#5cbdf5;color:#fff;"':'';?>>
                            <td>[[|reports.name|]]</td>
                            <td>[[|reports.need_rate|]]</td>
                            <td>[[|reports.overdue|]]</td>
                            <td>[[|reports.rated|]]</td>
                            <td><?php echo ([[=reports.id=]]=='label')?'Tổng số':[[=reports.total_order=]];?></td>
                            <td>[[|reports.ty_le_xu_ly|]]</td>
                            <td>[[|reports.ty_le_chua_xu_ly|]]</td>
                        </tr>
                        <!--/LIST:reports-->
                        <tr align="center">
                            <td><strong>Tổng</strong></td>
                            <td><strong>[[|need_rate|]]</strong></td>
                            <td><strong>[[|overdue|]]</strong></td>
                            <td><strong>[[|rated|]]</strong></td>
                            <td><strong>[[|total_order|]]</strong></td>
                            <td>x</td>
                            <td>x</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
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