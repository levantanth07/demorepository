<script src="packages/core/includes/js/multi_items.js"></script>
	<span style="display:none">
	<span id="mi_roles_activities_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
      <span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span>
      <span class="multi-edit-input" style="width:40px;"><input  name="mi_roles_activities[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
      <span class="multi-edit-input"><input  name="mi_roles_activities[#xxxx#][name]" style="width:250px;" class="multi-edit-text-input" type="text" id="name_#xxxx#"></span>
      <span class="multi-edit-input"><input  name="mi_roles_activities[#xxxx#][code]" style="width:250px;" class="multi-edit-text-input" type="text" id="code_#xxxx#"></span>
      <span class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;"><a class="btn btn-default btn-sm" href="#" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_roles_activities','#xxxx#','');event.returnValue=false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
		</div>
    <br clear="all">
	</span>
</span>
<?php $title = (URL::get('cmd')=='delete')?'Xóa Quản lý quyền':' Quản lý Quản lý quyền ';?>
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
			  <td id="toolbar-save"  align="center"><a onclick="EditAdminRolesActivitiesForm.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
			</tr>
		  </tbody>
		</table>
	  </div>
	</fieldset><br>
	<fieldset id="toolbar">
	<form name="EditAdminRolesActivitiesForm" method="post" enctype="multipart/form-data">
	<table class="table">
		<tr valign="top">
		<td>
		<input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
		<input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
		<table class="table">
	  <tr valign="top">
			<td>
			<div class="multi-item-wrapper">
		  <div id="mi_roles_activities_all_elems">
			<div>
			  <span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_roles_activities',this.checked);"></span>
			  <span class="multi-edit-input header" style="width:40px;">ID</span>
			  <span class="multi-edit-input header" style="width:250px;">Tên </span>
			  <span class="multi-edit-input header" style="width:250px;">CODE </span>
			  <span class="multi-edit-input header" style="width:45px;">&nbsp;</span>
			  <br clear="all">
			</div>
		  </div>
			</div>
		<br clear="all">
		<hr>
			<div><input type="button" value="Thêm" class="btn btn-default btn-sm" onclick="mi_add_new_row('mi_roles_activities');"></div>
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
mi_init_rows('mi_roles_activities',<?php if(isset($_REQUEST['mi_roles_activities'])){echo MiString::array2js($_REQUEST['mi_roles_activities']);}else{echo '[]';}?>);
</script>
