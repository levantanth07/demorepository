<script src="packages/core/includes/js/language_tab.js" type="text/javascript"></script>
<?php System::set_page_title(Portal::get_setting('company_name','').' '.Portal::language('delete_title'));?><?php echo Draw::begin_round_table();?>
<form method="post" name="ExportPackageForm">
<table cellspacing="0" width="100%">
	<tr valign="top">
		<td>&nbsp;</td>
		<td align="left" colspan="2"><font class="form_title"><b>[[.export_title.]]</b></font></td>
	</tr>
	<tr bgcolor="#EEEEEE" valign="top">
	<td align="right">&nbsp;</td>
	<td bgcolor="#EEEEEE"><span style="width:10px;line-height:24px;">&nbsp;</span></td>
	<td bgcolor="#C8E1C3" width="100%">&nbsp;</td>
	</tr>
	<?php if(Form::$current->is_error())
	{
	?>	<tr bgcolor="#EEEEEE" valign="top">
	<td align="right" nowrap="nowrap">B&#225;o l&#7895;i</td>
	<td bgcolor="#EEEEEE"><span style="width:10px;line-height:24px;">&nbsp;</span></td>
	<td bgcolor="#EEEEEE"><?php echo Form::$current->error_messages();?></td>
	</tr>
	<?php
	}
	?>
	<tr bgcolor="#EEEEEE" valign="top">
		<td nowrap align="right"><strong>[[.package.]]</strong></td>
		<td width="10" bgcolor="#EFEFEF"><span style="width:10px;line-height:24px;">:</span></td>
		<td width="496" bgcolor="#C8E1C3">
			<select name="package_id" id="package_id">
           	</select>
		</td>
	</tr>
    <tr bgcolor="#EEEEEE" valign="top">
		<td nowrap align="right"><strong>[[.code.]]</strong></td>
		<td width="10" bgcolor="#EFEFEF"><span style="width:10px;line-height:24px;">:</span></td>
		<td width="496" bgcolor="#C8E1C3">
			<textarea cols="80" rows="15">[[|code|]]</textarea>
		</td>
	</tr>
	<tr bgcolor="#EEEEEE">
		<td bgcolor="#EEEEEE" colspan="3">
			<p>
			<table>
			<tr><td>
				<?php Draw::button('  [[.export.]]  ',false,false,true,'ExportPackageForm');?>			</td><td>
				<p><?php Draw::button('[[.list.]]',URL::build_current(array()));?></p>
			</td></tr>
			</table>
			</p>
		</td>
	</tr>
</table>
</form>
<?php echo Draw::end_round_table();?>