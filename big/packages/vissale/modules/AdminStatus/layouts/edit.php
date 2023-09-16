<script src="assets/vissale/lib/bs-color-picker/js/bootstrap-colorpicker.js"></script>
<link href="assets/vissale/lib/bs-color-picker/css/bootstrap-colorpicker.css" rel="stylesheet">
<script src="packages/core/includes/js/multi_items.js"></script>
	<span style="display:none">
	<span id="mi_status_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
			<span class="multi-edit-input" style="width:20px;" id="checkboxContainer_#xxxx#">
				<input  type="checkbox" id="_checked_#xxxx#" tabindex="-1">
				<input  name="mi_status[#xxxx#][is_system]" class="multi-edit-text-input" type="hidden" id="is_system_#xxxx#">
				<input  type="number" tabindex="-1"><input  name="mi_status[#xxxx#][orders_total]" class="multi-edit-text-input" type="hidden" id="orders_total_#xxxx#">
			</span>
			<span class="multi-edit-input hidden" style="width:40px;"><input  name="mi_status[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
			<span class="multi-edit-input"><input  name="mi_status[#xxxx#][name]" style="width:150px;" class="multi-edit-text-input" type="text" id="name_#xxxx#"></span>
			<span class="multi-edit-input" style="width: 120px;padding-left:35px;"><input  name="mi_status[#xxxx#][no_revenue]" class="multi-edit-text-input" type="checkbox" id="no_revenue_#xxxx#"></span>
			<span class="multi-edit-input" style="width: 100px;padding-left:35px;"><input  name="mi_status[#xxxx#][not_reach]" class="multi-edit-text-input" type="checkbox" id="not_reach_#xxxx#"></span>
			<span class="multi-edit-input no-border" style="text-align:center;" id="del_#xxxx#" ><a class="btn btn-default btn-sm text-danger" href="#" style="color:#999;text-decoration:none;" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_status','#xxxx#','');event.returnValue=false;" title="Xóa">XÓA</a></span>
		</div>
    <br clear="all">
	</span>
</span>
<?php
$title = (URL::get('cmd')=='delete')?'Xóa trạng thái đơn hàng':' Quản lý trạng thái đơn hàng';
$allowAddOrderStatus = intval([[=allowAddOrderStatus=]]);
?>
<script>
	const allowAddOrderStatus = <?php echo $allowAddOrderStatus; ?>;
