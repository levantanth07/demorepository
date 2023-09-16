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
		document.ListManageAdvertismentForm.submit();
	}
</script>
<fieldset id="toolbar">
	<div id="toolbar-title">Quản lý cắm quảng cáo <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span></div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <?php if(User::can_add(false,ANY_CATEGORY)){?><td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'advertisment'));?>#"> <span title="New"> </span> [[.New.]] </a> </td><?php }?>
		  <?php if(User::can_delete(false,ANY_CATEGORY)){?><td id="toolbar-trash"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <span title="Trash"> </span> [[.delete.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
	<form name="ListManageAdvertismentForm" method="post">
		<div><?php if(Form::$current->is_error()){?><strong>B&#225;o l&#7895;i</strong><?php echo Form::$current->error_messages();?><?php }?></div>
		<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
		<thead>
			<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
				<th width="1%" title="[[.check_all.]]">
				<input type="checkbox" value="1" id="ManageAdvertisment_all_checkbox" onclick="select_all_checkbox(this.form,'ManageAdvertisment',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
				<th width="3%"  align="left" ><a>[[.id.]]</a></th>
				<th width="20%"  align="left"><a>[[.name.]]</a></th>
				<th width="4%"  align="left" nowrap="nowrap"><a>[[.image_url.]]</a></th>
				<th width="10%"  align="left"><a>[[.region.]]</a></th>
				<th width="5%" align="left"><a>Vị trí</a></th>
				<th width="10%" align="left"><a>[[.start_time.]]</a></th>
				<th width="10%" align="left"><a>[[.end_time.]]</a></th>
				<th width="1%"  align="left" ><a>[[.Edit.]]</a></th>        
			</tr>
		  </thead>
			<tbody>
			<?php $i=0;?>
			<!--LIST:items-->
				<tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],[[=just_edited_ids=]])))){ echo Portal::get_setting('crud_just_edited_item_bgcolor','#F7F7F7');} else {echo Portal::get_setting('crud_item_bgcolor','white');}?>" valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#F7F7F7'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="ManageAdvertisment_tr_[[|items.id|]]">
					<td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'ManageAdvertisment',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="ManageAdvertisment_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
					<td>[[|items.id|]]</td>
					<td>[[|items.name|]]</td>
					<td align="center">
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
					<td>[[|items.region|]]</td>
					<td>[[|items.position|]]</td>
					<td><?php echo date('h\h:i d-m-Y',[[=items.start_time=]]);?></td>
					<td><?php echo date('h\h:i d-m-Y',[[=items.end_time=]]);?></td>
         	<td><a href="<?php echo Url::build_current(array('cmd'=>'advertisment','id'=>[[=items.id=]]));?>"><img src="assets/default/images/buttons/button-edit.png" alt="Edit"></a></td>
				</tr>
				<?php $i++;?>
			<!--/LIST:items-->
			</tbody>
		</table>
		<div class="pt">[[|paging|]]</div>
		<input type="hidden" name="cmd" value="" id="cmd"/>
	</form>
	<div style="#height:8px"></div>
</fieldset>