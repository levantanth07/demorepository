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
    .text-red {
        color: red
    }
</style>
<?php
    $items_address = [[=items_address=]];
    $shippingOptions = [[=shippingOptions=]];
    $modalEdit = $_GET['modal_edit'] ?? false;
?>
<br>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-8">
                        <h3 class="title">Địa chỉ lấy hàng</h3>
                    </div>
                    <div class="col-xs-4 text-right">
                        <a href="#" id="add-new-shipping-address" class="btn btn-success" data-toggle="modal" data-target="#modal-address"><i class="fa fa-plus-circle" aria-hidden="true"></i> Thêm địa chỉ mới</a>
                    </div>
                </div>
            </div>
            <div>
                <div class="col-xs-12" id="box-shipping-address">
                <?php if (!empty($items_address)): ?>
                    <?php foreach ($items_address as $item): ?>
                        <div class="panel panel-default">
                        <div class="panel-heading clear-fix">
                            <h3 class="float-left address-panel-title"><?= $item['name'] ?></h3>
                            <div class="float-right">
    <!--                            --><?php
    //                                $syncWareHouseEms = DB::fetch("
    //                                    SELECT shipping_address_id FROM shipping_address_ems WHERE shipping_address_id = {$item['id']} LIMIT 1", "shipping_address_id");
    //                            ?>

                                <a class="btn btn-info btn-open-modal-ghn" <?= (isset($item['info']['ghn_warehouse_id'])) ? 'disabled' : '' ?> data-id="<?= $item['id'] ?>">
                                    <?= (isset($item['info']['ghn_warehouse_id'])) ? 'Đã đồng bộ kho hàng với GHN' : 'Đồng bộ kho hàng với GHN' ?>
                                </a>
<!--                                <a href="javascript:void(0)" class="btn btn-warning btn-sync-store-ems" --><?//= (isset($item['info']['ems_warehouse_id'])) ? 'disabled' : '' ?><!-- data-id="--><?//= $item['id'] ?><!--">-->
<!--                                    --><?//= (isset($item['info']['ems_warehouse_id'])) ? 'Đã đồng bộ kho hàng với EMS' : 'Đồng bộ kho hàng với EMS' ?>
<!--                                </a>-->
                                <?php if ($item['is_default'] == 1): ?>
                                    <button class="btn btn-default" disabled>Mặc định</button>
                                    <a href="javascript:void(0)" id="btn-edit-<?= $item['id'] ?>" class="btn btn-primary btn-edit" data-id="<?= $item['id'] ?>"><i class="fa fa-pencil" aria-hidden="true"></i> Cập nhật</a>
                                <?php else: ?>
                                    <a href="javascript:void(0)" class="btn btn-success btn-set-default" data-id="<?= $item['id'] ?>">
                                        <i class="fa fa-check-square-o" aria-hidden="true"></i> Đặt mặc định
                                    </a>
                                    <a href="javascript:void(0)" id="btn-edit-<?= $item['id'] ?>" class="btn btn-primary btn-edit" data-id="<?= $item['id'] ?>">
                                        <i class="fa fa-pencil" aria-hidden="true"></i> Cập nhật
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item"><label for="">Tên: </label> <?= $item['name'] ?></li>
                                        <li class="list-group-item"><label for="">Số điện thoại: </label> <?= $item['phone'] ?></li>
                                        <li class="list-group-item"><label for="">Địa chỉ: </label> <?= $item['address'] ?></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item"><label for="">Phường/Xã: </label> <?= $item['ward_name'] ?></li>
                                        <li class="list-group-item"><label for="">Quận/Huyện: </label> <?= $item['district_name'] ?></li>
                                        <li class="list-group-item"><label for="">Tỉnh/Thành phố: </label> <?= $item['province_name'] ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                        <?php else: ?>
                    <div class="txt-center.alert.alert-danger"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-sync-ghn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="" id="form-sync-ghn" name="form_sync_ghn">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Đồng bộ địa chỉ với Giao hàng nhanh</h4>
                </div>
                <div class="modal-body modal-sync-ghn-body">
                    <div class="form-group">
                        <label for="">Số điện thoại đăng ký tài khoản trên Giao hàng nhanh <span class="text-red">(*)</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Số điện thoại (*)" value="">
                        <input type="hidden" name="addressId" id="addressId">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Trở lại</button>
                    <button type="submit" class="btn btn-primary" id="btn-get-otp-ghn">Lấy OTP</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modal-sync-ghn-2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="" id="form-sync-ghn-2" name="form_sync_ghn_2">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Đồng bộ địa chỉ với Giao hàng nhanh</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">OTP <span class="text-red">(*)</span></label>
                        <input type="text" class="form-control" id="otp" name="otp" placeholder="OTP (*)" value="">
                        <input type="hidden" name="phone" id="phoneOtp">
                        <input type="hidden" name="addressId" id="addressIdOtp">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Trở lại</button>
                    <button type="submit" class="btn btn-primary" id="btn-get-otp-ghn">Đồng bộ địa chỉ</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal-address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="" id="form-add-address" name="form_add_address">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Thêm 1 Địa Chỉ Mới</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Tên shop <span class="text-red">(*)</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Tên (*)" value="<?= [[=shop_name=]] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="">Số điện thoại <span class="text-red">(*)</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Số điện thoại (*)" value="<?= [[=shop_phone=]] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="">Tỉnh/Thành phố <span class="text-red">(*)</span></label>
                        <select name="zone_id" id="zone_id" class="form-control zone_id zone_ajax" data-dependent=".district_id" data-option="Quận/Huyện (*)" required></select>
                    </div>
                    <div class="form-group">
                        <label for="">Quận/Huyện <span class="text-red">(*)</span></label>
                        <select name="district_id" id="district_id" class="form-control district_id zone_ajax" data-dependent=".ward_id" data-option="Phường/Xã (*)" required>
                            <option value="">Quận/Huyện (*)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Phường/Xã <span class="text-red">(*)</span></label>
                        <select name="ward_id" id="ward_id" class="form-control ward_id" required>
                            <option value="">Phường/Xã (*)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Địa chỉ chi tiết <span class="text-red">(*)</span></label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Địa chỉ chi tiết (*)" required>
                    </div>
                    <div class="form-group">
                        <div>
                            <label for="is_default">
                                <input type="checkbox" class="" id="is_default" name="is_default" value="1"> Đặt làm mặc định
                            </label>
                        </div>
                        <!-- <div>
                            <label for="ems_warehouse_id">
                                <input type="checkbox" class="" id="ems_warehouse_id" name="ems_warehouse_id" value="1"> Tạo kho hàng trên EMS
                            </label>
                        </div> -->
                    </div>
                    <?php if (!empty($shippingOptions)): ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                ĐỒNG BỘ VỚI KHO HÀNG BÊN EMS
                            </div>
                            <div class="panel-body">
                                <?php foreach ($shippingOptions as $optionItem): ?>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="shipping_options_id[]" value="<?= $optionItem['id'] ?>">
                                                <?= $optionItem['name'] ?> (<?= $optionItem['token'] ?>)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Trở lại</button>
                    <button type="submit" class="btn btn-primary">Hoàn thành</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="box-modal-edit"></div>
