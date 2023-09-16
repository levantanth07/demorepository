<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<fieldset id="toolbar">
	<div id="toolbar-title">
		<a href="<?php echo Url::build('zone_admin');?>">Quản lý phường xã [[|district_name|]]</a> <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<td id="toolbar-save"  align="center"><a onclick="EditCategoryFormV2.submit();" > <span title="save"> </span> Ghi lại </a> </td>
		 <td id="toolbar-back"  align="center"><a href="<?php echo URL::build_current(['district_id','cmd'=>'v2']);?>"> <span title="Back"> </span> Quay lại </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
		<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
		<form name="EditCategoryFormV2" method="post" enctype="multipart/form-data">
		<input type="hidden" name="confirm_edit" value="1" />
		<table cellspacing="4" cellpadding="4" border="0" width="100%" style="background-color:#FFFFFF;">
			<tr>
			  <td valign="top">
				  <table class="table">
					<tr>
						<td>
<!--							<div class="form_input_label">Mã (<span class="require">*</span>)</div>-->
<!--							<div class="form_input">-->
<!--								<input name="ward_code" type="text" id="ward_code" class="form-control">-->
<!--							</div>-->
                            <div class="form_input_label">Tên (<span class="require">*</span>)</div>
                            <div class="form_input">
                                <input name="ward_name" type="text" id="ward_name" class="form-control">
                            </div>
<!--                            <div class="form_input_label">CODE (<span class="require">*</span>)</div>-->
<!--                            <div class="form_input">-->
<!--                                <input name="code" type="text" id="code" class="form-control">-->
<!--                            </div>-->
						</td>
				   </tr>
				</table>
			  </td><td valign="top" style="width:40%">
<!--					<div id="panel_1" style="margin-top:8px;">-->
<!--					<table class="table">-->
<!--                        <tr>-->
<!--                            <td>[[.type.]]</td>-->
<!--                            <td><select name="type" id="type" class="select-large"></select></td>-->
<!--                        </tr>-->
<!--					</table>-->
<!--				  </div>-->
				</td>
			</tr>
		</table>
            <input name="district_id" type="hidden" id="district_id">
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
