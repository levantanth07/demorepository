<script>
	function check_selected()
	{
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked)
			{
				status = true;
			}
		});	
		return status;
	}
	function make_cmd(cmd)
	{
		jQuery('#cmd').val(cmd);
		document.ListCategoryForm.submit();
	}
</script>
<form method="post" name="SearchCategoryForm" id="SearchCategoryForm">
<fieldset id="toolbar">
	<div id="toolbar-title">
        <!--IF:cond([[=district_name=]])-->
        Quản lý quận huyện [[|district_name|]] / <a href="<?=Url::build_current(['province_id'=>[[=province_id=]]])?>">[[|province_name|]]</a><span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
        <!--ELSE-->
		Quản lý tỉnh thành [[|province_name|]]<span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
        <!--/IF:cond-->
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
            <?php
            if(URL::get('cmd')=='delete' and User::can_delete(false,ANY_CATEGORY)){?>
                <td id="toolbar-trash"  align="center"><a onclick="$('cmd').cmd='delete';ListCategoryForm.submit();" > <span title="Trash"> </span> [[.Trash.]] </a> </td>
                <td id="toolbar-back"  align="center"><a href="<?php echo URL::build_current();?>"> <span title="Back"> </span> [[.Back.]] </a> </td>
            <?php
		}?>
            <!--IF:cond(Url::iget('district_id'))-->
            <td> <a href="<?=Url::build_current(['district_id','cmd'=>'add_ward'])?>" class="btn btn-success"><span title="new"> </span>Thêm phường xã mới</a></td>
            <!--/IF:cond-->
            <!--IF:cond(Url::iget('province_id'))-->
            <td> <a href="<?=Url::build_current(['district_id','cmd'=>'add_district'])?>" class="btn btn-success"><span title="new"> </span>Thêm tỉnh / thành</a></td>
            <!--/IF:cond-->
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
</form>	
<br>
<fieldset id="toolbar">
    <form name="ListCategoryForm" method="post" class="form-inline">
		<div class="pull-right">
            <div class="form-group">
                <!--IF:district_id_cond(!Url::iget('district_id'))-->
                Chọn tỉnh thành
                <select name="province_id" id="province_id" onchange="document.SearchCategoryForm.submit();" class="form-control"></select>
                <!--/IF:district_id_cond-->
            </div>
            <a class="btn btn-default" href="https://partner.viettelpost.vn/v2/categories/listProvince/" target="_blank"> <i class="fa fa-search"></i> Check tỉnh / thành của Viettel post</a>
            <a class="btn btn-default" href="https://partner.viettelpost.vn/v2/categories/listDistrict?provinceId=1" target="_blank"> <i class="fa fa-search"></i> Check quận / huyện của Viettel post</a>
        </div>
        <br>
		<table class="table bordered-table table-striped">
		    <thead>
                    <tr valign="middle" bgcolor="<?php echo Portal::get_setting('crud_list_item_bgcolor','#F0F0F0');?>" style="line-height:20px">
                    <th width="1%" title="Chọn tất cả"><input type="checkbox" value="1" id="Category_all_checkbox" onclick="select_all_checkbox(this.form,'Category',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th />
                    <th>STT</th>
                    <th>Tên thường gọi</th>
                    <th>Tên Viettel</th>
					<th>ID EMS</th>
                    <th></th>
                </tr>
			</thead>
			<tbody>
			<?php $i=1;?>
			<!--LIST:items-->
			<?php $level =1;?>
			<tr>
				<td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'Category',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="Category_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td />
                <td width="1%" nowrap="nowrap">
					<?php if($level==1){echo $i++;}?>
				</td>
				<td align="left" nowrap <?php if(User::can_edit(false,ANY_CATEGORY) and Url::sget('page')!='portal_category'){?> <?php }?>>
                    <span class="page_indent">&nbsp;</span>
                    <!--IF:cond(Url::get('province_id'))-->
                    <span id="district_name_[[|items.id|]]">
						[[|items.district_name|]]
					</span>
                    <!--ELSE-->
                    <!--IF:cond_(Url::get('district_id'))-->
                    <span id="ward_name_[[|items.id|]]">
						[[|items.ward_name|]]
					</span>
                    <!--ELSE-->
                    <span id="province_name_[[|items.id|]]">
						[[|items.province_name|]]
					</span>
                    <!--/IF:cond_-->
                    <!--/IF:cond-->
                </td>
                <td align="left" nowrap <?php if(User::can_edit(false,ANY_CATEGORY) and Url::sget('page')!='portal_category'){?> <?php }?>>
                    <span class="page_indent">&nbsp;</span>
                    <a id="quick_edit_[[|items.id|]]" href="#" title="Quick edit" onClick="enableEdit('[[|items.id|]]');return false"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
                    <!--IF:cond(Url::get('province_id'))-->
                    <span id="viettel_name_[[|items.id|]]">
						[[|items.viettel_district_name|]]
					</span>
                    <!--ELSE-->
                    <!--IF:cond__(Url::get('district_id'))-->
                    <span id="viettel_name_[[|items.id|]]">
						[[|items.ward_name|]]
					</span>
                    <!--ELSE-->
                    <span id="viettel_name_[[|items.id|]]">
						[[|items.viettel_province_name|]]
					</span>
                    <!--/IF:cond__-->
                    <!--/IF:cond-->
                </td>
				<td>
				<span class="page_indent">&nbsp;</span>
                    <a id="quick_edit_ems_[[|items.id|]]" href="#" title="Quick edit" onClick="enableEmsEdit('[[|items.id|]]');return false">
						<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
					</a>
                    <!--IF:cond(Url::get('province_id'))-->
                    <span id="ems_code_[[|items.id|]]">
						[[|items.ems_district_code|]]
					</span>
                    <!--ELSE-->
                    <!--IF:cond__(Url::get('district_id'))-->
                    <span id="ems_code_[[|items.id|]]">
						[[|items.ems_ward_code|]]
					</span>
                    <!--ELSE-->
                    <span id="ems_code_[[|items.id|]]">
						[[|items.ems_provice_code|]]
					</span>
                    <!--/IF:cond__-->
                    <!--/IF:cond-->
				</td>
                <td class="text-right">
                    <!--IF:cond(!Url::get('province_id') and !Url::get('district_id'))-->
                    <a class="btn btn-warning btn-sm" href="<?=Url::build_current(['province_id'=>[[=items.province_id=]]])?>">Sửa quận huyện</a>
                    <!--ELSE-->
                    <!--IF:cond_(Url::get('district_id'))-->
                    <a class="btn btn-warning btn-sm" href="<?=Url::build_current(['cmd'=>'edit_ward','district_id','id'=>[[=items.id=]]])?>">Sửa</a>
                    <a onclick="if(!confirm('Bạn có chắc chắn muốn xóa?')){return false;}" class="btn btn-danger btn-sm" href="<?=Url::build_current(['cmd'=>'delete_ward','district_id','id'=>[[=items.id=]]])?>">Xóa</a>
                    <!--ELSE-->
                    <a class="btn btn-success btn-sm" href="<?=Url::build_current(['district_id'=>[[=items.district_id=]],'cmd'=>'edit_district'])?>">Sửa</a>
                    <a class="btn btn-warning btn-sm" href="<?=Url::build_current(['district_id'=>[[=items.district_id=]]])?>">Sửa phường xã</a>
                    <!--/IF:cond_-->
                    <!--/IF:cond-->
                </td>
			</tr>
			<!--/LIST:items-->
			</tbody>
		</table>
		<input type="hidden" name="cmd" value="" id="cmd"/>
