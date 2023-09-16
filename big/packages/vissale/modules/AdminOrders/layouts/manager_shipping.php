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
    #loader_ajax {
        position: fixed;
        display: none;
        background: url(/assets/vissale/images/loading.gif) rgba(255,255,255,.5) no-repeat center center;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 100%;
        z-index: 999999999;
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
    $HOST_API = HOST_API;
    $shipping_config_status = [[=shipping_config_status=]];
    $order_shippings = [[=order_shippings=]];
    $shipping_config_costs = [[=shipping_config_costs=]];
    $base_url = sprintf(
        "%s://%s/",
        System::getProtocol(),
        $_SERVER['SERVER_NAME']
    );
    $groups = [[=groups=]];
    $group_id = [[=group_id=]];
    $is_admin = [[=is_admin=]];
    $user_id = [[=user_id=]];
    $user_name = [[=user_name=]];
    $statistic_orders = [[=statistic_orders=]];
    $total_fee = [[=total_fee=]];
    $total_value = [[=total_value=]];
    $shipping_option_list = [[=shipping_option_list=]];
    // System::debug($order_shippings);
?>
<div id="viewShipDetail" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Chi tiết vận đơn</h3>
            </div>
            <div class="modal-body" style="padding: 0px; height: 800px">
                <iframe id="viewShipDetailIframe" src="" width="99%" height="100%"></iframe>
            </div>
            <div class="modal-footer" style="padding:5px;">
                <button type="button" class="btn btn-danger" data-dismiss="modal">x Đóng</button>
            </div>
        </div>
    </div>
