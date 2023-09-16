<?php
$title = (URL::get('cmd')=='delete')?'Delete':'view';
$action = (URL::get('cmd')=='delete')?'delete':'detail';
?>
<div class="form_bound">
	<table cellpadding="0" width="100%"><tr><td  class="form_title"><?php echo $title;?></td><?php
			if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a href="javascript:void(0)" onclick="CategoryForm.submit();"><img alt="" src="<?php echo Portal::template('core');?>/images/buttons/delete.jpg" style="text-align:center"/><br />[[.Delete.]]</a></td><?php
			}else{
				if(User::can_edit(false,ANY_CATEGORY)){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'edit','id'));?>"><img alt="" src="<?php echo Portal::template('core');?>/images/buttons/button-edit.png" style="text-align:center"/><br />[[.Edit.]]</a></td><?php }
				if(User::can_delete(false,ANY_CATEGORY)){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'delete','id'));?>"><img alt="" src="<?php echo Portal::template('core');?>/images/buttons/delete.jpg"/><br />[[.Delete.]]</a></td><?php }
			}?>
			<td class="form_title_button"><a href="<?php echo URL::build_current();?>"><img alt="" src="<?php echo Portal::template('core');?>/images/buttons/back.jpg" style="text-align:center"/><br />[[.back.]]</a></td></tr></table>
	</script>
<div class="form_content">
<table cellspacing="0" width="100%">
  <tr valign="top" >
  <td rowspan="5" align="center" valign="top">
	<!--IF:image([[=icon_url=]])-->
				<a target="_blank" href="../../FunctionCategory/layouts/[[:icon_url:]]"><img alt="" src="../../FunctionCategory/layouts/[[:icon_url:]]" height="100"></a><br />
				[[.icon_url.]]<br />
				<!--/IF:image-->	</td>
    <td class="form_detail_label">[[.id.]]</td>
    <td width="1">:</td>
    <td class="form_detail_value">[[|id|]]</td>
  </tr>
  	<tr>
		<td class="form_detail_label">[[.name.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|name_1|]]		</td>
	</tr><tr>
		<td class="form_detail_label">[[.description.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|description_1|]]		</td>
	</tr>
	</table>
	<!--IF:delete(URL::get('cmd')=='delete')-->
	<form name="CategoryForm" method="post">
	<input type="hidden" value="<?php echo URL::get('id');?>" name="selected_ids[]"/>
	<input type="hidden" value="1" name="confirm"/>
	<input type="hidden" value="delete" name="cmd"/>
	<input type="submit" value="  [[.Delete.]]  "/>
	<!--ELSE-->
		<!--IF:can_edit(User::can_edit())-->
		<input type="button" value="   [[.Edit.]]   " onclick="location='<?php echo URL::build_current(+array('cmd'=>'edit','id'=>$_REQUEST['id']));?>';" />
		<!--/IF:can_edit-->
		<!--IF:can_delete(User::can_delete())-->
		<input type="button" value="   [[.Delete.]]   " onclick="location='<?php echo URL::build_current(+array('cmd'=>'delete','id'=>$_REQUEST['id']));?>';" />
		<!--/IF:can_delete-->
	<!--/IF:delete-->
		<input type="button" value="   [[.list.]]   " onclick="location='<?php echo URL::build_current();?>';" />
	<!--IF:delete(URL::get('cmd')=='delete')-->
	</form>
	<!--/IF:delete-->
  </div>
</div>