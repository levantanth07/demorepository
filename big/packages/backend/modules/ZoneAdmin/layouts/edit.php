<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<fieldset id="toolbar">
	<div id="toolbar-title">
		<a href="<?php echo Url::build('zone_admin');?>">Quản lý tỉnh thành</a> <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>	
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<td id="toolbar-save"  align="center"><a onclick="EditCategoryForm.submit();" > <span title="save"> </span> Ghi lại </a> </td>
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
		<table cellspacing="4" cellpadding="4" border="0" width="100%" style="background-color:#FFFFFF;">
			<tr>
			  <td valign="top">
				  <table class="table">
					<tr>
						<td>
							<div class="form_input_label">Tên (<span class="require">*</span>)</div>
							<div class="form_input">
								<input name="name" type="text" id="name" class="form-control">
							</div>
							<div class="form_input_label">Tên quốc tế (Geoplugin's Name)</div>
							<div class="form_input">
								<input name="geoplugin_name" type="text" id="geoplugin_name" class="form-control">
							</div>
							<hr>
							<div class="form_input_label">Mô tả</div>
							<div class="form_input">
								<textarea id="description" name="description" cols="75" rows="20" style="width:99%; height:350px;overflow:hidden"><?php echo Url::get('description');?></textarea><br />
							</div>
						</td>
				   </tr>
				</table>
			  </td><td valign="top" style="width:40%">
					<table class="table">
						<tr>
							<td><strong>Trạng thái</strong></td>
							<td><?php echo Url::get('status','0');?></td>
						</tr>
						</table>
					<div id="panel_1" style="margin-top:8px;">
					<table class="table">
						<tr>
							<td width="1%" nowrap="nowrap">Thuộc</td>
							<td><select name="parent_id" id="parent_id" class="select-large" onchange="change_zone(this.value);"></select></td>
						</tr>
						<tr>
							<td>[[.type.]]</td>
							<td><select name="type" id="type" class="select-large"></select></td>
						</tr>
						<tr>
						  <td>[[.view_map.]]</td>
						  <td>
						  <div onclick="show_map();" style="color:#FF0000;cursor:pointer;text-decoration:underline">[[.select_map.]]</div>
						  </td>
					  </tr>
						<tr>
							<td>[[.latitude.]]</td>
							<td><input name="latitude" type="text" id="latitude" class="input-large"></td>
						</tr>
						<tr>
							<td>[[.longitude.]]</td>
							<td><input name="longitude" type="text" id="longitude" class="input-large"></td>
						</tr>
						<tr class="hide">
							<td>[[.radius.]]</td>
							<td><input name="radius" type="text" id="radius" class="input-large"></td>
						</tr>
						<tr>
							<td>Trạng thái</td>
							<td><select name="status" id="status"  class="select"></select></td>
						</tr>
						<tr>
						  <td colspan="2" valign="top"><!--IF:cond(isset([[=image_url=]]))--><img src="[[|image_url|]]" style="width:300px; height:180px;" /><!--/IF:cond--></td>
					  </tr>
						<tr>
							<td valign="top">[[.image_url.]]</td>
							<td>
								<input name="image_url" type="file" id="image_url" class="file" size="18"><div id="delete_image_url"><?php if(Url::get('image_url') and file_exists(Url::get('image_url'))){?>[<a href="<?php echo Url::get('image_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('image_url')));?>" onclick="jQuery('#delete_image_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>					</td>
						</tr>
						<tr>
							<td valign="top">[[.map.]]</td>
							<td>
								<input name="map" type="file" id="map" class="file" size="18"><div id="delete_map"><?php if(Url::get('map') and file_exists(Url::get('map'))){?>[<a href="<?php echo Url::get('map');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('map')));?>" onclick="jQuery('#delete_map').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>					</td>
						</tr>
						<tr>
							<td valign="top">[[.Flag.]]</td>
							<td>
								<input name="flag" type="file" id="flag" class="file" size="18"><div id="delete_flag"><?php if(Url::get('flag') and file_exists(Url::get('flag'))){?>[<a href="<?php echo Url::get('flag');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('flag')));?>" onclick="jQuery('#delete_flag').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>					</td>
						</tr>
					</table>
				  </div>
				</td>
			</tr>
		</table>
            <input name="province_id" type="hidden" id="province_id">
	</form>
</fieldset>
<div class="zone-info" lat="[[|parent_lat|]]" long="[[|parent_long|]]" zoom="[[|parent_zoom|]]"></div>
<script>
    $(document).ready(function() {
        $('#parent_id').select2();
    });
    function show_map()
    {
        var lat = jQuery('.zone-info').attr('lat');
        var long = jQuery('.zone-info').attr('long');
        var zoom = jQuery('.zone-info').attr('zoom');
        window.open('<?php echo Url::build('show_map');?>&lat='+lat+'&long='+long+'&zoom='+zoom,'show_map','status=1,resizable=0,width=600,height=500');
    }
    function change_zone(zone_id)
    {
        jQuery.ajax({
            type: "POST",
            url: "<?php echo Url::build('zone_admin',array('cmd'=>'get_zone_id'));?>",
            data: "zone_id="+zone_id,
            success: function(msg){
                eval(msg);
                jQuery('.zone-info').attr({'lat':lat,'long':long,'zoom':zoom});
                makeRegion(arr);
            }
        });
    }
    function makeRegion(arr){
        var str = '';
        for(i in arr)
        {
            str += '<option value="'+i+'">'+arr[i]+'</option>'
        }
        jQuery('#region_id').html(str);
    }
</script>