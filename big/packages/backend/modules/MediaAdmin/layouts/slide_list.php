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
		document.SlideListMediaAdmin.submit();
	}
</script>
<fieldset id="toolbar">
	<legend>[[.manage_media.]]</legend>
	<div id="toolbar-title">
		<?php echo Portal::language(Url::get('page'));?> <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
  		  <?php if(User::can_admin(false,ANY_CATEGORY)){?><td id="toolbar-param"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'make_slide'));?>#"> <span title="Make param"> </span> [[.Make_slide.]] </a> </td><?php }?>
  		  <?php if(User::can_delete(false,ANY_CATEGORY)){?><td id="toolbar-trash"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('slide_list')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <span title="Trash"> </span> [[.Trash.]] </a> </td><?php }?>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
	<form name="SlideListMediaAdmin" method="post">
		<a name="top_anchor"></a>
		<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
			<thead>
					<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
					<th width="1%" align="left" nowrap><a>#</a></th>
					<th width="1%" title="[[.check_all.]]">
					  <input type="checkbox" value="1" id="SlideListMediaAdmin_all_checkbox" onclick="select_all_checkbox(this.form,'SlideListMediaAdmin',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th />
					<th width="32%" align="left" nowrap><a>[[.name.]]</a></th>
					<th width="18%" align="left" nowrap><a>[[.effect.]]</a></th>
					<th width="13%" align="left" nowrap><a>[[.user_id.]]</a></th>
					<th width="8%" align="left" nowrap><a>[[.date.]]</a></th>
					<th width="9%" align="left" nowrap><a>[[.id.]]</a></th>
					<?php if(User::can_edit(false,ANY_CATEGORY))
					{?>
					<th width="3%" align="left" nowrap><a>[[.edit.]]</a></th>
					<th width="4%" align="left" nowrap><a>[[.view.]]</a></th>
					<?php }?>
				</tr>
		  </thead>
				<tbody>
				<?php $i=0;$total = [[=total=]];?>
				<!--LIST:items-->
				<tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]])){ echo Portal::get_setting('crud_just_edited_item_bgcolor','#FFFFDD');} else {echo Portal::get_setting('crud_item_bgcolor','white');}?>" valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="SlideListMediaAdmin_tr_[[|items.id|]]">
					<th align="left" nowrap><a><?php echo ++$i;?></a></th>
					<td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'SlideListMediaAdmin',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="SlideListMediaAdmin_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td />
					<td align="left" nowrap>[[|items.name|]]</td>
					<td align="left" nowrap>[[|items.effect|]]</td>
					<td align="left" nowrap>[[|items.user_id|]]</td>
					<td align="left" nowrap><?php echo date('hh:i d/m/Y',[[=items.time=]]);?></td>
					<td align="left" nowrap>[[|items.id|]]</td>
					<?php if(User::can_edit(false,ANY_CATEGORY))
					{?>
					<td width="3%" align="left" nowrap><a href="<?php echo Url::build_current(array('id'=>[[=items.id=]],'cmd'=>'make_slide'));?>"><img src="assets/default/images/buttons/button-edit.png"></a></td>
					<td width="4%" align="left" nowrap><a href="<?php echo Url::build_current(array('slide_id'=>[[=items.id=]],'cmd'=>'view_slide'));?>"><img src="assets/default/images/buttons/search.gif" width="20"></a></td>
					<?php }?>
				</tr>
				<!--/LIST:items-->
				</tbody>
	  </table>
			<table  width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;#width:99%" align="center">
			<tr>
				<td width="48%" align="left">
					[[.select.]]:&nbsp;
					<a href="javascript:void(0)" onclick="select_all_checkbox(document.SlideListMediaAdmin,'SlideListMediaAdmin',true,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_all.]]</a>&nbsp;
					<a href="javascript:void(0)" onclick="select_all_checkbox(document.SlideListMediaAdmin,'SlideListMediaAdmin',false,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_none.]]</a>
					<a href="javascript:void(0)" onclick="select_all_checkbox(document.SlideListMediaAdmin,'SlideListMediaAdmin',-1,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_invert.]]</a>
				</td>
			  </tr>
			</table>
	</table>
		<input type="hidden" name="cmd" value="" id="cmd"/>
  </form>
  <div style="#height:8px"></div>
</fieldset>