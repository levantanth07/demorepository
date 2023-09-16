<style>
    .tableFixHead {
        overflow: auto; height: 100px;
    }
    .tableFixHead tr.fix td {
        position: sticky; top: 0; z-index: 1;
    }
    table  {
        border-collapse: collapse; width: 100%;
    }
    .tableFixHead tr.fix td {
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

    tr td {
        border-right: 1px solid #f4f4f4 !important;
        border-top: 1px solid #f4f4f4 !important;
        border-bottom: none !important;
        border-left: none !important;
    }

    .multiselect-container label {
        font-weight: normal !important;
    }
</style>

<?php
$status_arr = [[=status_arr=]];
?>

<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item">Báo cáo doanh thu theo trạng thái</li>
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
                            <input name="date_from" type="text" id="date_from" class="form-control" placeholder="Từ ngày">
                        </div>
                        <div class="form-group">
                            <input name="date_to" type="text" id="date_to" class="form-control" placeholder="đến ngày">
                        </div>
                        <!--IF:cond(Session::get('account_type')==TONG_CONG_TY)-->
                        <div class="form-group">
                            <select name="group_id" id="group_id" class="form-control" required oninvalid="this.setCustomValidity('Bạn vui lòng chọn công ty.')" oninput="setCustomValidity('')"></select>
                        </div>
                        <!--/IF:cond-->

                        <!-- Option lọc theo người gán, người chốt -->
                        <VIEWTYPE></VIEWTYPE>

                        <div class="form-group">
                            <select id="status_id" class="form-control" multiple="multiple" style="display:none">
                                <?php
                                foreach ($status_arr as $key => $value):
                                    $selected = (Url::get('status_ids') && !in_array($key, Url::get('status_ids'))) ? '' : 'selected';
                                    ?>
                                    <option value="<?= $value['id'] ?>" <?= $selected ?>><?= $value['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <input name="view_report" type="submit" class="btn btn-primary" value="Xem báo cáo">
                        </div>

                    </div>
                    <div class="col-md-2 text-right">
                         <button type="button" class="btn btn-success" id="btnExport" onclick="fnExcelReport('ReportStatusTable');"> Excel </button>
                        <a href="#" onclick="printWebPart('reportForm');return false;" class="btn btn-default"><i class="fa fa-print"></i> IN</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div id="reportForm" style="background:#FFF;padding:10px;">
                        <table width="100%" border="0">
                            <tr>
                                <th width="30%" style="text-align: left;"><div>[[|full_name|]]</div>
                                    <div>Điện thoại: [[|phone|]]</div>
                                    <div>Địa chỉ: [[|address|]]</div></th>
                                <th width="40%" style="text-align: center;"><h2>Báo cáo doanh thu theo trạng thái</h2><div>Ngày <?php echo Url::get('date_from')?> đến <?php echo Url::get('date_to')?></div></th>
                                <th width="30%" style="text-align: right;">
                                    <div>Ngày in: <?php echo date('d/m/Y')?></div>
                                    <div>Tài khoản in: <?php echo Session::get('user_id')?></div>
                                </th>
                            </tr>
                        </table>
                        <!--IF:cond(!empty([[=reports=]]))-->
                        <div class="scroll" style="max-height: 500px; overflow: auto">
                            <table id="ReportStatusTable"  width="100%" class="table table-bordered tableFixHead" bordercolor="#000" style="border-collapse: separate;">
                                <thead style="position: sticky; top: 0; z-index: 2">
                                <tr style="font-weight:bold;background:#DDD;" class="fix">
                                    <td  style="position: sticky; left: 0;z-index: 11" rowspan="2"><?=$this->map['theads']['name']?></td>

                                    <?php foreach ($this->map['status'] as $value):?>
                                        <td align="center" colspan="3"><?=$value['name']?></td>
                                    <?php endforeach;?>

                                    <td align="center" colspan="2" style="position: sticky; right: 0; background: #DDD; z-index: 11; border-left: 1px solid #f4f4f4 !important">
                                        <div style="width: 138px">Tổng</div>
                                    </td>
                                </tr>

                                <tr style="font-weight:bold;background:#DDD;" class="fix;">
                                    <?php foreach ($this->map['status'] as $value):?>
                                        <td align="center">Doanh thu</td>
                                        <td align="center">Số lượng</td>
                                        <td align="center">Tỷ lệ doanh thu</td>
                                    <?php endforeach;?>

                                    <td align="center" style="position: sticky; right: 62px; background: #DDD; z-index: 11; width: 90px; border-left: 1px solid #f4f4f4 !important">Doanh thu</td>
                                    <td align="center" style="position: sticky; right: 0; background: #DDD; z-index: 11; width: 60px; border-left: 1px solid #f4f4f4 !important">Số lượng</td>
                                </tr>
                                </thead>

                                <tbody style="position: relative; z-index:1">
                                <?php foreach ($this->map['reports'] as $username => $report):?>
                                    <tr>
                                        <td style="position: sticky; left: 0; background: #DDD;"><?=$report['name']?></td>

                                        <?php foreach ($this->map['status'] as $statusID => $status):?>
                                            <td align="center"><?=$report[$statusID]['total']?></td>
                                            <td align="center"><?=$report[$statusID]['orders_qty']?></td>
                                            <td align="center"><?=$report['total'] ? round($report[$statusID]['total_num']/$report['total'],3) * 100 . '%' : ''?></td>
                                        <?php endforeach;?>

                                        <td align="center" style="position: sticky; right: 62px; background: #DDD; z-index: 11; width: 90px;"><?=System::display_number($report['total'])?></td>
                                        <td align="center" style="position: sticky; right: 0; background: #DDD; z-index: 11; width: 60px;"><?=System::display_number($report['orders_qty'])?></td>
                                    </tr>
                                <?php endforeach;?>
                                </tbody>

                                <!-- Tổng -->
                                <tfoot style="position: sticky; z-index: 2; bottom: 0; background: #fff; font-weight: bold;">
                                <tr>
                                    <td style="position: sticky; left: 0; background: #DDD;"><strong>Tổng</strong></td>

                                    <?php foreach ($this->map['status'] as $statusID => $status):?>
                                        <td align="center"><?=System::display_number($status['total'])?></td>
                                        <td align="center"><?=System::display_number($status['orders_qty'])?></td>
                                        <td align="center"><?=[[=_total=]] ? round($status['total']/[[=_total=]],3) * 100 . '%' : ''?></td>
                                    <?php endforeach;?>

                                    <td align="center" style="position: sticky; right: 62px; background: #DDD; z-index: 11; width: 90px;"><strong><?=System::display_number([[=_total=]])?></strong></td>
                                    <td align="center" style="position: sticky; right: 0; background: #DDD; z-index: 11; width: 60px;"><strong><?=System::display_number([[=_orders_qty=]])?></strong></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <br>
                        <!--ELSE-->
                        <div class="alert text-center">Vui lòng nhấn nút Xem báo cáo</div>
                        <!--/IF:cond-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="assets/standard/js/multiple.select.js"></script>
<script type="text/javascript" src="/packages/core/includes/js/helper.js"></script>
<script type="text/javascript">
	const VIEWTYPE = JSHELPER.render.select({
		data: {1: 'Ngày gán', 2: 'Ngày chốt', 3: 'Ngày chuyển'},
		selectAttrs: {name: 'view_type', id: 'view-type', class: 'form-control'},
		selected: '<?=URL::getUInt('view_type', 1)?>'
	}).mount('VIEWTYPE');

	$(document).ready(function() {
		$('#date_from').datetimepicker({format: 'DD/MM/YYYY'});
		$('#date_to').datetimepicker({format: 'DD/MM/YYYY'});
		$('#status_id').multiselect({
			selectAllText: 'Chọn tất cả',
			includeSelectAllOption: true,
			maxHeight: 400,
			buttonText: function(options, select) {
				if (options.length === 0) {
					return 'Chọn trạng thái báo cáo';
				}
				else if (options.length > 3) {
					return options.length + ' trạng thái đã chọn';
				}
				else {
					var labels = [];
					options.each(function() {
						if ($(this).attr('label') !== undefined) {
							labels.push($(this).attr('label'));
						}
						else {
							labels.push($(this).html());
						}
					});
					return labels.join(', ') + '';
				}
			},
			checkboxName: function(option) {
				return 'status_ids[]';
			}
		});
	});
</script>
