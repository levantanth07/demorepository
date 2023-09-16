<fieldset id="toolbar">
	<legend>[[.definition_config.]]</legend>
 	<div id="toolbar-title">
		[[.definition_config.]] <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
		  <td id="toolbar-preview"  align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Move"> </span> [[.Preview.]] </a> </td>
		  <td id="toolbar-save"  align="center"><a onclick="EditNewsAdmin.submit();"> <span title="Edit"> </span> [[.Save.]] </a> </td>
		  <td id="toolbar-cancel"  align="center"><a href="<?php echo Url::build_current(array());?>"> <span title="New"> </span> [[.Cancel.]] </a> </td>
		  <td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td>
		</tr>
	  </tbody>
	</table>
</fieldset>
<br clear="all">
<span style="display:none">
	<span id="config_sample">
		<span id="input_group_#xxxx#" style="white-space:nowrap; border-bottom:1px dotted #000099;">
			<input type="hidden" name="config[#xxxx#][id]" id="id_#xxxx#" />
			<span class="multi_edit_input">
				<span class="multi_input_title"></span>
				<span><input type="checkbox" id="_checked_#xxxx#"></span>
			</span><br />
			<span class="multi_edit_input">
				<span class="multi_input_title">[[.code.]]</span>
				<input name="config[#xxxx#][code]" style="width:200px;" class="multi_text_input" type="text" id="code_#xxxx#">
			</span><br>
			<span class="multi_edit_input">
					<span class="multi_input_title">[[.title.]]</span>
					<input style="width:200px;" name="config[#xxxx#][title]" class="multi_text_input" type="text" id="title_#xxxx#">
			</span><br>
			<span class="multi_edit_input">
				<span class="multi_input_title">[[.type.]]</span>
				<select  name="config[#xxxx#][type]" id="type_#xxxx#" class="multi_text_input" style="width:100px;" >
					<!--LIST:type_list-->
					<option value="[[|type_list.id|]]">[[|type_list.id|]]</option>
					<!--/LIST:type_list-->
				</select>
			</span><br>
			<span class="multi_edit_input">
				<span class="multi_input_title">[[.group.]]</span>
				<input style="width:200px" class="multi_text_input" name="config[#xxxx#][group]" type="text" id="group_#xxxx#" />
			</span><br>
			<span class="multi_edit_input">
				<span class="multi_input_title">[[.style.]]</span>
				<textarea style="width:85%" class="multi_text_input" name="config[#xxxx#][style]" id="style_#xxxx#" ></textarea>
			</span><br>
			<span class="multi_edit_input">
				<span class="multi_input_title">[[.default.]]</span>
				<textarea style="width:85%" class="multi_text_input" name="config[#xxxx#][default]" id="default_#xxxx#" ></textarea>
			</span><br>
			<!--IF:delete(User::can_delete(false,ANY_CATEGORY))-->
			<span class="multi_edit_input"><span style="width:20;">
				<img src="assets/default/images/buttons/delete.gif" onClick="if(Confirm('#xxxx#')){ mi_delete_row($('input_group_#xxxx#'),'config','#xxxx#');event.returnValue=false; }" style="cursor:hand;"/>
			</span></span><br>
			<!--/IF:delete-->
		</span>
	</span>
</span>
<div class="definition-config-bound">
<form method="post" name="config">
<input type="hidden" name="deleted_ids" id="deleted_ids" />
<div style="background-color:#ECE9D8;">
	<span id="config_all_elems">
	</span>
</div>
<!--IF:cond(User::can_edit(false,ANY_CATEGORY))-->
<div style="margin-top:20px; text-align:center">
<input type="button" value="[[.add_item.]]" onclick="mi_add_new_row('config','true');">
<input type="submit" name="cmd" value="[[.save.]]" />
</div>
<!--/IF:cond-->
</form>
</div>
<script>
jQuery(document).ready(function(){
	//jQuery('#config_all_elems span span span').css('border','1px solid #FF0000');
});
mi_init_rows('config',
	<?php if(isset($_REQUEST['config']))
	{
		echo String::array2js($_REQUEST['config']);
	}
	else
	{
		echo '[]';
	}
	?>,'id');
function Confirm(index)
{
	var Item = $('id_'+index).value;
	return confirm('[[.Are_you_sure_delete_item.]] '+Item+'?');
}
</script>