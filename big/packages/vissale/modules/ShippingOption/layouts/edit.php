<style type="text/css">
    .multi-item-group.del {
        background: #CCC;
    }
    .multi-item-group.del input,.multi-item-group.del select {
        background: #CCC;
    }
    .text-right {
        text-align: right;
    }
    .alert-warning-custom {
        color: rgb(138, 109, 59) !important;
        background-color: rgb(252, 248, 227) !important;
        border-color: rgb(138, 109, 59);
        margin-top: 10px;
    }
    .alert-warning-custom a {
        color: #3c8dbc
    }
    .wrapper {
        overflow-x: auto;
    }
</style>
<script src="packages/core/includes/js/multi_items.js"></script>
<div style="display:none">
	<div id="mi_product_sample">
        <div id="input_group_#xxxx#" class="multi-item-group" style="width: 1305px;overflow-x: auto;">
            <span class="multi-edit-input" style="width:40px;"><input  name="mi_product[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input" style="width:40px;text-align:right;" value="(auto)" tabindex="-1" readonly></span>
            <span class="multi-edit-input"><select  name="mi_product[#xxxx#][carrier_id]" style="width:150px;" class="multi-edit-text-input" id="carrier_id_#xxxx#" tabindex="-1">[[|carrier_options|]]</select></span>
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][name]" style="width:150px;" placeholder="VD: BDHN1" class="multi-edit-text-input" type="text" id="name_#xxxx#" tabindex="#xxxx#"></span>

            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][shop_name]" style="width:150px;" placeholder="VD: tuha shop" class="multi-edit-text-input" type="text" id="shop_name_#xxxx#" tabindex="#xxxx#"></span>
            <!--IF:cond([[=is_owner=]])-->
            <span class="multi-edit-input"><input  name="mi_product[#xxxx#][carrier_email]" style="width:150px;" placeholder="VD: hnpt@hn.vnn.vn" class="multi-edit-text-input" type="text" id="carrier_email_#xxxx#" tabindex="#xxxx#"></span>
            <!--/IF:cond-->

            <span class="multi-edit-input"  style="width:150px;"><input  name="mi_product[#xxxx#][client_id]" class="multi-edit-text-input" type="text" id="client_id_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input" style="width:150px;"><input  name="mi_product[#xxxx#][token]" class="multi-edit-text-input" type="text" id="token_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input" style="width:130px;"><input  name="mi_product[#xxxx#][barcode_prefix]" class="multi-edit-text-input" type="text" id="barcode_prefix_#xxxx#" tabindex="#xxxx#" placeholder="VD: ghtk"></span>
            <span class="multi-edit-input" style="width:125px;text-align:center;padding-top:5px;"><input  name="mi_product[#xxxx#][is_default]" type="checkbox" id="is_default_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input" style="width:50px;text-align:center;padding-top:5px;"><input  name="mi_product[#xxxx#][del]" type="checkbox" id="del_#xxxx#" tabindex="#xxxx#"></span>
            <span class="multi-edit-input" id="delete_#xxxx#" style="width:40px;text-align:center;padding-top:5px;"><button type="button" class="btn btn-danger btn-sm" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_product','#xxxx#','');return false;" alt="Xóa"><i class="fa fa-trash-o"></i></button></span>
        </div>
	</div>
</div>
<?php
    $title = 'Quản lý hãng vận chuyển';
?>
<section class="content">
<div class="row">
<fieldset style="position: sticky;
        top: 0;
        z-index: 999;
        width: 100%;" id="toolbar">
 	<div class="col-xs-8">
		<h3 class="title"><i class="fa fa-truck"></i> <?php echo $title;?></h3>
	</div>
    <div class="col-xs-4 text-right">
        <a onclick="if(checkValidate()){EditAdminProductsForm.submit();}" class="btn btn-primary">Lưu lại</a>
    </div>
