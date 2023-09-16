<script>
	function check_selected()
	{
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked && this.id=='ListLoginHistoryForm_checkbox')
			{
				status = true;
			}
		});
		return status;
	}
	function make_cmd(cmd)
	{
		jQuery('#cmd').val(cmd);
		document.ListLoginHistoryForm.submit();
	}
</script>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-8">
                    <div class="title"><strong><i class="fa fa-sign-in"></i> Quản lý lịch sử đăng nhập ([[|total|]])</strong></div>
                </div>
                <div class="col-md-4 text-right">
                    <!--IF:cond(User::is_admin())-->
                    <td id="toolbar-cancel"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}" class="btn btn-danger"> Delete </a> </td>
                    <!--/IF:cond-->
                </div>
            </div>
        </div>
        <div class="panel-body">
            <form name="ListLoginHistoryForm" method="post">
                <div class="panel">
                    <div class="row">
                        <div class="col-xs-3 form-group">
                            <label>Nhập tên tài khoản</label>
                            <input name="account_id" type="text" id="account_id" class="form-control">
                        </div>
                        <div class="col-xs-3 form-group">
                            <label>Nhập địa chỉ IP</label>
                            <input name="ip" type="text" id="ip" class="form-control">
                        </div>
                        <div class="col-xs-3 form-group">
                            <label>Nhập Client ID</label>
                            <input name="client_id" type="text" id="client_id" class="form-control">
                        </div>
                        <!--IF:cond(User::is_admin())-->
                        <div class="col-xs-3 form-group">
                            <label>Nhập tên Shop</label>
                            <input name="group_id" type="text" id="group_id" class="form-control">
                        </div>
                        <!--/IF:cond-->
                        <div class="col-xs-3 form-group" style="margin-top: 25px;">
                            <input name="search" type="submit" id="search" class="btn btn-default" value="Tìm kiếm">
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped">
                    <tr>
                        <!--IF:cond(User::is_admin())-->
                        <th width="3%"><input type="checkbox" value="1" id="ListLoginHistoryForm_all_checkbox" onclick="select_all_checkbox(this.form,'ListLoginHistoryForm',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                        <!--/IF:cond-->
                        <th width="40%" align="left"><a>Chi tiết</a></th>
                        <th width="14%" align="left"><a>IP</a></th>
<!--                        <th width="10%" align="left"><a>Client ID</a></th>-->

                        <th width="10%" align="left"><a>Client ID</a></th>
                        <!--IF:cond(User::is_admin())-->
                        <th width="7%" align="left"><a>SHOP</a></th>
                        <!--/IF:cond-->
                        <th width="10%" align="left"><a>Tài khoản</a></th>
                        <th width="10%" align="left"><a>Ảnh</a></th>
                        <th width="7%" align="left"><a>Thời gian</a></th>
                    </tr>
                    <?php $i=0;?>
                    <!--LIST:items-->
                    <tr valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="Category_tr_[[|items.id|]]">
                        <!--IF:cond(User::is_admin())-->
                        <td><?php $i++;?>
                            <input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'ListLoginHistory',this,'#FFFFEC','white');" id="ListLoginHistoryForm_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
                        <!--/IF:cond-->
                        <td><div style="overflow: auto;width: 400px;">[[|items.content|]]</div></td>
                        <td>[[|items.ip|]]</td>
                        <td><?php if(!empty([[=items.client_id=]])) :?>[[|items.client_id|]]<?php endif; ?></td>
                        <!--IF:cond(User::is_admin())-->
                        <td>[[|items.group_name|]]</td>
                        <!--/IF:cond-->
                        <td>[[|items.account_id|]]</td>
                        <td>
                            <?php if(!empty([[=items.image_url=]])) :?>
                                <?php
                                    $imageUrl = explode(',',[[=items.image_url=]]);
                                ?>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal-<?php echo [[=items._id=]];?>">Xem ảnh</button>
                            <div class="modal fade" id="myModal-<?php echo [[=items._id=]];?>">
                                <div class="modal-dialog modal-lg">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h4 class="modal-title">Ảnh nhân viên</h4>
                                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    
                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <div class="row" style="margin-right: 0px;">
                                            <?php foreach($imageUrl as $value): ?>
                                            <div class="col-md-4">
                                                <img src="<?php echo $value; ?>" alt="">
                                                <p><?php echo substr($value, -13); ?></p>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </td>
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
