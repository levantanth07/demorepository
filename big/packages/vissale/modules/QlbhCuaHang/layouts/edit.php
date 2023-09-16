<span style="display:none">
	<span id="mi_shop_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
      <span class="multi-edit-input"><span style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span></span>
      <span class="multi-edit-input"><input  name="mi_shop[#xxxx#][id]" type="text" id="id_#xxxx#" style="width:30px;text-align:right;font-size:11px;" value="(auto)" tabindex="-1" class="form-control" readonly></span>
      <span class="multi-edit-input"><input  name="mi_shop[#xxxx#][name]" style="width:120px;" type="text" id="name_#xxxx#" class="form-control"></span>      
      <span class="multi-edit-input"><input  name="mi_shop[#xxxx#][address]" style="width:160px;" type="text" id="address_#xxxx#" class="form-control"></span>
      <span class="multi-edit-input"><input  name="mi_shop[#xxxx#][phone]" style="width:100px;" type="text" id="phone_#xxxx#" class="form-control"></span>
      <span class="multi-edit-input"><select  name="mi_shop[#xxxx#][zone_id]" style="width:130px;" id="zone_id_#xxxx#" class="form-control">[[|zone_id_options|]]</select></span>
      <!--IF:cond(User::can_view(false,ANY_CATEGORY))-->
      <span class="multi-edit-input"><input  name="mi_shop[#xxxx#][account_id]" style="width:120px;" type="text" id="account_id_#xxxx#" class="form-control" readonly></span>      
      <!--/IF:cond-->
      <span class="multi-edit-input no-border" style="width:20px;padding-left:5px;"><input type="button" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_shop','#xxxx#','');event.returnValue=false;" value="Xóa" class="btn btn-danger btn-sm"></span>
		</div><br clear="all">
	</span>
</span>
<?php 
$title = (URL::get('cmd')=='delete')?'Xóa cửa hàng':'Danh sách cửa hàng';?>
<form name="EditQlbhCuaHangForm" method="post" enctype="multipart/form-data">
<div class="row">
  <div class="col-xs-9 col-sm-9">
  	<h2><?php echo $title?></h2>
    <select name="search_zone_id" id="search_zone_id" onChange="EditQlbhCuaHangForm.submit();"></select>
  </div>
  <div class="col-xs-3 col-sm-3" style="padding-top:23px;">
  <a href="javascript:void(0)" onclick="jQuery('#confirm_edit').val(1);EditQlbhCuaHangForm.submit();" class="btn btn-primary">Ghi</a>
  <input type="button" value="  Xóa  " onclick="mi_delete_selected_row('mi_shop');" class="btn btn-danger" />
  </div>
</div>
<hr>
<div class="row">
<div class="col-xs-12 col-sm-12">
<table cellspacing="0" width="100%">
	<tr valign="top">	
	<td>
	<input  name="selected_ids" type="" value="<?php echo URL::get('selected_ids');?>">
	<input  name="deleted_ids" id="deleted_ids" type="" value="<?php echo URL::get('deleted_ids');?>">
  <?php if(Form::$current->is_error()){?><div><?php echo Form::$current->error_messages();?></div><?php }?>
	<table width="100%" cellspacing="3">
  <tr valign="top">
		<td>
		<div class="multi-item-wrapper">
      <span id="mi_shop_all_elems">
        <span style="white-space:nowrap;">
          <span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_shop',this.checked);">
          </span>
          <span class="multi-edit-input header" style="width:30px;">ID</span>
          <span class="multi-edit-input header" style="width:120px;">Tên cửa hàng</span>
          <span class="multi-edit-input header" style="width:160px;">Địa chỉ</span>
          <span class="multi-edit-input header" style="width:100px;">Điện thoại</span>
          <span class="multi-edit-input header" style="width:130px;">Tỉnh thành</span>
          <!--IF:cond(User::can_view(false,ANY_CATEGORY))-->
          <span class="multi-edit-input header" style="width:120px;">Đại lý</span>          
          <!--/IF:cond-->
          <br clear="all">
      </span>
		</div>
    	<br clear="all">
		<div style="padding:5px;"><input type="button" value="Thêm mới" onclick="mi_add_new_row('mi_shop');" class="btn btn-sm btn-info"></div>
		<div>[[|paging|]]</div>
		</td>
	</tr>
	</table>
    <input name="confirm_edit" type="hidden" id="confirm_edit"/>
	</td>
</tr>
</table>
</div>
</div>
</form>
<script src="packages/core/includes/js/multi_items.js"></script>
<script>
mi_init_rows('mi_shop',<?php if(isset($_REQUEST['mi_shop'])){echo MiString::array2js($_REQUEST['mi_shop']);}else{echo '[]';}?>);
</script>
<script>
jQuery(document).ready(function(){
	for(var i=101;i<=input_count;i++){
		if(getId('id_'+i)){
			
		}
	}
});
function CheckDuplicatedCode(obj){
	for(var i=101;i<=input_count;i++){
		if(getId('id_'+i)){
			if(getId('code_'+i).id != obj.id && getId('code_'+i).value == obj.value){
				alert('Trùng mã "' + obj.value +'". Bạn vui lòn chọn mã khác');
				obj.value = '';
				obj.focus();
			}
		}
	}
}
</script>

