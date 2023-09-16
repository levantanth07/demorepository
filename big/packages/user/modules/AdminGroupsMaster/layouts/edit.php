<script src="packages/core/includes/js/multi_items.js"></script>
	<span style="display:none">
	<span id="mi_label_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
      <span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span>
      <span class="multi-edit-input" style="width:40px;"><input  name="mi_label[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
      <span class="multi-edit-input"><input  name="mi_label[#xxxx#][name]" style="width:250px;" class="multi-edit-text-input" type="text" id="name_#xxxx#"></span>
			<span class="multi-edit-input"><input  name="mi_label[#xxxx#][account_id]" style="width:250px;" class="multi-edit-text-input" type="text" id="account_id_#xxxx#"></span>
      <span class="multi-edit-input"><input  name="mi_label[#xxxx#][sort_order]" style="width:100px;" class="multi-edit-text-input" type="text" id="sort_order_#xxxx#"></span>
      <span class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;"><img src="assets/admin/images/buttons/delete.png" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_label','#xxxx#','');event.returnValue=false;" alt="Xóa"></span>
		</div>
    <br clear="all">
	</span>
</span>
<?php
$title = (URL::get('cmd')=='delete')?'Xóa nhóm user':' Quản lý nhóm user';?>
<div class="panel">
	<fieldset id="toolbar">
		<div id="toolbar-title">
			<?php echo $title;?>
		</div>
		<div id="toolbar-content">
			<table align="right">
				<tbody>
				<tr>
					<td id="toolbar-save"  align="center"><a onclick="EditAdminGroupsMasterForm.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
					<!--<td id="toolbar-back"  align="center"><a href="#" onClick="mi_delete_selected_row('mi_label');"> <span title="New"></span> Xóa </a> </td>-->
				</tr>
				</tbody>
			</table>
		</div>
	</fieldset><br>
	<fieldset id="toolbar">
		<form name="EditAdminGroupsMasterForm" method="post" enctype="multipart/form-data">
			<table class="table">
				<tr valign="top">
					<td>
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
										<div id="mi_label_all_elems">
											<div>
												<span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_label',this.checked);"></span>
												<span class="multi-edit-input header" style="width:40px;">ID</span>
												<span class="multi-edit-input header" style="width:252px;">Nhãn</span>
												<span class="multi-edit-input header" style="width:252px;">ID Tài khoản quản lý</span>
												<span class="multi-edit-input header" style="width:102px;">Thứ tự</span>
												<span class="multi-edit-input header" style="width:45px;">&nbsp;</span>
												<br clear="all">
											</div>
										</div>
									</div>
									<br clear="all">
									<hr>
									<div><input type="button" value="Thêm dòng" class="btn btn-default btn-sm" onclick="mi_add_new_row('mi_label');"></div>
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
		</form>
	</fieldset>
</div>
<script>
mi_init_rows('mi_label',<?php if(isset($_REQUEST['mi_label'])){echo MiString::array2js($_REQUEST['mi_label']);}else{echo '[]';}?>);
</script>
