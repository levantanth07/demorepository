<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<script>
	function check_selected() {
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked){
				status = true;
			}
		});
		return status;
	}
	function make_cmd(cmd){
		jQuery('#cmd').val(cmd);
		document.ListUserAdminForm.submit();
	}
</script>
<fieldset id="toolbar">
	<div id="toolbar-personal">
		<?php echo (Url::get('cmd')=='delete')?'Xác nhận xóa shop':'Quản lý shop';?>
	</div>
	<div id="toolbar-content">
	<!--IF:cond6(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
	<table align="right">
	  <tbody>
		<tr>
			<td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>#"> <span title="New"> </span> Thêm</a> </td>
		  <?php if(Url::get('cmd')!='delete')
		  {?>
		  	<td id="toolbar-trash"  align="center"><a onclick="if(confirm('Bạn có chắc muốn xóa?')){if(check_selected()){make_cmd('delete_shop')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}"> <span title="Trash"> </span> Xoá </a> </td>
			<?php }else{?>
				<td id="toolbar-trash"  align="center"><a onclick="if(check_selected()){make_cmd('delete_shop')}"> <span title="Trash"> </span> Xóa </a> </td>
			<?php }?>
		</tr>
	  </tbody>
	</table>
	<!--/IF:cond6-->
	</div>
</fieldset>
<br>
<div class="panel panel-default">
	<form name="SearchUserAdminForm" method="post">
		<table class="table">
			<tr>
				<td width="40%">
					<input name="user_id" type="text" id="user_id" class="form-control" placeholder="Nhập tên tìm kiếm">
				</td>
				<!--IF:cond6(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
				<td width="20%">
					<select name="account_type" id="account_type" class="form-control"></select>
				</td>
				<td width="20%">
					<select name="system_group_id" id="system_group_id" class="form-control select2"></select>
				</td>
				<td width="20%">
					<select name="expired_month" id="expired_month" class="form-control" onchange="SearchUserAdminForm.submit();"></select>
				</td>
				<!--/IF:cond6-->
				<td><input type="submit" value="Tìm kiếm" class="btn btn-warning"></td>
				<td width="20%" class="text-left">
				</td>
			</tr>
		</table>
		<input type="hidden" name="page_no" value="1" />
	</form>
	<form name="ListUserAdminForm" method="post">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Tổng số shop: [[|total|]]</h3>
                <div class="box-tools pull-right">
                    [[|good_shop|]] shop > 500 đơn,
                    [[|actived_shop|]] shop hoạt động, [[|expired_shop|]] shop hết hạn
                    <span class="label label-primary">Thống kê</span>
                </div>
                <!-- /.box-tools -->
            </div>
        </div>
        <table class="table table-bordered table-striped">
            <tr valign="middle" bgcolor="#F2F2CB">
                <th width="1%" title="[[.check_all.]]"><input type="checkbox" value="1" id="UserAdmin_all_checkbox" onclick="jQuery('.selected-ids').attr('checked',this.checked)"<?php if(URL::get('cmd')=='delete_shop') echo ' checked';?>></th>
                <th align="left" width="30%">Tên Shop</th>
                <th align="left" width="20%">Quản lý Shop</th>
                <th align="left" >Thông tin liên hệ</th>
                <th align="left" >Loại</th>
                <th nowrap align="left" >Kích hoạt</th>
                <th nowrap align="left" ><a href="<?php echo Url::build_current(array('cmd','order_by'=>'user_counter','order_dir'=>((Url::get('order_dir')=='DESC')?'ASC':'DESC')));?>">Max user</a></th>
                <th align="left" >Ngày tạo</th>
                <th align="left" ><a href="<?php echo Url::build_current(array('cmd','order_by'=>'expired_date','order_dir'=>((Url::get('order_dir')=='DESC')?'ASC':'DESC')));?>">Ngày hết hạn</a></th>
            </tr>
            <?php $i = 1;?>
            <!--LIST:items-->
            <tr valign="middle" <?php Draw::hover('#F0ECC8');?> style="<?php if($i%2){echo 'background-color:#FFF';}?>" id="UserAdmin_tr_[[|items.id|]]">
                <td>
                    <input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" class="selected-ids" onclick="" id="UserAdmin_checkbox" <?php if(URL::get('cmd')=='delete_shop') echo 'checked';?>>
                    <?php echo $i;?></td>
                <td align="left">
                    <a href="index062019.php?page=admin_group_info&group_id=[[|items.id|]]">
                        <strong>[[|items.name|]]</strong>
                    </a>
                    <div class="small">
                        Sở hữu bởi tài khoản: <strong>[[|items.code|]]</strong>
                    </div>
                    <div class="small">
                        (<!--IF:cond([[=items.master_group_name=]])-->Hệ thống: [[|items.master_group_name|]] | <!--/IF:cond-->[[|items.system_group_name|]], Group ID: [[|items.id|]], Số TK: [[|items.total_user|]])
                        <!--IF:cond([[=items.description=]])--><br>Ghi chú: [[|items.description|]]<!--/IF:cond-->
                        <!--IF:cond([[=items.phone_store_name=]])--><br>Kho số: <strong>[[|items.phone_store_name|]]</strong><!--/IF:cond-->
                    </div>
                </td>
                <td align="left">[[|items.admins|]]</td>
                <td align="left">
                    <span class="small">Email: [[|items.email|]]</span><br>
                </td>
                <td align="left">[[|items.account_type|]]</td>
                <td align="left">[[|items.active|]]</td>
                <td align="left">[[|items.user_counter|]]</td>
                <td align="left">[[|items.created|]]</td>
                <td align="left"><?php echo ([[=items.expired_date=]]!='0000-00-00 00:00:00' and [[=items.expired_date=]])?date('d/m/y',strtotime([[=items.expired_date=]])).((strtotime([[=items.expired_date=]])<=time())?'<span style="color:#f00"> - Hết hạn</span>':''):'Không thời hạn';?></td>
            </tr>
            <?php $i++;?>
            <!--/LIST:items-->
        </table>


		<table  width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;#width:99%" align="center">
			<tr>
				<td>[[|paging|]]</td>
			</tr>
		</table>
		<input type="hidden" name="cmd" value="delete_shop"/>
		<input type="hidden" name="page_no" value="1"/>
		<!--IF:delete(URL::get('cmd')=='delete_shop')-->
		<input type="hidden" name="confirm" value="1" />
		<!--/IF:delete-->
</form>
</div>
<script>
	$(document).ready(function(){
		$('.select2').select2({
            dropdownAutoWidth : true
        });
	})
</script>