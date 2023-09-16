<script src="packages/core/includes/js/multi_items.js"></script>
	<span style="display:none">
	<span id="mi_product_sample">
        <div id="input_group_#xxxx#" class="multi-item-group">
            <span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span>
            <span class="multi-edit-input" style="width:40px;"><input  name="mi_product[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][post_id]" style="width:150px;" class="multi-edit-text-input" type="text" id="post_id_#xxxx#"></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][description]" style="width:150px;" class="multi-edit-text-input" type="text" id="description_#xxxx#"></span>
            <span class="multi-edit-input"><select  name="mi_product[#xxxx#][fb_page_id]" style="width:200px;" class="multi-edit-text-input" id="fb_page_id_#xxxx#">[[|fb_page_id_options|]]</select></span>
            <span class="multi-edit-input"><select  name="mi_product[#xxxx#][product_id]" style="width:150px;" class="multi-edit-text-input" id="product_id_#xxxx#">[[|product_id_options|]]</select></span>
            <span class="multi-edit-input"><textarea  name="mi_product[#xxxx#][answer_phone]" style="width:200px;height:50px;font-size:11px;line-height: 15px;background: #fdffd4" class="multi-edit-text-input" id="answer_phone_#xxxx#"></textarea></span>
            <span class="multi-edit-input"><textarea  name="mi_product[#xxxx#][answer_nophone]" style="width:200px;height:50px;font-size:11px;line-height: 15px;" class="multi-edit-text-input" id="answer_nophone_#xxxx#"></textarea></span>
            <span class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;"><a href="#" class="btn btn-default btn-sm" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_product','#xxxx#','');event.returnValue=false;" alt="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
        </div>
    <br clear="all">
	</span>
</span>
<?php
$title = (URL::get('cmd')=='delete')?'Xóa sản phẩm theo Post của FB':'Khai báo sản phẩm theo Post của Facebook';?>
<section class="content">
<div class="row">
<fieldset id="toolbar">
 	<div id="toolbar-title" class="fb-icon">
		<?php echo $title;?>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <td id="toolbar-save"  align="center"><a onclick="EditAdminProductPostForm.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
		  <!--<td id="toolbar-back"  align="center"><a href="#" onClick="mi_delete_selected_row('mi_product');"> <span title="New"></span> Xóa </a> </td>-->
		</tr>
	  </tbody>
	</table>
  </div>
</fieldset><br>
<fieldset id="toolbar">
	<form name="EditAdminProductPostForm" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-4">
                <div class="input-group">
                    <input name="keyword" type="text" id="keyword" class="form-control" placeholder="Nhập page id hoặc post id">
                    <span class="input-group-btn">
                        <input  name="search" type="submit" id="search" class="btn btn-default" value="Tìm kiếm">
                    </span>
                </div>
            </div>
            <div class="col-md-8">
                <div class="search-region">Tổng số: <strong>[[|total|]]</strong> sản phẩm</div>
            </div>
        </div>
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
					<div id="mi_product_all_elems">
						<div>
							<span class="multi-edit-input header" style="width:20px;"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_product',this.checked);"></span>
							<span class="multi-edit-input header" style="width:40px;">ID</span>
							<span class="multi-edit-input header" style="width:152px;">FB Post ID</span>
							<span class="multi-edit-input header" style="width:152px;">Mô tả</span>
							<span class="multi-edit-input header" style="width:202px;">Fb Page</span>
							<span class="multi-edit-input header" style="width:152px;">Sản phẩm</span>
							<span class="multi-edit-input header" style="width:202px;">Trả lời có số dt</span>
							<span class="multi-edit-input header" style="width:202px;">Trả lời không có số dt</span>
							<span class="multi-edit-input header" style="width:45px;">&nbsp;</span>
							<br clear="all">
						</div>
					</div>
				</div>
			<br clear="all">
			<hr>
				<div><input type="button" value="Thêm" class="btn btn-default btn-sm" onclick="mi_add_new_row('mi_product');"></div>
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
</section>
<script>
mi_init_rows('mi_product',<?php if(isset($_REQUEST['mi_product'])){echo MiString::array2js($_REQUEST['mi_product']);}else{echo '[]';}?>);
</script>
