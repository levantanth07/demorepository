<fieldset id="toolbar">
	<legend>[[.utils.]]</legend>
	<div id="toolbar-info">[[.Utils_other.]]</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
		<td id="toolbar-preview"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'update'));?>"><span title="[[.update.]"> </span> [[.update.]] </a> </td>
		 <td id="toolbar-help" align="center"><a href="<?php echo Url::build('help');?>"> <span title="[[.Help.]"> </span> [[.Help.]] </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<script type="text/javascript">
	jQuery(function() {
		jQuery('#AccountUtils').tabs();
		});
</script>
<div style="height:8px;"></div>
 <fieldset id="toolbar">
<form name="UtilsForm " method="post" id="AccountUtilsForm" enctype="multipart/form-data">
  <div id="AccountUtils" align="center">
	<ul>
		<li><a href="#weather"><span>[[.weather.]]</span></a></li>
		<li><a href="#golden_exchange"><span>[[.golden_exchange.]]</span></a></li>
		<li><a href="#currency"><span>[[.currency.]]</span></a></li>
	</ul>
	<div id="golden_exchange">
		<br>
			<table  cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:4px;" border="1" bordercolor="#E7E7E7" align="center">
				<tr style="background-color:#F0F0F0">
					<th align="left"><a>[[.id.]]</a></th>
					<th align="left"><a>[[.name.]]</a></th>
					<th align="left"><a>[[.sell.]]</a></th>
					<th align="left"><a>[[.buy.]]</a></th>
				</tr>
				<!--LIST:golden-->
				<tr <?php Draw::hover('#FFFFDD');?> style="cursor:hand;<?php if([[=golden.id=]]%2){echo 'background-color:#F9F9F9';}?>">
					<td align="left">[[|golden.id|]]</td>
					<td align="left">[[|golden.name|]]</td>
					<td align="left">[[|golden.sell|]]</td>
					<td align="left">[[|golden.buy|]]</td>
				</tr>
				<!--/LIST:golden-->
			</table>
	</div>
	<div id="weather">
		<br>
			<table  cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:4px;" border="1" bordercolor="#E7E7E7" align="center">
				<tr style="background-color:#F0F0F0">
					<th width="4%" align="left"><a>[[.id.]]</a></th>
					<th align="left"><a>[[.province.]]</a></th>
					<th align="left"><a>[[.temperature.]]</a></th>
					<th align="left"><a>[[.image_url.]]</a></th>
				</tr>
				<!--LIST:weather-->
				<tr <?php Draw::hover('#FFFFDD');?> style="cursor:hand;<?php if([[=weather.id=]]%2){echo 'background-color:#F9F9F9';}?>">
					<td align="left">[[|weather.id|]]</td>
					<td align="left">[[|weather.province|]]</td>
					<td align="left">[[|weather.temperature|]]</td>
					<td align="left"><img src="[[|weather.images|]]" /></td>
				</tr>
				<!--/LIST:weather-->
			</table>
	</div>
	<div id="currency">
		<br>
			<table  cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:4px;" border="1" bordercolor="#E7E7E7" align="center">
				<tr style="background-color:#F0F0F0">
					<th align="left"><a>[[.id.]]</a></th>
					<th align="left"><a>[[.name.]]</a></th>
					<th align="left"><a>[[.exchange.]]</a></th>
					<th align="left"><a>[[.position.]]</a></th>
				</tr>
				<!--LIST:currency-->
				<tr <?php Draw::hover('#FFFFDD');?> style="cursor:hand;<?php if([[=currency.id=]]%2){echo 'background-color:#F9F9F9';}?>">
					<td align="left">[[|currency.id|]]</td>
					<td align="left">[[|currency.brief|]]</td>
					<td align="left">[[|currency.exchange|]]</td>
					<td align="left">[[|currency.position|]]</td>
				</tr>
				<!--/LIST:currency-->
			</table>
	</div>
</div>
<input name="cmd" type="hidden" id="cmd" value="save">
</form>
<table width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;height:8px;#width:99%" align="center">
	<tr>
		<td align="right">&nbsp;</td>
	</tr>
</table>
<div style="#height:8px;"></div>
</fieldset>