</script>
<div class="container full">
	<br>
	<div class="box box-info">
		<div class="box-header">
			<div class="col-md-8">
				<h3 class="box-title"><?php echo $title;?></h3>
			</div>
			<div class="col-md-4 text-right">
				<!--IF:cond([[=allowAddOrderStatus=]])-->
				<a class="btn btn-primary" onclick="EditAdminStatusForm.submit();"> <i class="fa fa-floppy-o"></i> Lưu </a>
				<!--/IF:cond-->
			</div>
		</div>
		<div class="box-body">
            <form name="EditAdminStatusForm" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-7">
                        <div class="bs-example" id="jsElConfigStatus" data-example-id="hoverable-table" style="background:#fff;">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="40%">Tên trạng thái</th>
                                        <th width="15%">Ko tiếp cận</th>
                                        <th width="15%" title="Không doanh thu khi đã xác nhận">Ko tính<br> doanh thu</th>
                                        <th width="10%">Vị trí</th>
                                        <th width="20%">Mầu tùy chỉnh</th>
                                        <th width="20%">Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!--LIST:status_defaults-->
                                    <tr>
                                        <td title="ID: [[|status_defaults.id|]]">
                                            [[|status_defaults.name|]]
                                            <input  name="id[]" type="hidden" value="[[|status_defaults.id|]]">

                                        </td>
                                        <td align="center">
                                            <?php echo [[=status_defaults.not_reach=]]?'x':''?>
                                        </td>
                                        <td align="center">
                                            <?php echo [[=status_defaults.no_revenue=]]?'x':''?>
                                        </td>

                                        <td><input  name="position[]" type="number" id="" value="[[|status_defaults.position|]]" class="form-control" style="padding: 2px; text-align: center"></td>
                                        <td>
                                            <div id="cp2" class="color-picker-ip input-group colorpicker-component" title="Using input value">
                                                <input  name="custom_color[]" type="text" id="" value="[[|status_defaults.custom_color|]]" class="form-control colorpicker-component multi-edit-text-input"">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-chevron-right"></i></span>
                                            </div>
                                        </td>
                                        <td>
                                            <!--IF:cond([[=status_defaults.is_default=]])-->
                                            <input  name="level[]" type="number" max="5" maxlength="5" value="[[|status_defaults.level|]]" class="form-control" style="padding: 2px; text-align: center" readonly>
                                            <!--ELSE-->
                                            <input  name="level[]" type="number" max="5" maxlength="5" value="[[|status_defaults.level|]]" class="form-control" style="padding: 2px; text-align: center">
                                            <!--/IF:cond-->
                                        </td>
                                    </tr>
                                    <!--/LIST:status_defaults-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="row" id="jsElCustomizeStatus">
                            <?php if(Form::$current->is_error()){ echo Form::$current->error_messages();?><?php }?>
                            <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
                            <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
                            <table class="table">
                                <tr valign="top">
                                    <td>
                                        <div class="alert alert-default">
                                            <h3>Chỉnh sửa trạng thái của shop</h3>
                                            <div class="multi-item-wrapper">
                                                <div id="mi_status_all_elems">
                                                    <div>
                                                        <span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_status',this.checked);"></span>
                                                        <span class="multi-edit-input header" style="width:152px;">Tên trạng thái</span>
                                                        <span class="multi-edit-input header" style="width:120px;">ko doanh thu</span>
                                                        <span class="multi-edit-input header" style="width:100px;">ko tiếp cận</span>
                                                        <span class="multi-edit-input header" style="width:45px;">Xóa</span>
                                                        <br clear="all">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
											<!--IF:cond([[=allowAddOrderStatus=]])-->
											<div>
												<input type="button" value="Thêm mới" id="add-row" class="btn btn-warning btn-sm" onclick="mi_add_new_row('mi_status');">
											</div>
											<!--/IF:cond-->
                                            <div>[[|paging|]]</div>
                                        </div>
                                    </td>
                                </tr>
								<tr>
									<td>
                                        <div class="alert alert-default alert-warning-custom">
                                            <label>Chú thích: </label>
                                            <ul class="">
                                                <li class="">Chưa xác nhận: Là số mới chưa liên hệ, chưa xử lý</li>
                                                <li class="">Không tiếp cận: là trạng thái không được tính là đã tiếp cận đến khách hàng</li>
                                                <li class="">Không doanh thu: là trạng thái không tính doanh thu cho dù là đơn đó đã xác nhận</li>
                                                <li>Cho phép xoá trạng thái khi chưa được gán vào đơn hàng </li>
                                            </ul>
                                        </div>
									</td>
								</tr>
                            </table>
                            <input name="confirm_edit" type="hidden" value="1" />
                            <input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
                        </div>
                    </div>
                </div>
				<!--IF:cond([[=allowAddOrderStatus=]])-->
                <div class="row">
                    <div class="col-md-8">
                    </div>
                    <div class="col-md-4 text-right">
                        <a class="btn btn-primary" onclick="EditAdminStatusForm.submit();"> Ghi lại </a>
                    </div>
                </div>
				<!--/IF:cond-->
            </form>
        </div>
	</div>
</div>
<script>
	$(document).ready(function () {
		if (!allowAddOrderStatus) {
			$('#jsElCustomizeStatus .multi-edit-text-input').attr("disabled", true);
			$('#jsElCustomizeStatus span[id^="checkboxContainer_"]').html('x');
			$('#jsElCustomizeStatus span[id^="del_"]').html('x');
			$('#jsElConfigStatus input').attr("disabled", true);
		}//end if
	});

	mi_init_rows('mi_status',<?php if(isset($_REQUEST['mi_status'])){echo MiString::array2js($_REQUEST['mi_status']);}else{echo '[]';}?>);
	for(var i=101;i<=input_count;i++){
		if(getId('id_'+i) && (getId('is_system_'+i).value == 1)){
			jQuery('#name_'+i).attr('readonly',true);
			jQuery('#del_'+i).html('x');
			jQuery('#input_group_'+i).click(function(){alert('Trạng thái mặc định, quý khách vui lòng không sửa xoá ');return false;});
			$('#checkboxContainer_'+i).html('');
		}
		if(getId('id_'+i) && getId('orders_total_'+i) && getId('orders_total_'+i).value !== null && getId('orders_total_'+i).value >=1){
			$('#name_'+i).attr('readonly',true);
			$('#del_'+i).html("(*)");
            $('#checkboxContainer_'+i).html('');
		}
	}

</script>

