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
		document.TableRestore.submit();
	}
</script>
<fieldset id="toolbar">
	<legend>[[.utils.]]</legend>
	<div id="toolbar-title">
		[[.restore_date.]]
	</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
   		  <?php if(User::can_admin(false,ANY_CATEGORY)){?><td id="toolbar-list"  align="center"><a onclick="if(check_selected()){make_cmd('restore')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}"> <span title="[[.Restore.]]"> </span> [[.Restore.]] </a> </td><?php }?>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
<form name="TableRestore" method="post">
<div align="right"><select name="folders" id="folders" onchange="TableRestore.submit();"></select></div>
<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
	<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
			<th width="3%" align="center"  title="[[.check_all.]]"><input type="checkbox" value="1" id="TableRestore_all_checkbox" onclick="select_all_checkbox(this.form, 'TableRestore',this.checked,'#FFFFEC','white');"></th>
			<th width="26%" align="left"><a>[[.table_name.]]</a></th>
			<th width="35%" align="left" nowrap ><a>[[.folder.]]</a></th>
			<th width="22%"  align="left" nowrap ><a>[[.last_modified.]]</a></th>
			<th width="14%"  align="left" nowrap ><a>[[.file_size.]]</a></th>
		  </tr>
		  <?php $i=0;?>
			<?php foreach([[=tables=]] as $key=>$value){?>
			  <?php $i++;?>
			<tr valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="TableRestore_tr_[[|items.id|]]">
				<td align="center"><input name="selected_ids[]" type="checkbox" value="<?php echo $key;?>" onclick="select_checkbox(this.form,'TableRestore',this,'#FFFFEC','white');"  /></td>
				<td><a href="<?php echo $key;?>" style="text-transform:uppercase;font-weight:bold" target="_blank"><?php echo str_replace('.sql','',$value);?></a></td>
				<td nowrap align="left">[[|path|]]</td>
				<td align="left" nowrap> <?php echo date('d/m/Y h:i:s',filemtime($key));?></td>
		        <td align="left" nowrap><?php echo @filesize($key);?></td>
		  </tr>
			<?php }?>
		</table>
<table width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;border-top:0px;height:8px;#width:99%;" align="center">
<tr>
	<td align="left">
		[[.select.]]:&nbsp;
		<a  onclick="select_all_checkbox(document.TableRestore,'TableRestore',true,'#FFFFEC','white');">[[.select_all.]]</a>&nbsp;
		<a  onclick="select_all_checkbox(document.TableRestore,'TableRestore',false,'#FFFFEC','white');">[[.select_none.]]</a>
		<a  onclick="select_all_checkbox(document.TableRestore,'TableRestore',-1,'#FFFFEC','white');">[[.select_invert.]]</a>
		<b>[[.Total.]] : [[|total|]] [[.table.]]</b>
	</td>
</tr>
</table>
<input name="cmd" type="hidden" id="cmd" value="restore">
</form>
<div style="#height:8px"></div>
</fieldset>    