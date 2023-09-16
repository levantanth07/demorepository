<style>
    input.error {
    border-color: red;
    color: red;
}

label.error {
    color: red;
}
</style>
<div class="container">
    <form name="EditUser" method="post" id="EditUser" enctype="multipart/form-data">
        <br>
        <div class="box box-info">
            <div class="box-header">
                <h3 class="box-title">THÔNG TIN TÀI KHOẢN</h3>
                <div class="box-tools pull-right">
                    <button  name="update" type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Lưu</button>
                    <a class="btn btn-default" href="<?php echo Url::build_current(array('cmd'=>'change_pass'));?>" title="Đổi mật khẩu"> <i class="fa fa-key"></i> Đổi mật khẩu</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="box box-widget widget-user">
                            <!-- Add the bg color to the header using any of the bg-* classes -->
                            <div class="widget-user-header bg-aqua-active">
                                <h3 class="widget-user-username"><?=Url::get('full_name');?></h3>
                                <h5 class="widget-user-desc"><?=Session::get('user_id').' (ID: '.get_user_id().')';?></h5>
                            </div>
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?php if(Url::get('image_url')){ echo Url::get('image_url');}?>" onerror="this.src='assets/standard/images/no_avatar.webp'" alt="User Avatar">
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-md-12 alert">
                                        <!--IF:cond([[=account_group=]])-->
                                        <div class="label label-warning">Nhóm tài khoản: [[|account_group|]]</div>
                                        <!--/IF:cond-->
                                        <div class="panel panel-warning">
                                            <div class="panel-heading">
                                                <div class="panel-title">
                                                    <i class="fa fa-users"></i> Trưởng phòng / quản lý nhóm
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <div class="row">
                                                    <!--LIST:account_group_admins-->
                                                    <div class="col-xs-6">
                                                        <div style="text-align:left; border:1px solid #CCC; border-radius: 3px; margin:2px;padding:2px;min-height: 45px;"><i class="fa fa-play"></i> [[|account_group_admins.name|]]</div>
                                                    </div>
                                                    <!--/LIST:account_group_admins-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <div class="panel-title">
                                                    <i class="fa fa-key"></i> Quyền
                                                </div>
                                            </div>
                                            <div class="panel-body">
                                                <?=[[=admin_group=]]?'<i class="fa fa-check"></i> <strong>Quản lý shop</strong><hr>':''?>
                                                <div class="row">
                                                    <!--LIST:roles_activities-->
                                                    <div class="col-xs-6">
                                                        <div style="text-align:left; border:1px solid #CCC; border-radius: 3px; margin:2px;padding:2px;min-height: 45px;"><i class="fa fa-check"></i> [[|roles_activities.name|]]</div>
                                                    </div>
                                                    <!--/LIST:roles_activities-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row hidden">
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">[[|total_unconfirmed|]]</h5>
                                            <span class="description-text">Đơn chưa xác nhận</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 border-right">
                                        <div class="description-block">
                                            <h5 class="description-header">[[|total_confirmed|]]</h5>
                                            <br><span class="description-text">Đơn chốt</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4">
                                        <div class="description-block">
                                            <h5 class="description-header">[[|total_successed|]]</h5>
                                            <span class="description-text">Đơn thành công</span>
                                        </div>
                                        <!-- /.description-block -->
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <fieldset id="toolbar">
                            <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
                            <!--IF:cond(Form::get_flash_message('update')=='success')-->
                            <div class="alert alert-success" role="alert">
                                Cập nhật thành công!
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <!--/IF:cond-->
                            <table class="table">
                                <tr>
                                    <td width="32%" align="right">Họ và tên (*)</td>
                                    <td width="68%"><input name="full_name" type="text" id="full_name" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td width="32%" align="right">Giới tính</td>
                                    <td width="68%"><select name="gender" class="select" id="gender"></select>
                                        / Ngày sinh: <input name="birth_date" type="text" id="birth_date" class="input"></td>
                                </tr>
                                <tr>
                                    <td width="32%" align="right">Điện thoại</td>
                                    <td width="68%"><?=Url::get('phone');?> <br><span class="small text-danger">(Chỉ quản lý shop mới có thể thay đổi số điện thoại)</span></td>
                                </tr>
                                <tr>
                                    <td width="32%" align="right">CMTND/Căn cước</td>
                                    <td width="68%"><?=Url::get('identity_card')?Url::get('identity_card'):'...';?><br><span class="small text-danger">(Chỉ quản lý shop mới có thể thay đổi CMTND/Căn cước)</span></td>
                                </tr>
                                <tr>
                                    <td width="32%" align="right">Địa chỉ</td>
                                    <td width="68%"><input name="address" type="text" id="address" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td width="32%" align="right">Khu vực</td>
                                    <td width="68%"><select name="zone_id" class="form-control" id="zone_id"></select></td>
                                </tr>
                                <tr class="hide">
                                    <td width="32%" align="right">[[.fax.]]</td>
                                    <td width="68%"><input name="fax" type="text" id="fax" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td width="32%" align="right">[[.skype.]]</td>
                                    <td width="68%"><input name="skype" type="text" id="skype" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td width="32%" align="right">[[.email.]] (*)</td>
                                    <td width="68%"><input name="email" type="text" id="email" class="form-control"></td>
                                </tr>
                                <tr style="display:none;">
                                    <td width="32%" align="right">[[.openid.]]</td>
                                    <td width="68%"><input name="openid" type="text" id="openid" class="form-control" readonly></td>
                                </tr>
                            </table>
                            
                        </fieldset>

                        <div class="box box-default box-solid mt-20">
                            <div class="box-header">
                                Thay ảnh Avatar
                            </div>
                            <div class="box-body">
                                <div class="small">
                                   <input name="image_url" type="file" id="image_url" class="form-control">200x200 pixel (*.jpg, *.jpeg, *.gif)
                                </div>
                            </div>
                        </div>

                        <div class="box box-default box-solid">
                            <div class="box-header">
                                <h3 class="box-title">Thông tin tiêm chủng vắc xin covid 19</h3>
                            </div>
                            <div class="box-body">
                                <div class="alert alert-danger">Chú ý: Để bảo vệ cộng đồng, bạn vui lòng khai báo thông tin chính xác về tình trạng dưới đây và chịu trách nhiệm với thông tin đã khai báo ở dưới và đảm bảo là sự thật!</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Số mũi đã tiêm</label>
                                            <select name="vaccination_count" id="vaccination_count" class="form-control">
                                                <option value="0">Không xác định</option>
                                                <option value="1">Chưa tiêm</option>
                                                <option value="2">Mũi 1</option>
                                                <option value="3">Mũi 2</option>
                                                <option value="4">Mũi 3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tình trạng sức khỏe</label>
                                            <select name="vaccination_status" id="vaccination_status" class="form-control">
                                                <option value="0">Chưa xác định</option>
                                                <option value="1">Bình thường</option>
                                                <option value="2">F0</option>
                                                <option value="3">F1</option>
                                                <option value="4">F2</option>
                                                <option value="5">F3</option>
                                                <option value="6">Khác</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Ghi chú</label>
                                            <textarea name="vaccination_note" id="vaccination_note" class="form-control"><?=$this->map['vaccination_note']?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="packages/core/includes/js/jquery/jquery.validate.js"></script>
<script>
  jQuery(document).ready(function(){
    $('#birth_date').datetimepicker({
        format: 'DD/MM/YYYY'
    });
    jQuery.validator.addMethod("birth_date", function(value, element) {
        return value ? Date.parse(value.split('/').reverse().join('/')) < new Date().getTime() : false;
    }, 'Ngày sinh không hợp lệ');

    jQuery('#EditUser').validate({
      rules: {
        full_name:{
            required: true
        },
        phone:{
            required: true
        },
        birth_date:{
            required: true,
            birth_date: true
        },
        email:{
            required: true,
            email: true,
            remote : 'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=check_email<?php echo Url::iget('id')?'&id='.Url::iget('id'):''?>'
        }
      },
      messages: {
        full_name:{
            required: 'Yêu cầu phải nhập'
        },
        phone:{
            required: 'Yêu cầu phải nhập'
        },
        birth_date:{
            required: 'Bạn phải nhập ngày sinh',
        },
        email:{
            required: 'Yêu cầu phải nhập',
            email: 'Yêu cập đúng định dạng email',
            remote:'Email này đã được đăng ký bởi tài khoản khác'
        }
      }
    });
  });
</script>