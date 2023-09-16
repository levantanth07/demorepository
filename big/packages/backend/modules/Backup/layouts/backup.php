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
		document.TableBackup.submit();
	}
</script>
<fieldset id="toolbar">
	<legend>[[.utils.]]</legend>
	<div id="toolbar-title">
		[[.backup_data.]]
	</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
   		  <?php if(User::can_admin(false,ANY_CATEGORY)){?><td id="toolbar-list"  align="center"><a onclick="if(check_selected()){make_cmd('backup')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}"> <span title="[[.Update.]]"> </span> [[.Backup.]] </a> </td><?php }?>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
<form name="TableBackup" method="post">
<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
	<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
		<th width="1%" align="center"><input type="checkbox" value="1" id="TableBackup_all_checkbox" onclick="select_all_checkbox(this.form, 'TableBackup',this.checked,'#FFFFEC','white');"></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.table_name.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Engine.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Version.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Row_format.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Rows.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Avg_row_length.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Data_length.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Max_data_length.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Index_length.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Auto_increment.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Create_time.]]</a></th>
		<th width="10%" align="left" nowrap="nowrap"><a>[[.Update_time.]]</a></th>
	</tr>
	<?php $i=0;?>
	<!--LIST:tables-->
	<tr valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="TableBackup_tr_[[|items.Name|]]">
		<td align="center"><?php $i++;?><input name="selected_ids[]" type="checkbox" value="[[|tables.Name|]]" onclick="select_checkbox(this.form,'TableBackup',this,'#FFFFEC','white');"  /></td>
		<td>[[|tables.Name|]]</td>
		<td>[[|tables.Engine|]]</td>
		<td>[[|tables.Version|]]</td>
		<td>[[|tables.Row_format|]]</td>
		<td>[[|tables.Rows|]]</td>
		<td>[[|tables.Avg_row_length|]]</td>
		<td>[[|tables.Data_length|]]</td>
		<td>[[|tables.Max_data_length|]]</td>
		<td>[[|tables.Index_length|]]</td>
		<td>[[|tables.Auto_increment|]]</td>
		<td nowrap="nowrap">[[|tables.Create_time|]]</td>
		<td nowrap="nowrap">[[|tables.Update_time|]]</td>
	</tr>
	<!--/LIST:tables-->
</table>
<table width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;border-top:0px;height:8px;#width:99%;" align="center">
<tr>
	<td align="left">
		[[.select.]]:&nbsp;
		<a  onclick="select_all_checkbox(document.TableBackup,'TableBackup',true,'#FFFFEC','white');">[[.select_all.]]</a>&nbsp;
		<a  onclick="select_all_checkbox(document.TableBackup,'TableBackup',false,'#FFFFEC','white');">[[.select_none.]]</a>
		<a  onclick="select_all_checkbox(document.TableBackup,'TableBackup',-1,'#FFFFEC','white');">[[.select_invert.]]</a>
		<b>[[.Total.]] : [[|total|]] [[.table.]]</b>
	</td>
</tr>
</table>
<input name="cmd" type="hidden" id="cmd" value="backup">
</form>
<div style="#height:8px"></div>
</fieldset>
