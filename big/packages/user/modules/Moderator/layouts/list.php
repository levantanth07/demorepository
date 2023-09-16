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
		document.ListModeratorForm.submit();
	}
</script>
<div class="container">
    <fieldset id="toolbar">
        <div id="toolbar-info">
            grant_privilege <span style="font-size:16px;color:#0B55C4;">[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
        </div>
        <div id="toolbar-content">
            <table align="right">
                <tbody>
                <tr>
                    <?php if(User::can_add(false,ANY_CATEGORY)){?><td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'grant'));?>#"> <span title="New"> </span> New </a> </td><?php }?>
                    <?php if(User::can_delete(false,ANY_CATEGORY)){?><td id="toolbar-trash"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <span title="Trash"> </span> delete </a> </td><?php }?>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <div style="height:8px;"></div>
    <fieldset id="toolbar">
        <a name="top_anchor"></a>
        <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
        <form name="ListModeratorForm" method="post" action="?<?php echo htmlentities($_SERVER['QUERY_STRING']);?>">
            <?php if(User::can_view(false,ANY_CATEGORY)){?>
                user <input name="user_id" type="text" id="user_id" size="30"/>&nbsp;<input type="submit" value="go" />
            <?php }?>
            <table class="table">
                <tr bgcolor="#EFEFEF" valign="top">
                    <th width="2%" title="check_all">
                        <input type="checkbox" value="1" id="Moderator_all_checkbox" onclick="select_all_checkbox(this.form,'Moderator',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>>
                    </th>
                    <th width="29%" title="check_all" align="left"><a>Tài khoản</a></th>
                    <th width="28%" align="left" nowrap><a>Danh mục</a></th>
                    <th width="23%" align="left" nowrap><a>Phân quyền</a></th>
                    <!--IF:cond1(User::can_admin(false,ANY_CATEGORY))--><th width="2%" title="check_all"><a>Edit</a></th><!--/IF:cond1-->
                </tr>
                <?php $i = 0;?>
                <!--LIST:items-->
                <?php $i ++;?>
                <tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],[[=just_edited_ids=]])))){ echo '#F7F7F7';} else {echo 'white';}?>" valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="Moderator_tr_[[|items.id|]]">
                    <td width="2%"><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'Moderator',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" id="Moderator_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
                    <td width="29%">[[|items.account_id|]]</td>
                    <td width="28%" align="left"  nowrap>[[|items.category_name|]]</td>
                    <td width="23%" align="left"  nowrap>[[|items.title|]]</td>
                    <!--IF:cond1(User::can_admin(false,ANY_CATEGORY))-->
                    <td width="2%"><a class="btn btn-warning btn-sm" href="<?php echo Url::build_current(array('cmd'=>'grant','id'=>[[=items.id=]]));?>">Sửa</a></td>
                    <!--/IF:cond1-->
                </tr>
                <!--/LIST:items-->
            </table>
            <div class="pt">[[|paging|]]</div>
            <input type="hidden" name="cmd" value="" id="cmd"/>
        </form>
        <div style="#height:8px"></div>
    </fieldset>
</div>