<div id="loading"><span class="loader"></span></div>
<script>
    var current_url = window.location.href;
    var modalEditId = '<?= $modalEdit ?>';

    function ajax_change_zone(id, dependent_id, type) {
        $.ajax({
            url: current_url + '&action=ajax&cmd=get_zones',
            data: {
                zone_id: id,
                type: type
            },
            dataType: 'json',
            success: function(data) {
                // console.log(data)
                if (Object.keys(data).length) {
                    $.each(data, function (i, val) {
                        dependent_id.append('<option value="'+ val.id +'">'+ val.name +'</option>')
                    })
                }
            }
        })
    }

    $(document).ready(function() {
        $('.btn-set-default').click(function() {
            if (confirm('Bạn có chắc chắn muốn đặt địa chỉ này làm mặc định?')) {
                $('#loading').show()
                $.ajax({
                    url: current_url + '&action=ajax&cmd=set_default_address',
                    type: 'POST',
                    data: {
                        id: $(this).data('id')
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success === true) {
                            // alert('Cập nhật thành công!')
                        } else {
                            alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                            window.location.reload(true)
                        }

                        window.location.reload(true)
                    },
                    complete: function() {
                        $('#loading').hide()
                    }
                })
            }
        })

        $('.btn-delete').click(function() {
            if (confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
                $('#loading').show()
                $.ajax({
                    url: current_url + '&action=ajax&cmd=delete_address',
                    type: 'POST',
                    data: {
                        id: $(this).data('id')
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success === true) {
                            alert('Xóa địa chỉ thành công!')
                        } else {
                            alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                        }

                        window.location.reload(true)
                    },
                    complete: function() {
                        $('#loading').hide()
                    }
                })
            }
        })

        $('.btn-edit').click(function() {
            $('#box-modal-edit').empty()
            $.ajax({
                url: current_url + '&action=ajax&cmd=edit_address',
                data: {
                    id: $(this).data('id')
                },
                success: function(data) {
                    if (data != '') {
                        let jsonData = $.parseJSON(data);
                        if (jsonData.success == false) {
                            alert(jsonData.messages)
                            window.location.reload(true)
                        } else {
                            $('#box-modal-edit').append(jsonData.html)
                            $('#modal-edit-address').modal('show')
                        }
                    }
                }
            })
        });

        // console.log('modalEditId', modalEditId);
        if (modalEditId) {
            $(`#btn-edit-${modalEditId}`).trigger('click');
        }

        $(document).on('change', '.zone_id', function() {
            var zone_id = $(this).val()
            var dependent_id = $(this).closest('div').next().find('.district_id')
            dependent_id.empty().append('<option value="">Quận/Huyện (*)</option>')
            $(this).closest('.modal-body').find('.ward_id').empty().append('<option value="">Phường/Xã (*)</option>')
            ajax_change_zone(zone_id, dependent_id, 'district')
        })

        $(document).on('change', '.district_id', function() {
            var district_id = $(this).val()
            var dependent_id = $(this).closest('div').next().find('.ward_id')
            dependent_id.empty().append('<option value="">Phường/Xã (*)</option>')
            ajax_change_zone(district_id, dependent_id, 'ward')
        })

        $('.btn-sync-store-ghn').click(function(e) {
          e.preventDefault()
          $('#loading').show()
          $.ajax({
            url: current_url + '&action=ajax&cmd=sync_ghn',
            data: {
              id: $(this).data('id')
            },
            success: function(data) {
              console.log(data)
              data = JSON.parse(data)
              if (data.success === true) {
                alert('Đồng bộ thành công')
                window.location.reload(true)
              } else {
                alert(data.messages)
              }
              $('#loading').hide()
            }
          })
        });

        $('.btn-open-modal-ghn').click(function(e) {
          $('#addressId').val($(this).data('id'));
          $('#modal-sync-ghn').modal('show')
        })

        $('.btn-sync-store-ems').click(function(e) {
          e.preventDefault()
          $('#loading').show()
          $.ajax({
            url: current_url + '&action=ajax&cmd=sync_ems',
            data: {
              id: $(this).data('id')
            },
            success: function(data) {
              console.log(data)
              data = JSON.parse(data)
              if (data.success === true) {
                alert('Đồng bộ thành công')
              } else {
                alert(data.messages)
              }
              $('#loading').hide()
              window.location.reload(true)
            }
          })
        });

      $('#form-sync-ghn-2').submit(function(e) {
        e.preventDefault()
        $('#loading').show()

        let otp = '';
        let phone = '';
        let addressId = '';
        var formData = $(this).serializeArray();
        formData.forEach(function (item) {
          if (item.name == 'otp') otp = item.value;
          if (item.name == 'phone') phone = item.value;
          if (item.name == 'addressId') addressId = item.value;
        })

        if (otp != '') {
          console.log(1);
          $.ajax({
            url: current_url + '&action=ajax&cmd=logistic_sync_otp_ghn',
            data: {
              otp: otp,
              phone: phone,
              id: addressId
            },
            success: function(data) {
              console.log(data)
              data = JSON.parse(data)
              if (data.success === true) {
                alert('Đồng bộ thành công')
              } else {
                alert(data.messages)
              }
              $('#loading').hide()
              window.location.reload(true)
            }
          })
        } else {
          console.log(2);
          alert('Yêu cầu nhập OTP!')
          $('#loading').hide()
        }
      })

      $('#form-sync-ghn').submit(function(e) {
        e.preventDefault()
        $('#loading').show()

        let phone = '';
        let addressId = '';
        var formData = $(this).serializeArray();
        formData.forEach(function (item) {
          if (item.name == 'phone') phone = item.value;
          if (item.name == 'addressId') addressId = item.value;
        })

        if (phone != '') {
          console.log(1);
          $.ajax({
            url: current_url + '&action=ajax&cmd=logistic_get_otp_ghn',
            data: {
              phone: phone
            },
            success: function(data) {
              console.log(data)
              data = JSON.parse(data)
              if (data.success === true) {
                alert('Lấy OTP thành công')
                $('#modal-sync-ghn').modal('hide')
                $('#phoneOtp').val(phone);
                $('#addressIdOtp').val(addressId);
                $('#modal-sync-ghn-2').modal('show')
              } else {
                alert('Không thể lấy OTP')
              }
              $('#loading').hide()
              // window.location.reload(true)
            }
          })
        } else {
          console.log(2);
          alert('Yêu cầu nhập số điện thoại!')
          $('#loading').hide()
        }
      });

        $('#form-add-address').submit(function(e) {
            e.preventDefault()
            $('#loading').show()
            var formData = $(this).serialize()
            $.ajax({
                url: current_url + '&cmd=save_address',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(data) {
                    $('#modal-address').modal('hide')
                    if (data.success === true) {
                        alert('Thêm mới địa chỉ thành công!')
                    } else {
                        if (data.messages) {
                            alert(data.messages)
                            window.location.reload(true)
                        } else {
                            alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                        }
                    }

                    window.location.reload(true)
                },
                complete: function() {
                    $('#loading').hide()
                    window.location.reload(true)
                }
            })
        })

        $(document).on('submit', '#form-edit-address', function (e) {
            e.preventDefault()
            $('#loading').show()
            var formData = $(this).serialize()
            $.ajax({
                url: current_url + '&action=ajax&cmd=save_edit_address',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(data) {
                    $('#modal-edit-address').modal('hide')
                    if (data.success === true) {
                        alert('Cập nhật địa chỉ thành công!')
                    } else {
                        if (data.messages) {
                            alert(data.messages)
                            window.location.reload(true)
                        } else {
                            alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                        }
                    }

                    window.location.reload(true)
                },
                complete: function() {
                    $('#loading').hide()
                }
            })
        })
    })
</script>
