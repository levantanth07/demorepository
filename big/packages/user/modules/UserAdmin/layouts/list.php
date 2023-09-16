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
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<div class="container full">
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">
                <i class="fa fa-user"></i> <?php echo (Url::get('cmd')=='delete')?'Xác nhận xóa tài khoản người dùng':'Quản lý tài khoản người dùng';?>
            </h3>
            <div class="box-tools pull-right">
                <!--IF:cond(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
                <a href="index062019.php?page=user_admin&cmd=shop" class="btn btn-default"><i class="fa fa-laptop" aria-hidden="true"></i> Quản lý Shop</a>
                <!--/IF:cond-->
                <a href="<?php echo Url::build_current(array('cmd'=>'add'));?>#" class="btn btn-warning"> + Thêm mới </a>
            </div>
        </div>
        <div class="box-body">
            <form name="SearchUserAdminForm" method="post">
                <table class="table">
                    <tr>
                        <td width="20%">
                            <input name="keyword" type="text" id="keyword" class="form-control" placeholder="Nhập tên tìm kiếm">
                        </td>
                        <td width="20%">
                            <select name="account_group_id" id="account_group_id" class="form-control"></select>
                        </td>

                        <td>
                            <select name="vaccination_count" id="vaccination_count" class="form-control d-none"></select>
                            <select name="vaccination_status" id="vaccination_status" class="form-control d-none"></select>
                        </td>
                        <!--IF:cond6(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
                        <td width="20%">
                            <select name="system_group_id" id="system_group_id" class="form-control select2"></select>
                        </td>
                        <td width="20%">
                            <select name="expired_month" id="expired_month" class="form-control" onchange="SearchUserAdminForm.submit();"></select>
                        </td>
                        <!--/IF:cond6-->
                        <td>
                            <button type="submit" class="btn btn-default">
                                <i class="fa fa-search"></i> Tìm kiếm
                            </button>

                        </td>
                        
                    </tr>
                    <tr>
                        <td width="20%" class="text-left">
                            Tổng: [[|total|]] tài khoản
                            <!--IF:cond6(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
                            / [[|total_group|]] shop
                            <!--/IF:cond6-->
                            <br>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="page_no" value="1" />
                <input type="hidden" name="item_per_page" value="[[|item_per_page|]]" />
            </form>
            <a name="top"></a>
            <form name="ListUserAdminForm" method="post">
                <div class="row">
                    <div class="col-xs-12 col-lg-4 mb-10">
                <ul class="nav nav-tabs" role="tablist" >
                    <li <?php echo Url::get('_not_is_active')?'':' class="active"';?>><a href="<?=Url::build_current(['keyword'])?>">Tài khoản kích hoạt</a></li>
                    <li <?php echo Url::get('_not_is_active')?'class="active"':'';?>><a href="<?=Url::build_current(['_not_is_active'=>1,'keyword'])?>">Tài khoản chưa kích hoạt</a></li>
                </ul>
                </div>
                    <div class="col-xs-12 col-lg-8 mb-10">
                
                <div id="adminFunction" class="row" >
                    <div class="col-xs-3" id="functionActiveAccount">
                        <button disabled  class="dropdown-toggle btn btn-default form-control" data-toggle="dropdown" role="button" aria-haspopup="true">Kích hoạt tài khoản <span class="caret"></span></button>
                          <ul class="multiselect-container dropdown-menu" 

                          style="padding:10px; left: 15px; width: calc(100% - 30px);box-shadow: 0px 6px 12px 3px #aeaeae;">
                            <li  style="border-bottom: 1px solid #ccc">
                               <input type="radio"name="active-action" value="active"
                               <?php echo Url::get('_not_is_active')== 1? 'checked':""; ?> > Kích hoạt
                            </li>
                            <li>
                                <input type="radio" name="active-action" value="deactive"
                                <?php echo !Url::get('_not_is_active')== 1? 'checked':""; ?>> Khoá tài khoản
                            </li>
                            <li class="text-center">
                                <button type="button" id="btnSaveSetStatus" class="btn btn-success btn-sm btn-block"><i class="fa fa-floppy-o"></i> Lưu</button>
                            </li>
                          </ul>
                    </div>
                    
                    <div class="col-xs-3"  id="functionSetGroup">
                        <button disabled  class="dropdown-toggle btn btn-default form-control" data-toggle="dropdown" role="button" aria-haspopup="true">Gán nhóm <span class="caret"></span></button>
                        <ul class="multiselect-container dropdown-menu" 
                            style="padding-left: 10px; left: 15px;box-shadow: 0px 6px 12px 3px #aeaeae; width: calc(100% - 30px)">
                            <li  style="border-bottom: 1px solid #ccc; max-height: 300px; overflow: auto;">
                            <div style="border-bottom: 1px solid #ccc">
                                <input type="radio" name="select-group" value="0"> Huỷ gán nhóm
                            </div>
                            <?php  foreach ($this->map['account_groups'] as $key => $value) { ?>
                            <div style="border-bottom: 1px solid #ccc" class="shop-id shop-id-<?= $value['group_id'] ?> ">
                                <input type="radio" name="select-group" value="<?= $value['id'] ?>"> <?= $value['name'] ?>
                            </div>
                            <?php } ?>
                            </li>
                            <li class="text-center mr-10 mb-10 mt-10">
                                <button type="button" id="btnSaveSetGroup" class="btn btn-success btn-sm btn-block">
                                    <i class="fa fa-floppy-o"></i> Lưu</button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-xs-3"  id="functionSetGroupLeader">
                        <button disabled class="dropdown-toggle btn btn-default form-control" data-toggle="dropdown" role="button" aria-haspopup="true">Gán trưởng nhóm <span class="caret"></span></button>
                        <ul class="multiselect-container dropdown-menu" 
                            style="padding-left: 10px; left: 15px;box-shadow: 0px 6px 12px 3px #aeaeae; width: calc(100% - 30px)">
                            <li style="border-bottom: 1px solid #ccc; max-height: 300px; overflow: auto;">
                                <div style="border-bottom: 1px solid #ccc">
                                    <input type="checkbox" name="select-group" id="cancelSelectLeader" value="0"> Huỷ gán
                                </div>
                                <?php  foreach ($this->map['account_groups'] as $key => $value) { ?>
                                <div style="border-bottom: 1px solid #ccc" class="shop-id shop-id-<?= $value['group_id'] ?> ">
                                    <input type="checkbox" name="select-group" value="<?= $value['id'] ?>"> <?= $value['name'] ?>
                                </div>
                                <?php } ?>
                            </li>
                            <li class="text-center mr-10 mb-10 mt-10">
                                <button type="button" id="btnSaveSetGroupLeader" class="btn btn-success btn-sm btn-block">
                                    <i class="fa fa-floppy-o"></i> Lưu</button>
                            </li>
                        </ul>
                    </div>
                    <?php if(User::can_admin(false,ANY_CATEGORY)){ ?>
                    <div class="col-xs-3" id="functionSetLeader">
                        <button disabled  type="button" id="btnSetLeader" class="dropdown-toggle btn btn-default form-control">Gán trưởng phòng <span class="caret"></span></button>
                        <ul class="dropdown-menu" style="padding-left: 10px; width: calc(100% - 30px);  left: unset;right: 15px;box-shadow: 0px 6px 12px 3px #aeaeae;">
                            <li style="padding-bottom: 10px;max-height:300px; overflow: auto;">
                                <div class="row m-0" style="border-bottom: 1px solid #ccc">
                                    <div class="col-xs-12 p-0">
                                        <input type="checkbox" id="cancelSelectManager" value="0"/> 
                                        Huỷ gán
                                    </div>
                                </div>
                                <?php  foreach ($this->map['account_groups'] as $key => $value) { ?>
                                <div class="row m-0 shop-id shop-id-<?= $value['group_id'] ?> "  style="border-bottom: 1px solid #ccc">
                                    <div class="col-xs-12 p-0">
                                        <input type="checkbox" value="<?= $value['id'] ?>"/> <?= $value['name'] ?>
                                    </div>
                                </div>
                                 <?php } ?>
                                 

                             </li>
                             <li class="text-center mr-10 mb-10 mt-10">
                                <button type="button" id="btnSaveSetLeader" class="btn btn-success btn-sm btn-block"><i class="fa fa-floppy-o"></i> Lưu</button>
                            </li>
                        </ul>
                    </div>
                    <?php } ?>
                </div>
            </div>
            </div>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr valign="middle" bgcolor="#DDD" >
                        <th width="1%" title="Chọn tất cả"><input type="checkbox" value="1" id="UserAdmin_all_checkbox" onclick="jQuery('.selected-ids').attr('checked',this.checked)"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                        <th align="left">Tên tài khoản</th>
                        <th align="left" >Thông tin tài khoản</th>
                        <th align="left" ><a href="<?php echo Url::build_current(array('order_by'=>'expired_date','order_dir'=>((Url::get('order_dir')=='DESC')?'ASC':'DESC')));?>">Thời hạn</a></th>
                        <th align="left" >Người tạo</th>
                        <th align="left" >Quyền</th>
                        <!--IF:cond([[=integrate_callio=]])-->
                        <th align="left">Tổng đài Callio</th>
                        <!--/IF:cond-->
                        <!--IF:cond([[=integrate_voip24h=]])-->
                        <th align="left">Tổng đài Voip24h</th>
                        <!--/IF:cond-->
                        <th align="left">QL SHOP</th>
                    </tr>
                    </thead>
                    <?php $i = 1;?>
                    <tbody>
                    <!--LIST:items-->
                    <?php $owner = ([[=items.code=]]==[[=items.id=]])?true:false;?>
                    <tr bgcolor="<?php echo [[=items.label=]];?>" valign="middle" id="UserAdmin_tr_[[|items.id|]]" data-groupId="[[|items.group_id|]]" data-userId="[[|items.id|]]">
                        <td>
                            <!--IF:cond(![[=items.admin_group=]] and [[=items.total_order=]]<=0 and [[=items.id=]] !=Session::get('user_id'))-->
                            <input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" class="selected-ids" onclick="" id="UserAdmin_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?> data-groupId="[[|items.group_id|]]">
                            <!--/IF:cond-->
                            <?php echo $i;?>.
                        </td>
                        <td align="left">
                            <strong>[[|items.is_online|]]
                                <?php if(!$owner or Session::get('user_id')==[[=items.code=]]){?>
                                <a href="<?php echo URL::build_current();?>&cmd=edit&id=[[|items.id|]]">[[|items.id|]]</a>
                                <?php } else {?>
                                [[|items.id|]]
                                <?php } ?>
                            </strong>
                            [[|items.rated_point|]]
                            <div class="small">(Group ID: <a href="<?= Url::build('admin_group_info',['group_id'=>[[=items.group_id=]]]);?>">[[|items.group_id|]]</a>/ [[|items.group_name|]] | Nhóm: [[|items.master_group|]])</div>
                            <!--IF:cond([[=items.page_name=]])-->
                            <div class="small" style="color:#00A8FF;">Page: [[|items.page_name|]]</div>
                            <!--/IF:cond-->
                            <!--IF:cond([[=items.account_group_department=]])-->
                            <div class="small" style="color:red;">Trưởng phòng: [[|items.account_group_department|]]</div>
                            <!--/IF:cond-->
                            <!--IF:cond([[=items.account_group=]])-->
                            <div class="small" style="color:#EA6D1B;">Quản lý Nhóm: [[|items.account_group|]]</div>
                            <!--/IF:cond-->
                            <!--IF:cond([[=items.account_group_name=]])-->
                            <div class="small" style="color:#ff7096;">Nhóm tài khoản: [[|items.account_group_name|]]</div>
                            <!--/IF:cond-->
                            [[|items.last_online_time|]]

                            <!--IF:cond([[=items.extension=]])-->
                            <div class="small" style="color:#35a178;">Đầu số tổng đài: [[|items.extension|]]</div>
                            <!--/IF:cond-->
                        </td>
                        <td align="left">
                            <span class="text-bold">[[|items.full_name|]]</span><br>
                            <span class="small">Email: [[|items.email|]]</span><br>
                            <span class="small">Phone: [[|items.phone_number|]]</span><br>
                            <span class="small">CMTND/Căn cước: [[|items.identity_card|]]</span><br>
                            <span class="small">Tỉnh/thành: [[|items.zone_name|]]</span>
                        </td>
                        <td align="left">
                            <div><?=[[=items.active=]]?'<span class="label label-success">Đã kích hoạt</span><br>':'<span class="label label-default">Chưa kích hoạt</span><br>'?></div>
                            <div class="small" style="margin-top: 5px;">Từ <?php echo date('d/m/Y',strtotime([[=items.create_date=]])); ?> </div>
                            <div class="small text-bold"><?php echo ([[=items.expired_date=]]!='0000-00-00 00:00:00' and [[=items.expired_date=]])?' đến '.date('d/m/Y',strtotime([[=items.expired_date=]])):'Không thời hạn';?></div>
                        </td>
                        <td align="left">
                            <div class="small">[[|items.user_created|]]<br> lúc <?=date('H:i\' d/m/Y',strtotime([[=items.created=]]))?></div>
                        </td>
                        <td align="left" width="20%">
                            <?php echo ($owner)?'<div class="label label-danger">Sở hữu</div>':''?>
                            [[|items.roles|]]
                        </td>
                        <!--IF:cond([[=integrate_callio=]])-->
                        <th align="center">
                            <!--IF:cond([[=items.integrate_callio=]])-->
                            <span class="label label-success">Được hoạt động</span>
                            
                            <div data-cmd="deactivate_callio" data-users_id="[[|items.users_id|]]" class="btn label d-inline-block label-danger callio-btn">Tắt kích hoạt</div>
                            <p style="margin-top: 10px">
                                <span class="small" style="margin-top: 5px">Email: [[|items.callio_info_email|]]</span><br>
                                <span class="small">Số nhánh: [[|items.callio_info_ext|]]</span>
                            </p>
                            <!--ELSE-->
                            <!--IF:cond1([[=items.callio_info=]])-->
                            <span class="label label-danger">Dừng hoạt động</span>
                            
                            <div data-cmd="activate_callio" data-users_id="[[|items.users_id|]]" class="btn label d-inline-block label-info callio-btn">Kích hoạt lại</div>
                            <!--ELSE-->
                            <div data-cmd="integrate_callio" data-users_id="[[|items.users_id|]]" data-total_user="[[|totalCallioUser|]]" class="btn label d-inline-block label-default callio-btn">Bấm để kích hoạt</div>
                            <!--/IF:cond1-->
                            <!--/IF:cond-->
                        </th>
                        <!--/IF:cond-->
                        <!--IF:cond2([[=integrate_voip24h=]])-->
                        <th align="center">
                            <!--IF:cond3([[=items.integrate_voip24h=]])-->
                            <a class="integrate-voip24h" data-id="[[|items.users_id|]]" data-line="[[|items.voip24h_info_line|]]" data-password="[[|items.voip24h_info_password|]]" data-enable="1" style="cursor: pointer">
                                <span class="label label-success">Được hoạt động</span>
                            </a>
                            <p style="margin-top: 10px">
                                <span class="small">Số nhánh: [[|items.voip24h_info_line|]]</span><br>
                                <span class="small">Password: ******</span>
                            </p>
                            <!--ELSE-->
                            <!--IF:cond1([[=items.voip24h_info=]])-->
                            <a class="integrate-voip24h" data-id="[[|items.users_id|]]" data-line="[[|items.voip24h_info_line|]]" data-password="[[|items.voip24h_info_password|]]" data-enable="0" style="cursor: pointer">
                                <span class="label label-danger">Dừng hoạt động</span>
                            </a>
                            <!--ELSE-->
                            <a class="integrate-voip24h" data-id="[[|items.users_id|]]" style="cursor: pointer">
                                <span class="label label-default">Bấm để kích hoạt</span>
                            </a>
                            <!--/IF:cond1-->
                            <!--/IF:cond3-->
                        </th>
                        <!--/IF:cond2-->
                        <td align="center">
                            <?php echo [[=items.admin_group=]]?'[<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>]<br>':'';?>
                            <?php if(!$owner or Session::get('user_id')==[[=items.code=]] or User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY)){?>
                            <a href="<?php echo URL::build_current();?>&cmd=edit&id=[[|items.id|]]" class="btn btn-warning btn-sm">Sửa</a>
                            <?php }?>
                        </td>
                    </tr>
                    <?php $i++;?>
                    <!--/LIST:items-->
                    </tbody>
                </table>
                <table  width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;#width:99%" align="center">
                    <tr>
                        <td>[[|paging|]]</td>
                        <td class="text-right" width="20%" style="padding-right: 10px">Số lượng hiển thị: <br>(Tối đa 200)</td>
                        <td width="10%" ><input title="Số lượng hiển thị" type="number" class="form-control" name="item_per_page" value="[[|item_per_page|]]" id="itemPerPage" min="10" max="100"></td>
                    </tr>
                </table>
                <input type="hidden" name="cmd" value="delete"/>
                <input type="hidden" name="page_no" value="1"/>
                <input type="hidden" name="item_per_page" value="[[|item_per_page|]]" />
                <!--IF:delete(URL::get('cmd')=='delete')-->
                <input type="hidden" name="confirm" value="1" />
                <!--/IF:delete-->
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="voip24hModal" role="dialog">
    <div class="modal-dialog" style="width: 400px">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tài khoản liên kết</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="inputLine" class="col-sm-4 col-form-label">Số nhánh</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputLine" name="line" placeholder="Ext" autocomplete="off">
                        <input type="hidden" id="userid">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword" class="col-sm-4 col-form-label">Password</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputPassword" name="password" placeholder="Password" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="funcIntegrate()" data-dismiss="modal">Tích hợp</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="voip24hModalUpdate" role="dialog">
    <div class="modal-dialog" style="width: 400px">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tài khoản liên kết</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="inputLineUpdate" class="col-sm-4 col-form-label">Số nhánh</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputLineUpdate" name="line" placeholder="Ext">
                        <input type="hidden" id="useridUpdate">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPasswordUpdate" class="col-sm-4 col-form-label">Password</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="inputPasswordUpdate" name="password" placeholder="Password">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputCheckboxUpdate" class="col-sm-4 col-form-label">Kích hoạt</label>
                    <div class="col-sm-8">
                        <input type="checkbox" id="inputCheckboxUpdate" name="enable">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="funcIntegrateUpdate()" data-dismiss="modal">Tích hợp</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<script>
	function funcIntegrate(){
		var userid = document.getElementById("userid").value;
		var inputLine = document.getElementById("inputLine").value;
		var inputPassword = document.getElementById("inputPassword").value;
		$.ajax({
			url: '<?php echo URL::build_current();?>&cmd=integrate_voip24h',
			type: 'post',
			data: {userid: userid, line: inputLine, password: inputPassword, enable: 1},
			success: function(response){
				$('#voip24hModal').modal('hide');
				alert("Tích hợp thành công");
				window.location.reload();
			}
		});
	}
	function funcIntegrateUpdate(){
		var userid = document.getElementById("useridUpdate").value;
		var inputLine = document.getElementById("inputLineUpdate").value;
		var inputPassword = document.getElementById("inputPasswordUpdate").value;
		let enable = 0;
		if (document.getElementById('inputCheckboxUpdate').checked) {
			enable = 1;
		}
		$.ajax({
			url: '<?php echo URL::build_current();?>&cmd=integrate_voip24h',
			type: 'post',
			data: {userid: userid, line: inputLine, password: inputPassword, enable: enable},
			success: function(response){
				$('#voip24hModalUpdate').modal('hide');
				alert("Tích hợp thành công");
				window.location.reload();
			}
		});
	}
	
    $(document).ready(function(){
        $('.select2').select2({
            dropdownAutoWidth : true
        });

	    $('.integrate-voip24h').click(function(){
		    if (this.hasAttribute("data-line") && this.hasAttribute("data-password")) {
			    document.getElementById("useridUpdate").value = $(this).data('id');
			    document.getElementById("inputLineUpdate").value = $(this).data('line');
			    document.getElementById("inputPasswordUpdate").value = $(this).data('password');
			    document.getElementById("inputCheckboxUpdate").checked = !!$(this).data('enable');
			    $('#voip24hModalUpdate').modal('show');
		    } else {
			    document.getElementById("userid").value = $(this).data('id');
			    $('#voip24hModal').modal('show');
		    }
	    });

        
        $('.callio-btn').click(function(){
            let users_id = $(this).attr('data-users_id');
            let total_user = $(this).attr('data-total_user');
            let cmd = $(this).attr('data-cmd');
            if(total_user != undefined && total_user != ''){
                data = {
                    users_id: users_id,
                    total_user: total_user,
                    cmd: cmd
                };
            }else{
                data = {
                    users_id: users_id,
                    cmd: cmd
                };
            }
            $.ajax({
                url: '<?php echo URL::build_current();?>',
                type: 'POST',
                data: data,
                success: function(response){
                    if(response.status == 'success'){
                        if(response.message){
                            alert(response.message);
                        }
                        window.location.reload();
                    }else{
                        alert('Thao tác thất bại');
                    }
                }
            });
        });

        const vaccinationCount = $('#vaccination_count');
        vaccinationCount.multiselect({
            buttonWidth: '150px',
            maxHeight: 200,
        })

        const vaccinationStatus = $('#vaccination_status');
        vaccinationStatus.multiselect({
            buttonWidth: '150px',
            maxHeight: 200,
        })
    }) 

    const MODULE_ID = <?=Module::block_id()?>;
    function getShopIDs(){
        let group_id = $.map($('.selected-ids:checked'), function(c){return $(c).data('groupid'); });
        var unique = group_id.filter((v, i, a) => a.indexOf(v) === i);
        return unique;
    }
    $(document).on('click', '#adminFunction .dropdown-menu', function (e) {
        e.stopPropagation();
    });

    $('.dropdown-toggle:not(#btnSetLeader)').on('click', function (event) {
        $('#functionSetLeader').removeClass('open');
    });

    $('#btnSetLeader').on('click', function (event) {
        $(this).parent().toggleClass('open');
    });

    $('#btnSaveSetStatus').on('click', function (event) {
        let conf = confirm('Bạn có chắc chắn muốn lưu thay đổi?');
        if(conf){
            let status_type = $('input[name="active-action"]:checked').val();
            let ids = $.map($('.selected-ids:checked'), function(c){return c.value; })
            if(ids.length > 0){
                $.post('/form.php', {
                    block_id: MODULE_ID,
                    cmd: 'api_active_accounts',
                    user_ids: ids,
                    status_type: status_type
                })
                .done(function(e){
                    if(typeof e !== 'object' || e.status !== 'success'){
                        if(typeof e.message !== 'undefined'){
                            return  alert(e.message);
                        }else{
                            return alert('Có lỗi xảy ra, vui lòng thử lại sau');
                        }
                    }else{
                        if(typeof e.message !== 'undefined'){
                            alert(e.message);
                        }else{
                            alert('Cập nhật thành công');
                        }
                        location.reload();
                    }
                })
                .fail(function(e){
                    alert('Thao tác thất bại!');
                })
            }
        }
    });

    $('#btnSaveSetGroup').on('click', function (event) {
        let conf = confirm('Bạn có chắc chắn muốn lưu thay đổi?');
        if(conf){ 
            let shop_ids = getShopIDs();
            if(shop_ids.length !== 1){
                alert("Vui lòng xemm lại dữ liệu đã chọn");
            }else{
                let shop_id = shop_ids[0];
                let group_id = $('#functionSetGroup input[name="select-group"]:checked').val();
                let ids = $.map($('.selected-ids:checked'), function(c){return c.value; })
                if(ids.length > 0){
                    $.post('/form.php', {
                        block_id: MODULE_ID,
                        cmd: 'api_set_group',
                        user_ids: ids,
                        group_id: group_id,
                        shop_id: shop_id
                    })
                    .done(function(e){
                        if(typeof e !== 'object' || e.status !== 'success'){
                            return alert('Thao tác thất bại !');
                        }else{
                            alert("Cập nhật thành công.");
                            location.reload();
                        }
                    })
                    .fail(function(e){
                        console.log(e);
                        alert('Thao tác thất bại !');
                    })
                }
            }
        }
    });

    $('#btnSaveSetGroupLeader').on('click', function (event) {
        let conf = confirm('Bạn có chắc chắn muốn lưu thay đổi?');
        if(conf){
            let shop_ids = getShopIDs();
            if(shop_ids.length !== 1){
                alert("Vui lòng xemm lại dữ liệu đã chọn");
            }else{
                let shop_id = shop_ids[0];
                let group_ids = $.map($('#functionSetGroupLeader input[name="select-group"]:checked'), function(c){return c.value; });
                let user_id = $('.selected-ids:checked').val();

                if(user_id){
                    $.post('/form.php', {
                        block_id: MODULE_ID,
                        cmd: 'api_set_leader',
                        user_id: user_id,
                        group_ids: group_ids,
                        shop_id: shop_id
                    })
                    .done(function(e){
                        if(typeof e !== 'object' || e.status !== 'success'){
                            return alert('Thao tác thất bại !');
                        }else{
                            alert("Cập nhật thành công.");
                            location.reload();
                        }
                        
                    })
                    .fail(function(e){
                        console.log(e);
                        alert('Thao tác thất bại !');
                    })
                }

            }
        }
    });

    $('#btnSaveSetLeader').on('click', function (event) {
        let conf = confirm('Bạn có chắc chắn muốn lưu thay đổi?');
        if(conf){
            var shop_ids = getShopIDs();
            if(shop_ids.length !== 1){
                alert("Vui lòng xemm lại dữ liệu đã chọn");
            }else{
                var shop_id = shop_ids[0];
                var group_ids = $.map($('#functionSetLeader input[type="checkbox"]:checked'), function(c){return c.value; });
                var user_ids = $.map($('.selected-ids:checked'), function(c){return c.value; });
                $.post('/form.php', {
                    block_id: MODULE_ID,
                    cmd: 'api_set_manager',
                    group_ids: group_ids,
                    user_ids: user_ids,
                    shop_id: shop_id
                })
                .done(function(e){
                    if(typeof e !== 'object' || e.status !== 'success'){
                        return alert('Thao tác thất bại !');
                    }else{
                        alert("Cập nhật thành công.");
                        location.reload();
                    }
                })
                .fail(function(e){
                    console.log(e);
                    alert('Thao tác thất bại !');
                });
            }
        }
    });

    $('body').on('change', '#cancelSelectManager', function() {
        $("#functionSetLeader input[type='checkbox']:not(#cancelSelectManager)").prop('checked', false); 
    });

    $('body').on('change', "#functionSetLeader input[type='checkbox']:not(#cancelSelectManager)", function() {
        $("#cancelSelectManager").prop('checked', false); 
    });


    $('body').on('change', '#cancelSelectLeader', function() {
        $("#functionSetGroupLeader input[type='checkbox']:not(#cancelSelectLeader)").prop('checked', false); 
    });

    $('body').on('change', "#functionSetGroupLeader input[type='checkbox']:not(#cancelSelectLeader)", function() {
        $("#cancelSelectLeader").prop('checked', false); 
    });

    $('body').on('keyup', '#itemPerPage', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            var itemsCount = $(this).val();
            $('input[name="item_per_page"]').val(itemsCount);
            SearchUserAdminForm.submit();
            
        }
    });

    $('body').on('change', '#itemPerPage', function(e) {
        var itemsCount = $(this).val();
        $('input[name="item_per_page"]').val(itemsCount);
        SearchUserAdminForm.submit();
        
        
    });
    $('body').on('change', '.selected-ids', function() {
        let ids = $.map($('.selected-ids:checked'), function(c){return c.value; });
        
        var shop_ids = getShopIDs();

        if(ids.length == 0){
            $('#adminFunction button.form-control').attr('disabled', 'disabled');
        }else if(ids.length === 1){
            $('#adminFunction button.form-control').removeAttr('disabled');
        }else{
            $('#adminFunction button.form-control').removeAttr('disabled');
            $('#functionSetGroupLeader button.form-control').attr('disabled', 'disabled');
        }
        if(shop_ids.length === 1){
            var shop_id = shop_ids[0];
            $('.shop-id').hide();
            $('.shop-id input').prop('checked', false);
            $('.shop-id-'+shop_id).show();
        }
        if(shop_ids.length >= 2){
            $('#functionSetGroupLeader button.form-control, #functionSetGroup button.form-control, #functionSetLeader  button.form-control').attr('disabled', 'disabled');
        }
    });
</script>

<style>
    .shop-id{
        overflow-wrap: break-word;
    }
</style>
