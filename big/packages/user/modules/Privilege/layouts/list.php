<script>
	function check_selected()
	{
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked)
			{
				status = true;
			}
		});
		return status;
	}
	function make_cmd(cmd)
	{
		jQuery('#cmd').val(cmd);
		document.ListPrivilegeForm.submit();
	}
</script>
<div class="container">
    <br>
    <div class="box box-info">
        <div class="box-header">
            <div class="box-title">
                [[.privilege.]] <span style="font-size:16px;color:#0B55C4;">[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
            </div>
            <div class="box-tools pull-right">
                <table align="right">
                    <tbody>
                    <tr>
                        <?php if(User::can_admin()){?><td id="toolbar-config"  align="center"><a href="<?php echo URL::build_current(array('cmd'=>'make_cache'));?>"> <span title="[[.Cache.]]"> </span> [[.Cache.]] </a> </td><?php }?>
                        <?php if(User::can_add()){?><td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>#"> <span title="New"> </span> [[.New.]] </a> </td><?php }?>
                        <?php if(User::can_delete()){?><td id="toolbar-trash"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <span title="Trash"> </span> [[.Trash.]] </a> </td><?php }?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="box-body">
            <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
            <form name="ListPrivilegeForm" method="post" action="?<?php echo htmlentities($_SERVER['QUERY_STRING']);?>">
                <table class="table">
                    <tr bgcolor="#EFEFEF" valign="top">
                        <th width="1%" title="[[.check_all.]]"><input type="checkbox" value="1" id="Privilege_all_checkbox" onclick="select_all_checkbox(this.form, 'Privilege',this.checked,'#FFFFEC','white');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                        <th nowrap align="left" >
                            <a href="<?php echo URL::build_current(((URL::get('order_by')=='privilege.title_1' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'privilege.title_1'));?>" title="[[.sort.]]">
                                <?php if(URL::get('order_by')=='privilege.title_1') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>
                                [[.title.]]</a>				</th>
                        <th nowrap align="left" ><a>[[.function_name.]]</a></th>
                        <th nowrap align="left">
                            <a href="<?php echo URL::build_current(((URL::get('order_by')=='package_id' and URL::get('order_dir')!='desc')?array('order_dir'=>'desc'):array())+array('order_by'=>'package_id'));?>" title="[[.sort.]]">
                                <?php if(URL::get('order_by')=='package_id') echo '<img src="'.Portal::template('core').'/images/buttons/'.((URL::get('order_dir')!='desc')?'down':'up').'_arrow.gif" alt="">';?>
                                [[.package_id.]]					</a>				</th>
                        <?php if(User::can_edit())
                        {
                            ?><th nowrap="nowrap" width="1%"><a>[[.Edit.]]</a></th>
                            <th nowrap="nowrap" width="1%"><a>[[.grant.]]</a></th><?php
                        }?>
                    </tr>
                    <?php $i=-1;?>
                    <!--LIST:items-->
                    <?php
			$i++;
			$action = User::can_edit()?' onclick="location=\''.URL::build_current().'&cmd=edit&id='.[[=items.id=]].'\';"':'';?>
                    <tr <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="Privilege_tr_[[|items.id|]]">
                        <td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'Privilege',this,'#FFFFEC','white');" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
                        <td align="left" nowrap <?php echo $action;?>>
                            [[|items.title|]] / [[|items.code|]]</td>
                        <td align="left" nowrap <?php echo $action;?>>[[|items.function_name|]]</td>
                        <td nowrap align="left" <?php echo $action;?>>
                            [[|items.package_id|]]				</td>
                        <?php
                        if(User::can_edit())
                        {
                            ?>
                            <td align="center">
                                <a class="btn btn-warning btn-sm" href="<?php echo Url::build_current(array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'',
)+array('cmd'=>'edit','id'=>[[=items.id=]])); ?>">Sửa</a></td>
                            <td align="center">
                                <a class="btn btn-success btn-sm" href="<?php echo Url::build_current(array('package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'')+array('cmd'=>'grant','id'=>[[=items.id=]])); ?>">Chỉnh module</a></td>
                            <?php
                        }
                        ?>
                    </tr>
                    <!--/LIST:items-->
                </table>
                <table class="table">
                    <tr>
                        <td>
                            [[.select.]]:&nbsp;
                            <a href="javascript:void(0)" onclick="select_all_checkbox(document.ListPrivilegeForm,'Privilege',true,'#FFFFEC','white');">[[.select_all.]]</a>&nbsp;
                            <a href="javascript:void(0)" onclick="select_all_checkbox(document.ListPrivilegeForm,'Privilege',false,'#FFFFEC','white');">[[.select_none.]]</a>
                            <a href="javascript:void(0)" onclick="select_all_checkbox(document.ListPrivilegeForm,'Privilege',-1,'#FFFFEC','white');">[[.select_invert.]]</a>
                        </td>
                        <td align="right">[[|paging|]]</td>
                    </tr>
                </table>
                <input type="hidden" name="cmd" value="delete" id="cmd"/>
                <input type="hidden" name="page_no" value="1"/>
                <!--IF:delete(URL::get('cmd')=='delete')-->
                <input type="hidden" name="confirm" value="1" />
                <!--/IF:delete-->
                </td>
                </tr>
                </table>
            </form>
        </div>
    </div>
</div>