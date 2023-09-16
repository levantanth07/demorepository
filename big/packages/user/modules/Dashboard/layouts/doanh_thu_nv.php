<iframe src="" id="ifrmPrint" class="hidden"></iframe>
<style>
    a[data-toggle="tooltip"]{
        display: block;
        text-align: center;
    }
    .tooltip-inner {
        white-space:pre-wrap;
    }
    .hide-native-select select{display: none;}
    button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
    i.glyphicon.glyphicon-remove-circle {top: 0px; }
    .tableFixHead tr th {
        position: sticky; top: 0; z-index: 1;
    }
    table  {
        border-collapse: collapse; width: 100%;
    }
    .tableFixHead tr th {
        background:#DDD;
    }
    .th-fixed {
        background: rgb(221, 221, 221);
        position: sticky;
        left: -11px;
        top: auto;
        white-space: normal;
        min-width: 150px;
    }
</style>
<?php
$type = Dashboard::$type;
$title = 'Báo cáo doanh thu nhân viên ';
?>
<div class="container full report">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">TUHA</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item">
                <?=$title?> - <a href="https://big.shopal.vn/bai-viet/huong-dan-su-dung/bao-cao-doanh-thu-sale/"
                                 target="_blank" class="btn btn-default"
                                 style="padding: 0px 2px;">
                                 <i class="fa fa-question-circle"></i>
                                 Hướng dẫn
                              </a>
            </li>
            <li class="pull-right">
                <div class="pull-right">
                    <?=Portal::get_setting('ads_text_link');?>
                </div>
            </li>
        </ol>
    </nav>
    <div class="box box-default">
        <div class="box-header">
            <form name="ReportForm" method="post" class="form-inline" onsubmit="disableButton('view_report');">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label><i class="fa fa-calendar"></i></label>
                        </div>
                        <div class="form-group">
                            <input name="date_from" type="text" id="date_from" class="form-control" style="width: 100px;" placeholder="Từ ngày">
                        </div>
                        <div class="form-group">
                            <input name="date_to" type="text" id="date_to" class="form-control" style="width: 100px;" placeholder="đến ngày">
                        </div>
                        <div class="form-group">
                            <select name="date_type" id="date_type" class="form-control" style="width: 125px;"></select>
                        </div>
                        <div class="form-group">
                            <select name="source_id" id="source_id" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="bundle_ids[]" id="bundle_ids" multiple="multiple" class="multiple-select-bundle" style="width:200px; display: none;">
                                [[|bundle_id_list|]]
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="users_ids[]" id="users_ids" multiple="multiple" class="multiple-select-sale" style="width:200px; display: none;">
                                [[|users_ids_option|]]
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="upsale_from_user_id[]" id="upsale_from_user_id" multiple="multiple" class="multiple-select" style="width:150px; display: none;">
                                [[|upsale_from_user_id|]]
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="type" id="type" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <select name="is_active" id="is_active" class="form-control" style="width: 110px;"></select>
                        </div>
                        <div class="form-group">
                            <select name="account_group_id" id="account_group_id" class="form-control" style="width: 150px;"></select>
                        </div>
                        <div class="form-group">
                            <select name="code" id="code" class="form-control"></select>
                        </div>
                        <!--IF:cond(Session::get('account_type')==TONG_CONG_TY)-->
                        <div class="form-group">
                            <select name="group_id" id="group_id" class="form-control"></select>
                        </div>
                        <!--/IF:cond-->
                        <div class="pull-right text-right">
                            <input  name="view_report" type="submit" id="view_report" class="btn btn-primary btn-sm" value="Xem báo cáo">
                            <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default btn-sm"><i class="fa fa-print"></i> IN</a>
                            <button type="button" class="btn btn-sm btn-success" id="btnExport" onclick="fnExcelReport('ReportTable');"> Excel </button>
                        </div>
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
                                    <div>
                                        <?=Url::get('type')?'Đơn: '.$type[Url::get('type')]:''?>
                                    </div>
                                    <div>[[|upsale|]]</div>
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
                        <style>
                            .doanh-thu-nv .fa-question-circle {
                                color: #fff;
                            }
                        </style>
                        <div class="table-responsive scroll" style="max-height: 800px; overflow: auto">
                            <table id="ReportTable" class="tableFixHead" width="100%" bordercolor="#999" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <?php
                                    $total_amount_confirmed = 0;$total_so_chia = 0;$total_transport = 0;$total_transport_total_price=0;
                                ?>
                                <!--LIST:reports-->
                                <!--IF:cond([[=reports.id=]]=='label')-->
                                <thead style="position: sticky; top: 0; z-index: 1">
                                    <tr style="font-weight:bold;background:#149317;color:#fff;">
                                        <td rowspan=2 align="center">[[|reports.name|]]</td>
                                        <!--LIST:status-->
                                        <td colspan=2 align="center"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['name'];?></td>
                                        <!--/LIST:status-->
                                        <td rowspan=2 nowrap="" title="Số được chia">Số cấp</td>
                                        <td rowspan=2 nowrap="" class="doanh-thu-nv">
                                            % chốt(SL)
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="(Tổng SL XN/Số được chia)*100%">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </td>
                                        <td rowspan=2 nowrap="" class="doanh-thu-nv">
                                            % chốt(đ)
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="(Doanh thu XN/(Số chia * 1tr))*100%">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </td>
                                        <td rowspan=2 nowrap="" class="doanh-thu-nv">
                                            % hủy
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="(Doanh thu Huỷ /Doanh thu cột Tổng)*100%">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </td>
                                        <td rowspan=2 nowrap="" class="doanh-thu-nv">
                                            % hoàn
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="(SL Hoàn/SL Vận chuyển)*100%">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </td>
                                        <td rowspan=2 nowrap="" class="doanh-thu-nv">
                                            % th.công
                                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="(SL thành công/SL Vận chuyển)*100%">
                                                <i class="fa fa-question-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr style="font-weight:bold;background:#f4f4f4;color:#333;">
                                        <!--LIST:status-->
                                        <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['qty'];?></td>
                                        <td align="center" class="col"><?php echo $this->map['reports']['current'][[[=status.id=]]][1]['total_price'];?></td>
                                        <!--/LIST:status-->
                                    </tr>
                                </thead>
                                <!--ELSE-->
                                <tbody>
                                    <tr>
                                        <?php
                                        $total_return = 0;
                                        $transport = [[=reports.transport=]];
                                        $transport_total_price = [[=reports.transport_total_price=]];
                                        $total_transport +=$transport;
                                        $total_transport_total_price += $transport_total_price;
                                        $total_success = 0;
                                        $amount_cancel = 0;
                                        $total_confirmed = 0;
                                        $amount_total = 0;
                                        $amount_confirmed = 0;
                                        $so_chia = [[=reports.so_chia=]];
                                        $total_so_chia +=$so_chia;
                                        ?>
                                        <!-- <td>[[|reports.name|]]</td> -->
                                        <td style="position: sticky; left: 0; background: #CCC;font-weight: bold" class="text-center">[[|reports.name|]]</td>
                                        <!--LIST:status-->
                                        <?php
                                            $qty = $this->map['reports']['current'][[[=status.id=]]][1]['qty'];
                                            $total_price = $this->map['reports']['current'][[[=status.id=]]][1]['total_price'];
                                            if([[=status.id=]]==CHUYEN_HOAN){
                                                $total_return = $qty;
                                            }
                                            if([[=status.id=]]==THANH_CONG){
                                                $total_success = $qty;
                                            }

                                            if([[=status.id=]]==HUY){
                                                $amount_cancel += System::calculate_number($total_price);
                                            }

                                            if([[=status.id=]]==1000000000){
                                                $amount_total += System::calculate_number($total_price);
                                            }
                                        ?>
                                        <td align="center" class="col">
                                            <?php
                                                if([[=status.id=]]==XAC_NHAN){
                                                    $total_confirmed +=$qty;
                                                    $amount_confirmed += System::calculate_number($total_price);
                                                    $total_amount_confirmed += System::calculate_number($total_price);
                                                }
                                                if([[=status.id=]]==CHUYEN_HANG){
                                                    echo '<span class="text-danger small">'.$qty.'</span>'.($transport?'<br>('.$transport.')':'');
                                                }else{
                                                    echo $qty;
                                                }
                                            ;?>
                                        </td>
                                        <td align="center" class="col">
                                            <?php
                                                if([[=status.id=]]==CHUYEN_HANG){
                                                    echo '<span class="text-danger">'.$total_price.'</span>'.($transport_total_price?'<br>('.System::display_number($transport_total_price).')':'');
                                                }else{
                                                    echo $total_price;
                                                }
                                            ?>
                                        </td>
                                        <!--/LIST:status-->
                                        <td align="center" class="col"><?=$so_chia?></td>

                                        <?php
                                        $ty_le_chot = ($so_chia>0)?(round($total_confirmed/($so_chia),3)*100):0;
                                        if($ty_le_chot<20 and $ty_le_chot>0){
                                            $ty_le_chot_color = '#f24e34';
                                        }elseif($ty_le_chot>=20 and $ty_le_chot<50){
                                            $ty_le_chot_color = '#f6c12b';
                                        }elseif($ty_le_chot>=50){
                                            $ty_le_chot_color = '#41f62b';
                                        }
                                        else{
                                            $ty_le_chot_color = '#FFF';
                                        }
                                        ?>
                                        <td class="text-center" style="background: <?=$ty_le_chot_color?>">
                                            <?=$ty_le_chot?>%
                                        </td>

                                        <?php
                                        $ty_le_chot_tien = ($so_chia>0)?(round($amount_confirmed/($so_chia*1000000),3)*100):0;
                                        if($ty_le_chot_tien<20 and $ty_le_chot_tien>0){
                                            $ty_le_chot_color = '#f24e34';
                                        }elseif($ty_le_chot_tien>=20 and $ty_le_chot_tien<50){
                                            $ty_le_chot_color = '#f6c12b';
                                        }elseif($ty_le_chot_tien>=50){
                                            $ty_le_chot_color = '#41f62b';
                                        }
                                        else{
                                            $ty_le_chot_color = '#FFF';
                                        }
                                        ?>
                                        <td class="text-center" style="background: <?=$ty_le_chot_color?>">
                                            <?=$ty_le_chot_tien?>%
                                        </td>

                                        <td class="text-center">
                                            <?php echo $amount_total > 0 ?round($amount_cancel*100/$amount_total,1) : 0; ?>%
                                        </td>
                                        <?php
                                        $ty_le_hoan = ($transport>0)?(round($total_return/$transport,3)*100):0;
                                        if($ty_le_hoan>=35){
                                            $ty_le_hoan_color = '#f24e34';
                                        }elseif($ty_le_hoan>=25 and $ty_le_hoan<35){
                                            $ty_le_hoan_color = '#f6c12b';
                                        }else{
                                            $ty_le_hoan_color = '#fff';
                                        }
                                        ?>
                                        <td class="text-center" title="<?=$total_return.'/'.$transport?>"
                                            style="background:<?=$ty_le_hoan_color?>">
                                            <?=$ty_le_hoan;?>%
                                        </td>
                                        <td class="text-center" title="<?=$total_success.'/'.$transport?>">
                                            <?=($transport>0)?(round($total_success/$transport,3)*100).'%':'0%';?>
                                        </td>
                                    </tr>
                                <!--/IF:cond-->
                                <!--/LIST:reports-->
                                </tbody>
                                <tfoot style="position: sticky; bottom: 0; background: #fff; font-weight: bold;">
                                    <tr style="position: sticky; left: 0; background: #DDD;font-weight: bold" class="text-center">
                                        <td style="position: sticky; left: 0; background: #CCC;font-weight: bold" class="text-center"><strong>Tổng</strong></td>
                                        <!--LIST:status-->
                                        <!--IF:cond([[=status.id=]]==XAC_NHAN)-->
                                        <?php $tong_so_luong_order_da_xac_nhan = [[=status.qty=]]; ?>
                                        <?php $tong_tien_order_da_xac_nhan = [[=status.total=]]; ?>
                                        <!--/IF:cond-->
                                        <!--IF:cond([[=status.id=]]==1000000000)-->
                                        <?php $tong_tien = [[=status.total=]]; ?>
                                        <!--/IF:cond-->
                                        <!--IF:cond([[=status.id=]]==HUY)-->
                                        <?php $tong_tien_order_da_huy = [[=status.total=]]; ?>
                                        <!--/IF:cond-->

                                        <!--IF:cond([[=status.id=]]==CHUYEN_HANG)-->
                                        <!--/IF:cond-->

                                        <!--IF:cond([[=status.id=]]==CHUYEN_HOAN)-->
                                        <?php $tong_so_luong_order_hoan = [[=status.qty=]]; ?>
                                        <!--/IF:cond-->

                                        <!--IF:cond([[=status.id=]]==THANH_CONG)-->
                                        <?php $tong_so_luong_order_thanh_cong = [[=status.qty=]]; ?>
                                        <!--/IF:cond-->

                                        <td align="center" class="col">
                                            <!--IF:cond([[=status.id=]]==CHUYEN_HANG)-->
                                            <strong><?='<span class="text-danger">'.System::display_number([[=status.qty=]]).'</span><br>('.System::display_number($total_transport);?>)</strong>
                                            <?php $tong_so_luong_order_van_chuyen = $total_transport; ?>
                                            <!--ELSE-->
                                            <strong><?php echo System::display_number([[=status.qty=]]);?></strong>
                                            <!--/IF:cond-->
                                        </td>
                                        <td align="center" class="col">
                                            <!--IF:cond([[=status.id=]]==CHUYEN_HANG)-->
                                            <strong><?='<span class="text-danger">'.System::display_number([[=status.total=]]).'</span><br>('.System::display_number($total_transport_total_price);?>)</strong>
                                            <!--ELSE-->
                                            <strong><?php echo System::display_number([[=status.total=]]);?></strong>
                                            <!--/IF:cond-->
                                        </td>
                                        <!--/LIST:status-->
                                        <td class="text-center"><strong><?=$total_so_chia?></strong></td>
                                        <td class="text-center"><?=$total_so_chia > 0 ? round($tong_so_luong_order_da_xac_nhan*100/$total_so_chia, 1) : 0;?>%</td>
                                        <td class="text-center"><?=$total_so_chia > 0 ? round($tong_tien_order_da_xac_nhan/10000/$total_so_chia, 1) : 0;?>%</td></td>
                                        <td class="text-center"><?=$tong_tien > 0 ? round($tong_tien_order_da_huy*100/$tong_tien, 1) : 0;?>%</td></td>
                                        <td class="text-center"><?=$tong_so_luong_order_van_chuyen > 0 ? round($tong_so_luong_order_hoan*100/$tong_so_luong_order_van_chuyen, 1) : 0;?>%</td>
                                        <td class="text-center"><?=$tong_so_luong_order_van_chuyen > 0 ? round($tong_so_luong_order_thanh_cong*100/$tong_so_luong_order_van_chuyen, 1) : 0;?>%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!--ELSE-->
                        <!--IF:view_cond(!Url::post('view_report'))-->
                        <div class="alert alert-warning-custom text-center">Bạn vui lòng nhấn nút Xem báo cáo</div>
                        <!--ELSE-->
                        <div class="alert alert-warning text-center">Không có dữ liệu báo cáo phù hợp</div>
                        <!--/IF:view_cond-->
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
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
        $('.multiple-select').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '150px',
            maxHeight: 200,
            nonSelectedText: 'Nhân viên Upsale',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
        $('.multiple-select-sale').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '150px',
            maxHeight: 200,
            nonSelectedText: 'Nhân viên Sale',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
        $('.multiple-select-bundle').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '150px',
            maxHeight: 200,
            nonSelectedText: 'Chọn phân loại',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
    });
</script>