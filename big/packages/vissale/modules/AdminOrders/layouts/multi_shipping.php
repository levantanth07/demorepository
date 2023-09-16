<style>
    .content {
        min-height: 250px;
        padding: 15px;
        margin-right: auto;
        margin-left: auto;
        padding-left: 15px;
        padding-right: 15px;
    }
    .box {
        position: relative;
        border-radius: 3px;
        background: rgb(255, 255, 255);
        border-top: 3px solid rgb(210, 214, 222);
        margin-bottom: 20px;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    }
    .box.box-solid {
        border-top: 0;
    }
    .box-body {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
        padding: 10px;
    }
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
    .panel-heading {
        padding: 10px 15px;
        height: auto;
        line-height: normal;
    }
    .clear-fix:after {
        content: "";
        display: block;
        clear: both;
    }
    .float-left {
        float: left;
    }
    .box-deliver {
        padding: 10px;
        background: rgb(241, 241, 241);
        margin-bottom: 10px;
    }
    .radio-break-work {
        white-space: normal;
        word-break: break-word;
    }
</style>
<?php
$ems_addon = [[=ems_addon=]];
    $shipping_costs = [[=shipping_costs=]];
    $shipping_address = [[=shipping_address=]];
    $shippingOptionActive = [[=shippingOptionActive=]];
    $viettel_post_addon = [[=viettel_post_addon=]];
    $syncWareHouseEms = [[=syncWareHouseEms=]];
    $default_shipping = [[=default_shipping=]];
    $is_freeship_list = [[=is_freeship_list=]];
    // System::debug(AdminOrdersDB::getShippingOptionByCarrierId('api_viettel_post'));
?>
<div id="page">
    <section class="content-header">
        <h1 class="page-title">Chuyển hàng <?= [[=new_ids=]]?'('.sizeof(explode(',',[[=new_ids=]])).' đơn)':''; ?></h1>
    </section>
    <section class="content">
        <div id="content">
            <div class="box box-solid">
                <div class="box-body">
                    <?php
                    if (isset($_SESSION['response_deliver'])) {
                        $response_deliver = $_SESSION['response_deliver'];
                        // System::debug($response_deliver);
                        $response_success = $response_deliver['success'];
                        $response_error = $response_deliver['errors'];
                        $text_success = "";
                        if (!empty($response_success)) {
                            $text_success = '
                                    <div class="text-success box-deliver">
                                        Đơn hàng đã chuyển trạng thái thành công: '. implode(", ", $response_success) .'
                                        <div><a href="index062019.php?page=admin_orders&cmd=manager-shipping" title="Xem chi tiết" target="_blank">Xem tất cả đơn vận chuyển.</a></div>
                                    </div>
                                ';
                        }

                        $text_error = "";
                        if (!empty($response_error)) {
                            $text_error = '<div class="box-deliver box-deliver-danger"><span style="color: red"></span>
                                                <ul class="" style="padding-left: 0px">';
                            foreach ($response_error as $value) {
                                $text_error .= '<li class="" style="color: red">'. $value .'</li>';
                            }

                            $text_error .= '</ul></div>';
                        }

                        echo $text_success . $text_error;
                        unset($_SESSION['response_deliver']);
                    }
                    ?>
                    <div class="alert alert-info">Quý khách lưu ý nên chuyển trạng thái chuyển hàng tầm dưới <b>100</b> đơn 1 lần để hệ thống đảm bảo hoạt động tốt nhất.</div>
                    <form class="transport-form" method="post" id="transport-form-form" autocomplete="off">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Chọn hãng vận chuyển</h3>
                            </div>
                            <div class="panel-body">
                                <ul class="list-group">
                                    <?php $i = 1; ?>
                                    <?php foreach ($shipping_costs as $k => $shipping): ?>
                                        <?php
                                        $checked = $default_shipping ? (($default_shipping === $shipping['alias']) ? 'checked = "checked"' : '') : (!$k ? 'checked = "checked"' : '');
                                        $required = ($i == 1) ? 'required' : "";
//                                        if (!in_array($k, $shippingOptionActive)) {
//                                            continue;
//                                        }

