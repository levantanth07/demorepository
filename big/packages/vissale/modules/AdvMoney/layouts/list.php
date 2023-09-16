<?php
    $timeSlot = [[=time_slot=]];
    $format = [[=format=]];
?>
<script>
	function check_selected() {
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked){
				status = true;
			}
		});
		return status;
	}
	function make_cmd(cmd){
		jQuery('#cmd').val(cmd);
		document.ListUserAdminForm.submit();
	}
</script>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-8">
                <h3 class="title"><i class="fa fa-money"></i> Tiền quảng cáo theo <?= ((defined('CPQC_FOLLOW') && CPQC_FOLLOW===2)?'ngày':'khung giờ') ?></h3>
            </div>
            <div class="col-xs-4 text-right">
                <!--IF:cond([[=allow_change=]])-->
                <a class="btn btn-warning" href="<?php echo Url::build_current(array('cmd'=>'add'));?>#"> + Khai báo</a>
                <!--ELSE-->
                <a class="btn btn-warning" href="#" disabled="disabled"> Đã quá hạn khai báo</a>
                <!--/IF:cond-->
                <a class="btn btn-default" href="<?=Url::build('dashboard')?>&do=<?= ((defined('CPQC_FOLLOW') && CPQC_FOLLOW===2)?'adv_money_day':'adv_money_new') ?>"> <i class="fa fa-money"></i> Xem báo cáo</a>
            </div>
        </div>
        <form name="SearchUserAdminForm" method="post">
            <table class="table">
                <tr>
                    <td width="30%">
                        Tên tài khoản
                        <input name="user_id" type="text" id="user_id" class="form-control">
                    </td>
                    <td width="20%">
                        Nguồn
                        <select class="form-control" name="source_id">
                            [[|source_options|]]
                        </select>
                    </td>
                    <td width="20%">
                        Phân loại SP
                        <select class="form-control" name="bundle_id">
                            [[|bundle_options|]]
                        </select>
                    </td>
                    <td width="20%">
                        Tháng
                        <input type="text" name="month" id="month" class="form-control" 
                        value="<?= isset($_REQUEST['month'])?  $_REQUEST['month'] : date('m-Y') ?>"/>
                    </td>
                    <!--/IF:cond6-->
                    <td width="20%">
                        Tổng: [[|total|]] bản
                        <br>
                        <input type="submit" value="Tìm kiếm" class="btn btn-default">
                    </td>
                </tr>
            </table>
            <div class="note">
                Chú ý:
                <ul>
                    <?php if (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2): ?>
                        <li style="list-style: none;color: red;">- Hệ thống không loại trừ ngày nghỉ ngày tết</li>
                        <li style="list-style: none;color: red;">- Chỉ được khai báo CPQC từ 00h đến <?= CPQC_HOUR ?>h</li>
                        <li style="list-style: none;color: red;">- Chỉ được sửa dữ liệu ngày hôm trước</li>
                    <?php else: ?>
                        <li style="list-style: none;color: red;">- Báo cáo chỉ tính CPQC cao nhất trong ngày của marketing</li>
                        <li style="list-style: none;color: red;">- Vui lòng điền đủ thông tin CPQC khung giờ 11h30, 17h30 và 24h</li>
                    <?php endif; ?>
                </ul>
            </div>
            <input type="hidden" name="page_no" value="1" />
        </form>
        <form name="ListUserAdminForm" method="post">
            <table class="table table-bordered table-striped">
                <tr>
                    <th width="1%" title="Chọn tất cả"><input type="checkbox" value="1" id="UserAdmin_all_checkbox" onclick="jQuery('.selected-ids').attr('checked',this.checked)"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                    <th nowrap align="left">MKT</th>
                    <th nowrap align="left">Ngày</th>
                    <th nowrap align="left">Nguồn</th>
                    <th nowrap align="left">Phân loại SP</th>
                    <?php foreach ($timeSlot as $value) { ?>
                        <th nowrap align="left"><?php echo (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) ? 'Chi phí QC' : $value; ?></th>
                    <?php } ?>
                    <th nowrap align="left">Lượt Click</th>
                    <th nowrap align="left">Lịch sử chỉnh sửa</th>
                    <th class="text-center" width="5%"></th>
                </tr>
                <?php $i = 1;?>
                <!--LIST:items-->
                <tr valign="middle">
                    <td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" class="selected-ids" onclick="" id="UserAdmin_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>> <?php echo $i;?>.</td>
                    <td align="center">[[|items.full_name|]]</td>
                    <td align="center">
                        <?php echo Date_Time::to_common_date([[=items.date=]])?>
                        <div class="small">Ngày tạo: <?php echo date('d/m/Y H:i:s',strtotime([[=items.created_date=]]))?></div>
                    </td>
                    <td align="center">[[|items.source_name|]]</td>
                    <td align="center">[[|items.bundle_name|]]</td>
                    <?php if(!empty($format['time_slot_1'])) : ?>
                        <td align="right"><?php echo System::display_number([[=items.time_slot_1=]])?></td>
                    <?php endif; ?>
                    <?php if(!empty($format['time_slot_2'])) : ?>
                        <td align="right"><?php echo System::display_number([[=items.time_slot_2=]])?></td>
                    <?php endif; ?>
                    <?php if(!empty($format['time_slot_3'])) : ?>
                        <td align="right"><?php echo System::display_number([[=items.time_slot_3=]])?></td>
                    <?php endif; ?>
                    <?php if(!empty($format['time_slot_4'])) : ?>
                        <td align="right"><?php echo System::display_number([[=items.time_slot_4=]])?></td>
                    <?php endif; ?>
                    <?php if(!empty($format['time_slot_5'])) : ?>
                        <td align="right"><?php echo System::display_number([[=items.time_slot_5=]])?></td>
                    <?php endif; ?>
                    <?php if(!empty($format['time_slot_6'])) : ?>
                        <td align="right"><?php echo System::display_number([[=items.time_slot_6=]])?></td>
                    <?php endif; ?>
                    <?php if(!empty($format['time_slot_7'])) : ?>
                        <td align="right"><?php echo System::display_number([[=items.time_slot_7=]])?></td>
                    <?php endif; ?>
                    <td align="right"><?php echo System::display_number([[=items.clicks=]])?></td>
                    <td align="left">
                        <?php if($this->map['logs']): ?>
                        <?php foreach ($this->map['logs'][[[=items.id=]]] as $log):?>
                        <i class="<?=AdvMoney::getIconByType($log['data']['type'])?>"></i> <b><?= $log['data']['by'] ?></b> <i><?=date('d/m/Y H:i:s', strtotime($log['data']['at']))?></i>
                        <?php endforeach;?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <!--IF:cond([[=allow_change=]] && [[=items.date=]]==[[=date_allow_change=]])-->
                        <a href="<?php echo URL::build_current();?>&cmd=edit&id=[[|items.id|]]" title="Sửa"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i></a>
                        <!--ELSE-->
                        <a href="<?php echo URL::build_current();?>&cmd=edit&id=[[|items.id|]]" title="Sửa"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                        <!--/IF:cond-->
                    </td>
                </tr>
                <?php $i++;?>
                <!--/LIST:items-->
            </table>
            <div class="pt">[[|paging|]]</div>
            <input type="hidden" name="cmd" value="delete"/>
            <input type="hidden" name="page_no" value="1"/>
            <!--IF:delete(URL::get('cmd')=='delete')-->
            <input type="hidden" name="confirm" value="1" />
            <!--/IF:delete-->
        </form>
    </div>
