<?php
    $admin_tuha = (User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))?true:false;
    $admin_group = (Session::get('admin_group'))?true:false;
    $admin_mkt = check_user_privilege('ADMIN_marketing')?true:false;
    $isOwner = is_group_owner();
    $linkApi = [[=link_api=]];
    $callio_info = [[=callio_info=]];
    $voip24h_info = [[=voip24h_info=]];
    $groupId = '';
    if($admin_tuha){
        $groupId = Url::get('group_id')??Session::get('group_id');
    }
    $check = 0;
    if (User::is_admin()) {
        $check = 1;
    }
    $timeSlot = [[=timeSlot=]];
    $advValue = [[=value_adv=]];
    $allows = 0;
    if(isset($_REQUEST['allow_ips']) && $_REQUEST['allow_ips'] != ''){
        $allows = 1;
    }
?>
<script type="text/javascript">
    var checkIsAdmin = '<?php echo $check; ?>';
    var checkAllows = '<?php echo $allows; ?>';
</script>
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<style type="text/css">
    .frm-integrate-callio {
        display: none;
    }
    <?php if(!Url::get('integrate_callio')) { ?>
    .lbl-integrate-callio {
        display: none;
    }
    <?php } ?>
    .frm-integrate-voip24h {
        display: none;
    }
    <?php if(!Url::get('integrate_voip24h')) { ?>
    .lbl-integrate-voip24h {
        display: none;
    }
    <?php } ?>
