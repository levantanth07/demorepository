<fieldset id="toolbar">
	<legend>[[.content_manage_system.]]</legend>
	<div id="toolbar-title">
		<?php echo Portal::language(Url::get('cmd'));?>
	</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-back" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="[[.List.]]"> </span> [[.List.]] </a> </td><?php }?>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-help" align="center"><a href="<?php echo Url::build('help');?>#"> <span title="Help"> </span> [[.Help.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
<form method="post" name="form1">
	<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
		<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
			<th width="26%" align="left"><a>[[.field_name.]]</a></th>
			<th width="70%" align="left"><a>[[.value.]]</a></th>
		</tr>
		<tr>
			<td>[[.url.]] (<span class="require">*</span>)</td>
			<td><input name="url" type="text" class="input-large" id="url"/></td>
	  </tr>
	   <tr>
			<td>[[.image_url.]]</td>
			<td> <input name="image_url" type="text" class="input-large" id="image_url"/></td>
	  </tr>
	  <tr>
			<td>[[.category_id.]]</td>
			<td><select name="category_id" id="category_id" class="select-large"></select></td>
	  </tr>
	   <tr>
			<td colspan="2"><input name="fetch" type="submit" value="[[.fetch_now.]]" /></td>
	  </tr>
	</table>
</form>
</fieldset>
