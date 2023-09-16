<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<?php $title = 'Báo cáo doanh thu nhân viên '.(Url::iget('type')?((Url::iget('type')==1)?'SALE':((Url::iget('type')==2)?'CSKH':'Đặt lại')):'');?>
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
    <div class="box box-default">
        <div class="box-header">
            <form name="ReportForm" method="post" class="form-inline">
                <div class="row">
                    <div class="col-xs-10">
                        <div class="form-group">
                            <label><i class="fa fa-calendar"></i> </label>
                        </div>
                        <div class="form-group">
                            <input name="date_from" type="text" id="date_from" class="form-control" style="width: 100px;" placeholder="Từ ngày">
                        </div>
                        <div class="form-group">
                            <input name="date_to" type="text" id="date_to" class="form-control" style="width: 100px;" placeholder="đến ngày">
                        </div>
                        <div class="form-group">
                            <select name="source_id" id="source_id" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="upsale_from_user_id" id="upsale_from_user_id" style="width: 180px;" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="type" id="type" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="is_active" id="is_active" class="form-control" style="width: 150px;"></select>
                        </div>
                        <div class="form-group">
                            <select name="account_group_id" id="account_group_id" class="form-control"></select>
                        </div>
                        <!--IF:cond(Session::get('account_type')==3)-->
                        <div class="form-group">
                            <select name="group_id" id="group_id" class="form-control"></select>
                        </div>
                        <!--/IF:cond-->
                    </div>
                    <div class="col-xs-2 pull-right text-right">
                        <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default"><i class="fa fa-print"></i> IN</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="reportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;height:1000px;">
                        <!--IF:cond(sizeof([[=reports=]])>1)-->
                        <table width="100%" border="0">
                            <tr>
                                <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                    <div>Điện thoại: [[|phone|]]</div>
                                    <div>Địa chỉ: [[|address|]]</div></th>
                                <th width="40%" style="text-align: center;">
                                    <h3 style="text-transform: uppercase;">
                                        <?=$title?>
                                    </h3>
                                    <div>(Chỉ dành cho nhân viên sale)</div>
                                    <div>
                                        Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?>
                                    </div>
                                </th>
                                <th width="30%" style="text-align: right;">
                                    <div>Ngày in: <?php echo date('d/m/Y')?></div>
                                    <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                                </th>
                            </tr>
                        </table>
                        <table id="ReportTable" width="100%" bordercolor="#999" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse;">
                            <tbody>
                            <!--LIST:reports-->
                            <!--IF:cond([[=reports.id=]]=='label')-->
                            <tr style="font-weight:bold;background:#DDD;">
                                <td rowspan=2 style="min-width: 130px;">[[|reports.name|]]</td>
                                <!--LIST:status-->
                                <td colspan=2 align="center"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['name'];?></td>
                                <!--/LIST:status-->
                                <td nowrap="" style="background:#dd4649;color:#fff;">% hoàn</td>
                                <td nowrap="" style="background:#9bdd99;">% th.công</td>
                            </tr>
                            <tr>
                                <!--LIST:status-->
                                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['qty'];?></td>
                                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['total_price'];?></td>
                                <!--/LIST:status-->
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                            </tr>
                            <!--ELSE-->
                            <tr>
                                <?php
                                    $total_return = 0;
                                    $total_transport = [[=reports.chuyen_hang=]];
                                    $total_success = 0;
                                ?>
                                <td>[[|reports.name|]]</td>
                                <!--LIST:status-->
                                <?php
                                    if([[=status.id=]]==CHUYEN_HOAN){
                                        $total_return = $this->map['reports']['current'][[[=status.id=]]][1]['qty'];
                                    }
                                    if([[=status.id=]]==THANH_CONG){
                                        $total_success = $this->map['reports']['current'][[[=status.id=]]][1]['qty'];
                                    }
                                ?>
                                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['qty'];?></td>
                                <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['total_price'];?></td>
                                <!--/LIST:status-->
                                <td class="text-center" title="[[|reports.chuyen_hang|]] đơn chuyển hàng">
                                    <?=($total_transport>0)?(round($total_return/$total_transport,2)*100).'%':'';?>
                                </td>
                                <td class="text-center" title="[[|reports.chuyen_hang|]] đơn chuyển hàng">
                                    <?=($total_transport>0)?(round($total_success/$total_transport,2)*100).'%':'';?>
                                </td>
                            </tr>
                            <!--/IF:cond-->
                            <!--/LIST:reports-->
                            <tr>
                                <td><strong>Tổng</strong></td>
                                <!--LIST:status-->
                                <td align="center" class="col"><strong><?php echo System::display_number([[=status.qty=]]);?></strong></td>
                                <td align="center" class="col"><strong><?php echo System::display_number([[=status.total=]]);?></strong></td>
                                <!--/LIST:status-->
                                <td class="text-center">x</td>
                                <td class="text-center">x</td>
                            </tr>
                            </tbody>
                        </table>
                        <!--ELSE-->
                        <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
                        <!--/IF:cond-->
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $.fn.datepicker.defaults.format = "dd/mm/yyyy";
        jQuery('#date_from').datepicker();
        jQuery('#date_to').datepicker();
    });
</script>