</div>
<div class="container full">
    <div id="page">
        <section class="content-header clearfix">
            <h1 class="page-title pull-left"><?= [[=title=]] ?></h1>
            <?php
            $excel_filter_array = [[=excel_filter_array=]];
            $url_export = 'index062019.php?page=' . $_GET['page'] . '&cmd='. $_GET['cmd'] .'&action=export-excel';
            foreach ($excel_filter_array as $key => $value) {
                if (!empty($_REQUEST[$value])) {
                    $url_export .= "&$value=" . $_REQUEST[$value];
                }
            }
            ?>
            <!--<a href="<?= $url_export ?>" class="btn btn-success pull-right">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Xuất excel
            </a>-->
        </section>
        <section class="content">
            <div id="content">
                <div class="box box-solid">
                    <div class="box-body">
                        <form class="form-inline donhang-search-form" method="post" id="donhang-search-form" autocomplete="off">
                            <input name="page" type="hidden" value="<?= $_GET['page'] ?>" />
                            <input name="page_no" type="hidden" />
                            <input name="do" type="hidden" value="search" />
                            <div class="row">
                                <div class="col-xs-2">
                                    <div class="form-group">
                                        <textarea name="search_text" id="search_text" cols="30" rows="3" class="form-control"
                                                  placeholder="Mã đơn hàng hoặc vận đơn" title="Mã vận đơn, mã đơn hàng cách nhau bởi dấu cách hoặc dấu phẩy"></textarea>
                                    </div>
                                </div>
                                <div class="col-xs-8">
                                    <div class="form-group">
                                        <select name="filter_date" id="filter_date" class="form-control"></select>
                                    </div>
                                    <div class="form-group">
                                        <input name="start_date" type="text" id="start_date" class="form-control" autocomplete="off" placeholder="Từ ngày" style="width: 120px">
                                    </div>
                                    <div class="form-group">
                                        <input name="end_date" type="text" id="end_date" class="form-control" autocomplete="off" placeholder="Đến ngày" style="width: 120px">
                                    </div>
                                    <div class="form-group">
                                        <select name="carrier_id" id="carrier_id" class="form-control"></select>
                                    </div>
                                    <div class="form-group">
                                        <select name="shipping_status" id="shipping_status" style="width:150px;" class="form-control"></select>
                                    </div>
                                    <?php if (1===1): ?>
                                    <div class="form-group">
                                        <select name="order_status_id" id="order_status_id" style="width:150px;" class="form-control"></select>
                                    </div>
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <select name="is_freeship" id="is_freeship" class="form-control"></select>
                                    </div>
                                    <div class="form-group">
                                        <select name="pick_option" id="pick_option" class="form-control"></select>
                                    </div>
                                    <?php
                                    if (!empty($groups) && 1===1):
                                    ?>
                                    <div class="form-group">
                                        <select name="group_id" id="group_id" class="form-control" select2="true">
                                            <option value="">Chọn công ty</option>
                                            <?php
                                            foreach ($groups as $group):
                                            $selected = Url::post('group_id') == $group['id'] ? 'selected' : '';
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
                                </div>
                                <div class="col-xs-2 no-padding">
                                    <button type="submit" class="btn btn-default"> <i class="fa fa-search"></i> Tìm kiếm</button>
                                    <a href="/index062019.php?page=admin_orders&cmd=manager-shipping"  class="btn btn-default" style="margin-bottom: 5px;">
                                        <i class="fa fa-retweet"></i> Làm mới
                                    </a>
                                </div>
                            </div>
                            <hr>
                            <div id="list-status" class="clearfix">
                                <div class="float-left">
                                    <?php
                                    if (1===2):
                                    foreach ($shipping_config_status as $k => $config_cost):
                                    $total_order_shipping = !empty($statistic_orders[$k]['total']) ? $statistic_orders[$k]['total'] : 0;
                                    ?>
                                    <a href="javascript:void(0)" data-status="<?= $k ?>" class="btn-status <?= $config_cost['class'] ?>">
                                        <?= $config_cost['name'] ?> <span class="badge"><?= $total_order_shipping ?></span>
                                    </a>
                                    <?php endforeach; endif; ?>

                                    <?php
                                    foreach ($statistic_orders as $status):
                                    ?>
                                    <a href="javascript:void(0)" data-status="<?= $status['status'] ?>" class="btn-status <?= $status['classBtn'] ?>">
                                        <?= $status['name'] ?> <span class="badge"><?= $status['total'] ?></span>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <div class="float-right">
                                    <select name="number_page" id="number_page" class="form-control"></select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped sticky-enabled tableheader-processed sticky-table">
                                <thead>
                                <tr>
                                    <th>STT</th>
                                    <?php if (!empty($is_admin) && 1===1): ?>
                                        <th>#GROUP_ID</th>
                                    <?php endif; ?>
                                    <th>Mã vận đơn/<br />Mã đơn hàng</th>
                                    <th>Thông tin vận chuyển</th>
                                    <th>Thông tin kiện hàng</th>
                                    <th>Phí vận chuyển</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tháng</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($order_shippings)):
                                    $i = 1;
                                foreach ($order_shippings as $shipping):
                                    $arrFreeship = array('Thanh toán cuối tháng', 'Không miễn phí vận chuyển', 'Miễn phí vận chuyển');
                                    $is_freeship = (isset($shipping['order']['paytype'])) ? $shipping['order']['paytype'] : 1;
                                    $text_freeship = $arrFreeship[$is_freeship];
                                    if (isset($shipping['detail']['is_freeship'])) {
                                        $arrFreeship = array('Không miễn phí vận chuyển', 'Miễn phí vận chuyển', 'Thanh toán cuối tháng');
                                        $text_freeship = $arrFreeship[(int)$shipping['detail']['is_freeship']];
                                    }
                                    if (isset($shipping['pick_option'])) {
                                        $pick_option = $shipping['pick_option'];
                                        $text_pick_option = 'COD đến lấy hàng';
                                        if ($pick_option == 'post') {
                                            $text_pick_option = 'Gửi hàng tại bưu cục';
                                        }
                                    } elseif (isset($shipping['order']['servicetype'])) {
                                        $text_pick_option = array(1 => 'Lấy hàng tận nơi', 6 => 'Đến bưu cục gửi kiện', 0 => 'Đến bưu cục gửi kiện', 2 => 'Đến bưu cục gửi kiện')[$shipping['order']['servicetype']];
                                    } elseif (isset($shipping['detail']['pick_option'])) {
                                        $text_pick_option = array('cod' => 'COD đến lấy hàng', 'post' => 'Gửi hàng tại bưu cục')[$shipping['detail']['pick_option']];
                                    }
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <?php if (!empty($is_admin) && 1===1): ?>
                                    <td><?= $shipping['shop_id'] ?></td>
                                    <?php endif; ?>
                                    <td>
                                        <div style="
                                            max-width: 200px;
                                            white-space: normal;
                                        ">
                                            <?= $shipping['txlogisticid'] ?><br>
                                            <a class="text-info" href="/index062019.php?page=admin_orders&cmd=edit&id=<?= $shipping['order_id'] ?>" target="_blank">
                                                <?= $shipping['order_id'] ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <ul style="max-width: 250px;padding-left: 0px; word-break: break-word">
                                            <?php if (isset($shipping['carrier_name'])): ?>
                                                <li><label>Tài khoản vận chuyển:</label> <?= $shipping['customerid'] ?></li>
                                                <li><label for="">Đơn vị vận chuyển</label>: <?= $shipping['carrier_name'] ?></li>
                                            <?php endif; ?>
                                            <?php if (isset($shipping['detail']['hidden_info'])): ?>
                                                <?php if ($shipping['detail']['hidden_info'] == 'phone_product'): ?>
                                                    <li><label>Ẩn thông tin:</label> Số người nhận + Hàng hoá</li>
                                                <?php endif; ?>
                                                <?php if ($shipping['detail']['hidden_info'] == 'phone'): ?>
                                                    <li><label>Ẩn thông tin:</label> Số người nhận</li>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <li><label>Miễn phí vận chuyển</label>: <?= $text_freeship ?></li>
                                            <?php if (isset($shipping['detail']['addons'])): ?>
                                            <li><label>Dịch vụ thêm</label>: <?php
                                                echo (in_array('bh', $shipping['detail']['addons'])) ? 'Bảo hiểm|' : '';
                                                echo (in_array('KG', $shipping['detail']['addons'])) ? 'Khai giá|' : '';
                                                echo (in_array('PTT', $shipping['detail']['addons'])) ? 'Phát tận tay|' : '';
                                                echo (in_array('ART', $shipping['detail']['addons'])) ? 'Báo phát|' : '';
                                                echo (in_array('GBH', $shipping['detail']['addons'])) ? 'Bảo hiểm|' : '';
                                                echo (in_array('GBP', $shipping['detail']['addons'])) ? 'Báo phát|' : '';
                                                echo (in_array('GTT', $shipping['detail']['addons'])) ? 'Phát tận tay|' : '';
                                                echo (in_array('GDK', $shipping['detail']['addons'])) ? 'Đồng kiểm|' : '';
                                                echo (in_array('GTC', $shipping['detail']['addons'])) ? 'Giá trị cao|' : '';
                                                ?>
                                            </li>
                                            <?php endif; ?>
                                            <li><label>Lấy hàng tại điểm</label>: <?= $text_pick_option ?></li>
                                            <li><label>Địa chỉ lấy hàng</label>: <?= (!$shipping['detail']['sender']['address']) ?: $shipping['detail']['sender']['address'] ?></li>
                                            <li><div style="white-space: normal"><label>Địa chỉ gửi hàng</label>: <?= (!$shipping['detail']['receiver']['address']) ?: $shipping['detail']['receiver']['address'] ?></div></li>
                                            <li><div style="white-space: normal"><label>Ghi chú</label>: <?= isset($shipping['detail']['shipping_note']) ? $shipping['detail']['shipping_note'] : (isset($shipping['detail']['remark']) ? $shipping['detail']['remark'] : '') ?></div></li>
                                        </ul>
                                    </td>
                                    <td><ul style="padding-left: 0px"><li>Cân nặng: <b><?= isset($shipping['order']['weight']) ? $shipping['order']['weight'] : $shipping['detail']['weight'] ?> gram</b></li></ul></td>
                                    <td><b><?= number_format($shipping['inquiryFee']) ?></b> đ</td>
                                    <td><b><?= number_format(isset($shipping['order']['total_price']) ? $shipping['order']['total_price'] : $shipping['detail']['itemsvalue']) ?></b> đ</td>
                                    <td>
                                        <?php
                                        if (in_array($shipping['status'], array(11, 12)))
                                            echo '<span class="label label-success" style="width: 100%">'.$shipping['status_name'].'</span>';
                                        elseif (in_array($shipping['status'], array(4, 18)))
                                            echo '<span class="label label-danger" style="width: 100%">'.$shipping['status_name'].'</span>';
                                        elseif (in_array($shipping['status'], array(2)))
                                            echo '<span class="label label-info" style="width: 100%">'.$shipping['status_name'].'</span>';
                                        elseif (in_array($shipping['status'], array(5, 6)))
                                            echo '<span class="label label-primary" style="width: 100%">'.$shipping['status_name'].'</span>';
                                        else
                                            echo '<span class="label label-warning" style="width: 100%">'.$shipping['status_name'].'</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <ul style="padding-left: 0px">
                                            <li>
                                                <label>Ngày tạo</label>:
                                                <?php
                                                    if (count($shipping['tracesTime']) > 0) {
                                                        foreach($shipping['tracesTime'] as $item) {
                                                            if ($item['status_code'] === 1) {
                                                                $timer = $item['time']['$date']['$numberLong'] / 1000;
                                                                echo date('d-m-Y H:i', $timer);
                                                                break;
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </li>
                                            <li>
                                                <label>Ngày lấy hàng</label>:
                                                <?php
                                                if (count($shipping['tracesTime']) > 0) {
                                                foreach($shipping['tracesTime'] as $item) {
                                                if ($item['status_code'] === 5 || $item['status_code'] === 6) {
                                                $timer = $item['time']['$date']['$numberLong'] / 1000;
                                                echo date('d-m-Y H:i', $timer);
                                                break;
                                                }
                                                }
                                                }
                                                ?>
                                            </li>
                                            <li>
                                                <label>Ngày giao hàng</label>:
                                                <?php
                                                if (count($shipping['tracesTime']) > 0) {
                                                foreach($shipping['tracesTime'] as $item) {
                                                if ($item['status_code'] === 7 || $item['status_code'] === 8 || $item['status_code'] === 9 || $item['status_code'] === 10) {
                                                $timer = $item['time']['$date']['$numberLong'] / 1000;
                                                echo date('d-m-Y H:i', $timer);
                                                break;
                                                }
                                                }
                                                }
                                                ?>
                                            <li>
                                                <label>Ngày hoàn thành</label>:
                                                <?php
                                                if (count($shipping['tracesTime']) > 0) {
                                                foreach($shipping['tracesTime'] as $item) {
                                                if ($item['status_code'] === 12) {
                                                $timer = $item['time']['$date']['$numberLong'] / 1000;
                                                echo date('d-m-Y H:i', $timer);
                                                break;
                                                }
                                                }
                                                }
                                                ?>
                                            </li>
                                            <li>
                                                <label>Ngày hủy</label>:
                                                <?php
                                                if (count($shipping['tracesTime']) > 0) {
                                                foreach($shipping['tracesTime'] as $item) {
                                                if ($item['status_code'] === 4) {
                                                $timer = $item['time']['$date']['$numberLong'] / 1000;
                                                echo date('d-m-Y H:i', $timer) . ' (' . $item['user_name'] . ')';
                                                break;
                                                }
                                                }
                                                }
                                                ?>
                                            </li>
                                            <li>
                                                <label>Ngày trả hàng</label>:
                                                <?php
                                                if (count($shipping['tracesTime']) > 0) {
                                                foreach($shipping['tracesTime'] as $item) {
                                                if ($item['status_code'] === 14) {
                                                $timer = $item['time']['$date']['$numberLong'] / 1000;
                                                echo date('d-m-Y H:i', $timer);
                                                break;
                                                }
                                                }
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <div><a target="viewShipDetail" onclick="openViewDetail(this);return false;" href="<?= Url::build('admin_orders').'&window=1&cmd=shipping_history&id=' . $shipping['order_id'];?>" style="width:100%" class="btn btn-default btn-sm"> <i class="fa fa-eye"></i> Xem chi tiết</a></div>
                                        <?php if (isset($shipping['carrier'])): ?>
                                        <?php if ($shipping['carrier'] == 'api_bdhn'): ?>
                                        <div style="margin-top: 5px;">
                                            <a href="<?= 'index062019.php?page=admin_orders&cmd=view_bdhn&postal_code=' . $shipping['shipping_order_code'] ?>" class="btn btn-warning btn-sm" target="_blank" style="width:100%">Lịch sử bưu điện</a>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($shipping['status'] === 2): ?>
                                        <div style="margin-top: 5px;">
                                            <a style="width:100%"  href="javascript:void(0)" class="btn btn-danger btn-sm btn-cancel-transport"
                                               data-host-api="<?= $HOST_API ?>"
                                               data-carrier-id="<?= $shipping['carrier'] ?>"
                                               data-order-code="<?= $shipping['txlogisticid'] ?>"
                                               data-order-id="<?= $shipping['order_id'] ?>"
                                               data-id="<?= $shipping['shipping_id'] ?>"
                                               data-user-id="<?= $user_id ?>"
                                               data-user-name="<?= $user_name ?>"
                                                <i class="fa fa-trash-o"></i> Hủy đơn</a>
                                        </div>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
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
                            <span>Tổng bản ghi: <b class="label label-default"><?= [[=total_current=]] ?>/<?= number_format([[=total=]]) ?></b></span>
                            <span>Tổng phí vận chuyển: <b class="label label-default"><?= number_format($total_fee) ?> đ</b></span>
                            <span>Tổng tiền: <b class="label label-default"><?= number_format($total_value) ?> đ</b></span>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div id="loader_ajax"></div>
<script>
    $(function() {
        $('.btn-status').click(function() {
            var status = $(this).data('status')
            $('#shipping_status').val(status)
            $('#donhang-search-form').submit()
        })

        $('.btn-reset').click(function() {
            $('#donhang-search-form')[0].reset()
            $('#donhang-search-form').submit()
        })

	    $('.btn-cancel-transport').click(function() {
		    if (confirm('Bạn có chắc muốn hủy đơn hàng này không?')) {
			    var carrierArr = ["api_jt", "api_ghn", "api_best", "api_ghn_v2", "api_bdvn"];
			    // let carrier_id = $(this).data('carrier-id')
			    let id = $(this).data('id');
			    let order_code = $(this).data('order-code');
			    let host_api = $(this).data('host-api');
			    let carrier_id = $(this).data('carrier-id');
			    let order_id = $(this).data('order-id');
			    let user_id = $(this).data('user-id');
			    let user_name = $(this).data('user-name');
			    $('#loader_ajax').show();

			    $.ajax({
				    method: "POST",
				    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
				    data : {
					    'cmd':'cancel_order_transport',
					    'carrier_id':carrier_id,
					    'user_name': user_name,
					    'user_id': user_id,
					    'order_code':order_code,
					    'order_id':order_id,
					    'id':id
				    },
				    dataType: 'json',
				    success: function(data) {
					    if (data.success) {
						    window.location.reload(true)
					    } else {
						    alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
					    }
				    },
				    error: function (err) {
					    // console.log(err);
				    },
				    complete: function() {
					    $('#loader_ajax').show();
				    }
			    });

		    }
	    });

        $('#start_date').datetimepicker({
            format: 'DD/MM/YYYY'
        });
        $('#end_date').datetimepicker({
            format: 'DD/MM/YYYY'
        });
    });
    function openViewDetail(obj){
        var frametarget = $(obj).attr('href');
        var targetmodal = $(obj).attr('target');
        if (targetmodal == undefined) {
            targetmodal = '#viewShipDetail';
        } else {
            targetmodal = '#'+targetmodal;
        }
        if ($(this).attr('title') != undefined) {
            $(targetmodal+ ' .modal-header h3').html($(obj).attr('title'));
            $(targetmodal+' .modal-header').show();
        } else {
            $(targetmodal+' .modal-header h3').html('');
            $(targetmodal+' .modal-header').hide();
        }
        $('#viewShipDetailIframe').attr("src", frametarget );
        $(targetmodal).modal({show:true});
        return false;
    }
</script>
