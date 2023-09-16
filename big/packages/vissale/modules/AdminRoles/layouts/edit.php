<style>
	.option-2 .ct{
		padding:15px 0px;
	}
</style>

<?php
$title = (URL::get('cmd')=='delete')?'Xóa nhóm quyền':' Quản lý nhóm quyền ';?>
<div class="container full">
    <br>
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-key"></i> <?php echo $title;?></h3>
            <div class="box-tools pull-right">
                <a onclick="EditAdminRolesForm.submit();" class="btn btn-primary"> <i class="fa fa-floppy-o"></i> Lưu </a>
                <a href="index062019.php?page=admin_roles" class="btn btn-default"> <i class="fa fa-list"></i> Danh sách </a>
            </div>
        </div>
        <div class="box-body">
            <form name="EditAdminRolesForm" method="post" enctype="multipart/form-data">
                <div class="content">
                    <div class="row">
                        <div class="col-xs-5">
                            <div class="box box-info box-solid">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Tên nhóm quyền</h4>
                                </div>
                                <div class="box-body">
                                    <input name="name" type="text" id="name" class="form-control" placeholder="Tên quyền">
                                </div>
                            </div>
                            <div class="box box-default box-solid" style="min-height: 250px;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Quyền được</h4>
                                </div>
                                <div class="box-body">
                                    <!--LIST:roles_activities-->
                                    <div class="col-xs-6">
                                        <label class="" for="[[|roles_activities.code|]]"><input name="[[|roles_activities.code|]]" id="[[|roles_activities.code|]]" aria-describedby="basic-addon1" value="1" type="checkbox" <?php if(in_array([[=roles_activities.code=]], [[=atv_privilege_code=]])) echo 'checked'; ?>> [[|roles_activities.name|]]</label>
                                    </div>
                                    <!--/LIST:roles_activities-->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <br>
                                    <p class="text-info">(1) <strong>Quyền bung đơn</strong> cho phép NV được phân quyền xem tất cả các đơn hàng trừ đơn Chưa xác nhận, Thành công, Đã thu tiền, Chuyển hoàn, Đã trả hàng về kho.</p>
                                    <p class="text-warning">
                                        (2) <strong>Quyền bung đơn nhóm</strong> cho phép NV được phân quyền xem tất cả các đơn hàng <strong>trong nhóm</strong> trừ đơn Thành công, Đã thu tiền, Chuyển hoàn, Đã trả hàng về kho.<br>
                                        <span style="color:#ff0000;font-style:italic;">(Quyền bung đơn nhóm sẽ xem được tất cả đơn hàng trong nhóm <strong>trừ những trạng thái đã chọn (Chỉ Xem hoặc Xem + Sửa) ở bên phải <i class="fa fa-hand-o-right"></i></strong>)</span>
                                    </p>
                                    <p class="text-deep-blue">
                                        (3) <strong>Bung đơn (không thành công và chuyển hàng)</strong>: NV không xem được các trạng thái Chưa xác nhận, Xác nhận chốt đơn, Chuyển hàng, Thành công, Đã thu tiền, Chuyển hoàn, Đã trả hàng về kho.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-7">
                            <div class="box box-default box-solid" style="min-height: 250px;">
                                <div class="box-header with-border">
                                    <h4 class="box-title">Trạng thái</h4>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered table-striple">
                                        <tr bgcolor="#EFEFEF">
                                            <th width="70%">Trạng thái</th>
                                            <th width="30%">
												Quyền
												<select class="form-control text-danger" onchange="$('.status-item').val($(this).val());">
                                                    <option value="">Áp dụng cho tất cả</option>
                                                    <option value="0">Bỏ chọn</option>
                                                    <option value="1">Chỉ Xem</option>
                                                    <option value="2">Xem + Sửa</option>
                                                </select>
											</th>
                                        </tr>
                                        <!--LIST:status-->
                                        <tr bgcolor="<?=([[=status.can_edit=]]==1)?'#7fffd4':(([[=status.can_view=]]==1)?'#faebd7':'');?>">
                                            <td><label for="status_[[|status.id|]]" style="font-weight: normal;"> <i style="height: 13px;width:13px;display:inline-block;background-color: <?=[[=status.color=]]?[[=status.color=]]:'#EFEFEF'?>;"></i> [[|status.name|]]</label></td>
                                            <td class="text-center">
                                                <select  name="status[[[|status.id|]]]" id="view_only_status_[[|status.id|]]" value="[[|status.id|]]" class="form-control status-item">
                                                    <option value="0">Không chọn</option>
                                                    <option value="1" <?php if(isset([[=status.can_view=]]) and [[=status.can_view=]]==1) echo 'selected'; ?>>Chỉ Xem</option>
                                                    <option value="2" <?php if(isset([[=status.can_edit=]]) and [[=status.can_edit=]]==1) echo 'selected'; ?>>Xem + Sửa</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <!--/LIST:status-->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

