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
            <h3 class="title float-left">ViettelPost - Lấy mã xác thực.</h3>
            <div class="float-right">
                <a href="index062019.php?page=shipping-option" class="btn btn-info"><i class="fa fa-reply" aria-hidden="true"></i> Quay lại</a>
            </div>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" id="frmViettelPost" autocomplete="off">
                <div class="form-group">
                    <label for="inputName" class="col-sm-3 control-label">Tên tài khoản (ViettelPost)</label>
                    <div class="col-sm-9">
                        <input type="text" name="name" class="form-control" id="inputName" placeholder="Tên tài khoản" autocomplete="off" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-3 control-label">Mật khẩu</label>
                    <div class="col-sm-9">
                        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Mật khẩu" autocomplete="off" required>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <input type="hidden" id="button_action" name="button_action" />
                        <input type="submit" class="btn btn-info" name="auto_register" value="Lấy mã xác thực & Tạo tài khoản vận chuyển tự động" />
                        <input type="submit" class="btn btn-default" name="only_register" value="Lấy mã xác thực" />
                        <div class="text-danger">
                        <!-- <a href="index062019.php?page=shipping-option&cmd=registered" class="btn btn-danger">Tài khoản đã đăng ký</a> -->
                            Bạn chưa có tài khoản, click vào <a href="index062019.php?page=shipping-option&cmd=register_viettel_post" target="_blank">đây</a> để đăng ký.
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><h3 class="title">Mã xác thực</h3></div>
        <div class="panel-body">
            <!-- <div class="form-group">
                <label for="">ID tra cứu</label>
                <input type="text" class="form-control" placeholder="ID tra cứu" id="cusId">
            </div> -->
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
        $('input[type=submit]').click(function(){
            $('#button_action').val($(this).attr('name'));
        });
        $('#frmViettelPost').submit(function(e) {
            e.preventDefault();
            var name = $(this).find('#inputName').val()
            var password = $(this).find('#inputPassword').val()
            $('#loading').show()
            $.ajax({
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>&cmd=get_token',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(data) {
                    console.log(data)
                    if (data.success === true) {
                        alert('Lấy mã xác thực thành công!')
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