<?php
$reports = [[=reports=]];
    $total_assigned = 0; $total_reached = 0;
    $total_sale = 0; $total_care = 0;
    $total_reset = 0;
    $total_toi_uu = 0;
    $total_cancel = 0;
    $total_refund = 0;
    $total_transport = 0;
    $total_revenue_cancel = 0;
    $total_revenue_toi_uu = 0;
    $total_revenue_sale = 0; $total_revenue_care = 0;
    $total_revenue_reset = 0; $total_revenue_cancel = 0;
    $total_revenue_refund = 0; $total_revenue = 0 ; $total = 0;
    $total_rate_sale = 0; // Tỷ lệ doanh thu 1 đơn chốt mới
    $total_rate_sale_has_value = 0; // Tổng nhân viên có doanh thu 1 đơn chốt mới > 0
    $total_rate_on_phone = 0; // Tỷ lệ doanh thu trên 1 SĐT
    $total_rate_on_phone_has_value = 0; // Tổng nhân viên có doanh thu trên 1 SĐT > 0
    $total_rate_on_day = 0; //  Tỷ lệ doanh thu trên ngày công
    $total_rate_on_day_has_value = 0; //  Tổng nhân viên có doanh thu trên ngày công > 0
    $total_number = 0;
    $total_ty_le_tiep_can_has_value = 0; // Tỷ lệ tiếp cận trung bình
    $ty_le_tiep_can_has_value = 0; // Tổng tỷ lệ tiếp cận > 0
    $cm_tiep_can_has_value = 0; // CM/Tiếp cận > 0
    $total_cm_tiep_can_has_value = 0; // Trung bình CM/Tiếp cận > 0
    $cm_so_duoc_chia_has_value = 0; // CM/Số được chia > 0
    $total_cm_so_duoc_chia_has_value = 0; // Trung bình CM/Số được chia > 0
    $title = 'BÁO CÁO TỔNG HỢP SALE';
?>
<style>
    body {
        overflow-y: auto;
        height: auto;
    }
    .th-fixed {
        background: rgb(221, 221, 221);
        position: sticky;
        left: -11px;
        top: auto;
        white-space: normal;
        min-width: 150px;
    }
    thead tr td {
        position: sticky; top: 0;
        background: #ffffff;
    }
    #tbl-clone {
        height: 600px;
        overflow: scroll;
    }
    #table-action {
        position: sticky;
        top: 0
    }
    .table-bordered{border: 1px solid #666;}
    .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
        border: 1px solid #666;
    }
