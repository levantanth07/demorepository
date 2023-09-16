<div class="container">
    <?php
    $title = (URL::get('cmd')=='delete')?Portal::language('delete_title'):Portal::language('list_title');
    $action = (URL::get('cmd')=='delete')?'delete':'list';
    System::set_page_title(Portal::get_setting('website_title','').' '.$title);?>
    <div id="title_region"></div>
    <table class="table">
        <tr>
            <td align="left" width="80%"><h3 class="title">Danh sách trang [<?php echo $action?>]</h3></td>
            <?php if(URL::get('cmd')=='delete'){?><td class="form-title-button"><a javascript:void(0) onclick="ListPageAdminForm.submit();"><img src="assets/default/images/buttons/delete_button.gif" alt="" width="20" height="20" style="text-align:center"/><br />
                    Xóa</a></td><td class="form-title-button"><a href="<?php echo URL::build_current(array('portal_id','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'', 'name'=>isset($_GET['name'])?$_GET['name']:''));?>" class="btn btn-default">Quay lại</a></td><?php }else{
                if(User::can_edit()){?><td class="form-title-button"><a href="<?php echo URL::build_current(array('portal_id','package_id','cmd'=>'delete_all_cache'));?>" class="btn btn-default">Xóa cache</a></td><?php }
                if(User::can_add()){?><td class="form-title-button"><a href="<?php echo URL::build_current(array('portal_id','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'', 'name'=>isset($_GET['name'])?$_GET['name']:'')+array('cmd'=>'add'));?>" class="btn btn-success">Thêm</a></td><?php }?><?php if(User::can_delete()){?><td class="form-title-button">
                    <a javascript:void(0) onclick="ListPageAdminForm.cmd.value='delete';ListPageAdminForm.submit();" class="btn btn-danger">Xóa</a></td><?php }}?>
        </tr></table>
    <hr>
    <table class="table">
        <tr bgcolor="#EFEFEF" valign="top">
            <td nowrap bgcolor="#EFEFEF">
                Các Packages
                <table width="100%" cellpadding="0" cellspacing="0" class="table">
                    <!--LIST:packages-->
                    <tr><td nowrap>
                            <a href="<?php echo URL::build_current(array('portal_id'));?>&package_id=[[|packages.id|]]" class="home-news-category-level<?php if(URL::get('package_id')==[[=packages.id=]])echo '_selected'.[[=packages.level=]];else echo [[=packages.level=]];?>">[[|packages.name|]]</a>
                        </td></tr>
                    <!--/LIST:packages-->
                </table>
                <a target="_blank" href="<?php echo Url::build('package');?>" class="btn btn-default">Package List</a>
            </td>
            <td width="100%">
                <table bgcolor="#EFEFEF" cellspacing="0" width="100%">
                    <tr>
                        <td width="100%">
                            <form method="post" name="SearchPageAdminForm">
                                Tên: <input name="name" type="text" id="name" style="width:200"> Portal: <input name="portal_id" type="text" id="portal_id" style="width:120">  <input type="submit" value="Tìm kiếm">
                            </form>
                            <form name="ListPageAdminForm" method="post">
                                <a name="top_anchor"></a>
                                <table class="table table-striped">
                                    <tr valign="middle" bgcolor="#EFEFEF" style="line-height:20px">
                                        <th width="1%" title="check_all"><input type="checkbox" value="1" id="PageAdmin_all_checkbox" onclick="select_all_checkbox(this.form, 'PageAdmin',this.checked,'#FFFFEC','white');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                                        <th>&nbsp;</th>
                                        <th nowrap align="left" >
                                            <a href="<?php echo URL::build_current(array('portal_id')+((URL::get('order_by')=='page.name' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'page.name'));?>" title="sort">
                                                <?php if(URL::get('order_by')=='page.name') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								name
                                            </a>
                                        </th>
                                        <th nowrap align="left">
                                            <a title="sort" href="<?php echo URL::build_current(array('portal_id')+((URL::get('order_by')=='page.title_'.Portal::language() and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'page.title_'.Portal::language()));?>" >
                                                <?php if(URL::get('order_by')=='page.title_'.Portal::language()) echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								title
                                            </a>
                                        </th>
                                        <th nowrap align="left">
                                            <a href="<?php echo URL::build_current(array('portal_id')+((URL::get('order_by')=='package_id' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'package_id'));?>" title="sort">
                                                <?php if(URL::get('order_by')=='package_id') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								package_id
                                            </a>
                                        </th>
                                        <th nowrap align="left">
                                            <a href="<?php echo URL::build_current(array('portal_id')+((URL::get('order_by')=='params' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'params'));?>" title="sort">
                                                <?php if(URL::get('order_by')=='params') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>								params
                                            </a>
                                        </th>
                                        <?php if(User::can_edit(false,ANY_CATEGORY))  {?>
                                            <th>&nbsp;</th>
                                            <th width="1%">&nbsp;</th>
                                        <?php }?>
                                    </tr>
                                    <!--LIST:items-->
                                    <tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],MAP['just_edited_ids'])))){ echo '#EFFFDF';} else {echo [[=items.is_sibling=]]?'#FFFFDF':'white';}?>" valign="middle" <?php Draw::hover('#E2F1DF');?> style="cursor:pointer;" id="PageAdmin_tr_[[|items.id|]]">
                                        <td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'PageAdmin',this,'#FFFFEC','white');" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
                                        <td><a class="btn btn-default btn-sm" target="_blank" href="<?php echo URL::build([[=items.name=]]);?>&[[|items.params|]]">Preview</a></td>
                                        <td nowrap align="left" onclick="location='[[|items.href|]]';">
                                            [[|items.name|]]
                                        </td>
                                        <td align="left" onclick="location='[[|items.href|]]';">
                                            [[|items.title|]]
                                        </td>
                                        <td nowrap align="left" onclick="location='[[|items.href|]]';">
                                            [[|items.package_id|]]
                                        </td>
                                        <td nowrap align="left" onclick="location='[[|items.href|]]';">
                                            [[|items.params|]]
                                        </td>
                                        <?php
                                        if(User::can_edit(false,ANY_CATEGORY))
                                        {
                                            ?>
                                            <td width="24px" align="center">
                                                <a class="btn btn-warning btn-sm" href="<?php echo Url::build_current(array('portal_id','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'','name'=>isset($_GET['name'])?$_GET['name']:'',)+array('cmd'=>'edit','id'=>[[=items.id=]])); ?>">SỬA</a>
                                            </td>
                                            <td>
                                                <a class="btn btn-default btn-sm" href="<?php echo Url::build_current(array('portal_id','package_id', 'name')+array('cmd'=>'duplicate','id'=>[[=items.id=]])); ?>">NHÂN BẢN</a>
                                            </td>
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
                        </td>
                    </tr>
                </table>
                <div class="pt">[[|paging|]]</div>
            </td>
        </tr>
    </table>
</div>