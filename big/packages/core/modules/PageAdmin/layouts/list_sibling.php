<?php
$title = (URL::get('cmd')=='delete')?Portal::language('delete_title'):Portal::language('list_title');
$action = (URL::get('cmd')=='delete')?'delete':'list';
System::set_page_title(Portal::get_setting('website_title','').' '.$title);?>
<div class="form_bound">
<table cellpadding="0" width="100%">
    <tr>
        <td  class="form_title"><img src="assets/default/images/buttons/<?php echo $action;?>_button.gif" align="absmiddle" alt=""/><?php echo $title;?></td><?php if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a javascript:void(0) onclick="ListPageAdminSiblingForm.submit();"><img src="assets/default/images/buttons/delete_button.gif" style="text-align:center" alt=""/><br />[[.Delete.]]</a></td><td class="form_title_button"><a href="<?php echo URL::build_current(array('portal_id','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  ));?>"><img src="assets/default/images/buttons/go_back_button.gif" style="text-align:center" alt=""/><br />[[.back.]]</a></td><?php }else{ if(User::can_add()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('portal_id','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  )+array('cmd'=>'add'));?>"><img src="assets/default/images/buttons/add_button.gif" style="text-align:center" alt=""/><br />[[.Add.]]</a></td><?php }?><?php if(User::can_delete()){?><td class="form_title_button">
				<a javascript:void(0) onclick="ListPageAdminSiblingForm.cmd.value='delete';ListPageAdminSiblingForm.submit();"><img src="assets/default/images/buttons/delete_button.gif" alt=""/><br />[[.Delete.]]</a></td><?php }}?>
				<td class="form_title_button">
				<a  href="<?php echo URL::build('default');?>"><img src="assets/default/images/buttons/frontpage.gif" alt=""/><br />Trang ch&#7911;</a></td></tr></table>
	<div class="form_content">
	<table cellspacing="0" width="100%">
	<tr bgcolor="#EFEFEF" valign="top">
		<td nowrap bgcolor="#EFEFEF">
			<table width="100%" cellpadding="0" cellspacing="0">
			<!--LIST:packages-->
			<tr><td nowrap>
				<a href="<?php echo URL::build_current(array('portal_id'));?>&package_id=[[|packages.id|]]" class="category_level<?php if(URL::get('package_id')==[[=packages.id=]])echo '_selected'.[[=packages.level=]];else echo [[=packages.level=]];?>">[[|packages.name|]]</a>
			</td></tr>
			<!--/LIST:packages-->
			</table>
			<a target="_blank" href="<?php echo Url::build('package');?>" class="category_level1">[[.packages/admin.]]</a>
		</td>
		<td width="100%">
			<table bgcolor="#EFEFEF" cellspacing="0" width="100%">
			<tr>
				<td width="100%">
					<form method="post" name="SearchPageAdminForm">
					<table>
						<tr><td align="right" nowrap style="font-weight:bold">[[.name.]]</td>
						<td>:</td>
						<td nowrap>
								<input name="name" type="text" id="name" style="width:200">
						</td></tr>
					</table>
					<input type="submit" value="   [[.search.]]   ">
					</form>
					<form name="ListPageAdminSiblingForm" method="post">
					<a name="top_anchor"></a>
					<div style="border:2px solid #FFFFFF;">
					<table width="99%" cellpadding="2" cellspacing="0" border="1" style="border-collapse:collapse" bordercolor="#CCCCCC">
						<tr valign="middle" bgcolor="#EFEFEF" style="line-height:20px">
							<th width="1%" title="[[.check_all.]]"><input type="checkbox" value="1" id="PageAdmin_all_checkbox" onclick="select_all_checkbox(this.form, 'PageAdmin',this.checked,'#FFFFEC','white');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
							<th nowrap align="left" >
								<a href="<?php echo URL::build_current(array('portal_id')+((URL::get('order_by')=='page.name' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'page.name'));?>" title="[[.sort.]]">
								<?php if(URL::get('order_by')=='page.name') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								[[.name.]]
								</a>
							</th><th nowrap align="left">
								<a title="[[.sort.]]" href="<?php echo URL::build_current(array('portal_id')+((URL::get('order_by')=='page.title_'.Portal::language() and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'page.title_'.Portal::language()));?>" >
								<?php if(URL::get('order_by')=='page.title_'.Portal::language()) echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								[[.title.]]
								</a>
							</th>
							<th nowrap align="left">
								<a href="<?php echo URL::build_current(array('portal_id')+((URL::get('order_by')=='package_id' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'package_id'));?>" title="[[.sort.]]">
								<?php if(URL::get('order_by')=='package_id') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								[[.package_id.]]
								</a>
							</th>
							<th nowrap align="left">
								<a href="<?php echo URL::build_current(array('portal_id')+((URL::get('order_by')=='params' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'params'));?>" title="[[.sort.]]">
								<?php if(URL::get('order_by')=='params') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								[[.params.]]
								</a>
							</th>
							<?php if(User::can_edit())
							{
							?><th>&nbsp;</th><th>&nbsp;</th><?php
							}?>						</tr>
						<!--LIST:items-->
						<tr bgcolor="white" valign="middle" <?php Draw::hover('#E2F1DF');?> style="cursor:pointer;" id="PageAdmin_tr_[[|items.id|]]">
							<td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'PageAdmin',this,'#FFFFEC','white'\);" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
							<td nowrap align="left" onclick="location='<?php echo URL::build('edit_page');?>&id=[[|items.id|]]';">
								[[|items.name|]]
							</td><td nowrap align="left" onclick="location='<?php echo URL::build('edit_page');?>&id=[[|items.id|]]';">
								[[|items.title|]]
							</td><td nowrap align="left" onclick="location='<?php echo URL::build('edit_page');?>&id=[[|items.id|]]';">
								[[|items.package_id|]]
							</td>
							<td nowrap align="left" onclick="location='<?php echo URL::build('edit_page');?>&id=[[|items.id|]]';">
								[[|items.params|]]
							</td>
							<?php
							if(User::can_edit())
							{
							?>							<td width="24px" align="center">
								<a href="<?php echo Url::build_current(array('portal_id','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  )+array('cmd'=>'edit','id'=>[[=items.id=]])); ?>"><img src="assets/default/images/buttons/edit.gif" alt="[[.Edit.]]" width="12" height="12" border="0"></a></td>
						  	<td nowrap><a href="<?php echo Url::build_current(array('portal_id','package_id', 'name')+array('cmd'=>'duplicate','id'=>[[=items.id=]])); ?>"><img src="assets/default/images/buttons/duplicate.gif" alt="[[.duplicate.]]" width="13" height="13" border="0"></a></td>
							<?php
							}
							?>						</tr>
						<!--/LIST:items-->
					</table>
					</div>
					<input type="hidden" name="cmd" value="delete"/>
					<input type="hidden" name="page_no" value="1"/>
					<!--IF:delete(URL::get('cmd')=='delete')-->
					<input type="hidden" name="confirm" value="1" />
					<!--/IF:delete-->
					</form>
				</td>
			</tr>
			</table>
			[[|paging|]]
			<table width="100%"><tr>
			<td width="100%">
				[[.select.]]:&nbsp;
				<a javascript:void(0) onclick="select_all_checkbox(document.ListPageAdminForm,'PageAdmin',true,'#FFFFEC','white');">[[.select_all.]]</a>&nbsp;
				<a javascript:void(0) onclick="select_all_checkbox(document.ListPageAdminForm,'PageAdmin',false,'#FFFFEC','white');">[[.select_none.]]</a>
				<a javascript:void(0) onclick="select_all_checkbox(document.ListPageAdminForm,'PageAdmin',-1,'#FFFFEC','white');">[[.select_invert.]]</a>
			</td>
			<td>
				<a name="bottom_anchor" javascript:void(0)><img src="<?php echo Portal::template('core');?>/images/top.gif" title="[[.top.]]" border="0" alt="[[.top.]]"></a>
			</td>
			</tr></table>
		</td>
</tr>
	</table>
	</div>
</div>