</style>
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
    <iframe src="" id="ifrmPrint" class="hidden"></iframe>
    <fieldset id="toolbar">
        <div>
            <form name="ReportForm" method="post" class="form-inline">
                <div class="row">
                    <div class="col-md-12">
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
                            <select name="is_active" id="is_active" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="account_group_id" id="account_group_id" class="form-control"></select>
                        </div>
                        <!--IF:cond(Session::get('account_type')==TONG_CONG_TY)-->
                        <div class="form-group">
                            <select name="group_id" id="group_id" class="form-control" required oninvalid="this.setCustomValidity('Bạn vui lòng chọn công ty.')" oninput="setCustomValidity('')"></select>
                        </div>
                        <!--/IF:cond-->
                        <div class="pull-right">
                            <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default"><i class="fa fa-print"></i> IN</a>
                        </div>
                        <div class="pull-right">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <p style="    padding: 2px 0px; margin: 0; font-style: italic; color: red;">
                            Chú ý: Đặt lại bao gồm doanh thu và số lượng các loại đơn Đặt lại 1,2, ...5
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </fieldset>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-body">
                    <div id="reportForm">
                        <style>
                            @media print {
                                #table-action {
                                    display: none
                                }
                            }
                        </style>
                        <!--IF:cond(!empty([[=reports=]]))-->
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
                        <div id="table-action" class="text-right">
                            <button class="btn btn-danger btn-prev" title="Kéo bảng sang trái"><i class="fa fa-arrow-circle-o-left"></i> Lùi lại</button>
                            <button class="btn btn-danger btn-next" title="Kéo bảng sang phải"><i class="fa fa-arrow-circle-o-right"></i> Xem tiếp</button>
                        </div>
                        <div id="tbl-clone">
                            <table id="ReportTable" width="100%" class="table table-bordered table-striped" bordercolor="#000" border="1" cellspacing="0" cellpadding="5">
                                <thead>
                                <?php $j = 0; $i = 0; ?>
                                <!--LIST:reports-->
                                <?php
                                $total_number_order = 0;
                                $total_revenue_order = 0;
                                ?>
                                <!--IF:cond([[=reports.id=]]=='label')-->
                                <tr style="font-weight:bold;background:#DDD;">
                                    <td rowspan=2 class="th-fixed">[[|reports.name|]]</td>
                                    <td rowspan=2>[[|reports.total_assigned|]]</td>
                                    <td rowspan=2>[[|reports.total_reached|]]</td>
                                    <td colspan="5" align="center">TỶ LỆ VỀ SỐ</td>
                                    <td colspan="7" align="center" style="background: #bbf2e0;">DOANH THU</td>
                                    <td colspan="3" align="center">TỶ LỆ DOANH THU</td>
                                    <td colspan="<?php echo 5+sizeof([[=status=]]);?>" align="center">TÌNH TRẠNG SỐ</td>
                                </tr>
                                <tr>
                                    <td>Tỷ lệ tiếp cận</td>
                                    <td>CM/ Tiếp cận</td>
                                    <td>CM/ Số được chia</td>
                                    <td style="color:#f00;">% Hủy</td>
                                    <td style="color:#f00;">% Hoàn</td>
                                    <td style="background: #bbf2e0;">CHỐT MỚI</td>
                                    <td style="background: #bbf2e0;">Chăm sóc</td>
                                    <td style="background: #bbf2e0;">Đặt lại</td>
                                    <td style="background: #bbf2e0;">Tối ưu</td>
                                    <td style="background: #bbf2e0;">Đơn hủy</td>
                                    <td style="background: #bbf2e0;">HOÀN</td>
                                    <td style="background: #bbf2e0;">TỔNG</td>
                                    <td>DTBQ/đơn chốt mới</td>
                                    <td>DTBQ/SĐT</td>
                                    <td>DTTB/ngày công</td>
                                    <td style="background: #e1d99b">Chốt mới</td>
                                    <td style="background: #e1d99b">Chăm sóc</td>
                                    <td style="background: #e1d99b">Đặt lại</td>
                                    <td style="background: #e1d99b">Tối ưu</td>
                                    <td align="center"  style="background: #e1d99b">TỔNG</td>
                                    <!--LIST:status-->
                                    <!--  <td align="center" class="col"><?php //echo $this->map['reports']['current'][[[=status.id=]]][1]['qty'];?></td> -->
                                    <td align="center" style="border:2px dotted #000;"><?php echo [[=status.name=]] ?></td>
                                    <!--/LIST:status-->
                                </tr>
                                <?php if ($i == 1): ?>
                                </thead>
                                <?php endif; ?>
                <?php $i++; ?>
                                    <!--ELSE-->
                                <?php
                                $total_revenue_order = [[=reports.total_revenue_reset=]] + [[=reports.total_revenue_toi_uu=]] + [[=reports.total_revenue_sale=]] + [[=reports.total_revenue_care=]] + [[=reports.total_revenue_refund=]] - [[=reports.total_revenue_refund=]];
                                $total_status_order = [[=reports.total_revenue_reset=]] + [[=reports.total_sale=]] + [[=reports.total_care=]] + [[=reports.total_reset=]];
                                $total_sale += [[=reports.total_sale=]];
                                $total_care += [[=reports.total_care=]];
                                $total_reset += [[=reports.total_reset=]];
                                $total_toi_uu += [[=reports.total_toi_uu=]];
                                $total_cancel += [[=reports.total_cancel=]];
                                $total_refund += [[=reports.total_refund=]];
                                $total_transport += [[=reports.total_transport=]];
                                $total_revenue_sale += [[=reports.total_revenue_sale=]];
                                $total_revenue_care += [[=reports.total_revenue_care=]];
                                $total_revenue_reset += [[=reports.total_revenue_reset=]];
                                $total_revenue_toi_uu += [[=reports.total_revenue_toi_uu=]];
                                $total_revenue_cancel += [[=reports.total_revenue_cancel=]];
                                $total_revenue_refund += [[=reports.total_revenue_refund=]];
                                $total += $total_revenue_order;
                                $rate_sale = ([[=reports.total_sale=]] != 0) ? round([[=reports.total_revenue_sale=]]/[[=reports.total_sale=]]) : 0; // Tỷ lệ doanh thu chốt mới
                                $total_rate_sale += $rate_sale;
                                if ($rate_sale > 0) {
                                $total_rate_sale_has_value++;
                                }

                                $rate_on_phone = ([[=reports.total_assigned=]] != 0) ? round($total_revenue_order / [[=reports.total_assigned=]]) : 0; // Tỷ lệ doanh thu trên 1 SĐT
                                $total_rate_on_phone += $rate_on_phone;
                                if ($rate_on_phone > 0) {
                                $total_rate_on_phone_has_value++;
                                }

                                $rate_on_day = ([[=reports.total_reached=]] > 0) ? round($total_revenue_order / [[=reports.total_reached=]]) : 0; // Tỷ lệ doanh thu trên 1 ngày công
                                $total_rate_on_day += $rate_on_day;
                                if ($rate_on_day > 0) {
                                $total_rate_on_day_has_value++;
                                }

                                $ty_le_tiep_can = 0;
                                if ([[=reports.total_assigned=]] != 0) {
                                $ty_le_tiep_can = round([[=reports.total_reached=]] / [[=reports.total_assigned=]], 2) * 100;
                                $ty_le_tiep_can_has_value++;
                                $total_ty_le_tiep_can_has_value += $ty_le_tiep_can;
                                }

                                $cm_tiep_can = 0;
                                if ([[=reports.total_reached=]] != 0) {
                                $cm_tiep_can = round([[=reports.total_sale=]] / [[=reports.total_reached=]], 2) * 100;
                                $total_cm_tiep_can_has_value += $cm_tiep_can;
                                $cm_tiep_can_has_value++;
                                }

                                $cm_so_duoc_chia = 0;
                                if ([[=reports.total_assigned=]] != 0) {
                                $cm_so_duoc_chia = round([[=reports.total_sale=]] / [[=reports.total_assigned=]], 2) * 100;
                                $cm_so_duoc_chia_has_value++;
                                $total_cm_so_duoc_chia_has_value += $cm_so_duoc_chia;
                                }

                                $ty_le_huy = ([[=reports.total_sale=]]?(round([[=reports.total_cancel=]]/[[=reports.total_sale=]],2)*100):'0').'%';
                                $ty_le_hoan = ([[=reports.total_transport=]]?(round([[=reports.total_refund=]]/[[=reports.total_transport=]],2)*100):'0').'%';
                                ?>
                <?php if ($j == 0): ?>
                                <tbody>
                                <?php endif;
                                $total_number_order += ([[=reports.total_sale=]] + [[=reports.total_care=]] + [[=reports.total_reset=]] + [[=reports.total_toi_uu=]]);
                                ?>
                                <tr>
                                    <td class="th-fixed" style="font-weight: bold;">[[|reports.name|]]</td>
                                    <td class="text-right">[[|reports.total_assigned|]]</td>
                                    <td class="text-right">[[|reports.total_reached|]]</td>
                                    <td class="text-right" style="white-space: pre;"><?= $ty_le_tiep_can ?> %</td>
                                    <td class="text-right" style="white-space: pre;"><?= $cm_tiep_can ?> %</td>
                                    <td class="text-right" style="white-space: pre;"><?= $cm_so_duoc_chia ?> %</td>
                                    <td class="text-center" style="white-space: pre;color:#f00;"><?=$ty_le_huy?></td>
                                    <td class="text-center" style="white-space: pre;color:#f00;"><?=$ty_le_hoan?></td>

                                    <td style="background: #bbf2e0;" class="text-right"><?= number_format([[=reports.total_revenue_sale=]]) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right"><?= number_format([[=reports.total_revenue_care=]]) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right"><?= number_format([[=reports.total_revenue_reset=]]) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right"><?= number_format([[=reports.total_revenue_toi_uu=]]) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right">-<?= number_format([[=reports.total_revenue_cancel=]]) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right">-<?= number_format([[=reports.total_revenue_refund=]]) ?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold"><?= number_format($total_revenue_order) ?></td>
                                    <td class="text-right"><?= number_format($rate_sale) ?></td>
                                    <td class="text-right"><?= number_format($rate_on_phone) ?></td>
                                    <td class="text-right"><?= number_format($rate_on_day) ?></td>

                                    <td class="text-right" style="background: #e1d99b">[[|reports.total_sale|]]<br><span class="small text-primary"><?=($total_number_order>0)?(round([[=reports.total_sale=]]/$total_number_order,2)*100).'%':''?></span></td>
                                    <td class="text-right" style="background: #e1d99b">[[|reports.total_care|]]<br><span class="small text-primary"><?=($total_number_order>0)?(round([[=reports.total_care=]]/$total_number_order,2)*100).'%':''?></span></td>
                                    <td class="text-right" style="background: #e1d99b">[[|reports.total_reset|]]<br><span class="small text-primary"><?=($total_number_order>0)?(round([[=reports.total_reset=]]/$total_number_order,2)*100).'%':''?></span></td>
                                    <td class="text-right" style="background: #e1d99b">[[|reports.total_toi_uu|]]<br><span class="small text-primary"><?=($total_number_order>0)?(round([[=reports.total_toi_uu=]]/$total_number_order,2)*100).'%':''?></span></td>
                                    <td class="text-right text-bold" style="background: #e1d99b">
                                        <?=$total_number_order?>
                                    </td>
                                    <?php $total_number += $total_number_order ?>
                                    <!--LIST:status-->
                                    <td align="center" class="col text-right" style="border:2px dotted #000;"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['qty'];?></td>
                                    <?php $total_number_order += $this->map['reports']['current'][[[=status.id=]]][1]['qty'] ?>
                                    <!--/LIST:status-->
                                </tr>
                                <?php if ($j == (count([[=reports=]]) - 2)): ?>
                                </tbody>
                                <?php endif; ?>
                <?php
                                $total_assigned += [[=reports.total_assigned=]];
                                $total_reached += [[=reports.total_reached=]];
                                $j++;
                                ?>
                                <!--/IF:cond-->
                                <!--/LIST:reports-->
                                <tfoot>
                                <?php
                                $avg_rate_sale = 0;
                                if ($total_rate_sale > 0) {
                                $avg_rate_sale = round($total_rate_sale / $total_rate_sale_has_value, 2);
                                }

                                $avg_rate_on_phone = 0;
                                if ($total_rate_on_phone > 0) {
                                $avg_rate_on_phone = round($total_rate_on_phone / $total_rate_on_phone_has_value, 2);
                                }

                                $avg_rate_on_day = 0;
                                if ($total_rate_on_day > 0) {
                                $avg_rate_on_day = round($total_rate_on_day / $total_rate_on_day_has_value, 2);
                                }

                                $avg_ty_le_tiep_can = 0;
                                if ($ty_le_tiep_can_has_value > 0) {
                                $avg_ty_le_tiep_can = round($total_ty_le_tiep_can_has_value / $ty_le_tiep_can_has_value, 2);
                                }

                                $avg_cm_tiep_can = 0;
                                if ($cm_tiep_can_has_value > 0) {
                                $avg_cm_tiep_can = round($total_cm_tiep_can_has_value / $cm_tiep_can_has_value, 2);
                                }

                                $avg_cm_so_duoc_chia = 0;
                                if ($cm_so_duoc_chia_has_value > 0) {
                                $avg_cm_so_duoc_chia = round($total_cm_so_duoc_chia_has_value / $cm_so_duoc_chia_has_value, 2);
                                }
                                ?>
                                <tr>
                                    <td class="th-fixed">Tổng</td>
                                    <td class="text-right text-bold"><?= $total_assigned ?></td>
                                    <td class="text-right text-bold"><?= $total_reached ?></td>
                                    <td class="text-center"><?= $total_assigned?(round($total_reached/$total_assigned,2)*100):'0';//$avg_ty_le_tiep_can ?> %</td>
                                    <td class="text-center"><?= $total_reached?(round($total_sale/$total_reached,2)*100):'0';//$avg_cm_tiep_can ?> %</td>
                                    <td class="text-center"><?= $total_assigned?(round($total_sale/$total_assigned, 2)*100):'0';//$avg_cm_so_duoc_chia ?> %</td>
                                    <td class="text-center" style="color:#f00;"><?=($total_sale?(round($total_cancel/$total_sale,2)*100):'0').'%';?></td>
                                    <td class="text-center" style="color:#f00;"><?=($total_transport?(round($total_refund/$total_transport,2)*100):'0').'%';?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold"><?= number_format($total_revenue_sale); ?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold"><?= number_format($total_revenue_care); ?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold"><?= number_format($total_revenue_reset); ?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold"><?= number_format($total_revenue_toi_uu); ?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold">-<?= number_format($total_revenue_cancel); ?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold">-<?= number_format($total_revenue_refund); ?></td>
                                    <td style="background: #bbf2e0;" class="text-right text-bold"><?= number_format($total); ?></td>
                                    <td class="text-right text-bold"><?= ($total_sale>0)?number_format(round($total_revenue_sale/$total_sale,2)):'0'; ?></td>
                                    <td class="text-right text-bold"><?= number_format($avg_rate_on_phone); ?></td>
                                    <td class="text-right text-bold"><?= number_format($avg_rate_on_day); ?></td>

                                    <td class="text-right text-bold" style="background: #e1d99b"><?= $total_sale ?><br><span class="small text-primary"><?=($total_number>0)?(round($total_sale/$total_number,2)*100).'%':''?></span></td>
                                    <td class="text-right text-bold" style="background: #e1d99b"><?= $total_care ?><br><span class="small text-primary"><?=($total_number>0)?(round($total_care/$total_number,2)*100).'%':''?></span></td>
                                    <td class="text-right text-bold" style="background: #e1d99b"><?= $total_reset ?><br><span class="small text-primary"><?=($total_number>0)?(round($total_reset/$total_number,2)*100).'%':''?></span></td>
                                    <td class="text-right text-bold" style="background: #e1d99b"><?= $total_toi_uu ?><br><span class="small text-primary"><?=($total_number>0)?(round($total_toi_uu/$total_number,2)*100).'%':''?></span></td>
                                    <td class="text-right text-bold" style="background: #e1d99b"><?= $total_number ?></td>
                                    <!--LIST:status-->
                                    <td align="center" class="col text-right"><strong><?php echo System::display_number([[=status.qty=]]);?></strong></td>
                                    <!--/LIST:status-->
                                </tr>
                                </tfoot>
                            </table>
                        </div>
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
    jQuery(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});

        $('.btn-prev').on('click', function() {
            // var pos = $('#tbl-clone').scrollLeft() - 50;
            $('#tbl-clone').scrollLeft(0);
        })

        $('.btn-next').on('click', function() {
            // var pos = $('#tbl-clone').scrollLeft() + 50;
            var $scrollWidth = $('#tbl-clone')[0].scrollWidth;
            var $scrollLeft = $('#tbl-clone').scrollLeft();

            $('#tbl-clone').scrollLeft($scrollWidth + $scrollLeft);
        })
    });
</script>