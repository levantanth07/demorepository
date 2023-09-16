<?php
$title = (URL::get('cmd')=='delete')?Portal::language('Delete_module_setting'):Portal::language('Module_setting');
$action = (URL::get('cmd')=='delete')?'delete':'detail';
System::set_page_title(Portal::get_setting('company_name','').' '.$title);?>
<div class="form_bound">
<table cellpadding="0" width="100%"><tr><td  class="form_title"><?php echo $title;?></td><?php
			if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a javascript:void(0) onclick="ModuleSettingForm.submit();"><img alt="" src="assets/default/images/buttons/delete.jpg" style="text-align:center"/><br />[[.Delete.]]</a></td><?php
			}else{
				if(User::can_edit()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'edit','id'));?>"><img alt="" src="assets/default/images/buttons/edit.jpg" style="text-align:center"/><br />[[.Edit.]]<\/a></td><?php }
				if(User::can_delete()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'delete','id'));?>"><img alt="" src="assets/default/images/buttons/delete.jpg"/><br />[[.Delete.]]</a></td><?php }
			}?>
			<td class="form_title_button"><a href="<?php echo URL::build_current(array('module_id'=>isset($_GET['module_id'])?$_GET['module_id']:'',
	));?>"><img alt="" src="assets/default/images/buttons/back.jpg" style="text-align:center"/><br />[[.back.]]</a></td>
			<td class="form_title_button"><a target="_blank" href="<?php echo URL::build('help_topic',array('id'=>Module::$current->get_help_topic_id()));?>"><img alt="" src="assets/default/images/buttons/help.jpg"/><br />[[.help.]]</a></td></tr></table>
<div class="form_content">
<table cellspacing="0" width="100%">
  <tr valign="top" >
    <td class="form_detail_label">[[.id.]]</td>
    <td width="1">:</td>
    <td class="form_detail_value">[[|id|]]</td>
	<td rowspan="8" valign="top">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="<?php echo '#'.Portal::get_setting('crud_detail_item_label_bgcolor','FFE680');?>" scope="col" nowrap="nowrap">&nbsp;[[.description.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img alt="" src="<?php echo Portal::template('core');?>/images/news_23.gif" width="8" height="7" /></th>
      </tr>
      <tr>
        <th colspan="2" align="left" valign="top" scope="col" style="font-weight:normal;font-style:italic;padding:0 0 0 5;">[[|description|]]</th>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="<?php echo '#'.Portal::get_setting('crud_detail_item_label_bgcolor','FFE680');?>" scope="col" nowrap="nowrap">&nbsp;[[.default_value.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img alt="" src="<?php echo Portal::template('core');?>/images/news_23.gif" width="8" height="7" /></th>
      </tr>
      <tr>
        <th colspan="2" align="left" valign="top" scope="col" style="font-weight:normal;font-style:italic;padding:0 0 0 5;">[[|default_value|]]</th>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="<?php echo '#'.Portal::get_setting('crud_detail_item_label_bgcolor','FFE680');?>" scope="col" nowrap="nowrap">&nbsp;[[.value_list.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img alt="" src="<?php echo Portal::template('core');?>/images/news_23.gif" width="8" height="7" /></th>
      </tr>
      <tr>
        <th colspan="2" align="left" valign="top" scope="col" style="font-weight:normal;font-style:italic;padding:0 0 0 5;">[[|value_list|]]</th>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="<?php echo '#'.Portal::get_setting('crud_detail_item_label_bgcolor','FFE680');?>" scope="col" nowrap="nowrap">&nbsp;[[.edit_condition.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img alt="" src="<?php echo Portal::template('core');?>/images/news_23.gif" width="8" height="7" /></th>
      </tr>
      <tr>
        <th colspan="2" align="left" valign="top" scope="col" style="font-weight:normal;font-style:italic;padding:0 0 0 5;">[[|edit_condition|]]</th>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="<?php echo '#'.Portal::get_setting('crud_detail_item_label_bgcolor','FFE680');?>" scope="col" nowrap="nowrap">&nbsp;[[.view_condition.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img alt="" src="<?php echo Portal::template('core');?>/images/news_23.gif" width="8" height="7" /></th>
      </tr>
      <tr>
        <th colspan="2" align="left" valign="top" scope="col" style="font-weight:normal;font-style:italic;padding:0 0 0 5;">[[|view_condition|]]</th>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="<?php echo '#'.Portal::get_setting('crud_detail_item_label_bgcolor','FFE680');?>" scope="col" nowrap="nowrap">&nbsp;[[.extend.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img alt="" src="<?php echo Portal::template('core');?>/images/news_23.gif" width="8" height="7" /></th>
      </tr>
      <tr>
        <th colspan="2" align="left" valign="top" scope="col" style="font-weight:normal;font-style:italic;padding:0 0 0 5;">[[|extend|]]</th>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="<?php echo '#'.Portal::get_setting('crud_detail_item_label_bgcolor','FFE680');?>" scope="col" nowrap="nowrap">&nbsp;[[.meta.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img alt="" src="<?php echo Portal::template('core');?>/images/news_23.gif" width="8" height="7" /></th>
      </tr>
      <tr>
        <th colspan="2" align="left" valign="top" scope="col" style="font-weight:normal;font-style:italic;padding:0 0 0 5;">[[|meta|]]</th>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #EEEEEE;">
      <tr>
        <th width="93%" align="left" bgcolor="<?php echo '#'.Portal::get_setting('crud_detail_item_label_bgcolor','FFE680');?>" scope="col" nowrap="nowrap">&nbsp;[[.update_code.]]</th>
        <th width="7%" bgcolor="#FFE680" scope="col"><img alt="" src="<?php echo Portal::template('core');?>/images/news_23.gif" width="8" height="7" /></th>
      </tr>
      <tr>
        <th colspan="2" align="left" valign="top" scope="col" style="font-weight:normal;font-style:italic;padding:0 0 0 5;">[[|update_code|]]</th>
        </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
	</td>
  </tr>
  	<tr>
		<td class="form_detail_label">[[.name.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|name|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.module_id.]]</td>
		<td>:</td>
		<td class="form_detail_value">[[|module_id|]]</td>
	</tr><tr>
		<td class="form_detail_label">[[.type.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|type|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.group_name.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|group_name|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.position.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|position|]]
		</td>
	</tr><tr>
		<td class="form_detail_label">[[.group_column.]]</td>
		<td>:</td>
		<td class="form_detail_value">
			[[|group_column|]]
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
	<form name="ModuleSettingForm" method="post">
	<input type="hidden" value="1" name="confirm"/>
	<input type="hidden" value="delete" name="cmd"/>
	<input type="hidden" value="<?php echo URL::get('id');?>" name="id"/>
	</form>
	<!--/IF:delete-->
	</div>
</div>