</div>
<style>
    .datepicker-months table{
        width: 100%;
        text-align: center;
        box-shadow: 1px 3px 5px 5px #d4d4d4;
    }

    .datepicker-months th{
        text-align: center;
    }
    .datepicker-months span{
        display: block;
        height: 25px;
        line-height: 25px;
        border-bottom: 1px solid #ccc;
    }
    .datepicker-months .month.active,.datepicker-months .month.active:hover{
        background: #2196f3;
        color: white;
    }
    .datepicker-months span:hover {
        background: #e6e6e6;
    }
</style>
<script>
    $.fn.datepicker.dates['vi'] = {
    days: ["Chủ nhật", "Thứ hai", "Thứ ba", "Thứ tư", "Thứ năm", "Thứ sáu", "thứ bảy"],
    daysShort: ["CN", "Th", "Tue", "Wed", "Thu", "Fri", "Sat"],
    daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
    months: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
    monthsShort:["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
    today: "Hôm nay",
    clear: "Xoá",
    format: "dd-mm-yyyy",
    titleFormat: "Tháng MM yyyy", 
    weekStart: 0
};
    $('#month').datepicker({format:'mm-yyyy',startView: "months", 
    minViewMode: "months", language: 'vi', locale: 'vi', autoclose: true});
    
</script>
