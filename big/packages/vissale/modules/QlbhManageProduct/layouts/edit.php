<link href="skins/admin/scripts/jquery.cleditor.css" rel="stylesheet"/>
<script src="skins/admin/scripts/jquery.cleditor.min.js"></script>
<span style="display:none">
	<span id="mi_product_sample">
	  <div id="input_group_#xxxx#" class="multi-item-group">
          <span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1" class="form-control"></span>
          <span class="multi-edit-input" style="width:40px;"><input  name="mi_product[#xxxx#][id]" type="text" id="id_#xxxx#" class="form-control" style="width:40px;text-align:right;" value="(auto)" readonly tabindex="-1"></span>
          <span class="multi-edit-input"><input  name="mi_product[#xxxx#][code]" style="width:100px;" class="form-control" type="text" id="code_#xxxx#" onchange="CheckDuplicatedCode(this);"></span>
          <span class="multi-edit-input"><input  name="mi_product[#xxxx#][name]" style="width:200px;" class="form-control" type="text" id="name_#xxxx#"></span>      
          <span class="multi-edit-input"><select  name="mi_product[#xxxx#][category_id]" style="width:150px;" class="form-control" id="category_id_#xxxx#">[[|category_id_options|]]</select></span>                    
          <span class="multi-edit-input"><input  name="mi_product[#xxxx#][price]" style="width:120px;text-align:right" class="form-control" type="text" id="price_#xxxx#"></span>      
          <span class="multi-edit-input"><select  name="mi_product[#xxxx#][unit_id]" style="width:100px;" class="form-control" id="unit_id_#xxxx#">[[|unit_id_options|]]</select></span>
          <span class="multi-edit-input no-border" style="width:20px;"><img src="skins/default/images/buttons/delete.gif" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_product','#xxxx#','');event.returnValue=false;" style="cursor:pointer;"/></span>
	  </div><br clear="all">
	</span>
</span>
<?php 
$title = (URL::get('cmd')=='delete')?'Xóa mặt hàng':'Danh sách mặt hàng';?>
<div align="center">
<form name="EditQlbhManageProductForm" method="post" enctype="multipart/form-data">
<table cellspacing="0" width="100%" class="multi-item-table">
	<tr valign="top" bgcolor="#FFFFFF">
		<td align="left">
        <table class="table">
            <tr>
                <td width="90%" class="form-title"><?php echo $title;?></td>
                <?php if(User::can_add(false,ANY_CATEGORY)){?><td width="1%"><a href="javascript:void(0)" onclick="EditQlbhManageProductForm.submit();"  class="btn btn-primary">Ghi</a></td><?php }?>
                <td><input type="button" value="  Xóa  " onclick="mi_delete_selected_row('mi_product');" class="btn btn-danger" /></td>
            </tr>
        </table>
		</td>
	</tr>
	<tr valign="top">	
	<td>
	<input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
	<input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
  <?php if(Form::$current->is_error()){?><div><?php echo Form::$current->error_messages();?></div><?php }?>
	<table width="100%" cellspacing="3">
  <tr valign="top">
		<td>
		<div class="multi-item-wrapper">
      <span id="mi_product_all_elems">
        <span style="white-space:nowrap;">
          <span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_product',this.checked);"></span>
          <span class="multi-edit-input header" style="width:40px;">ID</span>
          <span class="multi-edit-input header" style="width:100px;">Mã mặt hàng</span>
          <span class="multi-edit-input header" style="width:200px;">Tên Mặt hàng</span>
          <span class="multi-edit-input header" style="width:150px;">Loại</span>                    
          <span class="multi-edit-input header" style="width:120px;">Giá bán</span>          
          <span class="multi-edit-input header" style="width:100px;">Đơn vị</span>
          <span class="multi-edit-input header no-border no-bg" style="width:20px;">&nbsp;</span>
          <br clear="all">
        </span>
      </span>
		</div>
    <br clear="all">
		<div style="padding:5px;"><input type="button" value="   Thêm mới   " onclick="mi_add_new_row('mi_product');" class="btn btn-info"></div>
		<div>[[|paging|]]</div>
		</td>
	</tr>
	</table>
    <input name="confirm_edit" type="hidden" value="1" />
	</td>
</tr>
</table>
</form>
<script src="packages/core/includes/js/multi_items.js"></script>
<script>
mi_init_rows('mi_product',<?php if(isset($_REQUEST['mi_product'])){echo MiString::array2js($_REQUEST['mi_product']);}else{echo '[]';}?>);
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

