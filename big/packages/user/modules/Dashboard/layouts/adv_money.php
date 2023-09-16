<?php $title = 'Báo cáo chi phí quảng cáo theo khung giờ'?>
<style>
    .hide-native-select select{display: none;}
    button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
    i.glyphicon.glyphicon-remove-circle {top: 0px; }
</style>
<div class="box box-info">
    <div class="box-header">
        <form name="ReportForm" method="post" class="form-inline">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-7">
                        <div class="box-title">
                            <div class="form-group col-md-2">
                                <input name="date_from" type="text" id="date_from" placeholder="Từ ngày" class="form-control" style="width: 120px;">
                            </div>
                            <div class="form-group col-md-2">
                                <input name="date_to" type="text" id="date_to" placeholder="đến ngày" class="form-control" style="width: 120px;">
                            </div>
                            <div class="form-group col-md-2">
                                <select name="account_id" id="account_id" class="form-control" style="width: 120px;"></select>
                            </div>
                            <div class="form-group col-md-2">
                                <select name="account_group_id" id="account_group_id" class="form-control" style="width: 120px;"></select>
                            </div>
                            <div class="form-group col-md-2">
                                <select name="bundle_ids[]" id="bundle_ids" multiple="multiple" class="multiple-select-bundle" style="width:120px; display: none;">
                                    [[|bundle_id_list|]]
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <select name="source_ids[]" id="source_ids" multiple="multiple" class="multiple-select-source" style="width:120px; display: none;">
                                    [[|source_id_list|]]
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="box-tools">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                            <button type="button" class="btn btn-default" onclick="printWebPart('reportForm')"><i class="fa fa-print"></i> In báo cáo</button>
                            <a href="<?=Url::build('adv_money',['cmd'=>'add'])?>" class="btn btn-warning"> + Khai báo chi phí QC</a>
                            <!--IF:cond(Dashboard::$admin_group)-->
                            <a href="<?=Url::build('admin_group_info')?>#marketing" class="btn btn-default"> <i class="fa fa-exclamation-triangle"></i> Cài đặt cảnh báo</a>
                            <!--/IF:cond-->
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="box-body">
        <div id="reportForm" style="background:#FFF;margin: 5px;padding:5px;overflow:auto;height:1000px;">
            <table width="100%" border="0">
                <tr>
                    <th width="25%" style="text-align: left;"><div>[[|full_name|]]</div>
                        <div>Điện thoại: [[|phone|]]</div>
                        <div>Địa chỉ: [[|address|]]</div></th>
                    <th width="50%" style="text-align: center;">
                        <h2><?=$title?></h2>
                        <div>[[|marketing_name|]]</div>
                        <div class="small text-warning">
                            (Chú ý: báo cáo chỉ tính CPQC cao nhất trong ngày của marketing)
                        </div>
                    </th>
                    <th width="25%" style="text-align: right;">
                        <div>Ngày: <?php echo date('d/m/Y')?></div>
                        <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                    </th>
                </tr>
            </table>
            <hr>
            <?php if(!empty($_REQUEST['form_block_id'])) :?>
                <div class="table-responsive scroll" style="max-height: 800px; overflow: auto">
                            <table id="ReportTable" class="tableFixHead" width="100%" bordercolor="#999" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <?php $date='';$account_id='';$i=0;?>
                                <!--LIST:reports-->
                                <!--IF:cond([[=reports.id=]]=='label')-->
                                <thead style="position: sticky; top: 0; z-index: 1">
                                    <tr align="center" style="font-weight:bold;background:#DDD;position: sticky; top: 0; z-index: 1">
                                        <td style="font-weight: bold;border-left: 1px solid #999;<?=($account_id==[[=reports.account_id=]])?'border-bottom: 1px solid #fff;':'';?><?=($i==sizeof([[=reports=]])-2)?'border-bottom: 1px solid #999;':''?>">
                                            <?=($i==sizeof([[=reports=]])-2)?'<div style="width:100%;border-bottom:1px solid #999;">x</div>':''?>
                                            <?=($date!=[[=reports.date=]])?'<div style="color:#053d8c;width:100%;border-top:1px solid #999;box-sizing: border-box;">'.[[=reports.date=]].'</div>':'';?>
                                            <?=($account_id!=[[=reports.account_id=]])?'<div style="width:100%;border-top:1px solid #999;box-sizing: border-box;">'.[[=reports.account_id=]].'</div>':'';?>
                                        </td>
                                        <td style="border:1px solid #999;">[[|reports.time|]]</td>
                                        <td style="border:1px solid #999;" class="text-danger" align="right">
                                            <?=([[=reports.max_value=]]==System::calculate_number([[=reports.total=]]))?'<div class="text-bold">'.[[=reports.total=]].'</div>':[[=reports.total=]]?>

                                        </td>
                                        <td style="border:1px solid #999;">[[|reports.total_phone|]]</td>
                                        <td style="border:1px solid #999;" align="right">[[|reports.cost_per_phone|]]</td>
                                        <td style="border:1px solid #999;">[[|reports.sale_order_qty|]]</td>
                                        <td style="border:1px solid #999;">[[|reports.toi_uu_order_qty|]]</td>
                                        <td style="border:1px solid #999;">[[|reports.total_order_qty|]]</td>
                                        <td style="border:1px solid #999;" align="right">[[|reports.cost_per_order|]]</td>
                                        <td style="border:1px solid #999;" class="text-success" align="right">[[|reports.total_price|]]</td>
                                        <td style="border:1px solid #999;background:[[|reports.ads_warning_color|]]" align="center">[[|reports.cost_per_total|]]%</td>
                                    </tr>
                                </thead>
                                <!--ELSE-->
                                <tbody>
                                    <tr align="center" style="">
                                        <td style="background-color: #DDD;font-weight: bold;border-left: 1px solid #999;font-weight: bold;position: sticky; left: 0;<?=($account_id==[[=reports.account_id=]])?'border-bottom: 1px solid #DDD;':'';?>
                                        <?=($i==sizeof([[=reports=]])-2)?'border-bottom: 1px solid #999;':''?>">
                                            <?=($i==sizeof([[=reports=]])-2)?'<div style="width:100%;border-bottom:1px solid #999;">x</div>':''?>
                                            <?=($date!=[[=reports.date=]])?'<div style="color:#053d8c;width:100%;border-top:1px solid #999;box-sizing: border-box;">'.[[=reports.date=]].'</div>':'';?>
                                            <?=($account_id!=[[=reports.account_id=]])?'<div style="width:100%;border-top:1px solid #999;box-sizing: border-box;">'.[[=reports.account_id=]].'</div>':'';?>
                                        </td>
                                        <td style="border:1px solid #999;">[[|reports.time|]]</td>
                                        <td style="border:1px solid #999;" class="text-danger" align="right">
                                            <?=([[=reports.max_value=]]==System::calculate_number([[=reports.total=]]))?'<div class="text-bold">'.[[=reports.total=]].'</div>':[[=reports.total=]]?>

                                        </td>
                                        <td style="border:1px solid #999;">[[|reports.total_phone|]]</td>
                                        <td style="border:1px solid #999;" align="right">[[|reports.cost_per_phone|]]</td>
                                        <td style="border:1px solid #999;">[[|reports.sale_order_qty|]]</td>
                                        <td style="border:1px solid #999;">[[|reports.toi_uu_order_qty|]]</td>
                                        <td style="border:1px solid #999;">[[|reports.total_order_qty|]]</td>
                                        <td style="border:1px solid #999;" align="right">[[|reports.cost_per_order|]]</td>
                                        <td style="border:1px solid #999;" class="text-success" align="right">[[|reports.total_price|]]</td>
                                        <td style="border:1px solid #999;background:[[|reports.ads_warning_color|]]" align="center">[[|reports.cost_per_total|]]%</td>
                                    </tr>
                                <!--/IF:cond-->
                                <?php
                                    $date=($date!=[[=reports.date=]])?[[=reports.date=]]:$date;
                                    $account_id=($account_id!=[[=reports.account_id=]])?[[=reports.account_id=]]:$account_id;
                                    $i++;
                                ?>
                                <!--/LIST:reports-->
                                </tbody>
                            </table>
                        </div>
            <?php else: ?>
                <div style="display: flex; width: 100%; justify-content: center; padding: 40px; ">Vui lòng nhấn nút <b style="padding: 0 5px;">Xem báo cáo</b></div>
            <?php endif; ?>
            <br>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
        $('#date_to').datetimepicker({format: 'DD/MM/YYYY'});

        $('.multiple-select-source').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '120px',
            maxHeight: 200,
            nonSelectedText: 'Chọn nguồn',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
        $('.multiple-select-bundle').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '120px',
            maxHeight: 200,
            nonSelectedText: 'Chọn phân loại',
            includeSelectAllOption: true,
            selectAllText: 'Chọn tất cả'
        });
    });
</script>