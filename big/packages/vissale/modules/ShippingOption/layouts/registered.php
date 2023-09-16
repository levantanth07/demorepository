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
    $accounts = [[=accounts=]];
?>
<br>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h3 class="title float-left">ViettelPost - Tài khoản đã đăng ký</h3>
            <div class="float-right">
                <a href="index062019.php?page=shipping-option&cmd=register_viettel_post" class="btn btn-info"><i class="fa fa-reply" aria-hidden="true"></i> Quay lại</a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Mật khẩu</th>
                        <th>Tên Shop</th>
                        <th>Địa chỉ</th>
                        <th>Ngày tạo</th>
                        <th>Người tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!empty($accounts)) {
                            $i = 1;
                            foreach ($accounts as $account) {
                                $item = json_decode($account['content'], true);
                    ?>
                        <tr>
                            <td><?= $i; ?></td>
                            <td><?= $item['EMAIL']; ?></td>
                            <td><?= $item['PHONE']; ?></td>
                            <td><?= $item['PASSWORD']; ?></td>
                            <td><?= $item['NAME']; ?></td>
                            <td><?= $item['ADDRESS']; ?>, <?= $item['from_ward_name'] ?>, <?= $item['from_district_name'] ?>, <?= $item['from_province_name'] ?></td>
                            <td><?= $account['created_at'] ?></td>
                            <td><?= $account['user_id'] ?></td>
                        </tr>
                    <?php
                            $i++;
                            }
                        } else {
                    ?>
                        <tr>
                            <td colspan="8" class="text-center">Chưa có dữ liệu!</td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="loading"><span class="loader"></span></div>
<script>
    $(document).ready(function() {
        
    })
</script>