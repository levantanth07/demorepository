<style>
    body {
        font-family: Arial;
    }
    #toolbar-title {
        padding: 15px 10px;
        border-bottom: 1px solid #ddd
    }
    #toolbar-title h1 {
        font-size: 25px;
        line-height: normal;
        margin: 0px;
        font-family: Arial;
    }
    .clear-fix:after {
        content: "";
        clear: both;
        display: block;
    }
    .float-left {
        float: left;
    }
    .float-right {
        float: right
    }
    #box-shipping-address {
        margin-top: 30px;
        padding: 10px 20px;
    }
    .mb-5 {
        margin-bottom: 5px;
    }
    table tr.info th {
        background-color: #d9edf7;
    }
    @media screen and (min-width: 768px) {
        .modal-dialog {
            width: 768px;
            margin: 30px auto;
        }
    }
    .loader {
        border: 5px solid #f3f3f3;
        -webkit-animation: spin 1s linear infinite;
        animation: spin 1s linear infinite;
        border-top: 5px solid #555;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        position: absolute;
        top: 45%;
        left: 50%;
    }
    #loading {
        position: fixed;
        top: 0px;
        left: 0px;
        width: 100%;
        height:100%;
        z-index: 2000;
        background:rgba(255,255,255,.5) no-repeat center center;
        text-align:center;
        display: none;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .panel-body {
        clear: both;
    }
    .panel-heading {
        height: auto;
        line-height: normal;
        padding: 10px;
    }
    h3.address-panel-title {
        font-size: 20px;
        margin: 0px;
        font-weight: 600;
        padding-left: 10px;
    }
    #toolbar-title {
        background: #f5f5f5;
    }
    #toolbar {
        padding: 0px;
    }
    .clearfix:after {
        content: "",
        clear: "both",
        display: "block"
    }
</style>
<?php
    // $items_address = [[=items_address=]]
?>
<br>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h3 class="title float-left">ViettelPost - Đăng ký & Lấy mã xác thực.</h3>
            <div class="float-right">
                <a href="index062019.php?page=shipping-option&cmd=registered" class="btn btn-danger">Tài khoản đã đăng ký</a>
                <a href="index062019.php?page=shipping-option" class="btn btn-info"><i class="fa fa-reply" aria-hidden="true"></i> Quay lại</a>
            </div>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" id="frmViettelPost" autocomplete="disabled">
                <div class="form-group">
                    <label for="inputEmail" class="col-sm-3 control-label">Email (ViettelPost) <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="email" class="form-control" id="inputEmail" placeholder="Email" autocomplete="disabled" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPhone" class="col-sm-3 control-label">Điện thoại <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="phone" class="form-control" id="inputPhone" placeholder="Điện thoại" autocomplete="disabled" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-3 control-label">Mật khẩu</label>
                    <div class="col-sm-9">
                        <input type="password" name="user_password" class="form-control" id="inputPassword" placeholder="Mật khẩu" autocomplete="disabled" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputName" class="col-sm-3 control-label">Tên khách hàng, công ty <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" id="inputName" placeholder="Tên khách hàng, công ty" autocomplete="disabled" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputAddress" class="col-sm-3 control-label">Địa chỉ <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" name="address" class="form-control" id="inputAddress" placeholder="Địa chỉ" autocomplete="disabled" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="zone_id" id="zone_id" 
                            class="form-control zone_id zone_ajax" data-dependent=".district_id" data-option="Quận/Huyện (*)" required></select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Quận/Huyện <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="district_id" id="district_id" 
                            class="form-control district_id zone_ajax" data-dependent=".ward_id" data-option="Phường/Xã *" required>
                            <option value="">Quận/Huyện *</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Phường/Xã <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select name="ward_id" id="ward_id" class="form-control ward_id" required>
                            <option value="">Phường/Xã *</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <input type="hidden" id="button_action" name="button_action" />
                        <input type="submit" class="btn btn-info" name="auto_register" value="Đăng ký & Tạo tài khoản vận chuyển tự động" />
                        <input type="submit" class="btn btn-default" name="only_register" value="Đăng ký & Lấy mã xác thực" />
                    </div>
                </div>
            </form>
            <div class="alert alert-warning alert-warning-custom">
                Lưu ý:
                <div> - <b>Đăng ký & Tạo tài khoản vận chuyển tự động</b>: Sẽ đồng thời tạo tài khoản trên Viettel Post và tuha.vn.</div>
                <div> - <b>Đăng ký & Lấy mã xác thực</b>: Chỉ tạo tài khoản trên Viettel Post, và lấy mã xác thực trên Viettel Post. 
                    Sau đó, Quý khách truy cập link <a href="index062019.php?page=shipping-option" style="color: #00c0ef" target="_blank">Quản lý hãng vận chuyển</a> để nhập thông tin tài khoản vận chuyển.</div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><h3 class="title">Mã xác thực</h3></div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">Mã xác thực</label>
                <textarea name="" id="tokenContent" cols="30" rows="5" class="form-control" placeholder="Mã xác thực"></textarea>
            </div>
        </div>
    </div>
</div>
<div id="loading"><span class="loader"></span></div>
<script>
    $(document).ready(function() {
        function ajax_change_zone(id, dependent_id, type) {
            $.ajax({
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                data: {
                    cmd: 'get_zones',
                    zone_id: id,
                    type: type
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data)
                    if (Object.keys(data).length) {
                        $.each(data, function (i, val) {
                            dependent_id.append('<option value="'+ val.id +'">'+ val.name +'</option>')
                        })
                    }
                }
            })
        }

        $(document).on('change', '.zone_id', function() {
            var zone_id = $(this).val()
            var dependent_id = $(this).closest('.form-group').next().find('.district_id')
            dependent_id.empty().append('<option value="">Quận/Huyện *</option>')
            $(this).closest('.modal-body').find('.ward_id').empty().append('<option value="">Phường/Xã *</option>')
            ajax_change_zone(zone_id, dependent_id, 'district')
        })

        $(document).on('change', '.district_id', function() {
            var district_id = $(this).val()
            var dependent_id = $(this).closest('.form-group').next().find('.ward_id')
            dependent_id.empty().append('<option value="">Phường/Xã *</option>')
            ajax_change_zone(district_id, dependent_id, 'ward')
        })

        var requiredField = [
            'inputEmail', 'inputPhone', 'inputPassword', 'inputName', 'inputAddress', 'zone_id', 'district_id', 'ward_id'
        ];

        $('input[type=submit]').click(function(){
            $('#button_action').val($(this).attr('name'));
        });

        $('#frmViettelPost').submit(function(e) {
            e.preventDefault();
            var flag = true;
            $.each(requiredField, function (i, val) {
                if (!$(`#${val}`).val()) {
                    flag = false;
                    return false;
                }
            })
            if (!flag) {
                return;
            }

            $('#loading').show()
            $.ajax({
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=register_vtp_ajax',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(data) {
                    console.log(data)
                    if (data.success === true) {
                        alert('Đăng ký tài khoản và lấy mã xác thực thành công! Quý khách vui lòng lưu lại tài khoản để sử dụng.')
                        $('#tokenContent').val(data.token)
                        // $('#cusId').val(data.cusId)
                    } else {
                        alert(data.error)
                        $('#tokenContent').val('')
                        // $('#cusId').val('')
                    }

                    // window.location.reload(true)
                },
                complete: function() {
                    $('#loading').hide()
                }
            })
        })
    })
</script>