<?php
$title = (URL::get('cmd')=='delete')?Portal::language('delete_title'):Portal::language('detail_title');
$action = (URL::get('cmd')=='delete')?'delete':'detail';
System::set_page_title(Portal::get_setting('website_title','').' '.$title);?>
<style type="text/css">
<!--
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 24px;
	color: #FF0000;
}
.style2 {
	font-size: 24;
	color: #FF0000;
}
-->
</style>
<div class="form_bound">
<table cellpadding="0" width="100%"><tr><td  class="form_title"><img src="assets/default/images/buttons/<?php echo $action;?>_button.gif" align="absmiddle" alt=""/><?php echo $title;?></td><?php if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a javascript:void(0) onclick="ModuleAdminForm.submit();"><img src="assets/default/images/buttons/delete_button.gif" style="text-align:center" alt=""/><br />[[.Delete.]]</a></td><?php }else{ if(User::can_edit()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'edit','id'));?>"><img src="assets/default/images/buttons/edit.jpg" style="text-align:center" alt=""/><br />[[.Edit.]]</a></td><?php } if(User::can_delete()){?><td class="form_title_button">
				<a href="<?php echo URL::build_current(array('cmd'=>'delete','id'));?>"><img src="assets/default/images/buttons/delete_button.gif" alt=""/><br />[[.Delete.]]</a></td><?php }}?>
				<td class="form_title_button"><a href="<?php echo URL::build_current(array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  ));?>"><img src="assets/default/images/buttons/go_back_button.gif" style="text-align:center" alt=""/><br />[[.back.]]</a></td>
	  	<td class="form_title_button"><a target="_blank" href="<?php echo URL::build_current(array('cmd'=>'export','module_id'=>URL::get('id')));?>"><img src="assets/default/images/buttons/export_button.gif" style="text-align:center"/><br />[[.export.]]</a></td>
				<td class="form_title_button">
				<a target="_blank" href="<?php echo URL::build('help',array('id'=>Module::$current->data['module_id'],'href'=>'?'.$_SERVER['QUERY_STRING']));?>"><img src="assets/default/images/buttons/frontpage.gif" alt=""/><br />Trang ch&#7911;</a></td></tr></table>
<div class="form_content">
<table cellspacing="0" width="100%">
  <tr valign="top" >
    <td class="form_detail_label">[[.id.]]</td>
    <td width="1">:</td>
    <td class="form_detail_value">[[|id|]]</td>
  </tr>
  	<tr>
		<td class="form_detail_label">[[.name.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|name|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.package_id.]]</td>
		<td>:</td>
		<td class="form_detail_value">[[|package_id|]]</td>
	</tr><tr>
		<td class="form_detail_label">[[.type.]]</td>
		<td>:</td>
		<td class="form_detail_value">[[|type|]]</td>
	</tr><tr>
		<td class="form_detail_label">[[.title.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|title|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.description.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|description|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.use_dblclick.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|use_dblclick|]]
		</td>
	</tr>
	</table>
	<?php
	if(URL::get('cmd')!='delete')
	{
	?>			<fieldset><legend>[[.module_table.]]</legend>
		<table width="100%" class="form_multiple_item_area">
			<tr>
			<th width="200" class="form_multiple_item_label">
					[[.table.]]
			  </th>
			</tr>
			<!--LIST:module_table_items-->
			<tr>
			<td width="200" class="form_multiple_item_value">
					[[|module_table_items.table|]]
			  </td>
			</tr>
			<!--/LIST:module_table_items-->
		</table>
		</fieldset>
	    <p>
	      <?php
	}
	?>	      <!--IF:delete(URL::get('cmd')=='delete')-->
    </p>
	    <form name="ModuleAdminForm" method="post">
	<input type="hidden" value="1" name="confirm"/>
	<input type="hidden" value="delete" name="cmd"/>
	<input type="hidden" value="<?php echo URL::get('id');?>" name="id"/>
	</form>
	<!--/IF:delete-->
  </div>
</div>
<div>
	<p><span class="style1">Ch&uacute; &yacute;</span>:  H&#7879; th&#7889;ng s&#7869; x&oacute;a <span class="style2">h&ograve;an to&agrave;n</span> nh&#7919;ng d&#7919; li&#7879;u sau:</p>
	    <li>T&#7845;t c&#7843; ng&ocirc;n ng&#7919;</li>
	    <li>T&#7845;t c&#7843; help</li>
	    <li>T&#7845;t c&#7843; block_setting c&#7911;a nh&#7919;ng block s&#7917; d&#7909;ng module n&agrave;y</li>
	    <li>T&#7845;t c&#7843; block s&#7917; d&#7909;ng module n&agrave;y </li>
</div>