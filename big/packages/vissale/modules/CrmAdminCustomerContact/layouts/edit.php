<script src="packages/core/includes/js/multi_items.js"></script>
	<span style="display:none">
	<span id="mi_bundle_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
      <span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span>
      <span class="multi-edit-input" style="width:40px;"><input  name="mi_bundle[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
      <span class="multi-edit-input"><input  name="mi_bundle[#xxxx#][full_name]" style="width:250px;" class="multi-edit-text-input" type="text" id="full_name_#xxxx#"></span>
      <span class="multi-edit-input"><input  name="mi_bundle[#xxxx#][phone]" style="width:150px;" class="multi-edit-text-input" type="text" id="phone_#xxxx#"></span>
      <span class="multi-edit-input"><input  name="mi_bundle[#xxxx#][email]" style="width:250px;" class="multi-edit-text-input" type="text" id="email_#xxxx#"></span>
      <span class="multi-edit-input"><input  name="mi_bundle[#xxxx#][address]" style="width:250px;" class="multi-edit-text-input" type="text" id="address_#xxxx#"></span>
      <span class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;"><a class="btn btn-default btn-sm" href="#" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_bundle','#xxxx#','');event.returnValue=false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
		</div>
    <br clear="all">
	</span>
</span>
<?php $title = (URL::get('cmd')=='delete')?'Xóa người giới thiệu':' Quản lý người giới thiệu ';?>
<section class="content">
<div class="row">
	<fieldset id="toolbar">
		<div id="toolbar-title">
			<?php echo $title;?>
		</div>
		<div id="toolbar-content">
		<table align="right">
		  <tbody>
			<tr>
			  <td id="toolbar-save"  align="center"><a onclick="EditCrmAdminCustomerContactForm.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
			</tr>
		  </tbody>
		</table>
	  </div>
	</fieldset><br>
	<fieldset id="toolbar">
	<form name="EditCrmAdminCustomerContactForm" method="post" enctype="multipart/form-data">
	<table class="table">
		<tr valign="top">
		<td>
		<input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
		<input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
		<table class="table">
	  <tr valign="top">
			<td>
			<div class="multi-item-wrapper">
		  <div id="mi_bundle_all_elems">
			<div>
			  <span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_bundle',this.checked);"></span>
			  <span class="multi-edit-input header" style="width:40px;">ID</span>
			  <span class="multi-edit-input header" style="width:252px;">Họ và tên</span>
			  <span class="multi-edit-input header" style="width:152px;">Điện thoại</span>
			  <span class="multi-edit-input header" style="width:252px;">email</span>
			  <span class="multi-edit-input header" style="width:252px;">Địa chỉ</span>
			  <span class="multi-edit-input header" style="width:45px;">&nbsp;</span>
			  <br clear="all">
			</div>
		  </div>
			</div>
		<br clear="all">
		<hr>
			<div><input type="button" value="Thêm" class="btn btn-default btn-sm" onclick="mi_add_new_row('mi_bundle');"></div>
			<div>[[|paging|]]</div>
		<hr>
			</td>
		</tr>
		</table>
		<input  name="confirm_edit" type="hidden" value="1" />
		<input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
		</td>
	</tr>
	</table>
	</form>
	</fieldset>
</div>
</section>
<script>
mi_init_rows('mi_bundle',<?php if(isset($_REQUEST['mi_bundle'])){echo MiString::array2js($_REQUEST['mi_bundle']);}else{echo '[]';}?>);
</script>
