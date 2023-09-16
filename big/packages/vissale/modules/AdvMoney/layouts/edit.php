<?php
    $timeSlot = [[=time_slot=]];
    $format = [[=format=]];
?>
<script src="packages/core/includes/js/multi_items.js"></script>
<span style="display:none">
	<div id="mi_account_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
            <span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1" <?= [[=setReadonly=]] ? 'disabled' : '' ?>></span>
            <span class="multi-edit-input hidden" style="width:40px;"><input  name="mi_account[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
            <span class="multi-edit-input">
                <input  name="mi_account[#xxxx#][date]" style="width:100px;" class="multi-edit-text-input" id="date_#xxxx#" <?= (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) ? 'value="'.[[=yesterday=]].'" disabled' : '' ?> >
            </span>
            <span class="multi-edit-input">
                <select  name="mi_account[#xxxx#][source_id]" style="width:200px;" class="multi-edit-text-input" id="source_id_#xxxx#" <?= [[=setReadonly=]] ? 'disabled' : '' ?>>
                    [[|source_options|]]
                </select>
            </span>
            <span class="multi-edit-input">
                <select  name="mi_account[#xxxx#][bundle_id]" style="width:200px;" class="multi-edit-text-input" id="bundle_id_#xxxx#" <?= [[=setReadonly=]] ? 'disabled' : '' ?>>
                    [[|bundle_options|]]
                </select>
            </span>
            <?php if(!empty($format['time_slot_1'])) : ?>
                <span class="multi-edit-input"><input  name="mi_account[#xxxx#][time_slot_1]" style="width:90px;text-align:right;" class="multi-edit-text-input" id="time_slot_1_#xxxx#"></span>
            <?php endif; ?>
            <?php if(!empty($format['time_slot_2'])) : ?>
                <span class="multi-edit-input"><input  name="mi_account[#xxxx#][time_slot_2]" style="width:90px;text-align:right;" class="multi-edit-text-input" id="time_slot_2_#xxxx#"></span>
            <?php endif; ?>
            <?php if(!empty($format['time_slot_3'])) : ?>
                <span class="multi-edit-input"><input  name="mi_account[#xxxx#][time_slot_3]" style="width:90px;text-align:right;" class="multi-edit-text-input" id="time_slot_3_#xxxx#"></span>
            <?php endif; ?>
            <?php if(!empty($format['time_slot_4'])) : ?>
                <span class="multi-edit-input"><input  name="mi_account[#xxxx#][time_slot_4]" style="width:90px;text-align:right;" class="multi-edit-text-input" id="time_slot_4_#xxxx#"></span>
            <?php endif; ?>
            <?php if(!empty($format['time_slot_5'])) : ?>
                <span class="multi-edit-input"><input  name="mi_account[#xxxx#][time_slot_5]" style="width:90px;text-align:right;" class="multi-edit-text-input" id="time_slot_5_#xxxx#"></span>
            <?php endif; ?>
            <?php if(!empty($format['time_slot_6'])) : ?>
                <span class="multi-edit-input"><input  name="mi_account[#xxxx#][time_slot_6]" style="width:90px;text-align:right;" class="multi-edit-text-input" id="time_slot_6_#xxxx#"></span>
            <?php endif; ?>
            <?php if(!empty($format['time_slot_7'])) : ?>
                <span class="multi-edit-input"><input  name="mi_account[#xxxx#][time_slot_7]" style="width:90px;text-align:right;" class="multi-edit-text-input number-input-adv" id="time_slot_7_#xxxx#" <?= [[=setReadonly=]] ? 'disabled' : '' ?>></span>
            <?php endif; ?>
            <span class="multi-edit-input"><input  name="mi_account[#xxxx#][clicks]" style="width:80px;text-align:left;" class="multi-edit-text-input" id="clicks_#xxxx#" <?= [[=setReadonly=]] ? 'disabled' : '' ?>></span>
		</div>
	</div>
