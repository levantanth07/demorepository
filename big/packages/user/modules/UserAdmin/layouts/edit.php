<?php
    $__accept_image_exts = 'image/jpg,image/gif,image/png,image/jpeg';
    $flag = false;
    if (Url::get('flag')=='secur') {
        $flag = true;
    }
    $account_groups = [[=account_groups=]];
    $account_id = Url::get('id');
    $mobile = [[=mobile=]];
    $mobile_patterns = [[=mobile_patterns=]];
 ?>
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="assets/vissale/css/app.css?d=08062022">
<script src="assets/lib/select2/select2.js"></script>
<script type="text/javascript">
	var blockId = <?php echo Module::block_id(); ?>;
    var account_id = '<?php echo $account_id ?>';
    var mobile = '<?php echo $mobile; ?>';
    const mobile_type_domestic = '<?php echo MOBILE_TYPE_DOMESTIC; ?>';
    const mobile_type_foreign = '<?php echo MOBILE_TYPE_FOREIGN; ?>';
    const mobile_type_domestic_pattern = <?php echo $mobile_patterns[MOBILE_TYPE_DOMESTIC] . 'g'; ?>;
    const mobile_type_foreign_pattern = <?php echo $mobile_patterns[MOBILE_TYPE_FOREIGN] . 'g'; ?>;
</script>
<style>
    .modal-header .close {
        margin-top: -20px !important;
    }
    .required{color:#E45F2B; font-weight: normal}
    .error{
        border: 1px solid red;
        color: red;
        box-shadow: 0 0 6px 0px;
    }
    .success{
        border: 1px solid green;
        color: green;
    }
    .message_wrapper{
        display: none;
        align-items: center;
    }
    .message_wrapper .message {
        flex-grow: 1;
        font-size: 15px;
        font-weight: bold;
        color: green;
    }
    .preview {
        display: flex;
        overflow: auto;
        position: relative;
        flex-wrap: wrap;
    }

    .preview img {
        max-height: 80px;
        cursor: pointer;
        display: block;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    .preview::-webkit-scrollbar {
      display: none;
    }

    .preview {
      -ms-overflow-style: none;  /* IE and Edge */
      scrollbar-width: none;  /* Firefox */
    }

    #preview_zoom {
        display: none;
        position: fixed;
        background: #0000004f;
        width: 100%;
        text-align: center;
        height: 100%;
        z-index: 100000;
        left: 0;
        top: 0;
    }

    #preview_zoom img {
        max-height: 100%;
        max-width: 100%;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translateX(-50%) translateY(-50%);
    }

    .preview .img{
        position: relative;
        width: 80px;
        height: 80px;
        overflow: hidden;
        border-radius: 3px;
        border: 1px solid #ccc;
        margin: 3px;
    }
    .preview .clear::before {
        content: '×';
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #000;
    width: 16px;
    height: 16px;
    z-index: 1;
    color: white;
    top: 0;
    right: 0;
    cursor: pointer;
    }
    td.active.day {
        background: #0b8ec6;
        text-align: center;
        border-radius: 3px;
        color: #fff;
    }
    p.note{
        display: none;
    }
    label{
        font-weight: normal !important;
    }
    <?php if($flag == true): ?>
    .clear{
        display: none !important;
    }
    <?php endif; ?>

    .input-invalid {
        border-color: #dc3545 !important;
    }

    .input-valid {
        border-color: #00a65a !important;
    }

</style>
<script src="packages/core/includes/js/multi_items.js"></script>
<div style="display:none">
	<div id="mi_role_sample">
		<div id="input_group_#xxxx#" class="multi-item-group">
			<span class="multi-edit-input" style="width:20px;"><input  type="checkbox" id="_checked_#xxxx#" tabindex="-1"></span>
			<span class="multi-edit-input" style="width:40px;"><input  name="mi_role[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
			<span class="multi-edit-input"><select  name="mi_role[#xxxx#][role_id]" style="width:250px;" class="multi-edit-text-input" id="role_id_#xxxx#">[[|role_id_options|]]</select></span>
			<span class="multi-edit-input no-border" style="width:40px;text-align:center;padding-top:5px;"><a class="btn btn-default btn-sm" href="#" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_role','#xxxx#','');event.returnValue=false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></span>
		</div>
	</div>
