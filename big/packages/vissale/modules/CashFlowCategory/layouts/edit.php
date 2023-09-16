<script src="packages/core/includes/js/multi_items.js"></script>
	<span style="display:none">
	<span id="mi_category_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
          <span class="multi-edit-input hidden">
              <input  name="mi_category[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly>
              <input  name="mi_category[#xxxx#][group_id]" type="text" id="group_id_#xxxx#">
          </span>
          <span class="multi-edit-input"><input  name="mi_category[#xxxx#][name]" style="width:250px;" class="multi-edit-text-input" type="text" id="name_#xxxx#"></span>
            <span class="multi-edit-input">
                <select style="width: 100px;"  name="mi_category[#xxxx#][type]" class="multi-edit-text-input" id="type_#xxxx#">
                    <option value="1">Thu</option>
                    <option value="0">Chi</option>
                </select>
            </span>
            <span class="multi-edit-input no-border" id="del_#xxxx#" style="width:40px;text-align:center;padding-top:5px;"><a class="btn btn-default btn-sm" href="#" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_category','#xxxx#','');event.returnValue=false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
		</div>
    <br clear="all">
	</span>
</span>
<?php $title = (URL::get('cmd')=='delete')?'Xóa phân loại thu chi':' Quản lý phân loại thu nhi ';?>
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
			  <td id="toolbar-save"  align="center"><a onclick="EditCashFlowCategoryForm.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
			</tr>
		  </tbody>
		</table>
	  </div>
	</fieldset><br>
	<fieldset id="toolbar">
	<form name="EditCashFlowCategoryForm" method="post" enctype="multipart/form-data">
	<table class="table">
		<tr valign="top">
		<td>
		<input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
		<input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
		<table class="table">
	  <tr valign="top">
			<td>
			<div class="multi-item-wrapper">
		  <div id="mi_category_all_elems">
			<div>
			  <span class="multi-edit-input header hidden" style="width:40px;">ID</span>
			  <span class="multi-edit-input header" style="width:250px;">Tên phân loại</span>
			  <span class="multi-edit-input header" style="width:105px;">Loại phiếu</span>
			  <span class="multi-edit-input header" style="width:45px;">&nbsp;</span>
			  <br clear="all">
			</div>
		  </div>
			</div>
		<br clear="all">
		<hr>
			<div><input type="button" value="Thêm" class="btn btn-default btn-sm" onclick="mi_add_new_row('mi_category');"></div>
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
    let mi_category = <?php if(isset($_REQUEST['mi_category'])){echo MiString::array2js($_REQUEST['mi_category']);}else{echo '[]';}?>;
    mi_init_rows('mi_category',mi_category);
    for(var i=101;i<=input_count;i++){
        if(getId('id_'+i) &&  getId('group_id_'+i).value == 0){
            jQuery('#name_'+i).attr('readonly',true);
            jQuery('#type_'+i).attr('style','width: 100px;pointer-events: none;color:#CCC;');
            jQuery('#del_'+i).html('x');
            jQuery('#name_'+i).click(function(){alert('Phân loại mặc định, quý khách vui lòng không sửa xoá ');return false;})
        }
    }
</script>