//                                        $shippingCurrent = AdminOrdersDB::getShippingOptionByCarrierId($k);
                                        ?>
                                        <li class="list-group-item">
                                            <div class="radio">
                                                <label for="options-<?= $k ?>">
                                                    <input type="radio" name="shipping_carrier_id" class="carrier-radio"
                                                           id="options-<?= $k ?>" value="<?= $shipping['alias'] ?>" <?= $required ?> <?= $checked ?>>
                                                    <!--                                                    --><?//= $shipping['name'] ?>
                                                    <img src="<?= $shipping['image'] ?>" style="max-height: 50px" >
                                                </label>
                                            </div>
                                        </li>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <div id="box-shipping-option"></div>
                        <div id="box-shipping-area">
                            <div class="panel panel-default" style="margin-top: 15px">
                                <div class="panel-heading clear-fix">
                                    <span class="float-left">Chọn địa chỉ lấy hàng</span>
                                    <a href="index062019.php?page=admin-shipping-address&do=list" class="btn btn-primary float-right" style="margin-top: 2px;" target="_blank"><i class="fa fa-plus-circle"></i> Thêm mới</a>
                                </div>
                                <div class="panel-body">
                                    <?php
                                    if (!empty($shipping_address)) {
                                        foreach ($shipping_address as $key => $value) {
                                            $checked = "";
                                            if ($value['is_default'] == 1) {
                                                $checked = "checked";
                                            }

                                            $lb_default = $value['is_default'] == 1 ? '<span class="label label-default">Mặc định</span>' : "";
//                                                $lbEms = !empty($value['info']['ems_warehouse_id']) ? '<span class="label label-warning hide-all show-ems">Đã đồng bộ EMS</span>' : '';
                                            $lbGhn = !empty($value['info']['ghn_warehouse_id']) ? '&nbsp;<span class="label label-warning hide-all show-ghn">Đã đồng bộ GHN</span>' : '';
                                            $classEms = !empty($value['info']['ems_warehouse_id']) ? 'ems-sync' : 'ems-not-sync';
                                            $classGhn = !empty($value['info']['ghn_warehouse_id']) ? 'ghn-sync' : 'ghn-not-sync';
                                            ?>
                                            <div class="radio radio-item-<?= $value['id'] ?> radio-item <?= $classGhn ?> <?= $classEms ?>">
                                                <label>
                                                    <input type="radio" name="radio_shipping_address"
                                                           id="shipping-address-<?= $value['id'] ?>"
                                                           class = "radio_shipping_address"
                                                           value="<?= $value['id'] ?>"
                                                           data-province-id="<?= $value['province_id'] ?>"
                                                           data-province-name="<?= $value['province_name'] ?>"
                                                           data-district-id="<?= $value['district_id'] ?>"
                                                           data-district-name="<?= $value['district_name'] ?>"
                                                           data-ward-id="<?= $value['ward_id'] ?>"
                                                           data-ward-name="<?= $value['ward_name'] ?>"
                                                        <?= $checked ?>
                                                    >
                                                    <b><?= $value['name'] . '('. $value['phone'] .')' ?></b>
                                                    <?= $value['address'] .',' . $value['ward_name'] . ',' . $value['district_name'] . ',' . $value['province_name'] ?>
                                                    <?= $lb_default ?><?= $lbGhn ?>
                                                    <!--                                            <span class="ems-label"></span>-->
                                                </label>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <!--                                    <div class="text-danger only-ems">-->
                                        <!--                                        <b>Lưu ý:</b> Nếu chọn hãng vận chuyển là EMS, cần phải <b>đồng bộ</b> địa chỉ lấy hàng với EMS.-->
                                        <!--                                    </div>-->
                                        <div class="text-danger only-ghn">
                                            <b>Lưu ý:</b> Nếu chọn hãng vận chuyển là GHN, cần phải <b>đồng bộ</b> địa chỉ lấy hàng với GHN.
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="text-center">Chưa có địa chỉ lấy hàng.</div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Thông tin giao hàng
                            </div>
                            <div class="panel-body">
                                <div class="only-bdvn">
                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Ẩn thông tin đơn hàng</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <label><input name="hidden_info" type="radio" value="phone_product"> Số người nhận + Hàng hoá</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <label><input name="hidden_info" type="radio" value="phone"> Số người nhận</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <label><input name="hidden_info" type="radio" value="none" checked> Không ẩn</label>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="col-xs-3 control-label"> <label for="">Kiểm tra hàng</label></div>
                                    <div class="col-xs-9 form-inline no-padding">
                                        <select name="note_code" id="note_code" class="form-control" style="width:100%;"></select>
                                    </div>
                                    <div class="mb-05"></div>
                                </div>
                                <div class="clearfix"></div>

                                <div class="info-item">
                                    <div class="col-xs-3 control-label"> <label for="">Vận chuyển thu tiền hộ</label></div>
                                    <div class="col-xs-9 form-inline no-padding">
                                        <select name="is_cod" id="is_cod" class="form-control" style="width:100%;"></select>
                                    </div>
                                    <div class="mb-05"></div>
                                </div>
                                <div class="clearfix"></div>

                                <div class="info-item hide-ems">
                                    <div class="col-xs-3 control-label"> <label for="">Miễn phí vận chuyển</label></div>
                                    <div class="col-xs-9 form-inline no-padding">
                                        <select name="is_freeship" id="is_freeship" class="form-control" style="width:100%;"></select>
                                    </div>
                                    <div class="mb-05"></div>
                                </div>
                                <div class="clearfix"></div>

                                <div class="info-item hide-ems">
                                    <div class="col-xs-3 control-label"> <label for="">Lấy hàng tại điểm</label></div>
                                    <div class="col-xs-9 form-inline no-padding">
                                        <select name="pick_option" id="pick_option" class="form-control" style="width:100%;"></select>
                                    </div>
                                    <div class="mb-05"></div>
                                </div>
                                <div class="clearfix"></div>

                                <div class="only-ems">
                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Dịch vụ EMS</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <select name="ems_service" id="ems_service" class="form-control" style="width: 100%"></select>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Dịch vụ cộng thêm</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <?php foreach($ems_addon as $k => $value): ?>
                                                <div class="checkbox checkbox-ems-<?= $k ?>">
                                                    <label>
                                                        <input name="ems_addon[]" type="checkbox"
                                                               class="ems-<?= $k ?>" value="<?= $k ?>"
                                                            <?= $k == 'COD' ? 'checked' : '' ?>> <?= $value; ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Là hàng dễ vỡ</label></div>
                                        <?php $isFragile = Url::get('is_fragile') ? 'checked' : ''; ?>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <label>
                                                <input  name="is_fragile" value="1" type="checkbox" class="is-fragile"
                                                    <?=$isFragile?>
                                                >
                                            </label>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                </div>

                                <div class="only-viettel-post">
                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Dịch vụ Viettel Post</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <select name="viettel_service" id="viettel_service" class="form-control" style="width: 100%"></select>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Dịch vụ cộng thêm</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <?php foreach($viettel_post_addon as $k => $value): ?>
                                                <div class="checkbox checkbox-<?= $k ?>">
                                                    <label>
                                                        <input name="vtp_addon[]" type="checkbox" class="vtp-<?= $k ?>" value="<?= $k ?>"> <?= $value; ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="only-best">
                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Dịch vụ BEST</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <select name="best_ServiceId" class="form-control" style="width: 100%">
                                                <option value="12491">Giao hàng tiết kiệm</option>
                                                <option value="12490">Giao hàng nhanh</option>
                                            </select>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                </div>

                                <div class="only-bdvn">
                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Dịch vụ chuyển phát</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <select name="bdvn_ServiceId" class="form-control" style="width: 100%">
                                                <option value="TMDT_EMS">TMDT_EMS - TMĐT-Chuyển phát nhanh EMS</option>
                                                <option value="TMDT_BK">TMDT_BK - TMĐT-Chuyển phát tiêu chuẩn</option>
                                                <option value="TMDT_EMS_TK">TMDT_EMS_TK - TMĐT-Chuyển phát nhanh EMS tiết kiệm (liên vùng)</option>
                                                <option value="DONG_GIA">DONG_GIA - Dịch vụ thỏa thuận riêng giữa khách hàng và VNPost</option>
                                                <option value="EMS">EMS - Chuyển phát nhanh</option>
                                                <option value="BK">BK - Chuyển phát thường</option>
                                            </select>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Dịch vụ cộng thêm</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <label><input name="bdvn_addon[]" type="checkbox" value="bh"> Bảo hiểm</label>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="only-ghtk">
                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Hình thức vận chuyển</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <select name="ghtk_ServiceId" class="form-control" style="width: 100%">
                                                <option value="">Theo thông tin đơn hàng</option>
                                                <option value="road">Đường bộ</option>
                                                <option value="fly">Đường bay</option>
                                            </select>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Dịch vụ cộng thêm</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <label><input name="ghtk_addon[]" type="checkbox" value="bh" checked> Bảo hiểm</label>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="only-bigfast-v2">
                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Dịch vụ chuyển phát</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <select name="bigfast_service" class="form-control" style="width: 100%">
                                                <option value="ghn">Giao hàng nhanh</option>
                                                <option value="ghst">Giao hàng siêu tốc</option>
                                                <option value="ghvt">Giao hàng vũ trụ</option>
                                                <option value="dg35k">Đồng giá 1</option>
                                                <option value="dg25k">Đồng giá 2</option>
                                            </select>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                </div>

                                <div class="only-jnt">
                                    <div class="info-item">
                                        <div class="col-xs-3 control-label"> <label for="">Đăng ký giao 1 phần</label></div>
                                        <div class="col-xs-9 form-inline no-padding">
                                            <select name="jnt_partsign" class="form-control" style="width: 100%">
                                                <option value="0">Không cho giao 1 phần</option>
                                                <option value="1">Cho giao 1 phần</option>
                                            </select>
                                        </div>
                                        <div class="mb-05"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <input type="hidden" name="ids" id="ids" value="<?= [[=new_ids=]] ?>">
                            <input type="submit" class="btn btn-primary" value="Xác nhận chuyển hàng" id="btn-submit">
                            <a href="index062019.php?page=admin_orders" class="btn btn-default">Huỷ bỏ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="https://donhang.vnpost.vn/assets/myvnpost.uyquyen.js"></script>
<script>
	var shipping_options = <?= [[=shipping_options=]] ?>;
	var account_transport_selected = "";
	var shipping_option_viettel = <?= [[=shipping_option_viettel=]] ?>;
	var HOST_API = "<?= [[=HOST_API=]] ?>";
	var shop_id = <?= [[=shop_id=]] ?>;
	var system_id = <?= [[=system_id=]] ?>;
	var syncWareHouseEms = <?= json_encode($syncWareHouseEms) ?>;
	var is_freeship_list = <?= json_encode($is_freeship_list) ?>;
	if (system_id == 2) {
		$('#is_freeship').val(1);
	}
	$(function() {
		$('#viettel_service').change(function() {
			var vtpService = $(this).val();
			if (vtpService == 'VVT' || vtpService == 'PHT') {
				$('.checkbox-GGD').hide();
				$('.checkbox-GGD input').prop('checked', false);
			} else {
				$('.checkbox-GGD').show();
			}
		});

		if ($('.carrier-radio:checked').val()) {
			build_account_transport($('.carrier-radio:checked').val())
		}

		function syncWareHouse(optionId) {
			if (!optionId || $('.carrier-radio:checked').val() != 'api_ems') {
				return;
			}
			$('.radio-item input').attr('disabled', true)
			$('.radio-item .ems-label').html(`
                <a href="/index062019.php?page=admin-shipping-address&do=list&modal_edit=${optionId}" target="_blank">
                    <span class="label label-danger">Chưa đồng bộ EMS</span>
                </a>
            `)
			if (Object.keys(syncWareHouseEms).length) {
				$.each(syncWareHouseEms, function (i, val) {
					if (optionId == val.shipping_option_id) {
						// console.log('OptionId', optionId)
						$(`.radio-item-${val.shipping_address_id} input`).removeAttr('disabled')
						$(`.radio-item-${val.shipping_address_id} .ems-label`).html(`<span class="label label-warning">Đã đồng bộ EMS</span>`);
					}
				})
			}
		}

		$(document).on('change', '.shipping-option-radio', function () {
			// console.log($('.shipping-option-radio:checked').val())
			// syncWareHouse($('.shipping-option-radio:checked').val())
		})

		function build_account_transport(carrier_id_checked) {
			$('#box-shipping-option').empty();
			$('.radio_shipping_address').removeAttr('disabled')
			$('.hide-ems').show()
			$('.show-ghn').hide()
			$('.hide-all').hide()
			if (carrier_id_checked == 'api_ems') {
				// $('.ems-not-sync input').attr('disabled', true)
				$('.radio-item .ems-label').empty()
				$('.show-ems').show()
				$('.hide-ems').hide();
			}
			if (carrier_id_checked == 'api_ghn') {
				$('.ghn-not-sync input').attr('disabled', true)
				$('.show-ghn').show()
			}
			var box_shipping_options_html = "";
			if (typeof shipping_options == 'object') {
				let shipping_options_html = "";
				var shipping_options_viettel_html = "";
				var j = 0;
				var shipping_option_id_current = "";
				$.each(shipping_options, function (i, val) {
					if (val.carrier_id == carrier_id_checked) {
						let checked = "";
						// if (account_transport_selected != "" && account_transport_selected == val.id) {
						if (val.is_default) {
							checked = 'checked'
							// shipping_option_id_current = val.id
						} else {
							checked = (j == 0) ? 'checked' : ""
							// shipping_option_id_current = val.id
						}

						let name_txt = '';
						let token_txt = '';
						let client_id_txt = '';
						if (val.name) {
							name_txt = val.name;
						}
						if (val.token) {
							token_txt = `Token: ${val.token}`;
						}
						if (val.client_id) {
							client_id_txt = `ClientID: ${val.client_id}`;
						}
						let checkBdvnCode = false;
						if (carrier_id_checked == 'api_bdvn') {
							if (val.hasOwnProperty('customerCode')) {
								if (val.customerCode) checkBdvnCode = true;
							}
						}

						let lbBDVN = '';
						let disableInput = '';
						if (!checkBdvnCode && carrier_id_checked == 'api_bdvn') {
							disableInput = 'disabled';
							lbBDVN = '<span class="label label-warning hide-all show-ems" ' +
								'onclick="MVPUQ.requestPermision(\'61a50267-c2ce-4c0d-a44d-ab52a8fe85ff\', \'https://donhang.vnpost.vn\', function (status, customerCode, tenKhachHang) { ' +
								'let jsonData = { \'name\': tenKhachHang, \'shop_id\': shop_id, \'customerCode\': customerCode };' +
								'if (parseInt(status) == 1) {' +
								'var xhttp = new XMLHttpRequest();' +
								'xhttp.open(\'POST\', HOST_API + \'/api/transport/address/sync-bdvn\', true); ' +
								'xhttp.setRequestHeader(\'Content-type\', \'application/json\'); ' +
								'xhttp.send(JSON.stringify(jsonData));' +
								'setTimeout(function(){ alert(`Ủy quyền thành công`); }, 2000);' +
								'location.reload();' +
								'} else {' +
								'console.log(`Khong cap quyen`)'+
								'}' +
								'})">Bấm để đồng bộ!</span>';
						}

						shipping_options_html += `
                            <div class="radio radio-break-work">
                                <label>
                                    <input name="shipping_option_id" type="radio" ${checked}
                                        id="shipping-option-${i}" ${disableInput}
                                        class="shipping-option-radio" value="${i}" />
                                        ${name_txt} (${client_id_txt}-${token_txt}) ${lbBDVN}
                                </label>
                            </div>
                        `;

						j++
					}
				})

				if (shipping_options_html == "") {
					shipping_options_html = "<span class='text-danger'>Chưa có tài khoản vận chuyển đối với hãng vận chuyển này.</span>"
				}

				let box_shipping_options_html = `
                    <div class="panel panel-default">
                        <div class="panel-heading clear-fix">
                            <span class="float-left">Chọn tài khoản nhà vận chuyển</span>
                            <a href="index062019.php?page=shipping-option" class="btn btn-primary float-right" style="margin-top: 2px;" target="_blank"><i class="fa fa-plus-circle"></i> Thêm mới</a>
                        </div>
                        <div class="panel-body">
                            `+ shipping_options_html + `
                            <div>Click vào <a href="/index062019.php?page=shipping-option" target="_blank">đây</a> để thêm tài khoản nhà vận chuyển.</div>
                        </div>
                    </div>
                `;

				if (carrier_id_checked == 'api_viettel_post') {
					$('.only-viettel-post').show();
				} else {
					$('.only-viettel-post').hide();
				}

				if (carrier_id_checked == 'api_ems') {
					$('.only-ems').show();
				} else {
					$('.only-ems').hide();
				}

				if (carrier_id_checked == 'api_jt') {
					$('.only-jnt').show();
				} else {
					$('.only-jnt').hide();
				}

				if (carrier_id_checked == 'api_ghn') {
					$('.only-ghn').show();
				} else {
					$('.only-ghn').hide();
				}

				if (carrier_id_checked == 'api_viettel_post') {
					// note_code
					// $('#note_code').html('<option value="CHOTHUHANG">Cho khách xem hàng</option><option value="KHONGCHOXEMHANG">Không cho khách xem hàng</option>');
				}

				if (carrier_id_checked == 'api_best') {
					$('.only-best').show();
					$('#is_freeship').html('<option value="1">Miễn phí vận chuyển</option>');
				} else {
					$('.only-best').hide();
					let html = '';
					if (carrier_id_checked == 'api_jt') {
						html = '<option value="2">Thanh toán cuối tháng</option>';
					}
					if (Object.keys(is_freeship_list).length) {
						$.each(is_freeship_list, function (i, val) {
							html += '<option value="'+i+'">'+val+'</option>';
						})
					}
					$('#is_freeship').html(html);
				}

				if (carrier_id_checked == 'api_bdvn') {
					$('.only-bdvn').show();
				} else {
					$('.only-bdvn').hide();
				}

				if (carrier_id_checked == 'api_ghtk') {
					$('.only-ghtk').show();
				} else {
					$('.only-ghtk').hide();
				}

				if (carrier_id_checked == 'api_bigfastv2') {
					$('.only-bigfast-v2').show();
					$('#is_freeship').val("1").change();
				} else {
					$('.only-bigfast-v2').hide();
				}

				$('#box-shipping-option').html(box_shipping_options_html)
				// console.log($('.shipping-option-radio:checked').val())
				// syncWareHouse($('.shipping-option-radio:checked').val())
			}

			return box_shipping_options_html
		}

		if (system_id == 2) {
			// OBD
			// $('#options-api_viettel_post').trigger('click')
			// build_account_transport('api_viettel_post')
		}

		$(document).on('change', '.carrier-radio', function() {
			if ($(this).is(":checked")) {
				let carrier_id_checked = $(this).val()
				build_account_transport(carrier_id_checked)
			}
		})

		function validate_transport() {
			var flag = true;
			if (!$('.carrier-radio:checked').val()) {
				flag = false;
				alert('Bạn chưa chọn hãng vận chuyển.')
				return flag;
			}

			if (!$('.radio_shipping_address:checked').val()) {
				flag = false;
				alert('Bạn chưa chọn địa chỉ lấy hàng.')
				return flag;
			}

			if (!$('.shipping-option-radio:checked').val()) {
				flag = false;
				alert('Bạn chưa chọn tài khoản nhà vận chuyển.')
				return flag;
			}

			if (!$("#ids").val()) {
				flag = false;
				alert("Bạn chưa chọn đơn hàng để vận chuyển.")
				return flag;
			}

			// var carrier_id_checked = $('.carrier-radio:checked').val();
			/* if (carrier_id_checked == "api_viettel_post") {
                if (!$('.kho-viettel:checked').val()) {
                    alert("Bạn chưa chọn kho hàng bên Viettel Post.")
                    flag = false;

                    return flag
                }
            } */

			return flag;
		}

		$('#transport-form-form').submit(function(e) {
			if (!validate_transport()) {
				e.preventDefault();
			} else {
				$('#btn-submit').attr('disabled', true)
			}
		})
	})
</script>
