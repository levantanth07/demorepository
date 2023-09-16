<script>
jQuery(document).ready(function(){
	jQuery('#date_from').datepicker();
	jQuery('#date_to').datepicker();
});
</script>
<fieldset id="toolbar">
	<div id="toolbar-info">[[.Dashboard_by_date.]]</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		 <td>&nbsp;</td>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<div style="height:8px;"></div>
<fieldset id="toolbar">
<form name="UserDashboardForm" method="post" action="?<?php echo htmlentities($_SERVER['QUERY_STRING']);?>">
<table class="table">
  <tr>
	<td width="13%"><b>[[.user_id.]]</b></td>
	<td colspan="3"><b>
    <select name="user_id" id="user_id" style="width:150px;"></select></b></td>
  </tr>
	  <tr>
	<td width="13%"><b>[[.date_from.]]</b></td>
	<td width="18%"><b>
	  <input name="date_from" type="text" id="date_from" style="width:150px;"></b></td>
    <td width="7%">[[.date_to.]]</td>
    <td width="62%">  <input name="date_to" type="text" id="date_to" style="width:150px;"></td>
  </tr>
  <tr>
	    <td colspan="4"><input name="search" type="submit" value=" [[.filter_now.]] " id="search" ></td>
  </tr>
 </table>
	<input type="hidden" name="cmd" value="user" id="cmd"/>
</form>
	<div style="font-weight:bold;font-size:13px;height:30p;line-height:30px;width:100%;display:block;background-color:#CCC;text-indent:5px;">
		<?php
		if(Url::get('date_from') and Url::get('date_to')){
		?>[[.date_from.]] <?php echo Url::get('date_from')?> [[.date_to.]] <?php echo Url::get('date_to')?>
		<?php
		}else{?>
		[[.today.]]
		<?php }?>:
		<?php if(Url::get('user_id')){?>
		<b style="color:#FF0000"><?php echo Url::get('user_id');?></b>
		<?php }?>
		 [[.Post.]] [[|total|]] [[.items.]]
	</div>
	<table class="table">
	  <tr>
		<td width="6%"><b>[[.id.]]</b></td>
		<td width="16%"><b>[[.user_post.]]</b></td>
		<td width="20%"><b>[[.title.]]</b></td>
		<td width="13%"><b>[[.category.]]</b></td>
		<td width="16%" align="center"><b>[[.time.]]</b></td>
	  </tr>
	<!--LIST:items-->
	  <tr valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:pointer">
		<td><b>[[|items.id|]]</b></td>
		<td>[[|items.user_id|]]</td>
		<td>[[|items.name|]]</td>
		<td>[[|items.category_name|]]</td>
		<td align="center" width="16%">[[|items.time|]]</td>
	  </tr>
	<!--/LIST:items-->
	  <tr>
	  	<td colspan="5">&nbsp;</td>
	  </tr>
	</table>
</div>