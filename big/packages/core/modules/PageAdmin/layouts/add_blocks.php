<?php System::set_page_title(Portal::get_setting('website_title','').' '.'[[.add_blocks_title.]]');?><?php echo Draw::begin_round_table();?><table cellspacing="0" width="100%">
	<tr valign="top">
		<td>&nbsp;</td>
		<td align="left" colspan="2"><font class="form_title"><b>[[.Add_blocks_title.]]</b></font></td>
	</tr>
	<tr bgcolor="#EEEEEE" valign="top">
	<td align="right">&nbsp;</td>
	<td bgcolor="#EEEEEE"><div style="width:10px;">&nbsp;</div></td>
	<td bgcolor="#EEEEEE">&nbsp;</td>
	</tr>
	<?php if(Form::$current->is_error())
	{
	?>	<tr bgcolor="#EEEEEE" valign="top">
	<td align="right">&nbsp;</td>
	<td bgcolor="#EEEEEE"><div style="width:10px;">&nbsp;</div></td>
	<td bgcolor="#EEEEEE">B&#225;o l&#7895;i<br /><?php echo Form::$current->error_messages();?></td>
	</tr>
	<?php
	}
	?>	<tr bgcolor="#EEEEEE" valign="top">
	<td>&nbsp;</td>
	<td bgcolor="#EEEEEE">&nbsp;</td>
	<td bgcolor="#EEEEEE">
      <form name="addLayout" method="post">
	  	<table width="100%" border="0" class="body_text">
  		  <tr valign="top"><td>
        <table width="100%" border="0" class="body_text">
  		  <tr>
            <td width="149">[[.module.]]</td>
            <td width="14" class="body_text">:</td>
            <td width="452"><select name="module_id" id="module_id" onchange="change_module(this.value)"></select>            </td>
          </tr>
          <tr>
            <td width="149">[[.To_page_name.]]</td>
            <td width="14" class="body_text">:</td>
            <td width="452"><select name="page_name" id="page_name"></select></td>
          </tr>
		  <tr>
            <td width="149">[[.In_container.]]</td>
            <td width="14" class="body_text">:</td>
            <td width="452"><select name="container_id" id="container_id"></select></td>
          </tr>
		  <tr>
            <td width="149">[[.Block_name.]]</td>
            <td width="14" class="body_text">:</td>
            <td width="452"><input name="block_name" type="text" id="page_name" size="40"></td>
          </tr>
		   <tr>
            <td>[[.region.]]</td>
            <td>:</td>
            <td><select name="region" id="region"></select></td>
          </tr>
		  <tr>
            <td>[[.position.]]</td>
            <td>:</td>
            <td><select name="position" id="position"></select></td>
          </tr>
		  <tr>
            <td width="149">[[.Block_to_copy_setting_from.]]</td>
            <td width="14" class="body_text">:</td>
            <td width="452"><select name="copy_setting_id" id="copy_setting_id"></select></td>
          </tr>
          <tr>
            <td><input type="submit" name="Submit" value="   [[.Add.]]   "></td>
            <td>&nbsp;</td>
            <td><input type="submit" name="confirm_delete" value="   [[.Delete.]]   "></td>
          </tr>
        </table>
		</td>
		<td>
			<select name="portals[]" id="portals" multiple="true" size="20" >
			</select>
		</td>
		</tr></table>
      </form>
    </td>
  </tr>
</table>
<?php echo Draw::end_round_table();?>
<script>
function change_module(module_id)
{
	location = '<?php echo URL::build_current(array('cmd'));?>&module_id='+module_id;
}
</script>