<?php
$title = (URL::get('cmd')=='delete')?Portal::language('delete_title'):Portal::language('list_title');
$action = (URL::get('cmd')=='delete')?'delete':'list';
System::set_page_title(Portal::get_setting('company_name','').' '.$title);?>
<div class="form_bound">
<table cellpadding="0" width="100%" class="table"><tr valign="bottom"><td width="70%"><h2><?php echo $title;?></h2></td><?php
	if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a javascript:void(0) onclick="ListPackageAdminForm.submit();" class="btn btn-danger">[[.Delete.]]</a></td>
	<td class="form_title_button"><a href="<?php echo URL::build_current(array('name'=>isset($_GET['name'])?$_GET['name']:''));?>" class="btn btn-default">[[.back.]]</a></td><?php }
	else{ if(User::can_add()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array(	'name'=>isset($_GET['name'])?$_GET['name']:'')+array('cmd'=>'add'));?>" class="btn btn-success">[[.Add.]]</a></td><?php }?><?php
	if(User::can_edit()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'make_library_cache'));?>" class="btn btn-default">[[.make_cache.]]</a></td>
	<td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'delete_cache'));?>" class="btn btn-default">[[.delete_module_cache.]]</a></td><?php }?><?php
	if(User::can_delete()){?><td class="form_title_button">
				<a javascript:void(0) onclick="ListPackageAdminForm.cmd.value='delete';ListPackageAdminForm.submit();"  class="btn btn-danger">[[.Delete.]]</a></td><?php }}?></tr></table>
        <table class="table">
        <tr bgcolor="#EFEFEF" valign="top">
          <td width="100%">
            <form method="post" name="SearchPackageAdminForm">
                <table>
                  <tr><td align="right" nowrap style="font-weight:bold">[[.name.]]</td>
                  <td>:</td>
                  <td nowrap>
                    <input type="hidden" name="page_no" value="1" />
                    <input name="name" type="text" id="name" style="width:200"><input type="submit" value="   [[.search.]]   ">
                  </td></tr>
                </table>
                </form>
                <form name="ListPackageAdminForm" method="post">
                <a name="top_anchor"></a>
                <table class="table">
                  <tr valign="middle" bgcolor="#EFEFEF" style="line-height:20px">
                    <th width="1%" title="[[.check_all.]]"><input type="checkbox" value="1" id="PackageAdmin_all_checkbox" onclick="select_all_checkbox(this.form, 'PackageAdmin',this.checked,'#FFFFEC','white');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                    <th nowrap align="left" >
                      <a href="<?php echo URL::build_current(((URL::get('order_by')=='package.name' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'package.name'));?>" title="[[.sort.]]">
                      <?php if(URL::get('order_by')=='package.name') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								[[.name.]]
                      </a>
                    </th><th nowrap align="left">
                      <a title="[[.sort.]]" href="<?php echo URL::build_current(((URL::get('order_by')=='package.title_'.Portal::language() and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'package.title_'.Portal::language()));?>" >
                      <?php if(URL::get('order_by')=='package.title_'.Portal::language()) echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								[[.title.]]
                      </a>
                    </th>
                    <th>&nbsp;</th>
                    <?php if(User::can_edit())
                    {
                    ?><th>&nbsp;</th><?php
                    }?>							<?php if(User::can_edit())
                    {?>							<th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <?php }?>						</tr>
                  <!--LIST:items-->
                  <tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],MAP['just_edited_ids'])))){ echo '#EFFFDF';} else {echo 'white';}?>" valign="middle" <?php Draw::hover('#E2F1DF');?> style="cursor:pointer;" id="PackageAdmin_tr_[[|items.id|]]">
                    <td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'PackageAdmin',this,'#FFFFEC','white'\);" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
                    <td nowrap align="left" onclick="location='<?php echo URL::build_current();?>&cmd=edit&id=[[|items.id|]]';">
                        [[|items.indent|]]
                        [[|items.indent_image|]]
                        <span class="page_indent">&nbsp;</span>
                        [[|items.name|]]
                      </td><td nowrap align="left" onclick="location='<?php echo URL::build_current();?>&cmd=edit&id=[[|items.id|]]';">
                        [[|items.title|]]
                      </td>
                      <td nowrap align="left" onclick="location='<?php echo URL::build_current();?>&cmd=edit&id=[[|items.id|]]';">
                        [[|items.type|]]
                      </td>
                    <?php
                    if(User::can_edit(false,ANY_CATEGORY))
                    {
                    ?>							<td width="24px" align="center">
                      <a href="<?php echo Url::build_current(array(
        'name'=>isset($_GET['name'])?$_GET['name']:'',
          )+array('cmd'=>'edit','id'=>[[=items.id=]])); ?>"><img src="assets/default/images/buttons/button-edit.png" alt="[[.Edit.]]" border="0"></a></td>
                    <?php
                    }
                    ?>							<td width="24px" align="center">[[|items.move_up|]]</td>
                    <td width="24px" align="center">[[|items.move_down|]]</td>
                  </tr>
                  <!--/LIST:items-->
                </table>
                <input type="hidden" name="cmd" value="delete"/>
      <input type="hidden" name="page_no" value="1"/>
      <!--IF:delete(URL::get('cmd')=='delete')-->
                <input type="hidden" name="confirm" value="1" />
                <!--/IF:delete-->
                </form>
			<table width="100%"><tr>
			<td width="100%">
				[[.select.]]:&nbsp;
				<a javascript:void(0) onclick="select_all_checkbox(document.ListPackageAdminForm,'PackageAdmin',true,'#FFFFEC','white');">[[.select_all.]]</a>&nbsp;
				<a javascript:void(0) onclick="select_all_checkbox(document.ListPackageAdminForm,'PackageAdmin',false,'#FFFFEC','white');">[[.select_none.]]</a>
				<a javascript:void(0) onclick="select_all_checkbox(document.ListPackageAdminForm,'PackageAdmin',-1,'#FFFFEC','white');">[[.select_invert.]]</a>
			</td>
			</tr></table>
		</td>
</tr>
	</table>
</div>