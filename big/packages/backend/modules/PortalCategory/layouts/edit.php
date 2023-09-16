<fieldset id="toolbar">
	<div id="toolbar-title">
		<?php echo Portal::language(Url::get('page'));?> <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<td id="toolbar-save"  align="center"><a href="javascript:void(0)" onclick="EditCategoryForm.submit();" > <span title="save"> </span> Ghi lại </a> </td>
		 <td id="toolbar-back"  align="center"><a href="<?php echo URL::build_current();?>"> <span title="Back"> </span> Quay lại </a> </td>
		</tr>
	  </tbody>
	</table>
    </div>
</fieldset>
<br>
<fieldset id="toolbar">
		<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
		<form name="EditCategoryForm" method="post" enctype="multipart/form-data">
		<input type="hidden" name="confirm_edit" value="1" />
		 <div class="col-md-7">
			<ul class="nav nav-tabs" role="tablist">
				<?php $i=0;?>
        <!--LIST:languages-->
          <li role="presentation" <?php echo ($i==0)?'class="active"':'';?>><a href="#info_tab_[[|languages.id|]]" aria-controls="home" role="tab" data-toggle="tab"><img src="[[|languages.icon_url|]]" alt="[[|languages.name|]]" /> [[|languages.name|]]</a></li>
        <?php $i++;?>
        <!--/LIST:languages-->
        </ul>
        <?php $i=0;?>
             <div class="tab-content">
                 <!--LIST:languages-->
                 <div role="tabpanel" class="tab-pane  <?php echo ($i==0)?'active':'';?>" id="info_tab_[[|languages.id|]]">
                     <h3></h3>
                     <div class="form-group">
                         <label>Tên danh mục (<span class="require">*</span>)</label>
                         <input name="name_[[|languages.id|]]" type="text" id="name_[[|languages.id|]]" class="form-control">
                     </div>
                     <div class="form-group">
                         <label>Alias (url) (<span class="require">*</span>)</label>
                         <input name="name_id_[[|languages.id|]]" type="text" id="name_id_[[|languages.id|]]" class="form-control">
                     </div>
                     <div class="form_input_label" style="display:none;">[[.brief.]]</div>
                     <div class="form_input" style="display:none;">
                         <textarea id="brief_[[|languages.id|]]" name="brief_[[|languages.id|]]" cols="75" rows="5" style="width:99%; height:200px;overflow:hidden"><?php echo Url::get('brief_'.[[=languages.id=]],'');?></textarea><br />
                     </div>
                     <div class="form-group">
                         <label>Mô tả</label>
                         <textarea id="description_[[|languages.id|]]" name="description_[[|languages.id|]]" cols="75" rows="20" class="form-control"><?php echo Url::get('description_'.[[=languages.id=]],'');?></textarea><br />
                     </div>
                 </div>
                 <?php $i++;?>
                 <!--/LIST:languages-->
             </div>
         </div>
		<div class="col-md-5">
			<div>
        <table class="table">
          <tr class="hide">
            <td>Hiển thị ra trang chủ</td>
            <td><input  name="show_home" type="checkbox" value="1" id="show_home" <?php if(Url::get('show_home')==1){echo 'checked="checked"';}?>></td>
          </tr>
          <tr>
            <td width="30%">Danh mục cha</td>
            <td><select name="parent_id" id="parent_id" class="form-control"></select></td>
          </tr>
          <tr class="hide">
            <td>[[.type.]]</td>
            <td><select name="type" id="type" class="form-control"></select></td>
          </tr>
          <tr>
            <td>[[.url.]]</td>
            <td><input name="url" type="text" id="url" class="form-control">
            <div class="alert alert-warning" role="alert">Trường này để trống thì URL sẽ theo mặc định của hệ thống.</div></td>
          </tr>
          <tr>
            <td>[[.status.]]</td>
            <td><select name="status" id="status" class="form-control"></select></td>
          </tr>
          <tr>
            <td valign="top">Ảnh đại diện</td>
            <td>
              <input name="icon_url" type="file" id="icon_url" class="form-control"><div id="delete_icon_url"><?php if(Url::get('icon_url') and file_exists(Url::get('icon_url'))){?>[<a href="<?php echo Url::get('icon_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('icon_url')));?>" onclick="jQuery('#delete_icon_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>
            </td>
          </tr>
          <tr>
            <td valign="top">[[.banner_url.]]</td>
            <td>
              <input name="image_url" type="file" id="image_url" class="form-control"><div id="delete_image_url"><?php if(Url::get('image_url') and file_exists(Url::get('image_url'))){?>[<a href="<?php echo Url::get('image_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('image_url')));?>" onclick="jQuery('#delete_image_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>
            </td>
          </tr>
        </table>
			</div>
		</div>
	</form>
</fieldset>
<script>
	jQuery(document).ready(function(e) {
    jQuery('#name_1').change(function(){
			jQuery.ajax({
				method: "POST",
				url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
				data : {
					'cmd':'get_name_id',
					'name':jQuery(this).val()
				},
				beforeSend: function(){
					
				},
				success: function(content){
					jQuery('#name_id_1').val(content);
				},
				error: function(){
					//custom_alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
				}
			});
			
		});
		jQuery('#name_2').change(function(){
			jQuery.ajax({
				method: "POST",
				url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
				data : {
					'cmd':'get_name_id',
					'name':jQuery(this).val()
				},
				beforeSend: function(){
					
				},
				success: function(content){
					jQuery('#name_id_2').val(content);
				},
				error: function(){
					//custom_alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
				}
			});
			
		});
  });
</script>