</form>
</fieldset>
<?php
	$cmdEms = "update_ems_province";
	if (Url::get('province_id')) {
		$cmdEms = "update_ems_district";
	} else if (Url::get('district_id')) {
		$cmdEms = "update_ems_ward";
	}
?>
<script>
	function enableEdit(id){
		let value = jQuery('#viettel_name_'+id).html();
        value = $.trim(value);
		jQuery('#viettel_name_'+id).html('<input  name="quick_edit_name_'+id+'" type="text" id="quick_edit_name_'+id+'" value="'+value+'" class="form-control" onchange="upateEdit(this.value,'+id+');">');
		jQuery('#quick_edit_'+id).hide();
		jQuery('#quick_cancel_'+id).show();
	}
	function enableEmsEdit(id){
		let value = jQuery('#ems_code_'+id).html();
        value = $.trim(value);
		jQuery('#ems_code_'+id).html('<input  name="quick_edit_ems_code_'+id+'" type="text" id="quick_edit_ems_code_'+id+'" value="'+value+'" class="form-control" onchange="upateEmsEdit(this.value,'+id+');">');
		jQuery('#quick_edit_ems_'+id).hide();
		// jQuery('#quick_cancel_'+id).show();
	}
	function upateEmsEdit(value,id){
		$.ajax({
			method: "POST",
			url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
			data : {
				'cmd':'<?= $cmdEms ?>',
				'zone_id':id,
				'value':value
			},
			beforeSend: function(){
				//jQuery('#update-alert').show();
			},
			success: function(content){
				content = $.trim(content);
				$('#viettel_province_name'+id).html(content);
				//jQuery('#update-alert').hide();
				//jQuery('#quick_edit_'+id).show();
				//jQuery('#quick_cancel_'+id).hide();
			},
			error: function(){
				alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
			}
		});
	}
	function upateEdit(value,id){
		$.ajax({
			method: "POST",
			url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
			data : {
				'cmd':'<?=Url::get('province_id')?'update_district_name':'update_name'?>',
				'zone_id':id,
				'value':value
			},
			beforeSend: function(){
				//jQuery('#update-alert').show();
			},
			success: function(content){
				content = $.trim(content);
				$('#viettel_province_name'+id).html(content);
				//jQuery('#update-alert').hide();
				//jQuery('#quick_edit_'+id).show();
				//jQuery('#quick_cancel_'+id).hide();
			},
			error: function(){
				alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
			}
		});
	}
</script>