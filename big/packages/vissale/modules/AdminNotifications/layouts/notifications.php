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
        100% { -webkit-transform: rotate(360deg); }
    }
    table.table {
        table-layout: fixed;
    }
    table.table th, table.table td {
        word-break: break-word;
    }
    .d-inline {
        display: inline-block;
    }
    span.required {
        color: red;
    }
</style>
<?php
    $notifications = [[=notifications=]];
    $groups = [[=groups=]];
    $notification_type_config = [1 => "Thông Thường", 2 => "Popup"];
?>
<div id="page">
    <section class="content-header">
        <h1 class="page-title"><?= [[=title=]] ?></h1>
    </section>
    <section class="content">
        <div class="text-right">
            <a href="#" class="btn btn-success" data-toggle="modal" data-target="#modal-notification"><i class="fa fa-plus-circle"></i> Thêm mới</a>
        </div>
        <div id="content">
            <div class="box box-solid">
                <div class="box-body">
                    <form class="form-inline donhang-search-form" method="post" id="donhang-search-form" autocomplete="off">
                        <input name="page" type="hidden" value="<?=  DataFilter::removeXSSinHtml($_GET['page']) ?>" />
                        <input name="page_no" type="hidden" />
                        <input name="do" type="hidden" value="search" />
                        <div class="form-group">
                            <input name="search_text" type="text" id="search_text" class="form-control" placeholder="Nội dung thông báo" style="width: 330px">
                        </div>
                        <div class="form-group">
                            <select name="noti_type" id="noti_type" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <input name="start_date" type="text" id="start_date" class="form-control" autocomplete="off" placeholder="Từ ngày" autocomplete="disabled">
                        </div>
                        <div class="form-group">
                            <input name="end_date" type="text" id="end_date" class="form-control" autocomplete="off" placeholder="Đến ngày" autocomplete="disabled">
                        </div>
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <button type="reset" class="btn btn-default btn-reset">Làm mới</button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped sticky-enabled tableheader-processed sticky-table">
                            <thead>
                                <tr>
                                    <th width="50">STT</th>
                                    <th width="200">Tiêu đề</th>
                                    <th>Nội dung</th>
                                    <th>Loại thông báo</th>
                                    <th width="300">Người nhận thông báo</th>
                                    <th width="200">Ngày tạo</th>
                                    <th width="150">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($notifications)):
                                $i = 1;
                                foreach ($notifications as $notification):
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $notification['title'] ?></td>
                                <td><?= nl2br($notification['content']) ?></td>
                                <td>
                                    <?php if ($notification['type'] == 2): ?>
                                        <div style="margin-bottom: 5px;"><span class="label label-primary"><?= $notification_type_config[$notification['type']] ?></span></div>
                                        <div>
                                            Từ ngày: <strong><?= date('d-m-Y', strtotime($notification['date_from'])) ?></strong>
                                             - Đến ngày: <strong><?= date('d-m-Y', strtotime($notification['date_to'])) ?></strong>
                                        </div>
                                    <?php else: ?>
                                        <div><span class="label label-success"><?= $notification_type_config[$notification['type']] ?></span></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= ($notification['is_public'] == 1) ? 'Tất cả' : $notification['group_name'] ?></td>
                                <td><?= date('d-m-Y H:i:s', strtotime($notification['created_at'])) ?></td>
                                <td>
                                    <a href="#" class="btn btn-warning btn-edit" data-id="<?= $notification['id'] ?>">Sửa</a>
                                    <a href="#" class="btn btn-danger btn-delete" data-id="<?= $notification['id'] ?>">Xóa</a>
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

<div id="box-modal-edit"></div>
<div id="loading"><span class="loader"></soan></div>

