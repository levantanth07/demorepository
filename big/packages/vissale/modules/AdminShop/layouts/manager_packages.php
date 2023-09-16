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
    .view-header {
        border-bottom: 1px dotted rgb(241, 241, 241);
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
</style>
<?php
    $base_url = sprintf(
        "%s://%s/",
        System::getProtocol(),
        $_SERVER['SERVER_NAME']
    );
    $packages = [[=acc_packages=]];
?>
<div id="page">
    <section class="content-header">
        <h1 class="page-title"><?= [[=title=]] ?></h1>
    </section>
    <section class="content">
        <div id="content">
            <div class="box box-solid">
                <div class="box-body">
                    <div class="view-header">
                        <div class="text-right">
                            <a href="#" class="btn btn-primary" data-target="#modal-add-package" data-toggle="modal"><i class="fa fa-plus-circle"></i> Thêm mới</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped sticky-enabled tableheader-processed sticky-table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên gói cước</th>
                                    <th class="text-right">Cước phí</th>
                                    <th class="text-right">Số tháng</th>
                                    <th class="text-right">% chiết khấu</th>
                                    <th class="text-right">Số user tối đa</th>
                                    <th class="text-right">Số page tối đa</th>
                                    <th class="text-right">Thứ tự</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($packages)):
                                $i = 1;
                                foreach ($packages as $package):
                            ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $package['name'] ?></td>
                                    <td class="text-right"><?= number_format($package['price']) ?></td>
                                    <td class="text-right"><?= $package['number_months'] ?></td>
                                    <td class="text-right"><?= $package['percent_discount'] ?></td>
                                    <td class="text-right"><?= $package['max_user'] ?></td>
                                    <td class="text-right"><?= $package['max_page'] ?></td>
                                    <td class="text-right"><?= $package['weight'] ?></td>
                                    <td class="text-center">
                                        <a href="javascript:void(0)" class="btn btn-primary btn-edit" data-id="<?= $package['id'] ?>">Sửa</a>
                                        <a href="javascript:void(0)" class="btn btn-default btn-delete" data-id="<?= $package['id'] ?>">Xóa</a>
                                    </td>
                                </tr>
                            <?php
                                endforeach;
                            endif;
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modal-add-package" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="" id="form-add-package" name="form_add_address">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Thêm gói cước</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Tên gói cước (*)" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="price" name="price" placeholder="Cước phí/tháng (*)" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" id="number_months" name="number_months" placeholder="Số tháng (*)" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" id="percent_discount" name="percent_discount" placeholder="% chiết khấu">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="max_user" name="max_user" placeholder="Số user tối đa (*)" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="max_page" name="max_page" placeholder="Số page tối đa (*)" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" id="weight" name="weight" placeholder="Thứ tự">
                    </div>
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
<div id="loading"><span class="loader"></soan></div>

<script>
    var current_url = window.location.href
    function format_number(element) {
        $(document).on('keyup',element, function (event) {
            // skip for arrow keys
            if (event.which > 36 && event.which < 41)
                return;
            // format number
            $(this).val(function (index, value) {
                var temp = value.replace(/[^\.0-9-]/g, "");
                var n = temp.indexOf(".");
                if (n > -1)
                {
                    var x1 = temp.substring(0, n);
                    if (x1 == '')
                        x1 = '0';
                    var x2 = temp.substring(n);
                    x2 = x2.replace(/\./g, '');
                    x2 = x2.substring(0, 2);
                    return x1.replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '.' + x2;
                }
                else
                    return temp.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            });
        });
    }

    $(function() {
        $('#form-add-package').submit(function(e) {
            e.preventDefault()
            $('#loading').show()
            var formData = $(this).serialize()
            $.ajax({
                url: current_url + '&do=save_new',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(data) {
                    if (data.success === true) {
                        window.location.reload(true)
                    } else {
                        alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                    }
                },
                complete: function() {
                    $('#loading').hide()
                }
            })
        })

        $(document).on('submit', '#form-edit-package', function (e) {
            e.preventDefault()
            $('#loading').show()
            var formData = $(this).serialize()
            $.ajax({
                url: current_url + '&do=save_edit',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(data) {
                    if (data.success === true) {
                        window.location.reload(true)
                    } else {
                        alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                    }
                },
                complete: function() {
                    $('#loading').hide()
                }
            })
        })

        $('.btn-edit').click(function() {
            $('#box-modal-edit').empty()
            $.ajax({
                url: current_url + '&do=modal_edit_address',
                data: {
                    id: $(this).data('id')
                },
                success: function(data) {
                    console.log(data)
                    if (data != '') {
                        $('#box-modal-edit').append(data)
                        $('#modal-edit-package').modal('show')
                    }
                }
            })
        })

        $('.btn-delete').click(function() {
            if (confirm('Bạn có chắc chắn muốn xóa địa chỉ này?')) {
                $('#loading').show()
                $.ajax({
                    url: current_url + '&do=delete',
                    type: 'POST',
                    data: {
                        id: $(this).data('id')
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success === true) {
                            window.location.reload(true)
                        } else {
                            alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                        }
                    },
                    complete: function() {
                        $('#loading').hide()
                    }
                })
            }
        })

        format_number('#price')
        format_number('.price')
    })
</script>