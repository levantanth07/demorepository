<script>
	function make_cmd(cmd)
	{
		jQuery('#cmd').val(cmd);
		document.ListRecycleBinForm.submit();
	}
</script>
<fieldset id="toolbar">
	<legend>[[.System_management.]]</legend>
	<div id="toolbar-info">[[.manage_recyclebin.]]</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
			<td id="toolbar-cancel"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_empty');?>')){make_cmd('delete');}"> <span title="[[.empty.]]"> </span> [[.empty.]] </a> </td>
			<td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<form name="ListRecycleBinForm" method="post">
	<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
	  <tr style="background-color:#F0F0F0">
	    <th width="7%" align="left"><a>[[.select_each_item_below_to_restore.]]</a></th>
	  </tr>
	  <tr>
		<td>
			<!--LIST:items-->
				<!--IF:cond(preg_match('/(.*)\.([0-9a-zA-Z]+).sql/',[[=items.name=]],$matches))-->
				<div style="float:left;width:100px;height:88px;margin:9px;border:1px solid #E7E7E7;padding:2px;padding-bottom:0px;cursor:pointer" align="center"  onclick="location='<?php echo Url::build_current(array('cmd'=>'restore','path'=>[[=items.name=]],'table'=>$matches[1],'id'=>$matches[2]));?>'" title="[[|items.name|]]">
					<div><img src="[[|items.icon|]]" width="50"></div>
					<div>
						<div><?php echo $matches[1];?>(<?php echo $matches[2];?>)</div>
						<div>[[|items.time|]]</div>
					</div>
				</div>
				<!--/IF:cond-->
			<!--/LIST:items-->
		</td>
	  </tr>
	</table>
	<table width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;height:8px;#width:99%" align="center">
	  <tr>
		<td>&nbsp;</td>
	  </tr>
	</table>
	<input name="cmd" type="hidden" id="cmd">
</form><div style="#height:8px;"></div>
</fieldset>