<div class="modal fade" id="modal-notification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="" id="form-add-notification" name="form_add_notification">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Thêm mới thông báo</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Tiêu đề thông báo</label>
                        <input  type="text" id="title" class="title form-control" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Nội dung thông báo</label>
                        <textarea  name="content" class="form-control content allow-enter" id="edit-content" cols="30" rows="5" placeholder="Nội dung thông báo" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Chọn công ty nhận thông báo</label>
                        <select  name="group_id[]" id="group_id" class="form-control group_id" multiple>
                            <option value="">Chọn công ty</option>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?= $group['id'] ?>"><?= $group['id'] . '-' . $group['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Loại thông báo</label>
                        <select  name="type" id="type" class="form-control" required>
                            <option value="1">Thông Thường</option>
                            <option value="2">Popup</option>
                        </select>
                    </div>
                    <div class="form-group form-group-date hidden">
                        <label>Thời gian hiển thị</label>
                        <div>
                            <div class="d-inline">Từ ngày <span class="required">*</span></div>
                            <div class="d-inline"><input type="text" class="form-control date-popup date_from" name="date_from" id="date_from" autocomplete="off"></div>
                            <div class="d-inline">Đến ngày <span class="required">*</span></div>
                            <div class="d-inline"><input type="text" class="form-control date-popup date_to" name="date_to" id="date_to" autocomplete="off"></div>
                        </div>
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    var current_url = window.location.href
    $(function() {
        $('.btn-edit').click(function(e) {
            e.preventDefault()
            $('#box-modal-edit').empty()
            var id = $(this).data('id')
            $('#loading').show()
            $.ajax({
                url: current_url + '&action=show-modal',
                data: {
                    id: id
                },
                success: function(data) {
                    $('#box-modal-edit').html(data)
                    $('#modal-edit-notification').modal('show')
                    $('.group_id').select2()
                    $('#date_from').datepicker({format:'dd-mm-yyyy',language:'vi'});
                },
                complete: function() {
                    $('#loading').hide()
                }
            })
        })

        $(document).on('change', '#type', function() {
            let type = $(this).val()
            if (type == 1) {
                $('.form-group-date').addClass('hidden')
                $('.date-popup').removeAttr('required')
                $('.date-popup').val('')
            } else {
                $('.form-group-date').removeClass('hidden')
                $('.date-popup').attr('required', true)
            }
        })

        $('.btn-delete').click(function(e) {
            e.preventDefault()
            if (confirm("Bạn có chắc chắn muốn xóa thông báo này không? Thao tác không thể phục hồi.")) {
                var id = $(this).data('id')
                $.ajax({
                    url: current_url + '&action=delete',
                    data: {
                        id: id
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.location.reload(true)
                        } else {
                            alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                        }
                    },
                    complete: function() {
                    }
                })
            }
        })

        $('.btn-status').click(function() {
            var status = $(this).data('status')
            $('#shipping_status').val(status)
            $('#donhang-search-form').submit()
        })

        $('.btn-reset').click(function() {
            $('#search_text').val('')
            $('#start_date').val('')
            $('#end_date').val('')
            // $('#donhang-search-form')[0].reset()
            $('#donhang-search-form').submit()
        })

        $(document).on('submit', '#form-edit-notification', function(e) {
            e.preventDefault()
            var content = $(this).find('.content').val()
            var group_id = $(this).find('.group_id').val()
            var id = $(this).find('.notification_id').val()
            var type = $(this).find('#type').val()
            var date_from = $(this).find('.date_from').val()
            var date_to = $(this).find('.date_to').val()
            var title = $(this).find('#title').val()
            if (type == 1) {
                date_from = ""
                date_to = ""
            }

            $('#loading').show();
            $.ajax({
                url: current_url + '&action=save-edit',
                type: 'POST',
                data: {
                    content: content,
                    group_id: group_id,
                    id: id,
                    date_from: date_from,
                    date_to: date_to,
                    type: type,
                    title: title
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
        })

        $('#form-add-notification').submit(function(e) {
            e.preventDefault()
            var content = $(this).find('.content').val()
            var group_id = $(this).find('.group_id').val()
            var type = $(this).find('#type').val()
            var date_from = $(this).find('.date_from').val()
            var date_to = $(this).find('.date_to').val()
            var title = $(this).find('#title').val()
            if (type == 1) {
                date_from = ""
                date_to = ""
            }

            $('#loading').show()
            $.ajax({
                url: current_url + '&action=add-new',
                type: 'POST',
                data: {
                    content: content,
                    group_id: group_id,
                    date_from: date_from,
                    date_to: date_to,
                    type: type,
                    title: title
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
        })

        $('.group_id').select2()

        $('#start_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
        $('#end_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
        $('#date_from').datepicker({format:'dd-mm-yyyy',language:'vi'});
        $('#date_to').datepicker({format:'dd-mm-yyyy',language:'vi'});
    })
</script>
