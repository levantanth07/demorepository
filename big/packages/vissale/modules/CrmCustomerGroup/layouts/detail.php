<?php 
$title = (URL::get('cmd')=='delete')?'X&#243;a':'Chi ti&#7871;t';
$action = (URL::get('cmd')=='delete')?'delete':'detail';
System::set_page_title(Portal::get_setting('company_name','').' '.$title);?>
<link href="skins/default/category.css" rel="stylesheet" type="text/css" />
<div class="form_bound">
	<script type="text/javascript" >
		$('title_region').style.display='';
		$('title_region').innerHTML='<table cellpadding="0" width="100%"><tr><td  class="form_title"><img alt="" src="packages/portal/skins/default/images/buttons/<?php echo $action;?>_button.gif" align="absmiddle"/><?php echo $title;?></td><?php 
			if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a href="#a" onclick="CrmCustomerGroupForm.submit();"><img alt="" src="packages/portal/skins/default/images/buttons/delete_button.gif" style="text-align:center"/><br />[[.delete.]]<\/a></td><?php 
			}else{ 
				if(User::can_edit()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'edit','id'));?>"><img alt="" src="packages/portal/skins/default/images/buttons/edit.jpg" style="text-align:center"/><br />[[.edit.]]<\/a></td><?php } 
				if(User::can_delete()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'delete','id'));?>"><img alt="" src="packages/portal/skins/default/images/buttons/delete_button.gif"/><br />[[.delete.]]<\/a></td><?php }
			}?>\
			<td class="form_title_button"><a href="<?php echo URL::build_current(array(
	 'name'=>isset($_GET['name'])?$_GET['name']:'', 
	));?>"><img alt="" src="packages/portal/skins/default/images/buttons/go_back_button.gif" style="text-align:center"/><br />[[.back.]]<\/a></td>\
			</td><\/tr><\/table>';
	</script>
<div class="form_content">
<table cellspacing="0" width="100%">
  <tr valign="top" >
    <td class="form_detail_label">[[.id.]]</td>
    <td width="1">:</td>
    <td class="form_detail_value">[[|id|]]</td>
	<td rowspan="3" valign="top">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="#FFE680" scope="col" nowrap="nowrap">&nbsp;[[.description.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img alt="" src="skins/default/images/news_23.gif" width="8" height="7" /></th>
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
		<td class="form_detail_label">Tên</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|name|]]
		</td>
	</tr>
	</table>
	<?php
	if(URL::get('cmd')!='delete')
	{
	?>
	<?php
	}
	?>
	<!--IF:delete(URL::get('cmd')=='delete')-->
	<form name="CrmCustomerGroupForm" method="post">
	<input type="hidden" value="1" name="confirm"/>
	<input type="hidden" value="delete" name="cmd"/>
	<input type="hidden" value="<?php echo URL::get('id');?>" name="id"/>
	</form>
	<!--/IF:delete-->
	</div>
</div>

