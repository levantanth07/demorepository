<script>
	function check_selected()
	{
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked && this.id=='ListAccountLogForm_checkbox')
			{
				status = true;
			}
		});
		return status;
	}
	function make_cmd(cmd)
	{
		jQuery('#cmd').val(cmd);
		document.ListAccountLogForm.submit();
	}
</script>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-8">
                    <div class="title"><strong><i class="fa fa-sign-in"></i> Lịch sử sửa đổi tài khoản ([[|total|]])</strong></div>
                </div>
                <div class="col-md-4 text-right">
                    <!--IF:cond(User::is_admin())-->
                    <td id="toolbar-cancel"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}" class="btn btn-danger"> Delete </a> </td>
                    <!--/IF:cond-->
                </div>
            </div>
        </div>
        <div class="panel-body">
            <form name="ListAccountLogForm" method="post">
                <div class="panel">
                    <div class="row">
                        <div class="col-xs-1">Từ khoá</div>
                        <div class="col-xs-3">
                            <input name="keyword" type="text" id="keyword" class="form-control">
                        </div>
                        <div class="col-xs-3">
                            <input name="search" type="submit" id="search" class="btn btn-default" value="Tìm kiếm">
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped">
                    <tr>
                        <!--IF:cond(User::is_admin())-->
                        <th width="3%"><input type="checkbox" value="1" id="ListAccountLogForm_all_checkbox" onclick="select_all_checkbox(this.form,'ListAccountLogForm',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                        <!--/IF:cond-->
                        <th width="40%" align="left"><a>Chi tiết</a></th>
                        <th width="14%" align="left"><a>IP</a></th>
                        <!--IF:cond(User::is_admin())-->
                        <th width="7%" align="left"><a>SHOP</a></th>
                        <!--/IF:cond-->
                        <th width="10%" align="left"><a>Tài khoản</a></th>
                        <th width="7%" align="left"><a>Thời gian</a></th>
                    </tr>
                    <?php $i=0;?>
                    <!--LIST:items-->
                    <tr valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="Category_tr_[[|items.id|]]">
                        <!--IF:cond(User::is_admin())-->
                        <td><?php $i++;?>
                            <input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'ListAccountLog',this,'#FFFFEC','white');" id="ListAccountLogForm_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
                        <!--/IF:cond-->
                        <td><div style="overflow: auto;width: 400px;">[[|items.content|]]</div></td>
                        <td>[[|items.ip|]]</td>
                        <!--IF:cond(User::is_admin())-->
                        <td>[[|items.group_name|]]</td>
                        <!--/IF:cond-->
                        <td>[[|items.account_id|]]</td>
                        <td nowrap="nowrap"><?php echo date('H:i d/m/Y',[[=items.time=]]);?></td>
                    </tr>
                    <!--/LIST:items-->
                </table>
                <div class="pt">
                    [[|paging|]]
                </div>
                <input type="hidden" name="cmd" value="" id="cmd"/>
            </form>
        </div>
    </div>
</div>
