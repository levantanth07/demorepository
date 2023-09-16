<fieldset id="toolbar">
	<div id="toolbar-info">[[.Dashboard_advertisment.]]</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <?php if(User::can_view(MODULE_MANAGEADVERTISMENT,ANY_CATEGORY)){?><td id="toolbar-list" align="center"><a href="<?php echo Url::build('manage_advertisment');?>#"> <span title="[[.List_adv.]]"> </span> [[.List_adv.]] </a> </td><?php }?>
  		  <?php if(User::can_add(MODULE_MANAGEADVERTISMENT,ANY_CATEGORY)){?><td id="toolbar-new"  align="center"><a href="<?php echo Url::build('manage_advertisment',array('cmd'=>'advertisment'));?>#"> <span title="New"> </span> [[.New.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<div style="height:8px;"></div>
<fieldset id="toolbar">
<table  cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:4px;" border="1" bordercolor="#E7E7E7" align="center">
  <tr style="background-color:#F0F0F0">
	<th align="left" width="3%"><a>#</a></th>
	<th align="left"><a>[[.title.]]</a></th>
	<th align="left" width="20%"><a>[[.website.]]</a></th>
	<th align="left" width="10%"><a>[[.category_name.]]</a></th>
	<th align="left" width="7%" nowrap="nowrap"><a>[[.start_date.]]</a></th>
	<th align="left" width="7%" nowrap="nowrap"><a>[[.end_date.]]</a></th>
	<th align="left" width="10%"><a>[[.count_click.]]</a></th>
	<th align="left" width="15%"><a>[[.region.]]</a></th>
  </tr>
  <?php $i = 0;?>
  <!--LIST:items-->
  <tr style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>">
	<td><?php echo ++$i;?></td>
	<td><a href="<?php  echo Url::build('manage_advertisment',array('id'=>[[=items.id=]],'cmd'=>'advertisment'));?>"><img src="assets/default/images/buttons/button-edit.png" title="[[.edit.]]"></a>&nbsp;[[|items.name|]]</td>
	<td>[[|items.url|]]</td>
	<td>[[|items.category_name|]]</td>
	<td><?php echo date('d/m/Y',[[=items.start_time=]]);?></td>
	<td><?php echo date('d/m/Y',[[=items.end_time=]]);?></td>
	<td>[[|items.click_count|]]</td>
	<td>[[|items.region|]]&nbsp;<b><a style="color:#FF0000" href="<?php echo Url::build([[=items.page=]],array(),REWRITE);?>" target="_blank">([[|items.page|]])</a></b></td>
  </tr>
 <!--/LIST:items-->
</table>
 <table width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;height:8px;">
<tr>
	<td align="left">[[.have.]] <b>[[|total|]]</b> [[.advertisments.]]</td>
</tr>
</table>
</fieldset>