</div>
<div class="container">
    <br id="error_box">
    <?php if(Form::$current->is_error()):?>
        <?=Form::$current->error_messages();?>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12" id="error_messages_1" style="display: none;">
                <div class="alert alert-danger" id="error_messages_content1"></div>
            </div>
        </div>
    <?php endif;?>
    <form  name="EditUserAdminForm" id="EditUserAdminForm" method="post" enctype="multipart/form-data">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">
                    Cập nhật tài khoản
                </h3>
                <div class="box-tools">
                    <?php if($flag == true) : ?>
                        <a class="btn btn-default" onClick="window.close();">Danh sách</a>
                    <?php else : ?>
                        <button class="btn btn-primary" type="submit" id="updateButton"><i class="glyphicon glyphicon-floppy-disk"></i> Cập nhật</button>
                        <a class="btn btn-default" href="<?=Url::build('user_admin');?>">Danh sách</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <!-- <div class="col-md-2"></div> -->
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <input type="hidden" name="privilege_deleted_ids" value=""/>
                            <input type="hidden" name="group_deleted_ids" value=""/>
                            <table class="table">
                                <tr>

                                    <td title="Có thể chọn mầu sắc thể hiện đơn hàng của mỗi nhân viên">
                                        <label align="right"><span class="required">Tên tài khoản (*)</span></label>
                                        <input name="id" type="text" id="id" class="form-control" placeholder="không dùng có dấu, số 0, ký tự lạ, dấu cách" autocomplete="new-password" />
                                        <input name="user_id" type="hidden"/>
                                    </td>

                                    <td>
                                        <?php  if($flag == false): ?>
                                         <label align="right">
                                            <span class="required">
                                                Mật khẩu (*)
                                                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" data-html="true" data-original-title="<span class='small'>Mật khẩu phải có ít nhất 6 kí tự, bao gồm <strong>Số + chữ hoa + chữ thường + ký tự đặc  biệt</strong> mới có thể cập nhật được</span>">
                                                    <i class="fa fa-question-circle"></i>
                                                </a>
                                            </span>
                                        </label>
                                        <div class="input-password">
                                            <input name="password" type="password" id="password"class="form-control" autocomplete="new-password" onkeyup="getPasswordStrength(blockId,this.value);" placeholder="Nhập mật khẩu an toàn" />
                                            <i class="icon-eye icon-right">
                                                <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                                    <defs><clipPath><path fill="none" d="M124-288l388-672 388 672H124z" clip-rule="evenodd"/></clipPath></defs><path d="M508 624a112 112 0 0 0 112-112c0-3.28-.15-6.53-.43-9.74L498.26 623.57c3.21.28 6.45.43 9.74.43zm370.72-458.44L836 122.88a8 8 0 0 0-11.31 0L715.37 232.23Q624.91 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 0 0 0 51.5q56.7 119.43 136.55 191.45L112.56 835a8 8 0 0 0 0 11.31L155.25 889a8 8 0 0 0 11.31 0l712.16-712.12a8 8 0 0 0 0-11.32zM332 512a176 176 0 0 1 258.88-155.28l-48.62 48.62a112.08 112.08 0 0 0-140.92 140.92l-48.62 48.62A175.09 175.09 0 0 1 332 512z"/><path d="M942.2 486.2Q889.4 375 816.51 304.85L672.37 449A176.08 176.08 0 0 1 445 676.37L322.74 798.63Q407.82 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 0 0 0-51.5z"/>
                                                </svg>
                                            </i>
                                            <i class="icon-eye hide icon-right">
                                                <svg viewBox="64 64 896 896" focusable="false" fill="currentColor" width="1em" height="1em" data-icon="lock" aria-hidden="true">
                                                    <path d="M396 512a112 112 0 1 0 224 0 112 112 0 1 0-224 0zm546.2-25.8C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 0 0 0 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM508 688c-97.2 0-176-78.8-176-176s78.8-176 176-176 176 78.8 176 176-78.8 176-176 176z"/>
                                                </svg>
                                            </i>
                                        </div>
                                        <div class="password-strength-bar-container">
                                            <div id="passwordStrengthBar"></div>
                                            <div id="passwordStrengthLabel"></div>
                                        </div>
                                        <?php  endif; ?>
                                        <?php  if($flag == true): ?>
                                           <label align="right">Tên SHOP</label>
                                           <input name="group_name" type="text" id="group_name" class="form-control">
                                       <?php  endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <?php if($flag == true) : ?>
                                    <td colspan="2" style="display:none">
                                        <label class="text-right" >Chọn mầu cho tài khoản</label>
                                        <select  name="label" id="label">
                                            <option value=""></option>
                                            <option value="#7bd148">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#5484ed">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#a4bdfc">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#46d6db">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#7ae7bf">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#51b749">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#fbd75b">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#ffb878">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#ff887c">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#dc2127">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#dbadff">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#e1e1e1">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#eef763">Chọn mầu đơn hàng cho nhân viên</option>
                                        </select>
                                    </td>
                                <?php else : ?>
                                    <td colspan="2">
                                        <label class="text-right">Chọn mầu cho tài khoản</label>
                                        <select  name="label" id="label">
                                            <option value=""></option>
                                            <option value="#7bd148">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#5484ed">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#a4bdfc">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#46d6db">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#7ae7bf">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#51b749">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#fbd75b">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#ffb878">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#ff887c">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#dc2127">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#dbadff">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#e1e1e1">Chọn mầu đơn hàng cho nhân viên</option>
                                            <option value="#eef763">Chọn mầu đơn hàng cho nhân viên</option>
                                        </select>
                                    </td>
                                <?php endif; ?>
                                </tr>

                                <tr>
                                    <td width="40%">
                                        <label align="right"><span class="required">Họ và tên (*)</span></label>
                                        <input name="full_name" type="text" id="full_name"class="form-control"/>
                                    </td>

                                    <td>
                                        <table width="100%" class="table" style="margin: 0">
                                            <tr>
                                                <td style="padding: 0; padding-right: 15px">
                                                    <label align="right"><span class="required">Ngày sinh (*)</span></label>
                                                    <div class="input-group date" data-provide="datepicker" data-date-format="dd/mm/yyyy" data-today-highlight="true">
                                                        <input
                                                            name="birth_date"
                                                            type="text"
                                                            id="birth_date"
                                                            class="form-control"
                                                            value="<?=Url::get('birth_date')?>"
                                                            >
                                                        <div class="input-group-addon">
                                                            <span class="glyphicon glyphicon-th"></span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td align="left" style="padding: 0">
                                                    <label>Giới tính</label>
                                                    <select name="gender" id="gender" class="form-control"></select>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!--IF:cond8(Session::get('admin_group'))-->
                                <tr>
                                    <td>
                                        <label align="right" class="required">Số điện thoại (*)</label>
                                        <div style="display: flex;">
                                            <select name="mobiletype" id="mobiletype" style="width: 30%;" data-target="#phone" class="form-control"></select>
                                            <input onkeyup="validatePhoneNumber(this)" name="phone" type="text" id="phone" class="form-control" value="<?=Url::get('phone')?>" placeholder="Nhập từ 8-11 số"  data-tooltip='#jsElMobileTooltip' data-type='#mobiletype'/>
                                        </div>
                                        <span id="jsElMobileTooltip" class="text-danger h6"></span>
                                    </td>
                                    <td>
                                        <label align="right">Đầu số tổng đài</label>
                                        <input name="extension" type="text" id="extension" class="form-control">
                                    </td>
                                </tr>
                                <!--/IF:cond8-->

                                <tr>
                                    <td>
                                        <label align="right"><span class="required">Link FB (*)</span></label>
                                        <input name="fb_link" type="text" id="linkfb" placeholder="Nhập link FB" class="form-control"/></td>
                                    <td>
                                        <label align="right">Email</label>
                                        <input name="email" type="text" id="email"class="form-control"/></td>
                                </tr>


                                <tr>
                                    <td>
                                        <label align="right"><span class="required">Địa chỉ thường trú (*)</span></label>
                                        <input name="address" type="text" id="address" placeholder="Nhập số nhà, tên tòa nhà, tên đường" class="form-control"/></td>
                                    <td>
                                        <label align="right"><span class="required">Tỉnh / thành (*)</span></label>
                                        <select name="zone_id" id="zone_id" class="form-control"></select></td>
                                </tr>

                                <tr>
                                    <td>
                                        <label align="right">Địa chỉ tạm trú</label>
                                        <input name="temp_address" type="text" id="temp_address"class="form-control"/></td>
                                    <td>
                                        <label align="right">Tỉnh / thành</label>
                                        <select name="temp_zone_id" id="temp_zone_id" class="form-control"></select></td>
                                </tr>


                                <tr class="hide">
                                    <td align="right"></td>
                                    <td></td>
                                    <td align="right">Gán vào page:</td>
                                    <td>
                                        <!--IF:cond7(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY) or Session::get('admin_group'))-->
                                        <select name="fb_page_id" id="fb_page_id" class="form-control"></select>
                                        <!--ELSE-->
                                        <select name="fb_page_id" id="fb_page_id" class="form-control" disabled></select>
                                        <!--/IF:cond7-->
                                    </td>
                                </tr>
                                <tr>
                            </table>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="title">Thông tin tiêm chủng vắc xin covid 19</h4>
                            </div>
                            <div class="panel-body">
                                <div class="alert alert-danger">Chú ý: Để bảo vệ cộng đồng, bạn vui lòng khai báo thông tin chính xác về tình trạng dưới đây và chịu trách nhiệm với thông tin đã khai báo ở dưới và đảm bảo là sự thật!</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Số mũi đã tiêm</label>
                                            <select name="vaccination_count" id="vaccination_count" class="form-control">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tình trạng sức khỏe</label>
                                            <select name="vaccination_status" id="vaccination_status" class="form-control">
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

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="title">
                                    Thông tin xác thực nhân sự
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label><span class="required" style="font-weight: normal;">Số CMTND/căn cước (*)</span></label>
                                        <input onchange="validatePhoneNumber(this)" value="<?php echo Url::get('identity_card');?>" name="identity_card" type="text" id="identity_card" class="form-control" placeholder="Nhập từ 9-15 số">
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 15px">
                                    <div class="col-md-6">
                                        <label><span class="required" style="font-weight: normal;">Ngày cấp (*)</span></label>
                                        <!-- <input name="id_card_issued_date" type="text" id="id_card_issued_date" class="form-control"> -->
                                        <div class="input-group date" data-provide="datepicker" data-date-format="dd/mm/yyyy" data-today-highlight="true">
                                            <input name="id_card_issued_date" type="text" id="id_card_issued_date" class="form-control">
                                            <div class="input-group-addon">
                                                <span class="glyphicon glyphicon-th"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label><span class="required" style="font-weight: normal;">Nơi cấp (*)</span></label>
                                        <input name="id_card_issued_by" type="text" id="id_card_issued_by" class="form-control">
                                    </div>
                                </div>
                                <br>
                                <br>
                                <?php if($flag == true) : ?>
                                    <input type="hidden" name="flag" value="flag">
                                <?php endif; ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="required">Ảnh CMTND/căn cước mặt trước (*)</label>
                                        <div style="border-radius: 20px;overflow: hidden;">
                                            <img src="<?=Url::get('identity_card_front')?>" width="345" height="225" alt="Mặt trước" onerror="this.src='assets/vissale/images/cmt_front.webp'">
                                        </div>
                                        <?php if($flag != true) : ?>
                                        <input onchange="uploadIdCardImage(this)" type="file" class="form-control" accept="image/jpg,image/gif,image/png,image/jpeg,image/swf,image/ico">
                                        <?php endif; ?>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="identity_card_front" type="hidden">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="required">Ảnh CMTND/căn cước  mặt sau (*)</label>
                                        <div style="border-radius: 20px;overflow: hidden;">
                                            <img src="<?=Url::get('identity_card_back')?>" width="345" height="225" onerror="this.src='assets/vissale/images/cmt_back.webp'">
                                        </div>
                                        <?php if($flag != true) : ?>
                                        <input onchange="uploadIdCardImage(this)" type="file" class="form-control" accept="image/jpg,image/gif,image/png,image/jpeg,image/swf,image/ico">
                                        <?php endif; ?>

                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="identity_card_back" type="hidden">
                                    </div>
                                </div>
                                <br>

                                <hr>
                                <h4 style="color:black; padding-bottom: 10px;
                                margin-bottom: 10px">Chú ý: Bạn vui lòng tải ảnh Hồ sơ dấu đỏ, ảnh có dấu giáp lai xác nhận (mỗi loại tối đa 10 ảnh, mỗi ảnh ≤ 1MB)</h3>
                                <div class="row">
                                    <!-- Hồ sơ xin việc -->
                                    <div class="col-md-6">
                                        <label class="required">Hồ sơ xin việc (*)</label>
                                        <div class="preview preview_job_application">Chưa có ảnh nào!</div>
                                        <input <?php if($flag == true): ?> style="display: none;" <?php endif; ?> class="job_application" onchange="validateMultiImage(this)" class="form-control" type="file" multiple="multiple" accept="<?=$__accept_image_exts?>">
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="job_application" type="hidden">
                                    </div>

                                    <!-- Sổ hộ khẩu -->
                                    <div class="col-md-6">
                                        <label>Sổ hộ khẩu</label>
                                        <div class="preview preview_registration_book">Chưa có ảnh nào!</div>
                                        <input <?php if($flag == true): ?> style="display: none;" <?php endif; ?> class="registration_book" onchange="validateMultiImage(this)" type="file" class="form-control" multiple="multiple" accept="<?=$__accept_image_exts?>">
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="registration_book" type="hidden">
                                    </div>
                                </div>
                                <br>
                                <br>

                                <div class="row">
                                    <!-- Hợp đồng hợp tác -->
                                    <div class="col-md-6">
                                        <label class="required">Hợp đồng hợp tác (*)</label>
                                        <div class="preview preview_contract">Chưa có ảnh nào!</div>
                                        <input <?php if($flag == true): ?> style="display: none;" <?php endif; ?> class="contract" onchange="validateMultiImage(this)" type="file" class="form-control" multiple="multiple" accept="<?=$__accept_image_exts?>">
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="contract" type="hidden">
                                    </div>

                                    <!-- Bản cam kết QC, Tư vấn (nếu có) -->
                                    <div class="col-md-6">
                                        <label>Bản cam kết QC, Tư vấn (nếu có)</label>
                                        <div class="preview preview_commitment">Chưa có ảnh nào!</div>
                                        <input <?php if($flag == true): ?> style="display: none;" <?php endif; ?> class="commitment" onchange="validateMultiImage(this)" type="file" class="form-control" multiple="multiple" accept="<?=$__accept_image_exts?>">
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="commitment" type="hidden">
                                    </div>
                                </div>

                                    <br>
                                    <br>

                                <div class="row">
                                    <!-- Giấy khai sinh -->
                                    <div class="col-md-6">
                                        <label class="required">Giấy khai sinh (*)</label>
                                        <div class="preview preview_birth_certificate">Chưa có ảnh nào!</div>
                                        <input <?php if($flag == true): ?> style="display: none;" <?php endif; ?> class="birth_certificate" onchange="validateMultiImage(this)" type="file" class="form-control" multiple="multiple" accept="<?=$__accept_image_exts?>">
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="birth_certificate" type="hidden">
                                    </div>

                                    <!-- Giấy khám SK A3 -->
                                    <div class="col-md-6">
                                        <label>Giấy khám SK A3</label>
                                        <div class="preview preview_health_certification">Chưa có ảnh nào!</div>
                                        <input <?php if($flag == true): ?> style="display: none;" <?php endif; ?> class="health_certification" onchange="validateMultiImage(this)" type="file" class="form-control" multiple="multiple" accept="<?=$__accept_image_exts?>">
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="health_certification" type="hidden">
                                    </div>
                                </div>

                                    <br>
                                    <br>

                                <div class="row">
                                    <!-- Bằng cấp -->
                                    <div class="col-md-6">
                                        <label class="required">Bằng cấp (*)</label>
                                        <div class="preview preview_diploma">Chưa có ảnh nào!</div>
                                        <input <?php if($flag == true): ?> style="display: none;" <?php endif; ?> class="diploma" onchange="validateMultiImage(this)" type="file" class="form-control" multiple="multiple" accept="<?=$__accept_image_exts?>">
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="diploma" type="hidden">
                                    </div>

                                    <!-- Cam kết bảo mật thông tin -->
                                    <div class="col-md-6">
                                        <label>Cam kết bảo mật thông tin</label>
                                        <div class="preview preview_information_security">Chưa có ảnh nào!</div>
                                        <input <?php if($flag == true): ?> style="display: none;" <?php endif; ?> class="information_security" onchange="validateMultiImage(this)" type="file" class="form-control" multiple="multiple" accept="<?=$__accept_image_exts?>">
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng tải ảnh ≤ 1MB</p>
                                        <p class="note" style="font-size: bold; color: red">Bạn vui lòng upload 10 ảnh trở xuống</p>
                                        <input name="information_security" type="hidden">
                                    </div>
                                </div>


                                    <div class="row">
                                    <!-- ghi chú -->
                                    <div class="col-md-12">
                                        <br>
                                        <label>Ghi chú</label>
                                            <textarea name="note1" id="note1" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>

                            </div>
                            <div id="preview_zoom"></div>
                            <div class="required" style="padding: 15px;display: flex;align-items: center;">
                                <input type="checkbox" name="confirm" id="checkbox-confirm" style="margin: 0;">
                                <label for="checkbox-confirm" style="margin: 0 3px;">Bạn Cam kết QC, Tư vấn (*)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="panel panel-success">
                            <div class="panel-body" style="background: #efefef;">
                                <!--IF:cond6(Url::get('cmd')=='add' and User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
                                <table class="table">
                                    <tr>
                                        <td align="right"></td>
                                        <td>
                                            <select name="system_group_id" id="system_group_id" class="form-control select2"></select>
                                            <!-- <?=$this->map['system_group_id']?> -->
                                        </td>
                                    </tr>
                                </table>
                                <!--/IF:cond6-->
                                <!--IF:cond6(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
                               <table class="table">
                                   <tr>
                                       <td align="right"><span class="required">Tên SHOP</span></td>
                                       <td><input name="group_name" type="text" id="group_name" class="form-control"></td>
                                   </tr>
                                   <tr>
                                       <td align="right">SHOP ID</td>
                                       <td><input name="group_id" type="text" id="group_id" class="form-control" placeholder="Bỏ trống với trường hợp thêm mới"></td>
                                   </tr>
                                   <?php if(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY)){?>
                                       <tr>
                                           <td align="right"><span class="required">Loại tài khoản</span></td>
                                           <td><select name="account_type" id="account_type" class="form-control"></select></td>
                                       </tr>
                                       <tr>
                                           <td align="right">Thuộc hệ thống</td>
                                           <td><select name="master_group_id" id="master_group_id" class="form-control"></select></td>
                                       </tr>
                                   <?php }?>
                               </table>
                                <!--/IF:cond6-->
                            </div>
                        </div>

                        <div class="panel panel-warning">
                            <div class="panel-heading"><span class="glyphicon glyphicon-check" aria-hidden="true"></span> Kích hoạt tài khoản</div>
                            <div class="panel-body text-center">
                                <input  name="active" type="checkbox" id="active" value="1" <?php echo (URL::get('active')?' checked':'');?> style="zoom: 2.5;">
                                <table class="table">
                                    <tr>
                                        <td width="40%" align="right">Ngày hết hạn</td>
                                        <td>
                                            <!--IF:cond6(Url::get('cmd')=='add' and User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
                                            <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd">
                                                <input name="expired_date" type="text" id="expired_date" class="form-control">
                                                <div class="input-group-addon">
                                                    <span class="glyphicon glyphicon-th"></span>
                                                </div>
                                            </div>
                                            <!--ELSE-->
                                            <?=[[=expired_date=]]?date('d/m/Y',strtotime([[=expired_date=]])):'...';?>
                                            <!--/IF:cond6-->
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading"><span class="glyphicon glyphicon-check" aria-hidden="true"></span> Nhóm tài khoản</div>
                            <div class="panel-body">
                                <select name="account_group_id" type="text" id="account_group_id" class="form-control"></select>
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <div class="box-title">
                                            Trưởng phòng
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <!--LIST:account_groups-->
                                            <div class="col-lg-6 no-padding-r">
                                                <label class="small">
                                                    <input name="account_groups[]" id="account_group_[[|account_groups.id|]]" value="[[|account_groups.id|]]" type="checkbox" <?php if(in_array([[=account_groups.id=]], [[=account_group_admins=]])) echo 'checked'; ?>> [[|account_groups.name|]]
                                                </label>
                                            </div>
                                            <!--/LIST:account_groups-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading"><span class="glyphicon glyphicon-check" aria-hidden="true"></span> Phân quyền</div>
                            <div class="panel-body">
                                <div class="input-group">
                                    <label class="input-group-addon" id="basic-addon1" for="admin_group" style="font-weight:bold;color:#E45F2B;background: #efefef">Quyền quản lý shop</label>
                                    <span class="input-group-addon" style="background: #efefef">
                                        <?php if(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY) or Session::get('admin_group')){?>
                                            <input  name="admin_group" id="admin_group" type="checkbox" value="1" <?php echo (URL::get('admin_group')?'checked':'');?> style="zoom:2.0" />
                                        <?php }else{?>
                                            <input  name="admin_group" id="admin_group" type="checkbox" value="1" disabled <?php echo (URL::get('admin_group')?'checked':'');?> style="zoom:2.0" />
                                        <?php }?>
                                    </span>
                                </div>
                                <br>
                                <div class="box box-default">
                                    <div class="box-header with-border">
                                        <div class="box-title">
                                            Chọn Quyền
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="row">
                                            <!--LIST:roles_activities-->
                                            <div class="col-lg-6 no-padding-r roles">
                                                <label class="small">
                                                    <input class="role_id" name="roles[]" id="[[|roles_activities.id|]]" value="[[|roles_activities.id|]]" type="checkbox" <?php if(in_array([[=roles_activities.id=]], [[=atv_privilege_code=]])) echo 'checked'; ?>> [[|roles_activities.name|]]
                                                </label>
                                            </div>
                                            <!--/LIST:roles_activities-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
        <div class="modal fade" id="md-show-phone-number" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="display: flex">
                        <h3 style="flex-grow: 1" class="title text-info text-center"><i class="fa fa-users"></i> Tài khoản đã được tạo từ SĐT/CMTND</h3>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="box">
                            <table class="table table-bordered" style="table-layout: fixed;">
                                <thead>
                                <tr>
                                    <th>Tên tài khoản</th>
                                    <th>Họ Và tên</th>
                                    <th>SĐT</th>
                                    <th nowrap="">CMTND<br>Căn cước</th>
                                    <th>Địa chỉ</th>
                                    <th>Tên shop</th>
                                    <th>Người tạo</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody id="show-phone-number">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" value="1" name="confirm_edit">
        <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
        <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
        <div class="modal fade" id="myModal">
            <div class="modal-dialog">
              <div class="modal-content" style="width:500px;float:right">
              
                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">Danh sách quyền</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="row data_roles">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-dismiss="modal">Đóng</button>
                </div>
              </div>
            </div>
        </div>
    </form>
</div>
<script src="assets/admin/scripts/colorpicker/colorpicker.js"></script>
<link rel="stylesheet" href="assets/admin/scripts/colorpicker/colorpicker.css">
<script src="assets/vissale/js/app.js?d=08062022"></script>
<script>
	mi_init_rows('mi_role',<?php if(isset($_REQUEST['mi_role'])){echo MiString::array2js($_REQUEST['mi_role']);}else{echo '[]';}?>);
    function handleInputPhoneNumber () {
        this.value = this.value.replace(/[^0-9]/g, '')
    }

    function addInputInvalid (el) {
        el.removeClass('input-valid').addClass('input-invalid');
    }

    function addInputValid (el) {
        el.addClass('input-valid').removeClass('input-invalid');
    }

    function handleValidateMobile(element, isNullable = false) {
        let elMobile = $(element.data('target'));
        handleValidateNumber(elMobile, isNullable);
    }

    function handleValidateNumber(element, isNullable = false) {
        let phoneNumber = String(element.val());
        let elTooltip = $(element.data('tooltip'));
        let elMobileType = $(element.data('type'));

        const mobileType = elMobileType.val();
        let minLength = 10;
        let maxLength = 11;
        let pattern = mobile_type_domestic_pattern;
        let exPhone = '0987xxxxxx';

        let length = phoneNumber.length;
        if (isNullable && !length) {
            elTooltip.html('');
            addInputValid(element);
            addInputValid(elMobileType);
            return true;
        }//end if

        if (length && phoneNumber.charAt(0) != '0') {
            phoneNumber = '0' + phoneNumber;
        }//end if

        if (mobileType == mobile_type_foreign) {
            pattern = mobile_type_foreign_pattern;
            minLength = 4;

            maxLength = 20;
            exPhone = '0087xxxxxx';
            if (phoneNumber.charAt(1) != '0') {
                phoneNumber = '0' + phoneNumber;
            }//end if
        } else {
            phoneNumber = phoneNumber.replace(/^0+/g, '0');
        }//end if
    
        element.val(phoneNumber);
        length = phoneNumber.length;
        
        if (length < minLength || length > maxLength) {
            addInputInvalid(element);
            addInputInvalid(elMobileType);
            elTooltip.html(`(*) Vui lòng nhập SĐT từ ${minLength} - ${maxLength} ký tự <br> Bạn đã nhập <b>[ ${length} ]</b> ký tự`);
            return false;
        }//end if
        console.log(phoneNumber.match(pattern));
        if (!phoneNumber.match(pattern)) {
            addInputInvalid(element);
            addInputInvalid(elMobileType);
            elTooltip.html(`(*) Số điện thoại không hợp lệ (Ex: ${exPhone})`);
            return false;
        }//end if

        elTooltip.html('');
        addInputValid(element);
        addInputValid(elMobileType);
        return true;
    }

    function checkPhoneNumberType(phoneNumber, elName) {
        let el = $(elName);
        if (phoneNumber.charAt(0) != '0') {
            return;
        }//end if

        if (phoneNumber.charAt(1) == '0') {
            return el.val(mobile_type_foreign).change();
        }//end if
        
        return el.val(mobile_type_domestic).change();
    }


	jQuery(document).ready(function () {
        checkPhoneNumberType(mobile, '#mobiletype');
        $('#phone').on('keyup paste', handleInputPhoneNumber);
        $('#phone').on('change', function () {
            handleValidateNumber($(this));
        });

        $('#mobiletype').on('change', function () {
            handleValidateMobile($(this));
        });


        $('#id').change(function(){
            this.value = this.value.trim();
            let isValid = !this.value.match(/^[\w\.\@\-]+$/);
            if(isValid){
                alert('Vui lòng nhập username chứa các ký tự: từ 0->9, A-z .. và các ký tự @, gạch dưới, dấu chấm, gạch ngang');
            }
            $('button[type="submit"]').prop('disabled', isValid);
            $('button[type="submit"]').get(0).classList[isValid ? 'add' : 'remove']('error-btn');
            this.classList[isValid ? 'add' : 'remove']('error-field')
        })
        <?php if($flag == true) : ?>
            $('.roles').on('click',function(){
                let role_id = $(this).find('.role_id').val();
                if($(this).find('.role_id').prop('checked') == true){
                    $.ajax({
                        method: "GET",
                        url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                        data : {
                            'cmd':'get_roles',
                            'role_id':role_id,
                            'account_id':account_id
                        },
                        beforeSend: function(){
                        },
                        success: function(content){
                            content = $.parseJSON(content);
                            let html = '';
                            $.each(content, function(i, val) {
                                html += `
                                    <div class="col-md-6">
                                        <p>`+ val['role_name'] +`</p>
                                    </div>
                                    `
                                });
                            $('.data_roles').html(html);
                            $('#myModal').modal('show');
                        }
                    });
                }
            });
            jQuery('#id').attr('readonly',true);
            jQuery('input[name="password"]').attr('readonly',true);
            jQuery('#full_name').attr('readonly',true);
            jQuery('#birth_date').attr('disabled',true);
            jQuery('#gender').attr('disabled',true);
            jQuery('#phone').attr('readonly',true);
            jQuery('#extension').attr('readonly',true);

            jQuery('#linkfb').attr('readonly',true);
            jQuery('#email').attr('readonly',true);
            jQuery('#address').attr('readonly',true);
            jQuery('#zone_id').attr('disabled',true);
            jQuery('#temp_address').attr('readonly',true);
            jQuery('select[name="label"]').attr('readonly',true);
            jQuery('#checkbox-confirm').attr('disabled',true);
            jQuery('#identity_card').attr('readonly',true);
            jQuery('#temp_zone_id').attr('disabled',true);
            jQuery('#id_card_issued_date').attr('readonly',true);
            jQuery('#id_card_issued_by').attr('readonly',true);
            jQuery('#group_name').attr('readonly',true);
            jQuery('#group_id').attr('readonly',true);
            jQuery('#account_type').attr('readonly',true);
            jQuery('#note1').attr('disabled',true);
            jQuery('#master_group_id').attr('readonly',true);
            jQuery('#active').attr('disabled',true);
            jQuery('#account_group_id').attr('disabled',true);
            jQuery('#account_group_id').attr('disabled',true);
            jQuery('#admin_group').attr('disabled',true);

            jQuery('#vaccination_count').attr('readonly',true);
            jQuery('#vaccination_status').attr('readonly',true);
            jQuery('#vaccination_note').attr('readonly',true);

            $("input[name='account_groups[]']").attr('disabled', true);
            $("input[name='roles[]']").attr('disabled', true);
            $("input[type=file]").attr('disabled', true);
        <?php endif; ?>
		<!--IF:cond(isset([[=label=]]) and [[=label=]])-->
		jQuery('select[name="label"]').simplecolorpicker('selectColor', '[[|label|]]');
		<!--ELSE-->
		jQuery('select[name="label"]').simplecolorpicker();
		<!--/IF:cond-->
		<?php if(Url::get('cmd')=='edit'){?>
			jQuery('#id').attr('readonly',true);
		<?php }?>
        
	});
</script>
<script type="text/javascript">
    let IS_ADMIN = <?=[[=isAdmin=]] ? 'true' : 'false'?>;
    let oldValues = {
        job_application: '<?=Url::get('job_application')?>',
        registration_book: '<?=Url::get('registration_book')?>',
        contract: '<?=Url::get('contract')?>',
        commitment: '<?=Url::get('commitment')?>',
        birth_certificate: '<?=Url::get('birth_certificate')?>',
        health_certification: '<?=Url::get('health_certification')?>',
        diploma: '<?=Url::get('diploma')?>',
        information_security: '<?=Url::get('information_security')?>',
    };
    let idCard = {
        identity_card_back: '<?=Url::get('identity_card_back')?>',
        identity_card_front: '<?=Url::get('identity_card_front')?>',
    };

    function onClick(event){
        let tbody = event.target.parentElement.parentElement.parentElement;
        [].slice.call(tbody.querySelector('tr')).map(e => e.style.background = '');
        event.target.parentElement.parentElement.style.background = '#f7f7f7';

        let user = JSON.parse(event.target.dataset.user);

        let zone = document.querySelector('#zone_id').querySelector('option[value="'+user.zone_id+'"]');
        if(zone) zone.selected = true;

        document.querySelector('input[name=full_name]').value = user.name;
        document.querySelector('input[name=phone]').value = user.phone;
        document.querySelector('input[name=address]').value = user.address;
        document.querySelector('textarea[name=note1]').value = user.note1;
        document.querySelector('input[name=identity_card]').value = user.identity_card;
        let identity_card_front = document.querySelector('input[name=identity_card_front]');
        identity_card_front.value = user.identity_card_front;
        identity_card_front.previousElementSibling
        .previousElementSibling
        .previousElementSibling
        .previousElementSibling
        .querySelector('img').src = user.identity_card_front
        let identity_card_back = document.querySelector('input[name=identity_card_back]');
        identity_card_back.value = user.identity_card_back;
        identity_card_back.previousElementSibling
        .previousElementSibling
        .previousElementSibling
        .previousElementSibling
        .querySelector('img').src = user.identity_card_back
    }

    function uploadIdCardImage(el){
        // wrapper element
        let wrapper = el.parentElement;

        dom(el).doUploadOne();
    }
    function showImages(element, urls){
        element.innerHTML = urls.length ? urls.reduce(function(out, e){
            return out.push(`<div class="img"><img onclick="clickImg(event)" src="${e.url}" />
                            <i onclick="clearImage(this)" class="clear" data-hash="${e.hash}"></i></div>`), out;
        }, [])
        .join('')
        : "Chưa có ảnh nào";
    }
    function showIDImage(el, objs){
        objs.url ? (el.querySelector('img').src = objs.url) : '';
    }

    function clickImg(event){
        $('#preview_zoom').html('<img src="'+event.target.src+'">');
        $('#preview_zoom').show();
    }

    function clearImage(ctx){
        let input = ctx.parentElement.parentElement.nextElementSibling.nextElementSibling.nextElementSibling.nextElementSibling;
        object = JSON.parse(input.value);
        object = object.filter(function(e){
            return e.hash != ctx.dataset.hash;
        });

        input.value = JSON.stringify(object);
        showImages(ctx.parentElement.parentElement, object);

        if(object.length < 10){
            return input.previousElementSibling.previousElementSibling.previousElementSibling.disabled = false;
        }
    }

    $('#preview_zoom').click(function(){
        $('#preview_zoom').hide();
    })

    let dom = function(ele, errorClassName, successClassName){
        let element = ele;
        let __error = errorClassName ? errorClassName : 'error';
        let __success = successClassName ? successClassName : 'success';

        function uploadImages(formdata){
            return $.ajax({
                url: "/index062019.php?page=user_admin&cmd=upload_images",
                type: "POST",
                data: formdata,
                processData: false,
                contentType: false,
            });
        }

        return {
            error: function(error, success) {
                element.classList.remove(__success);
                element.classList.add(__error);

                return this;
            },

            success: function(error, success) {
                element.classList.remove(__error);
                element.classList.add(__success);

                return this;
            },

            default: function(){
                element.classList.remove(__error);
                element.classList.remove(__success);

                return this;
            },

            disabled: function(){
                element.disabled = 'true';

                return this;
            },

            doUpload: function(){
                if(!element.files.length)
                    return;



                let successCount = 0;
                let old = element.nextElementSibling.nextElementSibling.nextElementSibling.value;
                old = (old ? JSON.parse(old) : []);

                if(old.length + element.files.length > 10){
                    element.value= '';
                    element.nextElementSibling.nextElementSibling.style.display = 'block';
                    return dom(element).error();
                }
                else if(old.length + element.files.length== 10)
                     dom(element).disabled();

                element.nextElementSibling.nextElementSibling.style.display = '';

                for (i = 0; i < element.files.length; i++) {
                    let formdata = new FormData();
                    formdata.append("image", element.files[i]);
                    uploadImages(formdata)
                    .success(function(data, status, xhr){
                        try{
                            if(typeof data == "string"){
                                data = JSON.parse(data);
                            }

                            if(data.error){
                                alert(data.error);
                                return;
                            }

                            if(!data.url){
                                alert('Upload Error!');
                                return;
                            }

                            old.push(data);

                            element.nextElementSibling.nextElementSibling.nextElementSibling.value = JSON.stringify(old);
                            showImages(element.previousElementSibling, old);

                        }catch(e){
                            element.value = '';
                            alert('Upload Error!');
                        }
                    })
                    .error(function(xhr, status, error){
                    }).always(function(){
                        if(++successCount == element.files.length)
                            element.value = '';
                    });
                }
            },

            doUploadOne: function(){

                if(!element.files.length)
                    return;

                if(element.files[0].size > 1024*1204){
                    return element.nextElementSibling.style.display = 'block';
                }

                element.nextElementSibling.style.display = 'none';

                let formdata = new FormData();
                formdata.append("image", element.files[0]);
                uploadImages(formdata)
                .success(function(data, status, xhr){
                    try{
                        if(typeof data == "string"){
                            data = JSON.parse(data);
                        }

                        if(data.error){
                            alert(data.error);
                            return;
                        }

                        if(!data.url){
                            alert('Upload Error!');
                            return;
                        }

                        element.nextElementSibling.nextElementSibling.nextElementSibling.value = data.url;
                        showIDImage(element.previousElementSibling, data);
                        dom(element).success();
                    }catch(e){
                        alert('Upload Error!');
                        dom(element).error();
                    }
                })
                .error(function(xhr, status, error){
                    dom(element).error();
                }).always(function(){
                    element.value = '';
                });
            },


            element: function(){
                return element;
            }
        }
    };


    Object.keys(oldValues).map(function(nameAttr){
        let input = document.querySelector('input.' + nameAttr);
        input.nextElementSibling.nextElementSibling.nextElementSibling.value = oldValues[nameAttr];
        if(oldValues[nameAttr]){
            dom(input).success();
            try{
                let object = JSON.parse(oldValues[nameAttr]);
                if(object.length >= 10)
                    dom(input).disabled();
                showImages(input.previousElementSibling, object);
            }catch(e){
            }
        }
    })

    Object.keys(idCard).map(function(nameAttr){
        let input = document.querySelector('input[name=' + nameAttr+']');
        input.value = idCard[nameAttr];
        if(idCard[nameAttr]){
            dom(input).success();
            try{
                let object = JSON.parse(idCard[nameAttr]);
                showIDImage(input.previousElementSibling.previousElementSibling, object);
            }catch(e){
            }
        }
    })

    function validateMultiImage(element){
        var MAX_NUM_IMAGE = 10;
        if(element.files.length > MAX_NUM_IMAGE){
            element.nextElementSibling.nextElementSibling.style.display = 'block';
            dom(element).error()
        }
        else if(element.files.length > 0) {
            for(i = 0; i< element.files.length; i++){
                if(element.files[i].size > 1024*1204){
                    return element.nextElementSibling.style.display = 'block';
                }
            }
            element.nextElementSibling.style.display = 'none';

            let old = element.nextElementSibling.nextElementSibling.nextElementSibling.value;
            old = (old ? JSON.parse(old) : []);

            if(old.length + element.files.length > 10){
                element.value= '';
                element.nextElementSibling.nextElementSibling.style.display = 'block';
                return dom(element).error();
            }
            else if(old.length + element.files.length== 10)
                 dom(element).disabled();

            element.nextElementSibling.nextElementSibling.style.display = '';


            dom(element).success().doUpload();
        }
        else dom(element).default();
    }

    function validatePhoneNumber(element){
        if(element.value.match(/^\d{8,11}$/))
            dom(element).success()
        else {
            element.value = element.value.replace(/\D/g, '');
        }
    }


    $('#updateButton').click(function(e){
        let errors = [];

        let elPhone = $('#phone');
        res_validate = handleValidateNumber(elPhone);
        if(!res_validate){
            return false;
        }//end if

        <!--IF:condphone(Session::get('admin_group'))-->
        // validate so dien thoai
        let phone = $('input[name="phone"]')[0];
        dom(phone).success();
        // if(!phone.value.match(/^\d{8,11}$/)){
        //     dom(phone).error();
        //     errors.push('Số điện thoại không hợp lệ.');
        // }
        <!--/IF:condphone-->

        // validate họ tên
        let full_name = $('input[name="full_name"]')[0];
        dom(full_name).success();
        if(!full_name.value){
            dom(full_name).error();
            errors.push('Họ tên người dùng không hợp lệ.');
        }

        // validate dia chi
        let address = $('input[name="address"]')[0];
        dom(address).success();
        if(!address.value.length && !IS_ADMIN){
            dom(address).error();
            errors.push('Không được để trống địa chỉ thường trú.');
        }

        // validate link fb
        let fb_link = $('input[name="fb_link"]')[0];
        dom(fb_link).success();
        if(!fb_link.value.length && !IS_ADMIN){
            dom(fb_link).error();
            errors.push('Không được để trống link FB.');
        }

        // validate id thanh pho
        let zone_id = $('select[name="zone_id"]')[0];
        dom(zone_id).success();
        if(zone_id.value <= 0 && !IS_ADMIN){
            dom(zone_id).error();
            errors.push('Không được để trống thành phố thường trú.');
        }

        // validate so cmnd
        let identity_card = $('input[name="identity_card"]')[0];
        dom(identity_card).success();
        if(!identity_card.value.match(/^\d{9,15}$/) && !IS_ADMIN){
            dom(identity_card).error();
            errors.push('Số CMND/CCCD không hợp lệ.');
        }

        // validate noi cap cmnd
        let id_card_issued_by = $('input[name="id_card_issued_by"]')[0];
        dom(id_card_issued_by).success();
        if(id_card_issued_by.value <= 0 && !IS_ADMIN){
            dom(id_card_issued_by).error();
            errors.push('Không được để trống nơi cấp CMND/CCCD');
        }

        // validate noi cap cmnd
        let identity_card_front = $('input[name="identity_card_front"]')[0];
        dom(identity_card_front).success();
        if(!identity_card_front.value && !IS_ADMIN){
            dom(identity_card_front).error();
            errors.push('Không được để trống ảnh mặt trước CMND/CCCD');
        }

        // validate noi cap cmnd
        let identity_card_back = $('input[name="identity_card_back"]')[0];
        dom(identity_card_back).success();
        if(identity_card_back.value <= 0 && !IS_ADMIN){
            dom(identity_card_back).error();
            errors.push('Không được để trống ảnh mặt sau CMND/CCCD');
        }

        // validate ngay cap cmnd
        let id_card_issued_date = $('input[name="id_card_issued_date"]')[0];
        dom(id_card_issued_date).success();
        // Nếu dữ trường rỗng và không phải admin thì thông báo lỗi
        if(!id_card_issued_date.value && !IS_ADMIN){
            dom(id_card_issued_date).error();
            errors.push('Không được để trống CMND/CCCD!');
        }
        // Nếu tồn tại dữ liệu nhưng không phù hợp sẽ thông báo lỗi
        else if(id_card_issued_date.value && !id_card_issued_date.value.match(/\d{1,2}\/\d{1,2}\/\d{4}/) || new Date().getTime() <= Date.parse(id_card_issued_date.value.split('/').reverse().join('/'))){
            dom(id_card_issued_date).error();
            errors.push('Ngày cấp CMND/CCCD không hợp lệ');
        }

        // validate ngay sinh
        let birth_date = $('input[name="birth_date"]')[0];
        dom(birth_date).success();
        // Nếu dữ trường rỗng và không phải admin thì thông báo lỗi
        if(!birth_date.value && !IS_ADMIN){
            dom(birth_date).error();
            errors.push('Không được để trống ngày sinh!');
        }
        // Nếu tồn tại dữ liệu nhưng không phù hợp sẽ thông báo lỗi
        else if(birth_date.value && !birth_date.value.match(/\d{1,2}\/\d{1,2}\/\d{4}/) || new Date().getTime() <= Date.parse(birth_date.value.split('/').reverse().join('/'))){
            dom(birth_date).error();
            errors.push('Ngày sinh không hợp lệ');
        }

        // validate file upload
        let job_application = getField('job_application');
        let contract = getField('contract');
        let commitment = getField('commitment');
        let registration_book = getField('registration_book');
        let birth_certificate = getField('birth_certificate');
        let health_certification = getField('health_certification');
        let diploma = getField('diploma');
        let information_security = getField('information_security');


        if((!job_application.length) && !IS_ADMIN ){
            errors.push('Không được để trống file ảnh hồ sơ xin việc');
        }
        if((job_application.length < 4) && !IS_ADMIN ){
            errors.push('tối thiểu phải 4 file ảnh hồ sơ xin việc');
        }
        if((!birth_certificate.length) && !IS_ADMIN ){
            errors.push('Không được để trống file ảnh giấy khai sinh');
        }
        if((!health_certification.length) && !IS_ADMIN ){
            // errors.push('Không được để trống file ảnh giấy khám SK A3');
        }
        if((!diploma.length) && !IS_ADMIN ){
            errors.push('Không được để trống file ảnh bằng cấp');
        }
        if((!information_security.length) && !IS_ADMIN ){
            // errors.push('Không được để trống file ảnh Cam kết bảo mật thông tin');
        }

        let confirm = $('input[name="confirm"]')[0];
        dom(confirm).success();
        if(!confirm.checked && !IS_ADMIN){
            dom(confirm).error();
            errors.push('Bạn chưa xác nhận thông tin đã cung cấp.');
        }

        if(errors.length){
            $('#error_messages_1').css({'display': ''});
            let err = document.querySelector('#error_messages_content1');

            $(err).children('.jserr').remove();

            $(document.querySelector('#error_messages_content1')).append(
                 errors.map((e, i) => `<div class="jserr" onclick="var pos=jQuery('#identity_card_front').offset(); window.scrollTo(pos.left,pos.top);jQuery('#identity_card_front').focus().css('border','2px inset #ccc') ;return false;" title="Vị trí lỗi"><i class="fa fa-exclamation-triangle"></i> ${e}</div>`).join('')
            );

            e.preventDefault();
        }
        //window.close();
    });

    function getField(fieldname){
        let el = document.querySelector('input[name='+fieldname+']');
        return JSON.parse(el.value ? el.value : '[]');
    }

    const getPasswordStrength = _.debounce(_getPasswordStrength, 200);
    
    function _getPasswordStrength(blockId,password){
        jQuery.ajax({
            method: "POST",
            url: 'form.php',
            data : {
                'cmd':'get_password_length',
                'password':password,
                'username': $('input[name="id"]').val(),
                block_id:blockId
            },
            beforeSend: function(){

            },
            success: function(content){                
                var strength = parseInt(content) || 0;
                const labels = ['Quá yếu', 'Yếu', 'Trung bình', 'An toàn', 'Rất an toàn'];
                jQuery('#passwordStrengthBar').css({'width': 25 * strength + '%'});
                jQuery('#passwordStrengthLabel').html(labels[strength]);
                jQuery('#updateButton').attr('disabled', strength < 3);
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
    $('#phone').change(function(){
        suggestUserInfo('phone');
    });
    $('#identity_card').change(function(){
        suggestUserInfo('identity_card');
    });

	function suggestUserInfo(target) {
        $('#'+target).val($('#'+target).val().trim());
        let spnb = $('#show-phone-number');
        $.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : {
                'cmd':'get_user_info',
                'p':$('#'+target).val(),
                'account_id':'<?php echo Url::sget('id'); ?>'
            },
            beforeSend: function(){
            },
            success: function(content){
                console.log(content);
                if(content == 1){
                    alert('Số điện thoại đã được sử dụng cho tài khoản khác!');
                    $('#'+target).val('');
                }else if(content == 2){
                    alert('Cmtnd / căn cước đã được sử dụng cho tài khoản khác!');
                    $('#'+target).val('');
                }
                else if(content != 0){
                    spnb.html(content);
                    $('#md-show-phone-number').modal('show');
                }
            }
        });
    }

    const isToday = function(someDate){
      const today = new Date();
      if(someDate.getDate() == 20 && someDate.getFullYear() == 2021){
        console.log(someDate);
      }
      return someDate.getDate() == today.getDate() &&
        someDate.getMonth() == today.getMonth() &&
        someDate.getFullYear() == today.getFullYear()
    }
    $(document).ready(function(){
        $('.select2').select2({
            dropdownAutoWidth : true
        });
    })
</script>
