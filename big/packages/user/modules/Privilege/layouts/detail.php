<?php
$title = (URL::get('cmd')=='delete')?Portal::language('delete_privilege'):Portal::language('privilege_detail');
$action = (URL::get('cmd')=='delete')?'delete':'detail';
System::set_page_title(Portal::get_setting('website_title','').' '.$title);?>
<div class="form_bound">
	<table cellpadding="0" width="100%"><tr><td  class="form_title"><img src="<?php echo Portal::template('core');?>/images/buttons/<?php echo $action;?>_button.gif" align="absmiddle" alt=""/><?php echo $title;?></td><?php if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a href="javascript:void(0)" onclick="PrivilegeForm.submit();"><img src="<?php echo Portal::template('core');?>/images/buttons/delete_button.gif" style="text-align:center" alt=""/><br />[[.Delete.]]</a></td><?php }else{ if(User::can_edit()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'edit','id'));?>"><img src="<?php echo Portal::template('core');?>/images/buttons/edit.jpg" style="text-align:center" alt=""/><br />[[.Edit.]]</a></td><?php } if(User::can_delete()){?><td class="form_title_button">
				<a href="<?php echo URL::build_current(array('cmd'=>'delete','id'));?>"><img src="<?php echo Portal::template('core');?>/images/buttons/delete_button.gif" alt=""/><br />[[.Delete.]]</a></td><?php }}?>
				<td class="form_title_button"><a href="<?php echo URL::build_current(array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
	));?>"><img src="<?php echo Portal::template('core');?>/images/buttons/go_back_button.gif" style="text-align:center" alt=""/><br />[[.back.]]</a></td>
				<td class="form_title_button">
				<a  href="<?php echo URL::build('default');?>"><img src="<?php echo Portal::template('core');?>/images/buttons/frontpage.gif" alt=""/><br />Trang ch&#7911;</a></td></tr></table><div class="form_content">
<table cellspacing="0" width="100%">
  <tr valign="top" >
    <td class="form_detail_label">[[.id.]]</td>
    <td width="1">:</td>
    <td class="form_detail_value">[[|id|]]</td>
	<td rowspan="5" valign="top">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="#FFE680" scope="col" nowrap="nowrap">&nbsp;[[.description.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img src="<?php echo Portal::template('core');?>/images/news_23.gif" width="8" height="7" /></th>
      </tr>
      <tr>
        <th colspan="2" align="left" valign="top" scope="col" style="font-weight:normal;font-style:italic;padding:0 0 0 5;">[[|description|]]</th>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
	</td>
  </tr>
  <tr>
		<td class="form_detail_label">[[.title.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|title|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.package_id.]]</td>
		<td>:</td>
		<td class="form_detail_value">[[|package_id|]]</td>
	</tr>
	</table>
	<?php
	if(URL::get('cmd')!='delete')
	{
	?>
		<fieldset><legend>[[.group_privilege.]]</legend>
		<table width="100%" class="form_multiple_item_area">
			<tr>
			<th width="300" class="form_multiple_item_label">
					[[.group_id.]]
				</th><th width="100" class="form_multiple_item_label">
					[[.parameters.]]
				</th>
			</tr>
			<!--LIST:group_privilege_items-->
			<tr>
			<td width="300" class="form_multiple_item_value">[[|group_privilege_items.group_id_name|]]</td><td width="100" class="form_multiple_item_value">
					[[|group_privilege_items.parameters|]]
				</td>
			</tr>
			<!--/LIST:group_privilege_items-->
		</table>
		</fieldset>
	<?php
	}
	?>
	<!--IF:delete(URL::get('cmd')=='delete')-->
	<form name="PrivilegeForm" method="post" action="?<?php echo htmlentities($_SERVER['QUERY_STRING']);?>">
	<input type="hidden" value="1" name="confirm"/>
	<input type="hidden" value="delete" name="cmd"/>
	<input type="hidden" value="<?php echo URL::get('id');?>" name="id"/>
	</form>
	<!--/IF:delete-->
	</div>
</div>
