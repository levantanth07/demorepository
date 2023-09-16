<script>
	function make_cmd(cmd)
	{
		document.FilterWordBadForm.submit();
	}
</script>
<fieldset id="toolbar">
	<legend>[[.content_manage_system.]]</legend>
	<div id="toolbar-title">[[.FilterWordBad.]]</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
		 <?php if(User::can_edit(false,ANY_CATEGORY)){?> <td id="toolbar-trash"  align="center"><a onclick="make_cmd('save');"> <span title="Filter"> </span> [[.Filter.]] </a> </td><?php }?>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<form name="FilterWordBadForm" method="post">
	<?php if(Form::$current->is_error())
		{
		?>		<strong>B&#225;o l&#7895;i</strong><br>
		<?php echo Form::$current->error_messages();?><br>
		<?php
		}
		?>
	<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
	  <tr style="background-color:#F0F0F0">
		<td width="50%">[[.list_bad_word_will_replace_by_empty_string.]] ([[.each_word_are_by_commas.]])</td>
	    <td width="50%">[[.symbol_replace.]]</td>
	  </tr>
	  <tr>
		<td align="left">
			<textarea name="word_bad" style="width:70%;height:220px" id="word_bad">[[|bad_word|]]</textarea>		</td>
	    <td align="left"><textarea name="symbol" style="width:50%;height:220px" id="symbol">***</textarea></td>
	  </tr>
	</table>
</form>
</fieldset>
