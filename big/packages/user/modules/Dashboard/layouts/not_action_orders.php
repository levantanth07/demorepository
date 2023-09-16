<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item">Báo cáo đơn hàng chưa xử lý</li>
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
                    <div class="col-md-10">
                        <div class="form-group">
                            <label>Thời gian: </label>
                        </div>
                        <div class="form-group">
                            <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2 text-right">
                        <div class="form-group">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        </div>
                        <a href="#" onclick="printWebPart('reportForm');return false;" class="btn btn-default"><i class="fa fa-print"></i> IN</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div class="row" style="overflow: auto;width: 100%;">
                <div class="col-md-12" id="reportForm">
                    <table width="100%" border="0">
                        <tr>
                            <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                <div>Điện thoại: [[|phone|]]</div>
                                <div>Địa chỉ: [[|address|]]</div></th>
                            <th width="40%" style="text-align: center;"><h2>Báo cáo đơn hàng chưa xử lý</h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
                            <th width="30%" style="text-align: right;">
                                <div>Ngày in: <?php echo date('d/m/Y')?></div>
                                <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                            </th>
                        </tr>
                    </table>
                    <!--IF:report_cond(!empty([[=reports=]]))-->
                    <table width="100%" class="table table-bordered" bordercolor="#CCC" border="1" cellspacing="0" cellpadding="5">
                        <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã đơn hàng</th>
                            <th>Số điện thoại</th>
                            <th>Khách hàng</th>
                            <th>Trạng thái</th>
                            <th>Kênh</th>
                            <th>NV tạo</th>
                            <th>Ngày tạo</th>
                            <th>Chia cho</th>
                            <th>Ngày chia</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=1;?>
                        <!--LIST:reports-->
                        <tr>
                            <td><?php echo $i++;?></td>
                            <td>[[|reports.id|]]</td>
                            <td>[[|reports.mobile|]]</td>
                            <td>[[|reports.customer_name|]]</td>
                            <td>[[|reports.status_name|]]</td>
                            <td>[[|reports.source_name|]]</td>
                            <td>[[|reports.user_created|]]</td>
                            <td>[[|reports.created|]]</td>
                            <td>[[|reports.user_assigned|]]</td>
                            <td>[[|reports.assigned|]]</td>
                        </tr>
                        <!--/LIST:reports-->
                        </tbody>
                    </table>
                    <div style="padding:5px;text-align:right;">Tổng: <strong>[[|total|]]</strong></div>
                    <br>
                    <!--ELSE-->
                    <?php if(!Url::get('view_report')){?>
                        <div class="alert alert-warning-custom text-center">Vui lòng nhấn nút Xem báo cáo</div>
                    <?php }else{?>
                        <div class="alert text-center">Chưa có dữ liệu phù hợp.</div>
                    <?php }?>
                    <!--/IF:report_cond-->
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