</fieldset>
<br>
<fieldset id="toolbar">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Diễn giải</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div>- <strong>NVC</strong>: Nhà vận chuyển.</div>
            <div>- <strong>Tên</strong>: Để dễ nhận biết khi lựa chọn.</div>
            <div>- <strong>Id tra cứu</strong>: Là ID tra cứu (Đối với bưu điện Hà Nội), là ClientID (Đối với giao hàng nhanh (GHN)). Giao hàng tiết kiệm (GHTK), là Username (Đối với BEST).</div>
            <div>- <strong>Mã xác thực</strong>:</div>
            <div>  - Là Mã xác thực (Đối với bưu điện Hà Nội).</div>
            <div>  - Là customerid do J&T cung cấp (Đối với J&T).</div>
            <div>  - Là mật khẩu do BEST cung cấp (Đối với BEST).</div>
            <div>  - Là API key - Token (Đối với GHN). <span class="text-danger">Đường dẫn đăng nhập để lấy ClientID, API key bên GHN: <a href="https://sso.ghn.vn/ssoLogin?app=apiv3" target="_blank">https://sso.ghn.vn/ssoLogin?app=apiv3</a>.</span></div>
            <div>  - Là API Token Key do (Đối với GHTK). <span class="text-danger">Đường dẫn đăng nhập để lấy API Token Key bên GHTK: <a href="https://khachhang.giaohangtietkiem.vn/khach-hang/dang_nhap" target="_blank">https://khachhang.giaohangtietkiem.vn/khach-hang/dang_nhap</a>.</span>
                Sau khi đăng nhập, quý khách click vào <a href="https://khachhang.giaohangtietkiem.vn/khach-hang/thong-tin-ca-nhan" target="_blank">Sửa thông tin cửa hàng</a>, copy mã API Token Key.
            </div>
            <div>-  Để lấy mã xác thực bên <b>Viettel Post</b>, quý khách click vào <a href="index062019.php?page=shipping-option&cmd=viettel_post" target="_blank">link sau</a>, nhập vào <b>tài khoản Viettel Post</b>, rồi click <b>Lấy mã xác thực</b>. Nếu quý khách chưa có tài khoản trên Viettel Post, vui lòng click vào <a href="index062019.php?page=shipping-option&cmd=register_viettel_post" target="_blank">link sau</a> để <b>đăng ký</b> và lấy <b>mã xác thực</b>.</div>
            <div>- <strong>Tiền tố mã vạch</strong>: Sử dụng để in mã vạch. VD: SP06.GHTK0112121 (có thể bỏ qua).</div>
            <div>- <strong>Ẩn</strong>: Không sử dụng hãng vận chuyển này.</div>
            <div class="alert alert-warning alert-warning-custom">
                <div><b>TUHA mới tích hợp thêm đơn vị vận chuyển EMS.</b> Để tích hợp đơn vị vận chuyển này, quý khách cần thực hiện như sau:</div>
                <div><b>B1. Lấy mã xác thực:</b>
                    <div> - Quý khách đăng nhập tài khoản EMS theo đường dẫn sau <a href="https://bill.ems.com.vn/login" target="_blank">https://bill.ems.com.vn/</a>. Tại menu <b>Cấu hình</b>,
                    chọn <b>Api Key</b>, tạo 1 <b>API KEY</b> mới, copy mã <b>API KEY</b>.</div>
                </div>
                <div>
                    <b>B2. Cấu hình đường dẫn nhận những thay đổi trạng thái đơn hàng:</b>
                    <div>
                         - Sau khi <a href="https://bill.ems.com.vn/login" target="_blank">đăng nhập</a> trang EMS. Tại menu <b>Cấu hình</b>,
                    chọn <b>Webhook</b>, tạo 1 <b>Webhook</b> mới. Tại vị trí ô <b>Callback Url</b>
                    nhập link sau: <a href="https://tuha.vn/work-auth/shipping.php?cmd=ems_callback&hash=85a7ada570bc8d6cd9fa01f2248f5574" target="_blank">
                        https://tuha.vn/work-auth/shipping.php?cmd=ems_callback&hash=85a7ada570bc8d6cd9fa01f2248f5574
                    </a>.
                    </div>
                </div>
                <div>
                    <b>Bước 3.</b> Đồng bộ dữ liệu <a href="/index062019.php?page=admin-shipping-address" target="_blank">địa chỉ lấy hàng bên</a> <b>TUHA</b> và <b>EMS</b>:
                    <div>- Sửa địa chỉ lấy hàng, click nút <b>Tạo kho hàng trên EMS</b>.</div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->

        <!-- box-footer -->
    </div>
    <!-- /.box -->