</span>
<?php
$title = (URL::get('cmd')=='delete')?'Xác nhận xóa dữ liệu':' Câp nhật tiền Quảng cáo theo ' . (defined('CPQC_FOLLOW') && CPQC_FOLLOW===2?'ngày':'khung giờ');?>
<br>
<div class="container">
    <form name="EditAdvMoneyForm" method="post" enctype="multipart/form-data" onsubmit="return checkInput();">
        <div class="box box-default">
            <div class="box-header">
                <h3 class="title">
                    <?php echo $title;?><br>
                    <b style="font-size: 12px; line-height: 25px">&nbsp;Hệ thống mặc định chọn ngày hôm qua</b>
                </h3>
                <div class="box-tools">
                    <button type="submit" class="btn btn-primary" <?= [[=setReadonly=]] ? 'disabled' : '' ?>>Lưu </button>
                    <a href="<?=Url::build_current()?>" class="btn btn-default">Danh sách đã khai báo</a>
                </div>
            </div>
            <div class="box-body">
                <table class="table">
                    <tr>
                        <td width="100%">
                            <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
                            <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
                            <table width="100%" cellpadding="5" cellspacing="0">
                                <?php if(Form::$current->is_error())
                                {
                                    ?><tr valign="top">
                                    <td><?php echo Form::$current->error_messages();?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr valign="top">
                                    <td>
                                        <div class="multi-item-wrapper">
                                            <div id="mi_account_all_elems">
                                                <div>
                                                    <span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_account',this.checked);" <?= [[=setReadonly=]] ? 'disabled' : '' ?>></span>
                                                    <span class="multi-edit-input header hidden" style="width:40px;">ID</span>
                                                    <span class="multi-edit-input header" style="width:102px;">Ngày</span>
                                                    <span class="multi-edit-input header" style="width:200px;">Nguồn</span>
                                                    <span class="multi-edit-input header" style="width:200px;">Phân loại SP</span>
                                                    <?php foreach ($timeSlot as $value) { ?>
                                                        <span class="multi-edit-input header" style="width:92px;"><?php echo $value == '24h' ? ((defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) ? 'Chi phí QC' : $value) : $value; ?></span>
                                                    <?php } ?>
                                                    <span class="multi-edit-input header" style="width:82px;">Lượt click</span>
<!--                                                    <span class="multi-edit-input header" style="width:45px;">&nbsp;</span>-->
                                                    <br clear="all">
                                                </div>
                                            </div>
                                        </div>
                                        <br clear="all">
                                        <hr>
                                        <div><input type="button" value=" + Thêm dòng khai báo" class="btn btn-success btn-sm" onclick="mi_add_new_row('mi_account');$('#date_'+input_count).datetimepicker({format: 'DD/MM/YYYY'});" <?= [[=setReadonly=]] ? 'disabled' : '' ?>></div>
                                        <div>[[|paging|]]</div>
                                        <hr>
                                    </td>
                                </tr>
                            </table>
                            <input name="confirm_edit" type="hidden" value="1" />
                            <input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
    
    <div id="log-wrapper">
        <ul class="timeline">
            <!-- timeline time label -->
            <li class="time-label">
                <span class="bg-red">
                    Lịch sử thao tác
                </span>
            </li>
            <!-- /.timeline-label -->

            <?php foreach($this->map['logs'] as $log): ?>
            <!-- timeline item -->
            <li>
                <!-- timeline icon -->
                <i class="<?=AdvMoney::getIconByType($log['data']['type'])?>"></i>
                <div class="timeline-item">
                    <span class="time"><i class="fa fa-clock-o"></i> <?=$log['data']['at']?></span>

                    <h3 class="timeline-header">
                        <a href="#" onclick="event.preventDefault()"><?= $log['data']['by'] ?></a> <?=AdvMoney::getTitleByType($log['data']['type'])?>
                    </h3>

                    <?php if(in_array($log['data']['type'], [AdvMoney::LOG_UPDATE_TYPE, AdvMoney::LOG_ADD_TYPE])):?>
                    <div class="timeline-body">
                        <row><?=implode('</row><row>', $log['data']['data'])?></row>
                    </div>
                    <?php endif;?>
                </div>
            </li>
            <!-- END timeline item -->
            <?php endforeach;?>

        </ul>
    </div>
</div>
<script>
    mi_init_rows('mi_account',<?php if(isset($_REQUEST['mi_account'])){echo MiString::array2js($_REQUEST['mi_account']);}else{echo '[]';}?>);
    $(document).ready(function(){
        <?php if(!sizeof($_REQUEST['mi_account'])){?>
        mi_add_new_row('mi_account');
        $('#date_'+input_count).datetimepicker({format: 'DD/MM/YYYY'});
        <?php }?>
        for($i=101;$i<=input_count;$i++){
            if(getId('id_'+$i)){
                $('#date_'+$i).datetimepicker({format: 'DD/MM/YYYY'});
            }
        }
        $('.number-input-adv').on('change', function(e) {
		    $(e.target).val($(e.target).val().replace(/[^\d]/g, ''))
	    })
    });
    function checkInput() {
        $c=0;
        let CPQC_FOLLOW = <?= CPQC_FOLLOW ?>;
        for($i=101;$i<=input_count;$i++){
        	if (CPQC_FOLLOW == 2) {
		        if(!to_numeric(getId('source_id_'+$i).value)) {
			        getId('source_id_'+$i).focus();
			        alert('Bạn vui lòng chọn nguồn!');
			        return false;
                }
		        if(!to_numeric(getId('bundle_id_'+$i).value)) {
			        getId('bundle_id_'+$i).focus();
			        alert('Bạn vui lòng nhập chọn phân loại SP!');
			        return false;
                }
		        if(!to_numeric(getId('time_slot_7_'+$i).value)) {
			        getId('time_slot_7_'+$i).focus();
			        alert('Bạn vui lòng nhập chi phí quảng cáo!');
			        return false;
		        }
		        if(!is_numeric(getId('time_slot_7_'+$i).value)) {
			        getId('time_slot_7_'+$i).focus();
			        alert('Bạn vui lòng nhập chi phí quảng cáo là số!');
			        return false;
		        }
		        $c++;
            } else {
        		if(getId('id_'+$i)){
                    if(getId('date_'+$i).value==''){
                        alert('Bạn vui lòng nhập ngày!');
                        getId('date_'+$i).focus();
                        return false;
                    }
                    if(!to_numeric(getId('time_slot_1_'+$i).value)
                        && !to_numeric(getId('time_slot_2_'+$i).value)
                        && !to_numeric(getId('time_slot_3_'+$i).value)
                        && !to_numeric(getId('time_slot_4_'+$i).value)
                        && !to_numeric(getId('time_slot_5_'+$i).value)
                        && !to_numeric(getId('time_slot_6_'+$i).value)
                        && !to_numeric(getId('time_slot_7_'+$i).value)
                    ){
                        alert('Bạn vui lòng nhập chi phí quảng cáo!');
                        return false;
                    }
                    $c++;
                }
            }
        }
        if($('#deleted_ids').val()!=""){
            $c++;
        }
        if($c==0){
            alert('Bạn vui lòng nhập đủ thông tin trước khi lưu lại!');
            return false;
        }
    }
</script>
<style>
row{
    display: block
}

txt{
    color: blue;
    font-weight: bold
}

new{
    font-weight: bold
}
</style>
