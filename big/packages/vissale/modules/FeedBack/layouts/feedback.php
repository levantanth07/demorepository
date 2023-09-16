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
</style>
<?php
    $feedbacks = [[=feedbacks=]];
    $base_url = sprintf(
        "%s://%s/",
        System::getProtocol(),
        $_SERVER['SERVER_NAME']
    );
    $groups = [[=groups=]];
?>
<div id="page">
    <section class="content-header">
        <h1 class="page-title"><?= [[=title=]] ?></h1>
    </section>
    <section class="content">
        <div id="content">
            <div class="box box-solid">
                <div class="box-body">
                    <form class="form-inline donhang-search-form" method="post" id="donhang-search-form" autocomplete="off">
                        <input name="page" type="hidden" value="<?= DataFilter::removeXSSinHtml($_GET['page']) ?>" />
                        <input name="page_no" type="hidden" />
                        <input name="do" type="hidden" value="search" />
                        <?php
                            if (!empty($groups)):
                        ?>
                        <div class="form-group">
                            <select  name="group_id" id="group_id" class="form-control">
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
                            <select name="is_read" id="is_read" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <input name="start_date" type="text" id="start_date" class="form-control" autocomplete="off" placeholder="Từ ngày">
                        </div>
                        <div class="form-group">
                            <input name="end_date" type="text" id="end_date" class="form-control" autocomplete="off" placeholder="Đến ngày">
                        </div>
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <button type="reset" class="btn btn-default btn-reset">Làm mới</button>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped sticky-enabled tableheader-processed sticky-table">
                            <thead>
                                <tr>
                                    <th width="50">STT</th>
                                    <th>Người góp ý</th>
                                    <th>Ảnh chụp màn hình</th>
                                    <th>Nội dung</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($feedbacks)):
                                    $i = 1;
                                    foreach ($feedbacks as $feedback):
                                        $read = '';
                                        if ($feedback['is_read'] == 1) {
                                            $read = '<a href="javascript:void(0)" data-id="'. $feedback['id'] .'" class="btn-read" data-read="2"><span class="label label-success" title="Click để thay đổi trạng thái">Đã xem</span></a>';
                                        } else {
                                            $read = '<a href="javascript:void(0)" data-id="'. $feedback['id'] .'" class="btn-read" data-read="1"><span class="label label-danger" title="Click để thay đổi trạng thái">Chưa xem</span></a>';
                                        }
                                ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <div>#UserId: <?= $feedback['user_id'] ?> - <?= $feedback['username'] ?></div>
                                            <div>#GroupId: <?= $feedback['group_id'] ?> - <?= $feedback['group_name'] ?></div>
                                        </td>
                                        <td>
                                            <?php if (!empty($feedback['screenshot'])): ?>
                                                <a class="open-window" href="javascript:void(0)" data-href="<?= $feedback['screenshot'] ?>"><img src="<?= $feedback['screenshot'] ?>" width="300" alt="" /> </a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $feedback['content'] ?></td>
                                        <td><?= $read ?></td>
                                        <td><?= date('d-m-Y H:i:s', strtotime($feedback['created_at'])) ?></td>
                                        <td width="100"><a href="javascript:void(0)" class="btn btn-danger btn-delete-feedback" data-id="<?= $feedback['id'] ?>">Xóa</a></td>
                                    </tr>
                                <?php
                                    endforeach;
                                else:
                                ?>
                                <tr><td colspan="6" class="text-center">Chưa có dữ liệu !</td></tr>
                                <?php
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
<script>
    $(function() {
        $('.btn-reset').click(function() {
            $('#donhang-search-form')[0].reset()
            $('#donhang-search-form').submit()
        })

        $('#start_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
        $('#end_date').datepicker({format:'dd/mm/yyyy',language:'vi'});

        $('.btn-read').click(function() {
            let id = $(this).data('id')
            let read = $(this).data('read')
            $.ajax({
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                type: 'POST',
                data: {
                    id: id,
                    read: read,
                    cmd: 'change_status'
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        window.location.reload(true)
                    } else {
                        alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                    }
                }
            })
        })

        $('.btn-delete-feedback').click(function() {
            if (confirm("Bạn có chắc chắn muốn xóa góp ý này không? Thao tác không thể phục hồi?")) {
                let id = $(this).data('id')
                $.ajax({
                    url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                    type: 'POST',
                    data: {
                        id: id,
                        cmd: 'delete_feedback'
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.location.reload(true)
                            alert('Xóa thành công!')
                        } else {
                            alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau.')
                        }
                    }
                })
            }
        })

        $('.open-window').click(function(e) {
            e.preventDefault();
            let base64ImageData = $(this).attr('data-href')
            const contentType = 'image/png';

            const byteCharacters = atob(base64ImageData.substr(`data:${contentType};base64,`.length));
            const byteArrays = [];

            for (let offset = 0; offset < byteCharacters.length; offset += 1024) {
                const slice = byteCharacters.slice(offset, offset + 1024);

                const byteNumbers = new Array(slice.length);
                for (let i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                const byteArray = new Uint8Array(byteNumbers);

                byteArrays.push(byteArray);
            }
            const blob = new Blob(byteArrays, {type: contentType});
            const blobUrl = URL.createObjectURL(blob);

            window.open(blobUrl, '_blank');
        })
    })
</script>