<!-- color-picker -->
<script>
	$(function () {
		$(document).keypress(function(e) {
			if (allowAddOrderStatus) {
				if(e.which == 13) {
					mi_add_new_row('mi_status');
					return false;
				}
			}//end if
		});

		$('.color-picker-ip').colorpicker({
			horizontal: true,
			extensions: [
				{
					name: 'swatches',
					customClass: 'colorpicker-2x',
					colors: {
					'#000000': '#000000',
					'#ffffff': '#ffffff',
					'#FF0000': '#FF0000',
					'#777777': '#777777',
					'#337ab7': '#337ab7',
					'#5cb85c': '#5cb85c',
					'#5bc0de': '#5bc0de',
					'#f0ad4e': '#f0ad4e',
					'#d9534f': '#d9534f',
					'#cccccc': '#cccccc'
					,'#2c72c7': '#2c72c7'
					,'#74a82a': '#74a82a'
					,'#643ea0': '#643ea0'
					,'#a95337': '#a95337'
					,'#009289': '#009289'
					,'#ca998a': '#ca998a'
					,'#f5851f': '#f5851f'
					,'#91cdda':'#91cdda'
					,'#ff99cc':'#ff99cc'
					,'##336699':'#336699'
					,'#3366cc':'#3366cc'
					,'#003399':'#003399'
					,'#000099':'#000099'
					,'#0000cc':'#0000cc'
					,'#000066':'#000066'
					,'#669999':'#669999'
					,'#009999':'#009999'
					,'#33cccc':'#33cccc'
					,'#00ccff':'#00ccff'
					,'#0099ff':'#0099ff'
					,'#006600':'#006600'
					,'#003300':'#003300'
					,'#009933':'#009933'
					,'#336600':'#336600'
					,'#ff9966':'#ff9966'
					,'#ff6666':'#ff6666'
					,'#ff0066':'#ff0066'
					,'#cc0066':'#cc0066'
					,'#990033':'#990033'
					,'#cc0000':'#cc0000'
					,'#990033':'#990033'
					,'#993333':'#993333'
					,'#800000':'#800000'
					,'#990000':'#990000'
					,'#ffccff':'#ffccff'
					,'#ffcccc':'#ffcccc'
					,'#ffffcc':'#ffffcc'
					,'#ffff66':'#ffff66'
					,'#ccff33':'#ccff33'
					,'#cccc00':'#cccc00'
					},
					namesAsValues: true
				}
			]
		});
		
		if (!allowAddOrderStatus) {
			$('#jsElConfigStatus *').unbind();
		}//end if
	});

	if (allowAddOrderStatus) {
		$('#add-row').click(function(){
			$('.color-picker-ip').colorpicker({
				horizontal: true,
					extensions: [
					{
						name: 'swatches',
						customClass: 'colorpicker-2x',
						colors: {
						'#000000': '#000000',
						'#ffffff': '#ffffff',
						'#FF0000': '#FF0000',
						'#777777': '#777777',
						'#337ab7': '#337ab7',
						'#5cb85c': '#5cb85c',
						'#5bc0de': '#5bc0de',
						'#f0ad4e': '#f0ad4e',
						'#d9534f': '#d9534f',
						'#cccccc': '#cccccc'
						,'#2c72c7': '#2c72c7'
						,'#74a82a': '#74a82a'
						,'#643ea0': '#643ea0'
						,'#a95337': '#a95337'
						,'#009289': '#009289'
						,'#ca998a': '#ca998a'
						,'#f5851f': '#f5851f'
						,'#91cdda':'#91cdda'
						,'#ff99cc':'#ff99cc'
						,'##336699':'#336699'
						,'#3366cc':'#3366cc'
						,'#003399':'#003399'
						,'#000099':'#000099'
						,'#0000cc':'#0000cc'
						,'#000066':'#000066'
						,'#669999':'#669999'
						,'#009999':'#009999'
						,'#33cccc':'#33cccc'
						,'#00ccff':'#00ccff'
						,'#0099ff':'#0099ff'
						,'#006600':'#006600'
						,'#003300':'#003300'
						,'#009933':'#009933'
						,'#336600':'#336600'
						,'#ff9966':'#ff9966'
						,'#ff6666':'#ff6666'
						,'#ff0066':'#ff0066'
						,'#cc0066':'#cc0066'
						,'#990033':'#990033'
						,'#cc0000':'#cc0000'
						,'#990033':'#990033'
						,'#993333':'#993333'
						,'#800000':'#800000'
						,'#990000':'#990000'
						,'#ffccff':'#ffccff'
						,'#ffcccc':'#ffcccc'
						,'#ffffcc':'#ffffcc'
						,'#ffff66':'#ffff66'
						,'#ccff33':'#ccff33'
						,'#cccc00':'#cccc00'
						},
						namesAsValues: true
					}
				]
			});
		});
	}//end if
</script>
