<fieldset id="toolbar">
	<div id="toolbar-title">
		[[.currency_admin.]]<span> [ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <td id="toolbar-save"  align="center"><a onclick="CurrencyAdmin.submit();"> <span title="Edit"> </span> [[.Save.]] </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
<form name="CurrencyAdmin" method="post">
<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
	<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
		<th align="left"><a>[[.field_name.]]</a></th>
		<th align="left"><a>[[.value.]]</a></th>
	</tr>
	<tr>
		<td width="15%">[[.id.]] (<span style="color:#FF0000" class="require">*</span>)</td>
		<td width="85%"><input name="id" type="text" id="id" class="input-large"></td>
	</tr>
	<tr>
		<td width="15%">[[.name.]] (<span style="color:#FF0000" class="require">*</span>)</td>
		<td width="85%"><input name="name" type="text" id="name" class="input-large"></td>
	</tr>
	<tr>
		<td width="15%">[[.exchange.]] (<span style="color:#FF0000" class="require">*</span>)</td>
		<td width="85%"><input name="exchange" type="text" id="exchange" class="input-large"></td>
	</tr>
	<tr>
		<td width="15%">[[.position.]]</td>
		<td width="85%"><input name="position" type="text" id="position" class="input-large"></td>
	</tr>
</table>
</form>
<table width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;height:8px;#width:99%" align="center">
<tr>
	<td align="right">&nbsp;</td>
</tr>
</table>
<div style="#height:8px"></div>
</fieldset>