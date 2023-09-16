<?php
$title = (URL::get('cmd')=='delete')?'Xóa module':'Danh sách module';
$action = (URL::get('cmd')=='delete')?'delete':'list';
?>
<style>
.module_tab
{
	font-size:16px;
	background-color:#DDDDDD;
}
.module_tab_select
{
	font-size:16px;
	background-color:#EFEFEF;
}
</style>
<div class="container">
	<table class="table"><tr><td width="60%"><h2><?php echo $title;?></h2></td><?php
		if(URL::get('cmd')=='delete'){?><td class="form_title_button"><a javascript:void(0) onclick="ListModuleAdminForm.submit();" class="btn btn-danger">Delete</a></td>
		<td class="form_title_button"><a href="<?php echo URL::build_current(array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'','name'=>isset($_GET['name'])?$_GET['name']:''));?>"  class="btn btn-default">back</a></td><?php }else{
		if(User::can_edit()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('package_id','cmd'=>'update'));?>" class="btn btn-default">Update cache hằng số Module</a></td>
	<td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'delete_cache'));?>" class="btn btn-default">delete_module_cache</a></td><?php }?><?php
		if(User::can_add()){?><td class="form_title_button"><a href="<?php echo URL::build_current(array('type','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'','name'=>isset($_GET['name'])?$_GET['name']:'')+array('cmd'=>'add'));?>" class="btn btn-success">Add</a></td><?php }?><?php
		if(User::can_delete()){?>
	<td class="form_title_button"><a javascript:void(0) onclick="ListModuleAdminForm.cmd.value='delete';ListModuleAdminForm.submit();" class="btn btn-danger">Delete</a></td>
	<?php }}?>
				</tr>
  </table>
  <div class="row">
  <div class="col-md-2">
  	<h3>Package</h3>
    <ul class="nav">
    <!--LIST:packages-->
    <li>
      <a href="<?php echo URL::build_current(array('page_id','region','after','replace','type'));?>&package_id=[[|packages.id|]]" class="home-news-category-level<?php if(URL::get('package_id')==[[=packages.id=]])echo '_selected'.[[=packages.level=]];else echo [[=packages.level=]];?>">[[|packages.name|]]</a>
    </li>
    <!--/LIST:packages-->
    </ul>
    <a href="<?php echo Url::build('package');?>" class="btn btn-default btn-sm">Quản lý package</a>
  </div>
  <div class="col-md-10">
  <div>
  <a class="module_tab<?php if(!URL::get('type'))echo '_select';?>" href="<?php echo URL::build_current(array('package_id','name','region','page_id','after','replace','href'));?>">&nbsp;&nbsp;Normal module&nbsp;&nbsp;</a>
  <a class="module_tab<?php if(URL::get('type')=='content')echo '_select';?>" href="<?php echo URL::build_current(array('package_id','name','region','page_id','after','replace','type'=>'content','href'));?>">&nbsp;&nbsp;content&nbsp;&nbsp;</a>
  <a class="module_tab<?php if(URL::get('type')=='HTML')echo '_select';?>" href="<?php echo URL::build_current(array('package_id','name','region','page_id','after','replace','type'=>'HTML','href'));?>">&nbsp;&nbsp;HTML&nbsp;&nbsp;</a>
  <a class="module_tab<?php if(URL::get('type')=='PLUGIN')echo '_select';?>" href="<?php echo URL::build_current(array('package_id','name','region','page_id','after','replace','type'=>'PLUGIN','href'));?>">&nbsp;&nbsp;PLUGIN&nbsp;&nbsp;</a>
  <a class="module_tab<?php if(URL::get('type')=='WRAPPER')echo '_select';?>" href="<?php echo URL::build_current(array('package_id','name','region','page_id','after','replace','type'=>'WRAPPER','href'));?>">&nbsp;&nbsp;WRAPPER&nbsp;&nbsp;</a>
  </div>
  <hr>
  <form method="post" name="SearchModuleAdminForm">
  name: <input name="name" type="text" id="name" style="width:220px"><input type="hidden" name="page_no" value="1" /><input type="submit" value="   search   ">
  </form>
      <form name="ListModuleAdminForm" method="post">
          <table class="table">
              <tr valign="middle" bgcolor="#EFEFEF" style="line-height:20px">
                  <th width="1%" title="check_all"><input type="checkbox" value="1" id="ModuleAdmin_all_checkbox" onclick="select_all_checkbox(this.form,'ModuleAdmin',this.checked,'#FFFFEC','white');" <?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                  <th nowrap align="left" >ID</th>
                  <th nowrap align="left" >
                      <a href="<?php echo URL::build_current(((URL::get('order_by')=='module.name' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'module.name'));?>" title="sort">
                          <?php if(URL::get('order_by')=='module.name') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								name
                      </a>
                  </th>
                  <th nowrap align="left">
                      <a title="sort" href="<?php echo URL::build_current(((URL::get('order_by')=='module.title_'.Portal::language() and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'module.title_'.Portal::language()));?>" >
                          <?php if(URL::get('order_by')=='module.title_'.Portal::language()) echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								title
                      </a>
                  </th>
                  <th nowrap align="left">
                      <a href="<?php echo URL::build_current(((URL::get('order_by')=='package_id' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'package_id'));?>" title="sort">
                          <?php if(URL::get('order_by')=='package_id') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								package
                      </a>
                  </th>
                  <?php if(User::can_edit(false,ANY_CATEGORY))
                  {
                      ?><th>&nbsp;</th><?php
                  }?>
              </tr>
              <!--LIST:items-->
              <tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],MAP['just_edited_ids'])))){ echo '#EFEFEF';} else {echo 'white';}?>" valign="middle" <?php Draw::hover('#EFEFEF');?> id="ModuleAdmin_tr_[[|items.id|]]">
                  <td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'ModuleAdmin',this,'#FFFFEC','white');" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
                  <td nowrap align="left">[[|items.id|]]</td>
                  <td nowrap align="left">
                      <a href="#"  onclick="location='[[|items.href|]]';return false;"><h5>[[|items.name|]]</h5></a>
                  </td>
                  <td align="left">
                      [[|items.title|]]
                  </td>
                  <td nowrap align="left">[[|items.package_id|]]</td>
                  <?php
                  if(User::can_edit())
                  {
                      ?><td width="24px" align="center">
                      <a class="btn btn-warning btn-sm" href="<?php echo Url::build_current(array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
'name'=>isset($_GET['name'])?$_GET['name']:'',
)+array('cmd'=>'edit','id'=>[[=items.id=]])); ?>">Sửa</a></td>
                      <?php
                  }
                  ?>
              </tr>
              <!--/LIST:items-->
          </table>
          <input type="hidden" name="cmd" value="delete"/>
          <input type="hidden" name="page_no" value="1"/>
          <!--IF:delete(URL::get('cmd')=='delete')-->
          <input type="hidden" name="confirm" value="1" />
          <!--/IF:delete-->
      </form>
      <div class="paging">[[|paging|]]</div>
   </div>
   </div>
</div>