<?php
$title = (URL::get('do') == 'edit') ? 'Sửa thông tin khách hàng: ' . Url::get('name') : 'Thêm khách hàng mới';
$subtitle = (URL::get('do') == 'edit') ? 'Sửa thông tin khách hàng: <span class="label label-info" style="margin: 0 5px">' . Url::get('id') . '</span> ' . Url::get('name') : 'Thêm khách hàng mới';
$action = (URL::get('do') == 'edit') ? 'edit' : 'add';
System::set_page_title(Portal::get_setting('company_name', '') . ' ' . $title); ?>
<div class="container">
    <br>
  <span style="display:none">
    <span id="mi_card_sample">
      <span id="input_group_#xxxx#" style="float:left;background:#FFF;">
        <span class="multi-edit-input"><input name="mi_card[#xxxx#][id]" type="hidden" id="id_#xxxx#"
                                              class="form-control" tabindex="-1"></span>
        <span class="multi-edit-input"><input name="mi_card[#xxxx#][name]" type="text" id="name_#xxxx#"
                                              style="width:205px;" class="form-control" tabindex="1"></textarea></span>
        <span class="multi-edit-input"><input name="mi_card[#xxxx#][discount_rate]" type="text"
                                              id="discount_rate_#xxxx#" style="width:105px;" class="form-control"
                                              tabindex="2"></textarea></span>
        <span class="multi-edit-input"><input name="mi_card[#xxxx#][start_date]" type="text" id="start_date_#xxxx#"
                                              style="width:105px;" class="form-control" tabindex="3"
                                              readonly="readonly"></span>
        <span class="multi-edit-input"><input name="mi_card[#xxxx#][end_date]" type="text" id="end_date_#xxxx#"
                                              style="width:105px;" class="form-control" tabindex="4"
                                              readonly="readonly"></span>

        <?php if (Session::get('account_type') == 3 and check_user_privilege('ADMIN_KETOAN')) { ?>
            <span class="multi-edit-input" title="Xóa"><a
                    onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_card','#xxxx#','card_');return false;"
                    style="cursor:pointer;"><i class="glyphicon glyphicon-remove"></i></a></span>
        <?php } ?>
      </span><br clear="all">
    </span>
    <span id="mi_reminder_sample">
      <span id="input_group_#xxxx#" style="float:left;border-bottom:1px solid #999;background:#FFF;">
          <span class="multi-edit-input"><input name="mi_reminder[#xxxx#][id]" type="hidden" id="id_#xxxx#"
                                                class="form-control" style="width:40px;text-align:right;" tabindex="-1"></span>
          <span class="multi-edit-input"><input name="mi_reminder[#xxxx#][content]" type="text" id="content_#xxxx#"
                                                style="width:150px;" class="form-control" tabindex="1"></span>
          <span class="multi-edit-input"><input name="mi_reminder[#xxxx#][start_time]" type="text"
                                                id="start_time_#xxxx#" style="width:85px;" class="form-control"
                                                readonly="readonly"></span>
          <span class="multi-edit-input"><input name="mi_reminder[#xxxx#][finish_time]" type="text"
                                                id="finish_time_#xxxx#" style="width:85px;" class="form-control"
                                                readonly="readonly"></span>
          <span class="multi-edit-input" title="Xóa"><img src="skins/default/images/buttons/delete.gif"
                                                          onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_reminder','#xxxx#','reminder_');return false;"
                                                          style="cursor:pointer;"/></span>
      </span><br clear="all">
    </span>
    <span id="mi_shared_group_sample">
      <span id="input_group_#xxxx#" style="float:left;background:#FFF;">
        <span class="multi-edit-input"><input name="mi_shared_group[#xxxx#][id]" type="hidden" id="id_#xxxx#"
                                              class="form-control" tabindex="-1"></span>
        <span class="multi-edit-input"><select name="mi_shared_group[#xxxx#][group_id]" id="group_id_#xxxx#"
                                               style="width:405px;" class="form-control" tabindex="1">[[|branch_options|]]</select></span>
        <span class="multi-edit-input" title="Xóa"><a
                    onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_shared_group','#xxxx#','group_');return false;"
                    style="cursor:pointer;"><i class="glyphicon glyphicon-remove"></i></a></span>
      </span><br clear="all">
    </span>
  </span>
    <form name="EditCrmCustomerForm" method="post" enctype="multipart/form-data">
        <input name="card_deleted_ids" type="hidden" id="card_deleted_ids">
        <input name="group_deleted_ids" type="hidden" id="group_deleted_ids">
        <fieldset id="toolbar" style="display: flex;">
            <h3 class="title"  style="font-size: 18px; align-items: center; flex-grow: 1; display: flex; padding: 0 15px; ">
                <?php echo $subtitle ?>
            </h3>

            <div class="pull-right">
                <input name="save_task" type="submit" id="save_task" value="Ghi lại" class="btn btn-primary"/>
                <input type="button" value="Quay lại" class="btn btn-dafault"
                       onclick="window.location='<?php echo Url::build_current(array('act', 'branch_id')) ?>';"/>
            </div>
        </fieldset>
        <br>
        <div class="list panel">
            <div><?php if (Form::$current->is_error()) {
                    echo Form::$current->error_messages();
                } ?></div>
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="<?php if (Url::get('image_url') and file_exists(Url::get('image_url'))) {
                        echo Url::get('image_url');
                    } ?>" onerror="this.src='assets/standard/images/no_avatar.webp'"
                         style="border:1px solid #CCCCCC;width:200px;padding:5px;border-radius: 10px;">
                    <div style="padding: 20px;">Thay ảnh: <input name="image_url" type="file" id="image_url" class="form-control">200x200 pixel (*.jpg, *.jpeg,
                        *.gif)
                    </div>

                    <div class="panel panel-danger text-left">
                        <div class="panel-heading">
                            <strong><i class="glyphicon glyphicon-time"></i> Lịch sử</strong>
                        </div>
                        <div class="panel-body" style="max-height: 254px;overflow-y: auto">
                            <ul class="list-group">
                                <!--LIST:logs-->
                                <li class="list-group-item">
                                    <div class="row">
                                        <div class="col-md-12"><i class="fa fa-clock-o"></i>
                                            <?php echo date("d/m/Y H:i'", [[=logs.time=]]); ?>
                                        </div>
                                        <div class="col-md-12">
                                            <strong>[[|logs.user_id|]]</strong>
                                            <span class="small">[[|logs.description|]]</span>
                                        </div>
                                    </div>
                                </li>
                                <!--/LIST:logs-->
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <table class="table table-striped">
                        <tr>
                            <td width="120" align="right">Tên khách hàng(*):</td>
                            <td width="150"><input name="name" type="text" id="name" class="form-control"
                                                   oninvalid="this.setCustomValidity('Bạn vui lòng nhập tên khách hàng')"/>
                            </td>
                            <td align="right">Nghề nghiệp:</td>
                            <td><input name="career" type="text" id="career" class="form-control"/></td>
                        </tr>
                        <tr>
                            <td align="right" class="required">Cân nặng (kg):</td>
                            <td><input name="weight" type="number" id="weight" class="form-control"
                                       placeholder="00.00"/></td>
                            <td width="120" align="right">Chức vụ:</td>
                            <td nowrap="nowrap" width="150"><input name="job_title" type="text" id="job_title"
                                                                   class="form-control" autocomplete="on"/></td>
                        </tr>
                        <tr>
                            <td align="right">Ngày sinh:</td>
                            <td><input name="birth_date" type="text" id="birth_date" class="form-control"
                                       autocomplete="off"/></td>
                            <td align="right">Nhóm phân loại(<span class='text-red'>*</span>):</td>
                            <td><select name="crm_group_id" id="crm_group_id" class="form-control"
                                        oninvalid="this.setCustomValidity('Chọn nhóm phân loại')"></select></td>
                        </tr>
                        <tr>
                            <td align="right">Email:</td>
                            <td>
                                <input name="email" type="text" id="email" class="form-control"/>
                            </td>
                            <td align="right">Giới tính:</td>
                            <td><select name="gender" id="gender" class="form-control"></select></td>
                        </tr>
                        <tr>
                            <td align="right" class="required">Di động(*):</td>
                            <td>
                                <input name="mobile" type="text" id="mobile" class="form-control"
                                       placeholder="Bạn vui lòng nhập số điện thoại duy nhất" disabled />
                            </td>
                            <td align="right">Tỉnh / thành phố:</td>
                            <td>
                                <select name="zone_id" id="zone_id" class="form-control"></select>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">Địa chỉ:</td>
                            <td colspan="3">
                                <div>
                                    <textarea name="address" id="address" class="form-control"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">Người giới thiệu:</td>
                            <td>
                                <div class="input-group">
                                    <input name="contact_name" type="text" id="contact_name" class="form-control"
                                           placeholder="Khách hàng" readonly="">
                                    <span class="input-group-addon" id="searchContact" title="Chọn khách hàng"><i
                                                class="fa fa-search"></i></span>
                                    <input name="contact_id" type="hidden" id="contact_id" class="form-control">
                                </div>
                            </td>

                            <td align="right" valign="top">
                                Nguồn khách:
                            </td>
                            <td>
                                <select name="source_id" id="source_id" class="form-control"></select>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Nhân viên xác nhận:</td>
                            <td>
                                <!--IF:cond_caring_staff( (!Session::get('admin_group') && !empty($_REQUEST['user_id'])) )-->
                                    <select name="user_id" id="user_id" class="form-control" disabled></select>
                                <!--ELSE-->
                                    <select name="user_id" id="user_id" class="form-control"></select>
                                <!--/IF:cond_caring_staff-->
                            </td>
                            <td align="right">Tạo bởi:</td>
                            <td>
                                <?=(Url::get('do')=='edit')?[[=creator=]]:$_SESSION['user_data']['full_name'];?>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">Ghi chú chung:</td>
                            <td colspan="3">
                                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" valign="top" class="text-danger">Ghi chú cảnh báo:</td>
                            <td colspan="3">
                                <textarea name="warning_note" id="warning_note" class="form-control text-danger"
                                          rows="3"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">Lịch sử thay đổi SDT:</td>
                            <td colspan="3">
                                <textarea name="used_phones" id="used_phones" class="form-control" rows="3" disabled></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel panel-default hidden">
            <div class="panel-heading">
                Thẻ VIP
            </div>
            <div class="panel-body form-inline">
                <div id="mi_card_all_elems">
          <span style="white-space:nowrap;">
              <span class="multi-edit-input-header"><span style="width:210px;float:left;">Tên thẻ</span></span>
              <span class="multi-edit-input-header"><span style="width:110px;float:left;">Giảm giá(%)</span></span>
              <span class="multi-edit-input-header"><span style="width:110px;float:left;">Bắt đầu</span></span>
              <span class="multi-edit-input-header"><span style="width:110px;float:left;">Kết thúc</span></span>
              <span class="multi-edit-input-header"><span style="width:12px;float:left;">&nbsp;</span></span>
              <br clear="all">
          </span>
                </div>
                <?php if (Session::get('account_type') == 3 and check_user_privilege('ADMIN_KETOAN')) { ?>
                <div class="add-mi-button">
                    <input type="button" value="Thêm"
                                                  onclick="mi_add_new_row('mi_card');jQuery('#start_date_'+input_count).datepicker();jQuery('#end_date_'+input_count).datepicker();">
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="panel panel-default hidden">
            <div class="panel-heading">
                Chia sẻ với cơ sở khác
            </div>
            <div class="panel-body form-inline">
                <div id="mi_shared_group_all_elems">
              <span style="white-space:nowrap;">
                  <span class="multi-edit-input-header"><span style="width:410px;float:left;">Chọn cơ sở</span></span>
                  <br clear="all">
              </span>
                </div>
                <div class="add-mi-button"><input type="button" value="Thêm"
                                                  onclick="mi_add_new_row('mi_shared_group');"></div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                Tài khoản ngân hàng
            </div>
            <div class="panel-body form-inline">
                <div class="col-md-3">Số tài khoản: <input name="bank_account_number" type="text"
                                                           id="bank_account_number" class="form-control"></div>
                <div class="col-md-3">Tên tài khoản: <input name="bank_account_name" type="text" id="bank_account_name"
                                                            class="form-control"></div>
                <div class="col-md-6">Tên ngân hàng: <input name="bank_name" type="text" id="bank_name"
                                                            class="form-control"></div>
            </div>
        </div>
        <br clear="all">
    </form>
</div>
<script>
    mi_init_rows('mi_card',<?php if (isset($_REQUEST['mi_card'])) {
        echo MiString::array2js($_REQUEST['mi_card']);
    } else {
        echo '[]';
    }?>);
    mi_init_rows('mi_shared_group',<?php if (isset($_REQUEST['mi_card'])) {
        echo MiString::array2js($_REQUEST['mi_shared_group']);
    } else {
        echo '[]';
    }?>);
</script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        $.fn.datepicker.defaults.format = "dd/mm/yyyy";
        jQuery('#birth_date').datepicker();
        $('#searchContact').click(function () {
            window.open('index062019.php?page=customer&act=select&no_id=[[|no_id|]]');
        });

        $("form[name=EditCrmCustomerForm]").on('submit', function(event){
            event.preventDefault();
            console.log('submitting... form');
            if ( !$(`#crm_group_id`).val() ) {
                alert('Bạn chưa chọn nhóm khách hàng!');
                return false;
            }
            this.submit();
        });
    });
</script>