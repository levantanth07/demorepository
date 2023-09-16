<style>
    .donhang-search-form {
        margin-bottom: 15px;
    }
    #list-status {
        border-bottom: 1px solid rgb(204, 204, 204);
        margin-bottom: 5px;
    }
    .btn-default .badge {
        color: rgb(255, 255, 255);
        background-color: rgb(51, 51, 51);
    }
    .page-bottom {
        border-top: 1px solid rgb(241, 241, 241);
        padding-top: 10px;
    }
    .float-right {
        float: right
    }
    .donhang-search-form .form-group, .donhang-search-form button {
        margin-bottom: 5px;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    $(function() {
        $('[select2="true"]').select2();
        $('#search_text').change(function() {
            var searchText = $(this).val().replace(/[\s|\\n|\;]/g, ',');
            $(this).val(searchText)
        })
    })
</script>
<style>
    .select2-container--default .select2-selection--single {
        height: 34px;
        border: 1px solid #d2d6de;
    }
</style>
<?php
    $groups = null;
    $order_shippings = [[=order_shippings=]];
    $status_jobs_config = [[=status_jobs_config=]];
    $shipping_option_list = [[=shipping_option_list=]];
    $base_url = sprintf(
        "%s://%s/",
        System::getProtocol(),
        $_SERVER['SERVER_NAME']
    );
?>
<div class="container full">
    <div id="page">
        <section class="content-header clearfix">
            <h1 class="page-title pull-left"><?= [[=title=]] ?></h1>
        </section>
        <section class="content">
            <div id="content">
                <div class="box box-solid">
                    <div class="box-body">
                    <?php
                        if (isset($_SESSION['ids_shipping'])) {
                            $ids_shipping = $_SESSION['ids_shipping'];
                            if (!empty($ids_shipping)) {
                                echo $text_success = '
                                    <div class="text-success">
                                        - Đơn hàng đang được xử lý: <b>'. implode(", ", $ids_shipping) .'.</b>
                                    </div>
                                ';
                            }
                            unset($_SESSION['ids_shipping']);
                        }
                    ?>
                        <form class="form-inline donhang-search-form" method="post" id="donhang-search-form" autocomplete="off">
                            <input name="page" type="hidden" value="<?= $_GET['page'] ?>" />
                            <input name="page_no" type="hidden" />
                            <input name="do" type="hidden" value="search" />
                            <div class="form-group">
                                <textarea name="search_text" id="search_text" cols="30" rows="3" class="form-control"
                                    placeholder="Mã đơn hàng hoặc vận đơn" title="Mã vận đơn, mã đơn hàng cách nhau bởi dấu cách hoặc dấu phẩy"></textarea>
                            </div>
                            <div class="form-group">
                                <input name="start_date" type="text" id="start_date" class="form-control"
                                    autocomplete="off" placeholder="Từ ngày chuyển hàng">
                            </div>
                            <div class="form-group">
                                <input name="end_date" type="text" id="end_date" class="form-control"
                                    autocomplete="off" placeholder="Đến ngày chuyển hàng">
                            </div>
                            <div class="form-group">
                                <select name="carrier_id" id="carrier_id" class="form-control"></select>
                            </div>
                            <div class="form-group">
                                <select name="status_id" id="status_id" class="form-control"></select>
                            </div>
                            <?php
                            if (!empty($groups)):
                            ?>
                            <div class="form-group">
                                <select  name="group_id" id="group_id" class="form-control" select2="true">
                                    <option value="">Chọn công ty</option>
                                    <?php
                                    foreach ($groups as $group):
                                    $selected = Url::get('group_id') == $group['id'] ? 'selected' : '';
                                    ?>
                                    <option value="<?= $group['id'] ?>" <?= $selected ?>><?= $group['id'] . '-' . $group['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php
                            endif;
                            ?>
                            <div class="form-group">
                                <select name="shipping_option_id" id="shipping_option_id" class="form-control" style="width: 186px;" >
                                    <?php
                                        foreach ($shipping_option_list as $key => $value):
                                        $selected = Url::get('shipping_option_id') == $key ? 'selected' : '';
                                    ?>
                                    <option value="<?= $key ?>" <?= $selected ?>><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary"> <i class="fa fa-search"></i> Tìm kiếm</button>
                            <a href="/index062019.php?page=admin_orders&cmd=shipping-processing-v3"  class="btn btn-default" style="margin-bottom: 5px;">
                                <i class="fa fa-retweet"></i> Làm mới
                            </a>
                        </form>
                        <div class="text-right">
                            <!--
                            <label class="custom-control-label" style="float: left">
                                <a href="/index062019.php?page=admin_orders&cmd=shipping-processing">
                                    Bấm đây để xem thêm các hãng BĐHN, EMS, Viettel Post
                                </a>
                            </label>-->
                            Cập nhật lại đơn vận chuyển sau: <span class="label label-warning" id="countdownClock"></span> <i class="fa fa-clock-o"></i>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped sticky-enabled tableheader-processed sticky-table">
                                <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã đơn hàng</th>
                                    <th>Mã vận đơn</th>
                                    <th>Trạng thái</th>
                                    <th>Nội dung</th>
                                    <th>Ngày tạo</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($order_shippings)):
                                    $i = 1;
                                    foreach ($order_shippings as $shipping):
                                    ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <a href="/index062019.php?page=admin_orders&cmd=edit&id=<?= $shipping['order_id'] ?>" target="_blank">
                                                <?= $shipping['order_id'] ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="/index062019.php?page=admin_orders&cmd=manager-shipping&search_text=<?= $shipping['billcode'] ?>" target="_blank">
                                                <?= $shipping['billcode'] ?>
                                            </a>
                                        </td>
                                        <td width="150">
                                            <?php
                                                if ($shipping['status_name'] === 'Thành công')
                                                    echo '<span class="label label-success" style="width: 100%">'.$shipping['status_name'].'</span>';
                                                elseif ($shipping['status_name'] === 'Thất bại')
                                                    echo '<span class="label label-danger" style="width: 100%">'.$shipping['status_name'].'</span>';
                                                else
                                                    echo '<span class="label label-warning" style="width: 100%">'.$shipping['status_name'].'</span>';
                                            ?>
                                        </td>
                                        <td><?= $shipping['reasons'] ?></td>
                                        <td width="150"><?= date('d/m/Y H:i:s', strtotime($shipping['createdAt'])) ?></td>
                                    </tr>
                                    <?php
                                    endforeach;
                                else:
                                ?>
                                <tr><td colspan="12" class="text-center">Chưa có dữ liệu !</td></tr>
                                <?php
                                endif;
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (!empty($order_shippings)): ?>
                        <div class="paging">
                            <div class="row" style="display: flex;justify-content: center;">
                                [[|paging|]]
                            </div>
                        </div>
                        <div class="clear-fix page-bottom">
                            <span>Tổng bản ghi: <b class="label label-default"><?= [[=total_current=]] ?>/<?= [[=total=]] ?></b></span>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<link rel="stylesheet" href="assets/vissale/css/jquery-confirm.css">
<style>
    .select2-container--default .select2-selection--single {
        height: 34px;
        border: 1px solid #d2d6de;
    }
</style>
<script src="assets/vissale/js/jquery.countdown.min.js"></script>
<script>
    function updateCountdown(expiredTime){
        $('#countdownClock').countdown(expiredTime)
        .on('update.countdown', function(event) {
            let format = '%M:%S\'';
            if(event.offset.totalDays > 0) {
                format = '%-d day%!d ' + format;
            }
            if(event.offset.weeks > 0) {
                format = '%-w week%!w ' + format;
            }
            $(this).html(event.strftime(format));
        });
    }
    $(function() {
        $('[select2="true"]').select2();
        $('#search_text').change(function() {
            var searchText = $(this).val().replace(/[\s|\\n|\;]/g, ',');
            $(this).val(searchText)
        })

        $('#start_date').datetimepicker({
            format: 'DD/MM/YYYY'
        });
        $('#end_date').datetimepicker({
            format: 'DD/MM/YYYY'
        });

        let now = new Date().getTime() + 60000;
        updateCountdown(now);
        setInterval(function () {
            window.location.href = window.location.origin + '/index062019.php?page=admin_orders&cmd=shipping-processing-v3';
        }, 1000*60);
    })
</script>