<form  name="EditAdminProductsForm" method="post">
    <div class="tab-content">
        <div class="tab-pane fade in active">
            <table class="table">
                <tr valign="top">
                    <td>
                        <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
                        <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
                        <table width="100%" cellpadding="5" cellspacing="0">
                            <?php if(Form::$current->is_error())
                            {
                                ?><tr valign="top">
                                <td><?php echo Form::$current->error_messages();?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr valign="top">
                                <td>
                                    <div class="multi-item-wrapper form-group">
                                        <div id="mi_product_all_elems">
                                            <div style="width: 1305px;">
                                                <span class="multi-edit-input header" style="width:40px;height: 50px;">ID</span>
                                                <span class="multi-edit-input header" style="width:152px;height: 50px;">Hãng vận chuyển</span>
                                                <span class="multi-edit-input header" style="width:152px;height: 50px;">Tên</span>

                                                <span class="multi-edit-input header" style="width:152px;height: 50px;">Tên shop</span>
                                                <!--IF:cond([[=is_owner=]])-->
                                                <span class="multi-edit-input header" style="width:152px;height: 50px;">Email</span>
                                                <!--/IF:cond-->

                                                <span class="multi-edit-input header" style="width:150px;height: 50px;">Id tra cứu (NVC cung cấp)</span>
                                                <span class="multi-edit-input header" style="width:150px;height: 50px;">Mã xác thực (NVC cung cấp)</span>
                                                <span class="multi-edit-input header" style="width:130px;height: 50px;">Tiền tố mã vạch</span>
                                                <span class="multi-edit-input header" style="width: 125px;height: 50px;">Đặt làm mặc định</span>
                                                <span class="multi-edit-input header" style="width: 50px;height: 50px;">Ẩn</span>
                                                <span class="multi-edit-input header" style="width:45px;height: 50px;">Xóa</span>
                                                <br clear="all">
                                            </div>
                                        </div>
                                    </div>
                                    <br clear="all">
                                    <div><input type="button" value="+ Thêm mới" class="btn btn-warning btn-sm" onclick="mi_add_new_row('mi_product');"> (Sau khi thêm bạn nhớ nhấn nút Lưu lại)</div>
                                    <div class="alert alert-warning alert-warning-custom">
                                        Chú ý:
                                        <div>1. Với Bưu Điện Hà Nội bạn bắt buộc phải nhập thông tin tài khoản vận chuyển do BĐHN cung cấp.</div>
                                        <div>2. Với GHN và GHTK:</div>
                                        <div>- Nếu bạn chưa có tài khoản thì hệ thống sẽ sử dụng tài khoản mặc Định của Tuha.</div>
                                        <div>- Nếu bạn đã có tài khoản do GHN hoặc GHTK cung cấp thì vui lòng nhấn vào Thêm mới để khai báo tài khoản của bạn đã được đơn vị vận chuyển cung cấp.</div>
                                        <div>3. Với BEST, hệ thống sẽ sử dụng tài khoản mặc định của Tuha.</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <input  name="do" type="hidden" value="<?php echo Url::get('do');?>" />
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>
</fieldset>
</div>
</section>
<script src="http://shared_cookie_iframe.com:3001/xdomain_cookie.dev.js"></script>
<script>
	var xd_cookie = xDomainCookie( 'http://shared_cookie_iframe.com');
	xd_cookie.get( 'test_cookie', function(cookie_val){
		//cookie val will contain value of cookie as fetched from local val (if present) else from iframe (if set), else null
		if(!cookie_val){
            console.log(cookie_val);
			var new_val = cookie_val;
			xd_cookie.set( 'test_cookie', new_val );
		}
	});
</script>
<script>
mi_init_rows('mi_product',<?php if(isset($_REQUEST['mi_product'])){echo MiString::array2js($_REQUEST['mi_product']);}else{echo '[]';}?>);
for(var i=100;i<=input_count;i++){
    if(getId('total_order_'+i) && getId('total_order_'+i).value>0){
        getId('delete_'+i).innerHTML = 'x';
    }
    if(getId('del_'+i) && getId('del_'+i).checked){
        jQuery('#input_group_'+i).addClass('multi-item-group del');
    }
}
function checkValidate(){
    for (var i=100;i<=input_count;i++) {
        if (getId('carrier_id_'+i) && !getId('carrier_id_'+i).value) {
            alert('Bạn vui lòng chọn hãng vận chuyển');
            getId('carrier_id_'+i).focus();

            return false;
        }

        if(getId('name_'+i) && !getId('name_'+i).value){
          if (getId('carrier_id_'+i).value != 'api_ghn' && getId('carrier_id_'+i).value != 'api_best' && getId('carrier_id_'+i).value != 'api_bdvn') {
            alert('Bạn vui lòng nhập tên');
            getId('name_'+i).focus();

            return false;
          }
        }

        // if(getId('token_'+i) && !getId('token_'+i).value){
        //   if (getId('carrier_id_'+i).value != 'api_ghn' && getId('carrier_id_'+i).value != 'api_best' && getId('carrier_id_'+i).value != 'api_bdvn') {
        //     alert('Bạn vui lòng nhập mã token');
        //     getId('token_'+i).focus();

        //     return false;
        //   }
        // }
    }

    return true;
}
jQuery(document).ready(function(){
        //////////
        //suggestCustomer();
        $(document).keypress(function(e) {
            if(e.which == 13) {
              mi_add_new_row('mi_product');
              return false;
            }
        });
    }
);
function offProduct(index){
    if(getId('del_'+index)){

    }
}
</script>
