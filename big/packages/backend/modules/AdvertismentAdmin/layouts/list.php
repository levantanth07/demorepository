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
		document.AdvertismentAdmin.submit();
	}
</script>
<fieldset id="toolbar">
	<div id="toolbar-title">
		[[.advertisment_admin.]] <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <?php if(User::can_add(false,ANY_CATEGORY)){?><td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>"> <span title="New"> </span> [[.New.]] </a> </td><?php }?>
		  <?php if(User::can_delete(false,ANY_CATEGORY)){?><td id="toolbar-trash"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <span title="Trash"> </span> [[.delete.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
	<form name="AdvertismentAdmin" method="post">
		<a name="top_anchor"></a>
			<table cellpadding="0" cellspacing="6" width="100%">
				<tr>
					<td width="100%">
						[[.Filter.]]:
						<input name="search" type="text" id="search"  class="text_area">
						<button onclick="document.AdvertismentAdmin.submit();">&nbsp;[[.Go.]]&nbsp;</button>
					</td>
					<td nowrap="nowrap">
					<select name="status" id="status" class="inputbox" size="1" onchange="document.AdvertismentAdmin.submit();"></select>
				  </td>
				</tr>
		</table>
		<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
			<thead>
					<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
					<th width="1%" align="left" nowrap><a>#</a></th>
					<th width="1%" title="[[.check_all.]]">
					  <input type="checkbox" value="1" id="AdvertismentAdmin_all_checkbox" onclick="select_all_checkbox(this.form,'AdvertismentAdmin',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th />
					<th width="26%" align="left" nowrap><a>[[.name.]]</a></th>
					<th width="5%" align="left" nowrap><a>[[.image_url.]]</a></th>
					<th width="15%" align="left" nowrap><a>[[.url.]]</a></th>
					<th width="2%" align="left" nowrap><a>[[.status.]]</a></th>
					<th width="7%" align="left" nowrap><a>[[.user_id.]]</a></th>
					<th width="4%" align="left" nowrap><a>[[.date.]]</a></th>
					<th width="4%" align="left" nowrap><a>[[.id.]]</a></th>
					<?php if(User::can_edit(false,ANY_CATEGORY))
					{?>
					<th width="2%" align="left" nowrap><a>[[.edit.]]</a></th>
					<?php }?>
				</tr>
		  </thead>
				<tbody>
				<?php $i=0;$total = [[=total=]];?>
				<!--LIST:items-->
				<tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],[[=just_edited_ids=]])))){ echo Portal::get_setting('crud_just_edited_item_bgcolor','#FFFFDD');} else {echo Portal::get_setting('crud_item_bgcolor','white');}?>" valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="AdvertismentAdmin_tr_[[|items.id|]]">
					<th width="1%" align="left" nowrap><a><?php echo ++$i;?></a></th>
					<td width="1%"><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'AdvertismentAdmin',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="AdvertismentAdmin_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td />
					<td align="left" nowrap>[[|items.name|]]</td>
					<td align="center" nowrap>
					<?php
					if(preg_match_all('/.swf/',[[=items.image_url=]],$matches))
					{
						echo '<embed src="'.[[=items.image_url=]].'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="60" height="50"></embed>';
					}
					else
					{
						echo '<img src="'.[[=items.image_url=]].'" width="60" height="50" onerror="this.src=\'assets/default/images/no_image.gif\'">';
					}
					?>
					</td>
					<td align="center" nowrap>[[|items.url|]]</td>
					<td align="left" nowrap>[[|items.status|]]</td>
					<td align="left" nowrap>[[|items.user_id|]]</td>
					<td align="left" nowrap><?php echo date('hh:i d/m/Y',[[=items.time=]]);?></td>
					<td align="left" nowrap>[[|items.id|]]</td>
					<?php if(User::can_edit(false,ANY_CATEGORY))
					{?>
					<td align="left" nowrap width="2%"><a href="<?php echo Url::build_current(array('id'=>[[=items.id=]],'cmd'=>'edit'));?>"><img src="assets/default/images/buttons/button-edit.png"></a></td>
					<?php }?>
				</tr>
				<!--/LIST:items-->
				</tbody>
	  </table>
		<div class="pt">[[|paging|]]</div>
		<input type="hidden" name="cmd" value="" id="cmd"/>
  </form>
  <div style="#height:8px;"></div>
</fieldset>