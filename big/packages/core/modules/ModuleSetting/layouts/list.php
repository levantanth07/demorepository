<?php
$title = (URL::get('cmd')=='delete')?Portal::language('Delete_module_settings'):Portal::language('Module_settings');
System::set_page_title(Portal::get_setting('company_name','').' '.$title);?>
<form method="post" name="SearchModuleSettingForm">
	<table cellpadding="0" width="100%">
	<tr><td  class="form_title"><?php echo $title;?></td>
	<td class="form_title_search" nowrap="nowrap">
		Module: <select name="module_id" onchange="location='<?php echo URL::build('module_setting');?>&module_id='+this.value;"></select>
	</td>
	<?php
		if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a javascript:void(0) onclick="$('cmd').cmd='delete';ListModuleSettingForm.submit();"><img src="assets/default/images/buttons/delete.jpg" alt="" width="16" height="16" class="icon_cmd"/>[[.Delete.]]</a></td>
			<td class="form_title_button"><a href="<?php echo URL::build_current(array('module_id'=>isset($_GET['module_id'])?$_GET['module_id']:'',
	));?>"><img alt="" src="assets/default/images/buttons/back.jpg" class="icon_cmd"/>[[.back.]]</a></td><?php
		}else{
			if(User::can_add()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('module_id','cmd'=>'add','name', 'description', 'type', 'default_value', 'value_list', 'style','edit_condition', 'view_condition', 'extend', 'group_name', 'position', 'meta', 'group_column', 'update_code'));?>"><img alt="" src="assets/default/images/buttons/add.jpg" style="text-align:center" class="icon_cmd"/>[[.Add.]]</a></td><?php }?><?php
			if(User::can_delete()){?><td class="form_title_button"><a javascript:void(0) onclick="ListModuleSettingForm.cmd.value='delete';ListModuleSettingForm.submit();"><img alt="Delete" src="assets/default/images/buttons/delete.jpg" class="icon_cmd"/>[[.Delete.]]</a></td><?php }
		}?></tr></table>
</form>
<div class="form_bound">
	<div class="form_content">
		<form name="ListModuleSettingForm" method="post">
		<a name="top_anchor"></a>
		<div style="border:2px solid <?php echo Portal::get_setting('crud_list_item_frame_border_color','#FFFFFF');?>;">
		<table width="99%" cellpadding="2" cellspacing="0" border="1" style="border-collapse:collapse" bordercolor="<?php echo Portal::get_setting('crud_list_item_frame_color','#C3C3C3');?>">
		<thead>
			<tr valign="middle" bgcolor="<?php echo Portal::get_setting('crud_list_item_bgcolor','#E6E6E6');?>" style="line-height:20px">
				<th width="1%" title="[[.check_all.]]">
					<input type="checkbox" value="1" id="ModuleSetting_all_checkbox" onclick="select_all_checkbox(this.form,'ModuleSetting',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th />
				<th nowrap align="left" >
					[[.id.]]
				</th>
				<th nowrap align="left" >
					[[.module_name.]]
				</th>
				<th nowrap align="left" >
					[[.name.]]
				</th><th nowrap align="left" >
					<a href="<?php echo URL::build_current(((URL::get('order_by')=='module_setting.group_name' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'module_setting.group_name'));?>" title="[[.sort.]]">
					<?php if(URL::get('order_by')=='module_setting.group_name') echo '<img alt="" src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif">';?>
					[[.group_name.]]
					</a>
				</th><th nowrap align="right" >
					<a href="<?php echo URL::build_current(((URL::get('order_by')=='module_setting.position' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'module_setting.position'));?>" title="[[.sort.]]">
					<?php if(URL::get('order_by')=='module_setting.position') echo '<img alt="" src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif">';?>
					[[.position.]]
					</a>
				</th>
			</tr>
			</thead>
			<tbody>
			<!--LIST:items-->
			<tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],MAP['just_edited_ids'])))){ echo Portal::get_setting('crud_just_edited_item_bgcolor','#F7F7F7');} else {echo Portal::get_setting('crud_item_bgcolor','white');}?>" valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#F7F7F7'));?> style="cursor:pointer;" id="ModuleSetting_tr_[[|items.id|]]">
				<td><input name="selected_ids[]" type="checkbox" value="[[|items.module_id|]]_[[|items.id|]]" onclick="select_checkbox(this.form,'ModuleSetting',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
				<td nowrap align="left" onclick="location='<?php echo URL::build_current();?>&cmd=edit&id=[[|items.module_id|]]_[[|items.id|]]';">
						[[|items.id|]]
					</td>
				<td nowrap align="left" onclick="location='<?php echo URL::build_current();?>&cmd=edit&id=[[|items.module_id|]]_[[|items.id|]]';">
						[[|items.module_name|]]
					</td>
				<td nowrap align="left" onclick="location='<?php echo URL::build_current();?>&cmd=edit&id=[[|items.module_id|]]_[[|items.id|]]';">
						[[|items.name|]]
                </td><td nowrap align="left" onclick="location='<?php echo URL::build_current();?>&cmd=edit&id=[[|items.module_id|]]_[[|items.id|]]';">
                    [[|items.group_name|]]
                </td><td nowrap align="right" onclick="location='<?php echo URL::build_current();?>&cmd=edit&id=[[|items.module_id|]]_[[|items.id|]]';">
                    [[|items.position|]]
                </td>
			</tr>
			<!--/LIST:items-->
			</tbody>
		</table>
		</div>
		<table width="100%"><tr>
		<td width="100%">
			[[.select.]]:&nbsp;
			<a javascript:void(0) onclick="select_all_checkbox(document.ListModuleSettingForm,'ModuleSetting',true,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_all.]]</a>&nbsp;
			<a javascript:void(0) onclick="select_all_checkbox(document.ListModuleSettingForm,'ModuleSetting',false,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_none.]]</a>
			<a javascript:void(0) onclick="select_all_checkbox(document.ListModuleSettingForm,'ModuleSetting',-1,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');">[[.select_invert.]]</a>
		</td>
		<td>
			<a name="bottom_anchor" javascript:void(0)><img alt="" src="<?php echo Portal::template('core');?>/images/top.gif" title="[[.top.]]" border="0" alt="[[.top.]]"></a>
		</td>
		</tr></table>
		<table width="100%" class="table_page_setting">
<tr>
    <td class="paging_block">
		[[|paging|]]
	</td>
    <td class="total_item"><strong>[[.total.]] [[|total_page|]] [[.item.]]</strong>
	<span style="width:10px">|</span>
        <strong>[[.show.]]</strong>
		<span style="line-height:10px;">&nbsp;</span>
		<?php draw_select(array('10'=>'10','20'=>'20','50'=>'50','100'=>'100'),'item_per_page', 'item_per_page', 'select_style', 'document.ListModuleSettingForm.submit();');?>
	</td>
    <td class="gotopage_block"><strong>[[.go_to_page.]]</strong>
      <input type="text" name="page_no" value="" id="page_no_input" class="inp_go_to_page"/> <span class="button_text" onclick="goToPage('<?php echo Url::build_current();?>'+'&amp;page_no='+$('page_no_input').value);"><img src="assets/default/images/top.gif" alt="gotopage" style="cursor:pointer"/></span></td>
  </tr>
</table>
		<input type="hidden" name="cmd" value="" id="cmd"/>
<!--IF:delete(URL::get('cmd')=='delete')-->
		<input type="hidden" name="confirm" value="1" />
		<!--/IF:delete-->
		</form>
	</div>
</div>