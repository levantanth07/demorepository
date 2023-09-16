<fieldset id="toolbar">
	<div id="toolbar-title">
		[[.currency_admin.]]
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <?php if(User::can_add(false,ANY_CATEGORY)){?><td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>"> <span title="[[.New.]]"> </span> [[.New.]] </a> </td><?php }?>
   		  <?php if(User::can_admin(false,ANY_CATEGORY)){?><td id="toolbar-list"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'update'));?>#"> <span title="[[.Update.]]"> </span> [[.Update.]] </a> </td><?php }?>
   		  <?php if(User::can_admin(false,ANY_CATEGORY)){?><td id="toolbar-config"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'cache'));?>#"> <span title="[[.cache.]]"> </span> [[.cache.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<form name="CurrencyAdmin" method="post">
<table cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
	<tr valign="middle" bgcolor="#F0F0F0" style="line-height:20px">
		<th width="3%" align="left" nowrap><a>#</a></th>
		<th width="10%" align="left" nowrap><a>[[.id.]]</a></th>
		<th width="40%" align="left" nowrap><a>[[.name.]]</a></th>
		<th width="20%" align="left" nowrap><a>[[.exchange.]]</a></th>
		<th width="10%" align="left" nowrap><a>[[.position.]]</a></th>
		<!--IF:cond(User::can_edit(false,ANY_CATEGORY))--><th width="1%" align="left" nowrap><a>[[.edit.]]</a></th><!--/IF:cond-->
		<!--IF:cond1(User::can_delete(false,ANY_CATEGORY))--><th width="1%" align="left" nowrap><a>[[.delete.]]</a></th><!--/IF:cond1-->
	</tr>
	<?php $i=0;?>
	<!--LIST:items-->
	<?php $i++;?>
	<tr valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="CurrencyAdmin_tr_[[|items.id|]]">
		<td><?php echo $i;?></td>
		<td>[[|items.id|]]</td>
		<td>[[|items.brief|]]</td>
		<td>[[|items.exchange|]]</td>
		<td>[[|items.position|]]</td>
		<!--IF:cond(User::can_edit(false,ANY_CATEGORY))--><td align="center"><a href="<?php echo Url::build_current(array('cmd'=>'edit','id'=>[[=items.id=]]));?>"><img src="assets/default/images/buttons/button-edit.png"></a></td><!--/IF:cond-->
		<!--IF:cond1(User::can_delete(false,ANY_CATEGORY))--><td align="center"><a onclick="if(confirm('[[.are_you_want_to_delete.]]')){location='<?php echo Url::build_current(array('cmd'=>'delete','id'=>[[=items.id=]]));?>'}"><img src="assets/default/images/buttons/uncheck.gif"></a></td><!--/IF:cond1-->
	</tr>
	<!--/LIST:items-->
</table>
<table width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;border-top:0px;height:8px;#width:99%;" align="center">
<tr>
	<td align="right">&nbsp;</td>
</tr>
</table>
</form>
<div style="#height:8px"></div>
</fieldset>