</style>
<div class="container full">
    <br>
    <?=Form::draw_flash_message_error('upload_images')?>
    <form name="EditUser" method="post" id="EditUser" enctype="multipart/form-data" onsubmit="return CheckInput();">
        <div class="box box-default">
            <div class="box-header bg-gray-light">
                <h3 class="box-title"> <i class="fa fa-laptop" aria-hidden="true"></i> CÀI ĐẶT CỬA HÀNG</h3>
                <div class="box-tools pull-right">
                    <!--IF:cond($admin_tuha)-->
                    <a class="btn btn-default" href="<?=Url::build('admin-shop');?>">Danh sách shop</a>
                    <!--/IF:cond-->
                    <button type="submit" class="btn btn-primary btn-sm text-bold"><i class="fa fa-floppy-o"></i> Lưu </button>
                </div>
            </div>
            <!--IF:admin_cond($admin_tuha or $admin_group)-->
            <div class="box-body bg-gray-light">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="panel">
                            <img src="<?php if(Url::get('image_url')){ echo Url::get('image_url');}?>" onerror="this.src='assets/standard/images/tuha_logo.png?v=03122021'" style="border:1px solid #CCCCCC;width:200px;padding:5px;border-radius: 10px;">
                            <div style="padding: 20px;">Thay ảnh: <input name="image_url" type="file" id="image_url" class="form-control">200x200 pixel (*.jpg, *.jpeg, *.gif)</div>
                            <table class="table">
                                <!--IF:cond($admin_tuha)-->
                                <tr>
                                    <td width="50%" align="right">Loại tài khoản</td>
                                    <td width="50%">
                                        <select name="account_type" id="account_type" class="form-control"></select>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" align="right">Thuộc hệ thống</td>
                                    <td width="50%">
                                        <select name="master_group_id" id="master_group_id" class="form-control"></select>
                                    </td>
                                </tr>
                                <!--ELSE-->
                                <tr>
                                    <td width="50%" align="right">Loại tài khoản</td>
                                    <td width="50%">
                                        <select name="account_type" id="account_type" class="form-control" disabled=""></select>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" align="right">Thuộc hệ thống</td>
                                    <td width="50%">
                                        <select name="master_group_id" id="master_group_id" class="form-control" disabled=""></select>
                                    </td>
                                </tr>
                                <!--/IF:cond-->
                            </table>
                            <br>
                            <!--IF:cond($admin_tuha)-->
                            <div class="panel panel-defaut">
                                <div class="panel-heading">
                                    Ghi chú của admin
                                </div>
                                <div class="panel-body">
                                    <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <!--/IF:cond-->
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="box">
                            <div class="box-body">
                                <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
                                <div id="groupInfoHome" class="tab-pane fade in active">
                                    <table class="table">
                                        <tr>
                                            <td align="right">ID Shop </td>
                                            <td><span style="font-size:16px;font-weight:bold;color:#E9110C"><?php echo Url::get('id')?></span></td>
                                        </tr>
                                        <tr>
                                            <td align="right">
                                                Tên Shop
                                            </td>
                                            <td><input name="name" type="text" id="name" class="form-control" style="font-size:16px;font-weight:bold;color:#336699"></td>
                                        </tr>
                                        <!--IF:cond($admin_tuha)-->
                                        <tr bgcolor="#7fffd4">
                                            <td align="right">
                                                <label for="active">Kích hoạt SHOP</label>
                                            </td>
                                            <td>
                                                <input name="active" type="checkbox" id="active">
                                            </td>
                                        </tr>
                                        <!--/IF:cond-->
                                        <tr>
                                            <td align="right">
                                                <label for="syn_group_name">Đồng bộ tên Work.tuha.vn</label>
                                            </td>
                                            <td>
                                                <input name="syn_group_name" type="checkbox" id="syn_group_name"> (Tích chọn xong nhấn Lưu)
                                            </td>
                                        </tr>
                                        <!--IF:cond($admin_tuha)-->
                                        <tr>
                                            <td align="right">
                                                <label for="is_crm"> Đồng bộ CRM</label>
                                            </td>
                                            <td>
                                                <?php if(Url::get('is_crm') == 2 || Url::get('is_crm') == 3) {?>
                                                    <input name="is_crm" type="checkbox" id="is_crm" disabled="disabled"> (Đang đồng bộ)
                                                <?php }else{ ?>
                                                <input name="is_crm" type="checkbox" id="is_crm"> (Tích chọn xong nhấn Lưu)
                                                <?php } ?>

                                            </td>
                                        </tr>
                                        <!-- <tr>
                                            <td align="right">
                                                <label for="sync_data"> Đồng bộ dữ liệu sang CRM</label>
                                            </td>
                                            <td>
                                                <a class="btn btn-success btn-sm text-bold sync_data" data-id="<?php echo $groupId; ?>"><i class="fa fa-refresh" aria-hidden="true"></i> Đồng Bộ </a>
                                            </td>
                                        </tr> -->
                                        <!--/IF:cond-->
                                        <tr>
                                            <td align="right">Tài khoản sở hữu (owner)</td>
                                            <td>
                                                <!--IF:cond($admin_tuha)-->
                                                <input name="code" type="text" id="code" class="form-control" style="font-size:16px;font-weight:bold;color:#F00">
                                                <!--ELSE-->
                                                <input name="code" type="text" id="code" class="form-control" readonly style="font-size:16px;font-weight:bold;color:#F00">
                                                <!--/IF:cond-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">Thuộc hệ thống</td>
                                            <td>
                                                <!-- <select name="system_group_id" id="system_group_id" class="form-control select2"></select> -->
                                                <?=$this->map['system_group_id']?>
                                            </td>
                                        </tr>
                                        <!--IF:cond(Session::get('admin_group') and Session::get('account_type')==TONG_CONG_TY)-->
                                        <tr>
                                            <td align="right">Kho số</td>
                                            <td>
                                                <select name="phone_store_id" id="phone_store_id" class="form-control"></select>
                                            </td>
                                        </tr>
                                        <!--/IF:cond-->
                                        <tr>
                                            <td align="right">Email</td>
                                            <td><input name="email" type="text" id="email" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <td align="right">Điện thoại</td>
                                            <td><input name="phone" type="text" id="phone" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <td align="right">Địa chỉ</td>
                                            <td><input name="address" type="text" id="address" class="form-control"></td>
                                        </tr>
                                        <tr>
                                            <td align="right">Ngày thành lập <i class="fa fa-star"></i></td>
                                            <td>
                                                <!--IF:cond($admin_group)-->
                                                <div class="input-group date">
                                                    <input name="date_established" type="text" id="date_established" class="form-control">
                                                    <div class="input-group-addon">
                                                        <span class="glyphicon glyphicon-th"></span>
                                                    </div>
                                                </div>
                                                <!--ELSE-->
                                                <span><?php if(!Url::get('date_established') || Url::get('date_established')=='' || Url::get('date_established')=='0000-00-00 00:00:00'){ echo '...';}else{echo date('Y-m-d',strtotime(Url::get('date_established')));} ?></span>
                                                <!--/IF:cond-->
                                                <div class="label label-default">Ngày tạo shop: <?=Url::get('created')?date('Y-m-d',strtotime(Url::get('created'))):''?></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">Ngày hết hạn <i class="fa fa-lock"></i></td>
                                            <td>
                                                <!--IF:cond($admin_tuha)-->
                                                <div class="input-group date">
                                                    <input name="expired_date" type="text" id="expired_date" class="form-control">
                                                    <div class="input-group-addon">
                                                        <span class="glyphicon glyphicon-th"></span>
                                                    </div>
                                                </div>
                                                <!--ELSE-->
                                                <span><?php if(!Url::get('expired_date') || Url::get('expired_date')=='' || Url::get('expired_date')=='0000-00-00 00:00:00'){ echo 'Không có thời hạn';}else{echo date('Y-m-d',strtotime(Url::get('expired_date')));} ?></span>
                                                <!--/IF:cond-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">Tiền tố tạo tài khoản</td>
                                            <td>
                                                <input name="prefix_account" type="text" id="prefix_account" class="form-control">
                                                Ví dụ: HBG.khoand thì "HBG." là tiền tố tạo tài khoản
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">Mã công ty (Mã bưu cục)</td>
                                            <td>
                                                <input name="prefix_post_code" type="text" id="prefix_post_code" class="form-control" placeholder="Sẽ được gắn vào đầu mã vạch của đơn hàng">
                                                <p class="hidden">
                                                    <br>
                                                    <img src="assets/vissale/images/ma_buu_cuc.png" alt="" style="max-width: 100%;">
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">Chỉ cho phép vào từ IP (IP của bạn: <strong style="color:#F00;"><?php echo System::get_client_ip_env();?></strong>)</td>
                                            <td>
                                                <textarea name="allow_ips" id="allow_ips" class="form-control"></textarea>
                                                Ví dụ: 123.456.789.101,123.456.789.102
                                            </td>
                                        </tr>
                                        <tr id="user_ip">
                                            <td align="right">Ngoại trừ các tài khoản sau</td>
                                            <td>
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                      <select multiple="multiple" id="users_ids" name="users_ids[]" class="multiple-select" style="width:100%">
                                                            [[|users_ids_option|]]
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="reset_password">
                                            <td align="right">Số ngày cập nhật mật khẩu / 1 lần
                                                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Người dùng đổi password/1 lần trong vòng n ngày cài đặt (kể từ ngày cuối cùng đổi pass)">
                                                <i class="fa fa-question-circle"></i>
                                            </a></td>
                                            <td>
                                                <div class="form-group row">
                                                    <div class="col-sm-12">
                                                        <JSHELPER id="reset_pass"></JSHELPER>
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="reset_pass_immediate" name="reset_pass_immediate" value="1">
                                                            <label class="custom-control-label" for="reset_pass_immediate">Lập tức yêu cầu nhân viên thay đổi pass</label>
                                                        </div>
                                                        <script>
                                                            JSHELPER.render.select({
                                                                data: {0: 'Không kích hoạt', 7: '7 ngày', 14: '14 ngày', 20: '20 ngày', 30: '30 ngày', 45: '45 ngày', 60: '60 ngày', 90: '90 ngày'},
                                                                selectAttrs: {class: 'form-control', name: "reset_pass", id: 'reset_pass'},
                                                                selected: [<?=intval($this->map['reset_pass_periodic'])?>]
                                                            }).mount('#reset_pass');
                                                        </script>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <!--IF:cond($admin_tuha)-->
                                        <tr>
                                            <td align="right">Số Tài khoản được tạo</td>
                                            <td>
                                                <input name="user_counter" type="text" id="user_counter" class="form-control" readonly="true">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="right">Số Fanpage được đăng ký</td>
                                            <td>
                                                <input name="page_counter" type="text" id="page_counter" class="form-control" readonly="true">
                                            </td>
                                        </tr>
                                        <!--/IF:cond-->
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--IF:cond($admin_tuha or Session::get('account_type')==3)-->
                        <input name="group_id" type="hidden" id="group_id">
                        <!--/IF:cond-->

                        <div class="box box-default box-solid">
                            <div class="box-header">
                                <h4 class="box-title">Tích hợp tổng đài</h4>
                            </div>
                            <div class="box-body">
                                <table class="table">
                                    <tr>
                                        <td align="right" style="width: 40%">
                                            <label for="integrate_callio">Tích hợp Callio</label>
                                            <input name="integrate_callio" type="checkbox" id="integrate_callio">
                                            <script type="text/javascript">
                                                <?php if(Url::get('integrate_callio')){?>
							                    getId('integrate_callio').checked = true;
                                                <?php }?>

							                    const checkbox = document.getElementById('integrate_callio')
							                    checkbox.addEventListener('change', (event) => {
								                    // getId('integrate_voip24h').checked = false;
								                    if (event.currentTarget.checked) {
                                                        <?php if(Url::get('callio_info')){?>
									                    toggle('frm-integrate-callio', 'none');
									                    toggle('lbl-integrate-callio', 'table-row');
                                                        <?php } else {?>
									                    toggle('lbl-integrate-callio', 'none');
									                    toggle('frm-integrate-callio', 'table-row');
                                                        <?php }?>
									                    // toggle('frm-integrate-voip24h', 'none');
									                    // toggle('lbl-integrate-voip24h', 'none');
								                    } else {
									                    toggle('frm-integrate-callio', 'none');
									                    toggle('lbl-integrate-callio', 'none');
								                    }
							                    })
                                            </script>
                                        </td>
                                        <td style="text-align: right">
                                            <!--
                                            <label for="integrate_voip24h">Tích hợp Voip24h</label>
                                            <input name="integrate_voip24h" type="checkbox" id="integrate_voip24h">
                                            <script type="text/javascript">
                                                <?php if(Url::get('integrate_voip24h')){?>
							                    getId('integrate_voip24h').checked = true;
                                                <?php }?>

							                    const checkbox1 = document.getElementById('integrate_voip24h')
							                    checkbox1.addEventListener('change', (event) => {
								                    getId('integrate_callio').checked = false;
								                    if (event.currentTarget.checked) {
                                                        <?php if(Url::get('voip24h_info')){?>
									                    toggle('frm-integrate-voip24h', 'none');
									                    toggle('lbl-integrate-voip24h', 'table-row');
                                                        <?php } else {?>
									                    toggle('lbl-integrate-voip24h', 'none');
									                    toggle('frm-integrate-voip24h', 'table-row');
                                                        <?php }?>
									                    toggle('frm-integrate-callio', 'none');
									                    toggle('lbl-integrate-callio', 'none');
								                    } else {
									                    toggle('frm-integrate-voip24h', 'none');
									                    toggle('lbl-integrate-voip24h', 'none');
								                    }
							                    })
                                            </script>-->
                                        </td>
                                    </tr>
                                    <?php if(Url::get('callio_info')){?>
                                        <tr class="lbl-integrate-callio">
                                            <td align="right">Tên công ty</td>
                                            <td><?= $callio_info->companyName ?></td>
                                        </tr>
                                        <tr class="lbl-integrate-callio">
                                            <td align="right">Email</td>
                                            <td><?= $callio_info->email ?></td>
                                        </tr>
                                        <tr class="lbl-integrate-callio">
                                            <td align="right">Số điện thoại</td>
                                            <td><?= $callio_info->phone ?></td>
                                        </tr>
                                    <?php } else {?>
                                        <tr class="frm-integrate-callio">
                                            <td align="right">Tên công ty</td>
                                            <td>
                                                <input name="callio_name" type="text" id="callio_name" class="form-control">
                                            </td>
                                        </tr>
                                        <tr class="frm-integrate-callio">
                                            <td align="right">Email</td>
                                            <td>
                                                <input name="callio_email" type="email" id="callio_email" class="form-control">
                                            </td>
                                        </tr>
                                        <tr class="frm-integrate-callio">
                                            <td align="right">Số điện thoại</td>
                                            <td>
                                                <input name="callio_phone" type="text" id="callio_phone" class="form-control">
                                            </td>
                                        </tr>
                                        <tr class="frm-integrate-callio">
                                            <td></td>
                                            <td>
                                                <button type="submit" name="integrateBtn" class="btn btn-primary" onclick="return validateForm();">Tích hợp</button>
                                            </td>
                                        </tr>
                                    <?php }?>

                                    <?php if (Url::get('voip24h_info')) {?>
                                        <tr class="lbl-integrate-voip24h">
                                            <td align="right">Domain</td>
                                            <td><?= $voip24h_info->domain ?></td>
                                        </tr>
                                    <?php } else {?>
                                        <tr class="frm-integrate-voip24h">
                                            <td align="right">Domain</td>
                                            <td>
                                                <input name="voip24h_domain" type="text" id="voip24h_domain" class="form-control">
                                            </td>
                                        </tr>
                                        <tr class="frm-integrate-voip24h">
                                            <td></td>
                                            <td>
                                                <button type="submit" name="integrateBtn" class="btn btn-primary" onclick="return validateFormVoip24h();">Tích hợp</button>
                                            </td>
                                        </tr>
                                    <?php }?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box box-default box-solid">
                            <div class="box-header">
                                <h4 class="box-title">Tuỳ chỉnh đơn hàng</h4>
                            </div>
                            <div class="box-body">
                                <table class="table">
                                    <tr>
                                        <td>
                                            Kích hoạt kết nối vận chuyển:
                                        </td>
                                        <td>
                                            <input name="integrate_shipping" type="checkbox" id="integrate_shipping">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Cộng phí vận chuyển vào tổng tiền đơn hàng:
                                        </td>
                                        <td>
                                            <select name="add_deliver_order" id="add_deliver_order" class="form-control"></select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-right">
                                            <a href="<?=Url::build('shipping-option');?>" class="btn btn-default btn-sm" style="width: 100%">
                                                <i class="fa fa-truck"></i> Quản lý đơn vị vận chuyển
                                            </a>
                                        </td>
                                    </tr>
                                    <!-- <tr>
                                        <td>
                                            Xác nhận yêu cầu nhập tỉnh thành, quận huyện, phường xã:
                                            <span class="label label-success">* Mới</span>
                                        </td>
                                        <td>
                                            <input name="require_address" type="checkbox" id="require_address">
                                            <script type="text/javascript">
                                                <?php if(Url::get('require_address')){?>
                                                getId('require_address').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr> -->
                                    <tr>
                                        <td width="55%" align="right">Hiển thị <strong>Họ Và Tên</strong> thay cho <strong>Tên tài khoản</strong></td>
                                        <td width="45%">
                                            <input name="show_full_name" type="checkbox" id="show_full_name">
                                            <script type="text/javascript">
                                                <?php if(Url::get('show_full_name')){?>
                                                getId('show_full_name').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="55%" align="right">Ẩn <strong>Tổng tiền</strong> đơn hàng với MKT</td>
                                        <td width="45%">
                                            <input name="hide_total_amount" type="checkbox" id="hide_total_amount">
                                            <script type="text/javascript">
                                                <?php if(Url::get('hide_total_amount')){?>
                                                getId('hide_total_amount').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                    <!--IF:cond($isOwner || $admin_tuha)-->
                                    <tr>
                                        <td width="55%" align="right">Hiện <strong>Số điện thoại</strong> xuất excel đơn hàng</td>
                                        <td width="45%">
                                            <input name="show_phone_number_excel_order" type="checkbox" id="show_phone_number_excel_order">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="55%" align="right">Hiện <strong>Số điện thoại</strong> in đơn hàng</td>
                                        <td width="45%">
                                            <input name="show_phone_number_print_order" type="checkbox" id="show_phone_number_print_order">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="55%" align="right">Hiện <strong>Số điện thoại</strong> tại popup sửa nhanh đơn hàng với các quyền bung đơn</td>
                                        <td width="45%">
                                            <input name="hien_sdt_bung_don" type="checkbox" id="hien_sdt_bung_don">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="55%" align="right">Hiện <strong>tên sản phẩm đầy đủ</strong> khi xuất excel đơn hàng</td>
                                        <td width="45%">
                                            <input name="show_full_name_export_excel_order" type="checkbox" id="show_full_name_export_excel_order">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="55%" align="right"><strong>Chụp ảnh nhân viên đang sử dụng </strong><br>(Lưu ý: Khi bật tính năng này, nhân viên phải cho phép phần mềm truy cập camera thì mới được sử dụng)</td>
                                        <td width="45%">
                                            <?php if(isset($_REQUEST['chup_anh_nhan_vien']) && $_REQUEST['chup_anh_nhan_vien'] == 1): ?>
                                                <input name="chup_anh_nhan_vien" type="checkbox" id="chup_anh_nhan_vien" checked>
                                            <?php else : ?>
                                                <input name="chup_anh_nhan_vien" type="checkbox" id="chup_anh_nhan_vien">
                                            <?php  endif; ?>
                                        </td>
                                    </tr>
                                    <!--/IF:cond-->
                                    <tr>
                                        <td width="55%" align="right"><strong>Hiện lịch sử đơn hàng</strong> tại popup sửa nhanh đơn hàng</td>
                                        <td width="45%">
                                            <input name="show_history_order" type="checkbox" id="show_history_order">
                                            <script type="text/javascript">
                                                <?php if(!Url::get('show_history_order')){?>
                                                getId('show_history_order').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                    <tr class="text-warning text-bold">
                                        <td align="right">Ẩn số điện thoại</td>
                                        <td>
                                            <select name="hide_phone_number" id="hide_phone_number" class="form-control"></select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Tìm số điện thoại tối thiểu</td>
                                        <td>
                                            <select name="min_search_phone_number" id="min_search_phone_number" class="form-control"></select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Sắp xếp đơn hàng mặc định ưu tiên theo</td>
                                        <td>
                                            <select name="default_sort_of_order_list" id="default_sort_of_order_list" class="form-control"></select>
                                        </td>
                                    </tr>
                                    <tr class="bg-red">
                                        <td align="right">Trùng đơn theo phân loại sản phẩm</td>
                                        <td>
                                            <select name="duplicate_type" id="duplicate_type" class="form-control" disabled></select>
                                        </td>
                                    </tr>
                                    <tr class="bg-red">
                                        <td align="right">Không cho phép tạo đơn mới khi trùng số</td>
                                        <td>
                                            <input name="no_create_order_when_duplicated" type="checkbox" id="no_create_order_when_duplicated" disabled>
                                            <script type="text/javascript">
                                                <?php if(Url::get('no_create_order_when_duplicated')){?>
                                                getId('no_create_order_when_duplicated').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Quyền sửa đơn hàng</td>
                                        <td>
                                            <select name="users_can_edit_order" id="users_can_edit_order" class="form-control"></select>
                                        </td>
                                    </tr>
                                    <tr class="text-warning">
                                        <td align="right">SALE có thể tự chia đơn cho chính mình</td>
                                        <td>
                                            <select name="sale_can_self_assigned" id="sale_can_self_assigned" class="form-control"></select>
                                        </td>
                                    </tr>
                                    <tr class="text-warning">
                                        <td align="right">SALE được gán người tạo đơn</td>
                                        <td>
                                            <select name="sale_can_assigned_created_user" id="sale_can_assigned_created_user" class="form-control"></select>
                                        </td>
                                    </tr>
                                    <tr class="text-danger">
                                        <td align="right">Tự động làm mới danh sách đơn hàng</td>
                                        <td>
                                            <select name="time_to_refesh_order" id="time_to_refesh_order" class="form-control"></select>
                                        </td>
                                    </tr>
                                    <?php if($this->admin_tuha): ?>
                                    <tr>
                                        <td align="right"><strong>Form MEDIDOC</strong></td>
                                        <td>
                                            <select class="form-control" name="form_medidoc[]" id="form_medidoc" multiple style="display: none;">
                                                <option value="">Chọn Form</option>
                                                <option value="TANG_CHIEU_CAO">Tăng chiều cao - KT</option>
                                                <option value="TANG_CHIEU_CAO_NEW">Tăng chiều cao</option>
                                                <option value="TRI_MAT_NGU">Trị mất ngủ</option>
                                                <option value="GIAM_CAN">Giảm cân</option>
                                                <option value="MO_HOI">SP Mồ hôi</option>
                                                <option value="TIEU_DUONG_MO_MAU">Tiểu đường, mỡ máu</option>
                                                <option value="SP_TOC">SP về tóc</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endif;?>
                                </table>
                            </div>
                        </div>
                        <div class="box box-default box-solid hidden">
                            <div class="box-header">
                                <h4 class="box-title">Kích hoạt POS (Có thể dùng cho bán hàng offline)</h4>
                            </div>
                            <div class="box-body">
                                <table class="table">
                                    <tr>
                                        <td align="right">

                                        </td>
                                        <td>
                                            <select name="enable_pos" id="enable_pos" class="form-control"></select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="box box-default box-solid">
                            <div class="box-header">
                                <h4 class="box-title">Tuỳ chỉnh sản phẩm</h4>
                            </div>
                            <div class="box-body">
                                <table class="table">
                                    <tr>
                                        <td align="right">Đánh giá sản phẩm</td>
                                        <td>
                                            <input name="enable_product_rating" type="checkbox" id="enable_product_rating">
                                            <script type="text/javascript">
                                                <?php if(Url::get('enable_product_rating')){?>
                                                getId('enable_product_rating').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Hiển thị đầy đủ thông tin sản phẩm (Mã SP, Mầu, Size) khi hiển thị ở danh sách và excel</td>
                                        <td>
                                            <input name="show_product_detail" type="checkbox" id="show_product_detail">
                                            <script type="text/javascript">
                                                <?php if(Url::get('show_product_detail')){?>
                                                getId('show_product_detail').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="box box-default box-solid">
                            <div class="box-header">
                                <h4 class="box-title">Tuỳ chỉnh kho</h4>
                            </div>
                            <div class="box-body">
                                <table class="table">
                                    <tr>
                                        <td align="right">Không cho xuất âm</td>
                                        <td>
                                            <input name="disable_negative_export" type="checkbox" id="disable_negative_export">
                                            <script type="text/javascript">
                                                <?php if(Url::get('disable_negative_export')){?>
                                                getId('disable_negative_export').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Hiển thị giá bán trên phiếu xuất kho</td>
                                        <td>
                                            <input name="show_price_in_export_invoice" type="checkbox" id="show_price_in_export_invoice">
                                            <script type="text/javascript">
                                                <?php if(Url::get('show_price_in_export_invoice')){?>
                                                getId('show_price_in_export_invoice').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Tạo phiếu xuất kho khi đơn xác nhận</td>
                                        <td>
                                            <input name="create_export_invoice_when_confirmed" type="checkbox" id="create_export_invoice_when_confirmed" onclick="if(this.checked){getId('create_export_invoice_when_delivered').checked = false;}">
                                            <script type="text/javascript">
                                                <?php if(Url::get('create_export_invoice_when_confirmed')){?>
                                                getId('create_export_invoice_when_confirmed').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Tạo phiếu xuất kho khi đơn chuyển hàng</td>
                                        <td>
                                            <input name="create_export_invoice_when_delivered" type="checkbox" id="create_export_invoice_when_delivered" onclick="if(this.checked){getId('create_export_invoice_when_confirmed').checked = false;}">
                                            <script type="text/javascript">
                                                <?php if(Url::get('create_export_invoice_when_delivered')){?>
                                                getId('create_export_invoice_when_delivered').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="right">Tạo phiếu nhập kho đơn Đã trả hàng về kho</td>
                                        <td>

                                            <input name="create_import_invoice_when_return" type="checkbox" id="create_import_invoice_when_return">
                                            <script type="text/javascript">
                                                <?php if(Url::get('create_import_invoice_when_return')){?>
                                                getId('create_import_invoice_when_return').checked = true;
                                                <?php }?>
                                            </script>
                                        </td>
                                    </tr>
                                </table>
                                <div class="alert alert-warning-custom">Chú ý: chỉ chọn được một trong 2 tùy chọn xuất kho khi xác nhận hoặc chuyển hàng.</div>
                            </div>
                        </div>
                        <fieldset id="toolbar" class="hidden">
                            <table class="table">
                                <tr>
                                    <td width="50%" align="right">Mô hình kinh doanh</td>
                                    <td width="50%">
                                        <select name="business_model" id="business_model" class="form-control"></select>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                </div>
            </div>
            <!--/IF:admin_cond-->
            <!--IF:mkt_cond($admin_mkt)-->
            <div class="row">
                <div class="col-md-6">
                    <div id="groupApi" class="box box-default">
                        <div class="box-header">
                            <h3 class="title">API kết nối</h3>
                            <a name="groupAPI"></a>
                        </div>
                        <div class="box-body">
                            <div class="form-wrapper form-labels-120">
                                <div class="form-group">
                                    <label class="form-label control-label">Tên kết nối</label>
                                    <div class="form-wrap">
                                        <input name="api_name" type="text" id="api_name" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label control-label">Client ID</label>
                                    <div class="form-wrap">
                                        <input type="text" class="form-control" placeholder="Hệ thống tự động sinh mã" disabled="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label control-label">Mã bảo mật</label>
                                    <div class="form-wrap">
                                        <input name="api_key" type="text" id="api_key" class="form-control" placeholder="Click vào tạo mã" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label control-label">Link API <span style="font-weight: normal;"><i>(Thông tin API vui lòng điền đầy đủ mã token và ID phân loại sản phẩm)</i></span></label>
                                    <div class="form-wrap">
                                        <textarea id="link_api" name="link_api" readonly="" class="form-control" cols="3" rows="4"><?php echo $linkApi??''; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <div class="pull-left">
                                        <a class="btn btn-default" href="https://documenter.getpostman.com/view/2811666/RzfniRMM#3645091c-e6cd-4775-86cf-bc4bbc6a7ef2" target="_blank">
                                            <i class="fa fa-question-circle"></i> Hướng dẫn sử dụng
                                        </a>
                                    </div>
                                    <label>
                                        Kích hoạt
                                        <input  name="api_is_active" type="checkbox" id="api_is_active" />
                                        <!--IF:cond(Url::get('api_is_active'))-->
                                        <script>
                                            getId('api_is_active').checked = true;
                                        </script>
                                        <!--/IF:cond-->
                                    </label>
                                    <a href="#" onclick="genarateApiKey();return false;" class="btn btn-default">Tạo mã</a>
                                    <a href="#" onclick="UpdateApiKey();return false;" class="btn btn-primary">Lưu</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <a name="marketing"></a>
                    <div class="box box-default box-solid">
                        <div class="box-header">
                            <h4 class="box-title">Tuỳ chỉnh Marketing</h4>
                        </div>
                        <div class="box-body">
                            <table class="table">
                                <tr class="alert-danger">
                                    <td align="right">Chi phí/doanh thu nguy hiểm (VD: 35%)</td>
                                    <td>
                                        <select  name="mkt_cost_per_revenue_danger" id="mkt_cost_per_revenue_danger" class="form-control">
                                            <option value="">Chọn</option>
                                            <?php for($i=1;$i<=100;$i++){?>
                                                <option value="<?=$i?>"><?=$i?>%</option>
                                            <?php }?>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="alert-warning">
                                    <td align="right">Chi phí/doanh thu cảnh báo (VD: 30%)</td>
                                    <td>
                                        <select  name="mkt_cost_per_revenue_warning" id="mkt_cost_per_revenue_warning" class="form-control">
                                            <option value="">Chọn</option>
                                            <?php for($i=1;$i<=100;$i++){?>
                                                <option value="<?=$i?>"><?=$i?>%</option>
                                            <?php }?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">Chọn khung giờ Khai báo tiền QC</td>
                                    <td width="50%">
                                        <select class="js-example-placeholder-multiple js-states form-control" name="choose_time_declare_advertising_money[]" multiple="multiple" <?= (defined('CPQC_FOLLOW') && CPQC_FOLLOW === 2) ? 'disabled' : '' ?>>
                                            <?php foreach ($timeSlot as $key => $value) { ?>
                                                <?php  if(in_array($value, $advValue)) :?>
                                                    <option value="<?php echo $value; ?>" selected><?php echo $value; ?></option>
                                                <?php else: ?>
                                                     <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                                <?php endif; ?>
                                            <?php  } ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <script type="text/javascript">
                                <?php if(Url::get('mkt_cost_per_revenue_danger')){?>
                                getId('mkt_cost_per_revenue_danger').value = <?=Url::get('mkt_cost_per_revenue_danger')?>;
                                <?php }?>
                                <?php if(Url::get('mkt_cost_per_revenue_warning')){?>
                                getId('mkt_cost_per_revenue_warning').value = <?=Url::get('mkt_cost_per_revenue_warning')?>;
                                <?php }?>
                            </script>
                        </div>
                    </div>
                </div>
            </div>
            <!--/IF:mkt_cond-->
        </div>
    </form>
</div>
<script>
    $(document).ready(function(){
        $body = $("body");
        $('.sync_data').on('click',function(){
            $body.addClass("loading");
            var groupId = $(this).data('id');
            $.ajax({
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                type: "POST",
                cache: false,
                data : {
                    'do':'sync_data_crm',
                    'groupId': groupId
                },
                success: function(data){
                    if (data === 'FALSE') {
                        alert('SHOP chưa tích chọn đồng bộ CRM hoặc SHOP không tồn tại !')
                        $body.removeClass("loading");
                        return false;
                    }
                    if (data.indexOf('TRUE') > -1)
                    {
                        $body.removeClass("loading");
                        alert('Đồng bộ thành công');
                    } else {
                        $body.removeClass("loading");
                        alert('Đồng bộ thất bại');
                    }
                }
            });
        });
        $('#is_crm').on('click',function(){
            if($("#is_crm").is(':checked')){
                $(this).val(1);
              }else{
                $(this).val(0);
              }
        });
        <!--IF:cond(Url::get('is_crm'))-->
          $('#is_crm').attr('checked',true);
          $('#is_crm').val(<?php echo Url::get('is_crm') ?>);
          <!--/IF:cond-->
    })
    $(function(){
        $('#form_medidoc').multiselect({
            buttonWidth: '150px',
            maxHeight: 200,
            onChange: function(option, checked) {
                if(option.val()){
                    return;
                }
 
                return $('#form_medidoc').multiselect(!checked ? 'deselectAll' : 'selectAll', false)
            }
        })
        // Mặc định
        .multiselect('select', <?=json_encode(URL::getArray('form_medidoc', [0]))?>);
    })
  $(document).ready(function(e) {
    
     $(document).ready(function(){
        $('.multiple-select').multipleSelect();

        $('.select2').select2({
            dropdownAutoWidth : true,
        });

        $(".js-example-placeholder-multiple").select2({
            placeholder: "Chọn tất cả"
        });
        $('.multiple-select ul li.ms-select-all span').html('Chọn tất cả');
        if(checkAllows == 1){
            $('#user_ip').show();
        } else {
            $('#user_ip').hide();
        }
        $('#allow_ips').change(function(){
            var content = $(this).val();
            if(!content){
                $('#user_ip').hide();
            } else {
                $('#user_ip').show();
            }
        })
    })
     if(checkIsAdmin == 0){
            $('#system_group_id').attr({'disabled':true});
        }
      $('#date_established').datetimepicker({
          format: 'YYYY-MM-DD'
      });
      $('#expired_date').datetimepicker({
          format: 'YYYY-MM-DD'
      });
      <!--IF:cond(Url::get('active'))-->
      $('#active').attr('checked',true);
      <!--/IF:cond-->
      <!--IF:cond(Url::get('integrate_shipping'))-->
      $('#integrate_shipping').attr('checked',true);
      <!--/IF:cond-->
      <!--IF:cond(Url::get('show_phone_number_excel_order'))-->
      $('#show_phone_number_excel_order').attr('checked',true);
      <!--/IF:cond-->
      <!--IF:cond(Url::get('show_phone_number_print_order'))-->
      $('#show_phone_number_print_order').attr('checked',true);
      <!--/IF:cond-->
      <!--IF:cond(Url::get('show_full_name_export_excel_order'))-->
      $('#show_full_name_export_excel_order').attr('checked',true);
      <!--/IF:cond-->
      <!--IF:cond(Url::get('hien_sdt_bung_don'))-->
      $('#hien_sdt_bung_don').attr('checked',true);
      <!--/IF:cond-->
      $('#master_group_id').val(<?php echo Url::iget('master_group_id')?>);
  });
  function genarateApiKey(){
      $.ajax({
          method: "POST",
          url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
          data : {
              'do':'generate_api_key'
          },
          beforeSend: function(){
          },
          success: function(content){
              content = content.trim();
              $('#api_key').val(content);
          }
      });
  }
  function UpdateApiKey(){
    if(!$('#api_key').val()){
        alert('Bạn vui lòng nhấn tạo mã!');
    }else{
        console.log($('#api_is_active').is(':checked'));
        $.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : {
                'do':'update_api',
                'api_key':$('#api_key').val(),
                'api_name':$('#api_name').val(),
                'api_is_active':$('#api_is_active').is(':checked')?1:0
            },
            beforeSend: function(){
            },
            success: function(content){
                if (content) {
                    alert('Tạo thành công!')
                    $('#link_api').val(content)
                }
            }
        });
    }
  }
  function CheckInput(){
      let dateEstablished = $('#date_established').val();
      if(dateEstablished == ''){
        alert('Bạn vui lòng nhập ngày thành lập shop!');
        $('#date_established').focus();
        return false;
      }
      let email = $('#email').val();
      if(email != '' && !validateEmail(email)) {
          alert('Bạn vui lòng xem lại email đã nhập!');
          return false;
      }
      return true;
  }
    function toggle(className, displayState){
	    var elements = document.getElementsByClassName(className)
	    for (var i = 0; i < elements.length; i++){
		    elements[i].style.display = displayState;
	    }
    }
    function validateForm() {
	    let callio_name = document.forms["EditUser"]["callio_name"].value;
	    let callio_email = document.forms["EditUser"]["callio_email"].value;
	    let callio_phone = document.forms["EditUser"]["callio_phone"].value;
	    if (callio_name == "") {
		    alert("Yêu cầu nhập tên công ty");
		    document.getElementById("callio_name").focus();
		    return false;
	    }
	    if (callio_email == "") {
		    alert("Yêu cầu nhập email");
		    document.getElementById("callio_email").focus();
		    return false;
	    }
	    if (callio_phone == "") {
		    alert("Yêu cầu nhập số điện thoại");
		    document.getElementById("callio_phone").focus();
		    return false;
	    }
    }
    function validateFormVoip24h() {
	    let voip24h_domain = document.forms["EditUser"]["voip24h_domain"].value;
	    if (voip24h_domain == "") {
		    alert("Yêu cầu nhập domain/ip");
		    document.getElementById("voip24h_domain").focus();
		    return false;
	    }
    }

    const validateEmail = (email) => {
        return String(email)
        .toLowerCase()
        .match(
        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
    };
</script>
