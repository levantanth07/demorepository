<script>
	function getImageList(){
		var input = $('images');
		//empty list for now...
		jQuery('#image_list').html('');
		//for every file...
		//for every file...
		imageList = '';
		for(var x = 0;x<input.files.length;x++){
			//add to list
		  	imageList += '[[.image.]] ' + (x + 1) + ':  <img src="'+input.files[x].src+'">' + input.files[x].name + '<br>';
		}
		jQuery('#image_list').html(imageList);
	}
</script>
<fieldset id="toolbar">
	<legend><?php if(isset($_SESSION['product_name'])) echo $_SESSION['product_name']; ?></legend>
 	<div id="toolbar-title">
		[[.manage_images.]]
	</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
		  <td id="toolbar-save"  align="center"><a onclick="form_manage_image.submit();"> <span title="Edit"> </span> [[.Save.]] </a> </td>
		  <td id="toolbar-back"  align="center"><a href="<?php echo Url::build('panel',array('category_id'=>'67'));?>"> <span title="Back"> </span> [[.Back.]] </a> </td>
		  <td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br clear="all">
<form  name="form_manage_image" id="form_manage_image" method="POST" enctype="multipart/form-data">
<div class="notice">[[|notice|]]</div>
<div style="padding-left:10px;margin-bottom:10px;">Nếu kích thước hình ảnh quá lớn bạn phải <a target="_blank" href="http://www.diendandulich.biz/thuthuat/timagebatchresize3-0-thay-doi-kich-thuoc-hinh-anh-hang-loat-t9042.html" style="font-weight:bold;color:#0066CC;text-decoration:underline;">resize(thu nhỏ)</a> lại trước khi tải lên!</div>
<div><?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?></div>
<div style="padding-left:50px; margin-bottom:20px;">
<!--IF:limit([[=limit=]]>0)-->
<input type="file" name="images[]" id="images" class="multi" multiple onchange="getImageList();">
<div id="image_list"></div>
<input  type="hidden"  name="product_id"  id="product_id" value="[[|product_id|]]" />
<!--ELSE-->
<div>[[.you_uploaded_full_images.]]</div>
<!--/IF:limit-->
</div>
<!--IF:images([[=images=]])-->
<fieldset id="toolbar">
	<table width="100%" border="1">
		<tr>
			<td width="1%" valign="top" nowrap="nowrap">
			<img src="[[|image|]]" style="width:400px; " id="view_image" />
			</td>
			<td valign="top">
				<div style="height:400px;overflow:scroll">
				<table width="99%" border="1" bordercolor="#BCE9FE">
				<?php $i = 1; ?>
				<!--LIST:images-->
					<tr>
						<td width="1%" nowrap="nowrap"><img class="img_thumb" value="[[|images.image_url|]]" style="width:70px; height:50px; padding:3px 4px;" src="[[|images.small_thumb_url|]]" /></td>
						<td valign="top" style="padding-top:3px;"><label>[[.Title.]]: </label><input type="text" name="title[[[|images.id|]]]" id="title_[[[|images.id|]]]" style="width:70%" value="[[|images.name|]]" /><span style="margin-left:5px;"><?php echo $i++; ?></span></td>
						<td valign="top" width="1%" align="center" style="padding:3px 5px;"><img title="down" style="cursor:pointer" onclick="ChangePosition('down',[[|images.id|]],[[|images.position|]]);" src="skins/default/images/buttons/down_arrow.gif"  /></td>
						<td valign="top" width="1%" align="center" style="padding:3px 5px;"><img title="up" style="cursor:pointer" onclick="ChangePosition('up',[[|images.id|]],[[|images.position|]]);" src="skins/default/images/buttons/up_arrow.gif"  /></td>
						<td valign="top" width="1%" align="center" style="padding:3px 5px;"><img title="delete" style="cursor:pointer" onclick="DeleteImage([[|images.id|]])" src="skins/default/images/buttons/delete.gif" /></td>
					</tr>
				<!--/LIST:images-->
				</table>
				</div>
			</td>
		</tr>
	</table>
</fieldset>
<script>
	jQuery('.img_thumb').click(function(){
		jQuery('#view_image').attr('src',jQuery(this).attr('value'));
	})
	function ChangePosition(cmd,id,pos){
		jQuery.ajax({
			method: "POST",
			url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
			data :{
				'cmd':cmd,
				'position_id':id,
				'position':pos,
				'product_id':[[|product_id|]]
			},
			beforeSend: function(){
			},
			success: function(content){
				document.getElementById('module_<?php echo Module::block_id(); ?>').innerHTML=content;
			}
		});
	}
	function DeleteImage(id){
		if(confirm('Are you sure to delete this images')){
			jQuery.ajax({
				method: "POST",
				url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
				data :{
					'cmd':'delete',
					'position_id':id,
					'product_id':[[|product_id|]]
				},
				beforeSend: function(){
				},
				success: function(content){
					document.getElementById('module_<?php echo Module::block_id(); ?>').innerHTML=content;
				}
			});
		}
	}
</script>
<!--ELSE-->
<fieldset id="toolbar">
	<div class="note-unavailable">[[.no_image_available.]]</div>
</fieldset>
<!--/IF:images-->
</form>