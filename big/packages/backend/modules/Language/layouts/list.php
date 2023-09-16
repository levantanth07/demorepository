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
		document.ListLanguageForm.submit();
	}
</script>
<fieldset id="toolbar">
	<div id="toolbar-info">
		[[.language.]]
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<?php
		if(URL::get('cmd')=='delete' and User::can_delete(false,ANY_CATEGORY)){?>
		 	<td id="toolbar-trash"  align="center"><a onclick="ListLanguageForm.cmd.value='delete';ListLanguageForm.submit();"> <span title="Trash"> </span> [[.Trash.]] </a> </td>
			<td id="toolbar-back"  align="center"><a href="<?php echo URL::build_current();?>"> <span title="Back"> </span> [[.Back.]] </a> </td>
		<?php
		}else{
		if(User::can_add(false,ANY_CATEGORY)){?>
				<td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>"> <span title="New"> </span> [[.New.]] </a> </td>
			<?php }?>
		<?php if(User::can_delete(false,ANY_CATEGORY)){?>
		 <td id="toolbar-trash"  align="center"><a  onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <span title="Trash"> </span> Xóa </a> </td>
		<?php }
		}?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
	<form name="ListLanguageForm" method="post">
			<input name="cmd" type="hidden" id="cmd" />
			<input name="confirm" type="hidden" id="confirm" value="1"/>
			<table width="100%">
			<tr>
				<td width="100%">
					<a name="top_anchor"></a>
					<table class="table">
						<tr valign="middle" bgcolor="#EFEFEF" style="line-height:20px">
							<th width="1%" title="[[.check_all.]]"><input type="checkbox" value="1" id="Language_all_checkbox" onclick="select_all_checkbox(this.form, 'Language',this.checked,'#FFFFEC','white');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
							<th nowrap align="left" width="2%">ID</th>
							<th nowrap align="left" width="2%">&nbsp;</th>
							<th nowrap align="left" width="10%">
								<a href="<?php echo URL::build_current(((URL::get('order_by')=='language.code' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'language.code'));?>" title="[[.sort.]]">
								<?php if(URL::get('order_by')=='language.code') echo '<img src="'.'assets/default/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								[[.code.]]
								</a>
							</th>
							<th align="left" nowrap > <a href="<?php echo URL::build_current(((URL::get('order_by')=='language.name' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'language.name'));?>" title="[[.sort.]]">
							  <?php if(URL::get('order_by')=='language.name') echo '<img src="'.'assets/default/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>
							  [[.name.]] </a> </th>
							<th nowrap align="left" >Kích hoạt</th>
							<th nowrap align="left" >Mặc định</th>
						</tr>
						<!--LIST:items-->
						<tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],MAP['just_edited_ids'])))){ echo '#EFFFDF';} else {echo 'white';}?>" valign="middle" <?php Draw::hover('#FFFFDD');?> style="cursor:pointer;" id="Language_tr_[[|items.id|]]">
							<td><?php if([[=items.id=]]!=1){?><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'Language',this,'#FFFFEC','white');" <?php if(URL::get('cmd')=='delete') echo 'checked';?>><?php }?></td>
							<td nowrap align="left" onclick="location='<?php echo URL::build_current(array('cmd'=>'edit','id'=>[[=items.id=]]));?>';">[[|items.id|]]</td>
							<td nowrap align="left" onclick="location='<?php echo URL::build_current(array('cmd'=>'edit','id'=>[[=items.id=]]));?>';"><img src="[[|items.icon_url|]]"></td>
							<td nowrap align="left" onclick="location='<?php echo URL::build_current(array('cmd'=>'edit','id'=>[[=items.id=]]));?>';">
									[[|items.code|]]
								</td>
							<td align="left" nowrap onclick="location='<?php echo URL::build_current(array('cmd'=>'edit','id'=>[[=items.id=]]));?>';"> [[|items.name|]] </td>
							<td align="left" nowrap onclick="location='<?php echo URL::build_current(array('cmd'=>'edit','id'=>[[=items.id=]]));?>';"> [[|items.active|]] </td>
							<td align="left" nowrap onclick="location='<?php echo URL::build_current(array('cmd'=>'edit','id'=>[[=items.id=]]));?>';"> [[|items.default|]] </td>
						</tr>
						<!--/LIST:items-->
					</table>
				</td>
			</tr>
			</table>
			[[|paging|]]
	</form>
			<input type="hidden" name="cmd" value="delete"/>
<input type="hidden" name="page_no" value="1"/>
<!--IF:delete(URL::get('cmd')=='delete')-->
				<input type="hidden" name="confirm" value="1" />
				<!--/IF:delete-->
	<div style="#height:8px;"></div>
</fieldset>