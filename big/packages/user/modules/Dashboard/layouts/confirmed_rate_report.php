<?php //System::Debug($_REQUEST);die; ?>
<style>
    .bor{
        display: flex;
        flex-wrap: wrap;
    }    
</style>
<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item">Báo Cáo Tỷ lệ chốt</li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box box-default">
        <div class="box-body">
            <form name="ReportForm" method="post" class="form-inline">
            <div class="row">
                <div class="col-xs-10">
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
                    <!--IF:cond(Session::get('account_type')==TONG_CONG_TY)-->
                    <div class="form-group">
                        <select name="group_id" id="group_id" class="form-control"></select>
                    </div>
                    <!--/IF:cond-->
                </div>
                <div class="col-xs-2">
                    <div class="pull-right">
                        <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default"><i class="fa fa-print"></i> IN</a>
                    </div>
                    <div class="pull-right">
                        <input  name="view_report" type="submit" id="view_report" class="btn btn-primary" value="Xem báo cáo">
                    </div>
                </div>
            </div>
        </form>
            <?php if(!isset([[=show_reports_all=]])){ ?>
            <div class="alert alert-default">
                *Tỷ lệ chốt = ( số chốt / số chia)<br>
                *Tỷ lệ chốt thật = ( số chốt / số chia tiếp cận được)<br>
                *Tỷ lệ hủy = ( số hủy / số xác nhận)
            </div>
            <div class="bor" id="reportForm">
                <div class="col-md-6">
                    <div class="" style="padding: 20px 10px 0px 10px;font-size: 20px;"><div style="padding:6px;" class="alert alert-danger" role="alert">Hôm nay</div></div>
                    <div id="ConfirmedRateReportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;">
                        <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                            <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Được chia</th>
                                <th>Xác nhận</th>
                                <!--                        <th>Sản phẩm</th> -->
                                <th>Hủy</th>
                                <th>Tỷ lệ chốt</th>
                                <th>Tỷ lệ chốt thật</th>
                                <th>%Hủy</th>
                                <th>Doanh thu</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!--LIST:reports_today-->
                            <tr>
                                <td>[[|reports_today.name|]]</td>
                                <td>[[|reports_today.tong_so_chia|]]</td>
                                <td>[[|reports_today.so_chot|]]</td>
                                <!--                            <td>[[|reports_today.sp|]]</td>-->
                                <td>[[|reports_today.huy|]]</td>
                                <td>[[|reports_today.ty_le_chot|]]%</td>
                                <td>[[|reports_today.ty_le_chot_thuc|]]%</td>
                                <td>[[|reports_today.pthuy|]]%</td>
                                <td>[[|reports_today.doanh_thu|]]</td>
                            </tr>
                            <!--/LIST:reports_today-->
                            <tr class="text-bold">
                                <td>Tổng</td>
                                <td>[[|tong_so_chia_today|]]</td>
                                <td>[[|tong_so_chot_today|]]</td>
                                <td>[[|tong_so_huy_today|]]</td>
                                <td>[[|ty_le_chot_today|]]%</td>
                                <td>[[|ty_le_chot_that_today|]]%</td>
                                <td>[[|ty_le_huy_today|]]%</td>
                                <td>[[|tong_doanh_thu_today|]]</td>
                            </tr>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="" style="padding: 20px 10px 0px 10px;font-size: 20px;"><div style="padding:6px;" class="alert alert-success" role="alert">Hôm qua</div></div>
                    <div id="ConfirmedRateReportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;">
                        <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                            <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Được chia</th>
                                <th>Xác nhận</th>
                                <!--                        <th>Sản phẩm</th> -->
                                <th>Hủy</th>
                                <th>Tỷ lệ chốt</th>
                                <th>Tỷ lệ chốt thật</th>
                                <th>%Hủy</th>
                                <th>Doanh thu</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!--LIST:reports_ytd-->
                            <tr>
                                <td>[[|reports_ytd.name|]]</td>
                                <td>[[|reports_ytd.tong_so_chia|]]</td>
                                <td>[[|reports_ytd.so_chot|]]</td>
                                <!--                            <td>[[|reports_ytd.sp|]]</td>-->
                                <td>[[|reports_ytd.huy|]]</td>
                                <td>[[|reports_ytd.ty_le_chot|]]%</td>
                                <td>[[|reports_ytd.ty_le_chot_thuc|]]%</td>
                                <td>[[|reports_ytd.pthuy|]]%</td>
                                <td>[[|reports_ytd.doanh_thu|]]</td>
                            </tr>
                            <!--/LIST:reports_ytd-->
                            </tbody>
                            <tr class="text-bold">
                                <td>Tổng</td>
                                <td>[[|tong_so_chia_ytd|]]</td>
                                <td>[[|tong_so_chot_ytd|]]</td>
                                <td>[[|tong_so_huy_ytd|]]</td>
                                <td>[[|ty_le_chot_ytd|]]%</td>
                                <td>[[|ty_le_chot_that_ytd|]]%</td>
                                <td>[[|ty_le_huy_ytd|]]%</td>
                                <td>[[|tong_doanh_thu_ytd|]]</td>
                            </tr>
                        </table>
                        <br>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="" style="padding: 20px 10px 0px 10px;font-size: 20px;"><div style="padding:6px;" class="alert alert-danger" role="alert">Tháng này (01-<?=date('d/m')?>)</div></div>
                    <div id="ConfirmedRateReportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;">
                        <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                            <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Được chia</th>
                                <th>Xác nhận</th>
                                <!--                        <th>Sản phẩm</th> -->
                                <th>Hủy</th>
                                <th>Tỷ lệ chốt</th>
                                <th>Tỷ lệ chốt thật</th>
                                <th>%Hủy</th>
                                <th>Doanh thu</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!--LIST:reports_this_month-->
                            <tr>
                                <td>[[|reports_this_month.name|]]</td>
                                <td>[[|reports_this_month.tong_so_chia|]]</td>
                                <td>[[|reports_this_month.so_chot|]]</td>
                                <!--<td>[[|reports_this_month.sp|]]</td>-->
                                <td>[[|reports_this_month.huy|]]</td>
                                <td>[[|reports_this_month.ty_le_chot|]]%</td>
                                <td>[[|reports_this_month.ty_le_chot_thuc|]]%</td>
                                <td>[[|reports_this_month.pthuy|]]%</td>
                                <td>[[|reports_this_month.doanh_thu|]]</td>
                            </tr>
                            <!--/LIST:reports_this_month-->
                            <tr class="text-bold">
                                <td>Tổng</td>
                                <td>[[|tong_so_chia_this_month|]]</td>
                                <td>[[|tong_so_chot_this_month|]]</td>
                                <td>[[|tong_so_huy_this_month|]]</td>
                                <td>[[|ty_le_chot_this_month|]]%</td>
                                <td>[[|ty_le_chot_this_month|]]%</td>
                                <td>[[|ty_le_huy_this_month|]]%</td>
                                <td>[[|tong_doanh_thu_this_month|]]</td>
                            </tr>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="" style="padding: 20px 10px 0px 10px;font-size: 20px;"><div style="padding:6px;" class="alert alert-success" role="alert">Cùng kỳ tháng trước (01-<?=date('d').'/'.date('m',strtotime("-1 months"))?>)</div></div>
                    <div id="ConfirmedRateReportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;">
                        <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                            <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th>Được chia</th>
                                <th>Xác nhận</th>
                                <!--                        <th>Sản phẩm</th> -->
                                <th>Hủy</th>
                                <th>Tỷ lệ chốt</th>
                                <th>Tỷ lệ chốt thật</th>
                                <th>%Hủy</th>
                                <th>Doanh thu</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!--LIST:reports_last_month-->
                            <tr>
                                <td>[[|reports_last_month.name|]]</td>
                                <td>[[|reports_last_month.tong_so_chia|]]</td>
                                <td>[[|reports_last_month.so_chot|]]</td>
                                <!--                            <td>[[|reports_last_month.sp|]]</td>-->
                                <td>[[|reports_last_month.huy|]]</td>
                                <td>[[|reports_last_month.ty_le_chot|]]%</td>
                                <td>[[|reports_last_month.ty_le_chot_thuc|]]%</td>
                                <td>[[|reports_last_month.pthuy|]]%</td>
                                <td>[[|reports_last_month.doanh_thu|]]</td>
                            </tr>
                            <!--/LIST:reports_last_month-->
                            <tr class="text-bold">
                                <td>Tổng</td>
                                <td>[[|tong_so_chia_last_month|]]</td>
                                <td>[[|tong_so_chot_last_month|]]</td>
                                <td>[[|tong_so_huy_last_month|]]</td>
                                <td>[[|ty_le_chot_last_month|]]%</td>
                                <td>[[|ty_le_chot_last_month|]]%</td>
                                <td>[[|ty_le_huy_last_month|]]%</td>
                                <td>[[|tong_doanh_thu_last_month|]]</td>
                            </tr>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>
            </div>
            <?php }else{ ?>
            <div class="col-lg-12">
                <table width="100%" border="0">
                    <tr>
                        <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                            <div>Điện thoại: [[|phone|]]</div>
                            <div>Địa chỉ: [[|address|]]</div></th>
                        <th width="40%" style="text-align: center;">
                            <h2>Tỷ lệ chốt đơn</h2>
                            <div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div>
                        </th>
                        <th width="30%" style="text-align: right;">
                            <div>Ngày in: <?php echo date('d/m/Y')?></div>
                            <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                        </th>
                    </tr>
                </table>
            </div>
            <div class="col-md-12">
                <div id="ConfirmedRateReportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;">
                    <table id="ReportTable" width="100%" class="table table-bordered" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                        <thead>
                        <tr>
                            <th>Nhân viên</th>
                            <th>Được chia</th>
                            <th>Xác nhận</th>
                            <!--                    <th>Sản phẩm</th> -->
                            <th>Hủy</th>
                            <th>Tỷ lệ chốt</th>
                            <th>Tỷ lệ chốt thật</th>
                            <th>%Hủy</th>
                            <th>Doanh thu</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--LIST:reports_all-->
                        <tr>
                            <td>[[|reports_all.name|]]</td>
                            <td>[[|reports_all.tong_so_chia|]]</td>
                            <td>[[|reports_all.so_chot|]]</td>
                            <!--                        <td>[[|reports_all.sp|]]</td>-->
                            <td>[[|reports_all.huy|]]</td>
                            <td>[[|reports_all.ty_le_chot|]]%</td>
                            <td>[[|reports_all.ty_le_chot_thuc|]]%</td>
                            <td>[[|reports_all.pthuy|]]%</td>
                            <td>[[|reports_all.doanh_thu|]]</td>
                        </tr>
                        <!--/LIST:reports_all-->
                        <tr class="text-bold">
                            <td>Tổng</td>
                            <td>[[|tong_so_chia_all|]]</td>
                            <td>[[|tong_so_chot_all|]]</td>
                            <td>[[|tong_so_huy_all|]]</td>
                            <td>[[|ty_le_chot_all|]]%</td>
                            <td>[[|ty_le_chot_all|]]%</td>
                                <td>[[|ty_le_huy_all|]]%</td>
                            <td>[[|tong_doanh_thu_all|]]</td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                </div>
            </div>
            <?php }?>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
        <!--IF:cond(Session::get('account_type')==TONG_CONG_TY)-->
        $('#view_report').click(function(){
            if(to_numeric($('#group_id').val())==0){
                alert('Bạn vui lòng chọn công ty để xem báo cáo');
                return false;
            }
        });
        <!--/IF:cond-->
    });
</script>