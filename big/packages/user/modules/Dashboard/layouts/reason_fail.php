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
$title = 'Báo cáo Lý do đơn chưa thành công';
?>
<div class="container full report">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">TUHA</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item"><?=$title?></li>
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
                            <select name="date_type" id="date_type" class="form-control" style="width: 200px;"></select>
                        </div>
                        <div class="form-group">
                            <select name="status_type" id="status_type" class="form-control" style="width: 150px;"></select>
                        </div>

                        <div class="form-group">
                            <select name="is_active" id="is_active" class="form-control" style="width: 150px;"></select>
                        </div>

                        <div class="form-group">
                            <input  name="view_report" type="submit" id="view_report" class="btn btn-primary btn-sm" value="Xem báo cáo">
                        </div>

                        <!--IF:cond(Session::get('account_type')==TONG_CONG_TY)-->
                        <div class="form-group">
                            <select name="group_id" id="group_id" class="form-control"></select>
                        </div>
                        <!--/IF:cond-->
                        <div class="pull-right text-right">
                            <a href="#" onclick="ClickHereToPrint('ifrmPrint', 'reportForm');return false;" class="btn btn-default btn-sm"><i class="fa fa-print"></i> IN</a>
                            <button type="button" class="btn btn-sm btn-success" id="btnExport" onclick="fnExcelReport('ReportReasonsTable');"> Excel </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="reportForm" style="background:#FFF;margin: 10px;padding:10px;overflow:auto;height:1000px;">
                                <table width="100%" border="0">
                                    <tr>
                                        <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                            <div>Điện thoại: [[|phone|]]</div>
                                            <div>Địa chỉ: [[|address|]]</div></th>
                                        <th width="40%" style="text-align: center;"><h2>Báo cáo Lý do đơn chưa thành công</h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
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
                                <?php if (isset($this->map['reports']) && is_array($this->map['reports']) && count($this->map['reports'])) :?>
                                    <div class="table-responsive scroll" style="max-height: 800px; overflow: auto">
                                        <table id="ReportReasonsTable" class="tableFixHead" width="100%" bordercolor="#999" border="1" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                            <?php
                                            $percentage = 0;
                                            $percentages = $this->map['status'][1000000000]['total_price'];
                                            ?>
                                            <thead style="position: sticky; top: 0; z-index: 1">
                                            <tr style="font-weight:bold;background:#149317;color:#fff;">
                                                <td rowspan=2 align="center">Nhân viên</td>
                                                <?php foreach ( $this->map['status'] as $k=>$val ) : ?>
                                                    <td colspan=3 align="center"><?php echo $val['name']?></td>
                                                <?php endforeach;?>
                                            </tr>
                                            <tr style="font-weight:bold;background:#f4f4f4;color:#333;">
                                                <?php foreach ( $this->map['status'] as $statusKey=>$statusValue ) : ?>
                                                    <td  align="center">Số lượng</td>
                                                    <td  align="center">Doanh thu</td>
            <!--                                        --><?php //if (!array_key_last($statusValue)  ): ?>
                                                    <td  align="center">Tỷ lệ (%)</td>
            <!--                                        --><?php //endif;?>
                                                <?php endforeach;?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ( $this->map['reports'] as $reportKey=>$reportValue ) : ?>
                                                <?php
                                                $percentage = 0;
                                                foreach ($reportValue as $key=>$val )
                                                {
                                                    if ($key == 8) {
                                                        $percentage += $val['total_price'];
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td style="position: sticky; left: 0; background: #CCC;font-weight: bold" class="text-center"><?php echo $reportValue['usename'] ?? ''?></td>
                                                    <?php foreach ($reportValue as $k=>$v ) : ?>
                                                        <?php if ($k == 'usename' || $k == 'id' || $k == 1000000000 ) continue;?>
                                                            <td  align="center"><?php echo $v['total_qty'] ?? 0 ?></td>
                                                            <td  align="center">
                                                                <?php echo isset($v['total_price']) ? System::display_number($v['total_price']) : 0  ?>
                                                            </td>
                                                        <td  align="center">
                                                            <?php
                                                                echo $percentage > 0 && isset($v['total_price']) ?  round($v['total_price']*100/$percentage,1) : 0;
                                                            ?>
                                                        </td>
                                                    <?php endforeach;?>
                                                </tr>
                                            <?php endforeach;?>
                                            </tbody>
                                            <tfoot style="position: sticky; bottom: 0; background: #fff; font-weight: bold;">
                                                <tr style="position: sticky; left: 0; background: #DDD;font-weight: bold" class="text-center">
                                                    <td style="position: sticky; left: 0; background: #CCC;font-weight: bold" class="text-center"><strong>Tổng</strong></td>
                                                    <?php foreach ( $this->map['status'] as $statusKey=>$statusValue ) : ?>
                                                        <td  align="center"><?php echo $statusValue['total_qty']?></td>
                                                        <td  align="center"><?php echo System::display_number($statusValue['total_price'])?></td>
                                                        <td  align="center"><?php echo $percentages > 0 ? round($statusValue['total_price']*100/$percentages,1) : 0 ?></td>
                                                    <?php endforeach;?>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php endif;?>
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
    });
</script>]
