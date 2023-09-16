<style>
    body {
        height: auto;
    }
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
        border-top: 1px solid rgb(230, 236, 240)
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
    ul.list-group li:nth-child(2n) {
        background: rgb(245, 245, 245)
    }
    .timeline>li>.timeline-item>.timeline-header {
        font-size: 13px;
    }
    .timeline:before {
        background: rgb(221, 221, 221);
    }
    a.btn-abs {
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 99;
        left: 0;
        top: 0;
        right: 0;
    }
    ul.timeline li:hover .timeline-item {
        background: rgb(217, 237, 247)
    }
    h3.panel-heading-title {
        margin: 0px;
    }
    .cke_button__templateconsignment_label {
        display: inline;
    }
    .rowItemKeyword {
        margin-bottom: 5px;
    }
    .panel-heading {
        padding: 10px 15px;
    }
    #list-tabs {
        margin-bottom: 10px;
    }
    .float-left {
        float: left;
    }
    .media-left, .media>.pull-left {
        padding-right: 10px;
    }
    .media, .media-body {
        overflow: hidden;
        zoom: 1;
    }
    .media-body {
        width: 10000px;
    }
    .media-body, .media-left, .media-right {
        display: table-cell;
        vertical-align: top;
    }
    .media-heading {
        margin-top: 0;
        margin-bottom: 5px;
    }
    #add-note {
        /*resize: none;*/
        border: 2px solid rgb(164, 217, 249);
        border-radius: 5px;
        box-shadow: 0 0 0 1px #A4D9F9
    }
    .box-note {
        background: rgb(232, 245, 253);
        padding: 10px 12px;
    }
    .btn-note {
        margin-top: 5px;
    }
    .box-google-keep {
        border: 1px solid rgb(218, 220, 224);
        overflow: hidden;
        position: relative;
        border-radius: 8px;
        padding: 4px 20px 12px 16px;
        background-color: rgb(255, 244, 117);
    }
    .text-ellipsis {
        max-height: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        /*white-space: nowrap;*/
        word-break: break-word; 
        display: block;
        min-height: 150px;
    }
    a.pins, a.btn-delete-note {
        font-size: 20px;
        color: rgb(51, 51, 51);
        position: absolute;
        z-index: 99;
        top: 10px;
        right: 10px;
        display: none;
    }
    a.btn-delete-note {
        top: auto;
        bottom: 10px;
        color: red;
    }
    .box-google-keep:hover .pins, .box-google-keep:hover .btn-delete-note {
        display: block;
    }
    .masonry {
        display: grid;
        grid-gap: 1em;
        grid-template-columns: repeat(auto-fill, minmax(260px,1fr));
        grid-auto-rows: 0;
        margin-bottom: 30px;
    }
    .grid-item {
    }
    .title-pins {
        margin-bottom: 5px;
    }
    @media screen and (min-width: 768px) {
        .modal-dialog {
            width: 600px;
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
    .masonry-footer {
        margin-top: 5px;
        border-top: 1px dotted rgb(0, 0, 0);
        padding-top: 5px;
        font-size: 11px;
        color: rgb(153, 153, 153);
    }
</style>
<?php
    $note_no_pins = [[=note_no_pins=]]; // Không ghim
    $note_pins = [[=note_pins=]]; // Ghim
?>

<div id="page">
    <section class="content-header">
        <h1 class="page-title"><i class="fa fa-book"></i> <?= [[=title=]] ?></h1>
    </section>
    <section class="content">
        <div id="content">
            <div class="box box-solid">
                <form action="" method="POST" id="">
                    <div class="box-note">
                        <div class="media">
                            <div class="media-left">
                                <a href="#">
                                    <img class="media-object img-circle" src="/assets/vissale/images/default_profile.png" alt="...">
                                </a>
                            </div>
                            <div class="media-body">
                                <textarea name="content" id="add-note" cols="30" rows="5" class="form-control" placeholder="Viết ghi chú..." required></textarea>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-note"><i class="fa fa-pencil"></i> Tạo ghi chú</button>
                        </div>
                    </div>
                </form>
                <div class="box-body">
                    <?php if (!empty($note_pins)): ?>
                        <div class="title-pins"><b>ĐƯỢC GHIM</b></div>
                        <div class="masonry">
                            <?php foreach ($note_pins as $note): ?>
                                <div class="grid-item masonry-brick">
                                    <div class="box-google-keep masonry-content" data-id="<?= $note['id'] ?>"  title="Click để sửa">
                                        <?php if (!empty($note['title'])): ?>
                                            <h4><?= $note['title'] ?></h4>
                                        <?php endif; ?>
                                        <div class="text-ellipsis"><?= $note['content'] ?></div>
                                        <div class="masonry-footer text-right">Đã chỉnh sửa <?= date('d-m-Y H:i:s', strtotime($note['updated_at'])) ?></div>
                                        <a href="javascript:void(0)" title="Bỏ ghim ghi chú"
                                            class="pins" data-toggle="tooltip"
                                            data-placement="bottom"
                                            data-id="<?= $note['id'] ?>"
                                            data-pin="2"
                                        ><i class="fa fa-bookmark"></i></a>
                                        <a href="javascript:void(0)" title="Xóa ghi chú" class="btn-delete-note"
                                            data-toggle="tooltip" data-placement="top" data-id="<?= $note['id'] ?>"
                                        ><i class="fa fa-trash-o"></i></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($note_no_pins)): ?>
                        <?php if (!empty($note_pins)): ?>
                            <div class="title-pins"><b>KHÁC</b></div>
                        <?php endif; ?>
                        <div class="masonry masonary-no-pins">
                            <?php foreach ($note_no_pins as $note): ?>
                                <div class="grid-item masonry-brick">
                                    <div class="box-google-keep masonry-content" data-id="<?= $note['id'] ?>" title="Click để sửa">
                                        <?php if (!empty($note['title'])): ?>
                                            <h4><?= $note['title'] ?></h4>
                                        <?php endif; ?>
                                        <div class="text-ellipsis"><?= $note['content'] ?></div>
                                        <div class="masonry-footer text-right">Đã chỉnh sửa <?= date('d-m-Y H:i:s', strtotime($note['updated_at'])) ?></div>
                                        <a href="javascript:void(0)" title="Ghim ghi chú"
                                            class="pins" data-toggle="tooltip"
                                            data-placement="bottom"
                                            data-id="<?= $note['id'] ?>"
                                            data-pin="1"
                                        ><i class="fa fa-bookmark-o"></i></a>
                                        <a href="javascript:void(0)" title="Xóa ghi chú" class="btn-delete-note"
                                            data-toggle="tooltip" data-placement="top" data-id="<?= $note['id'] ?>"
                                        ><i class="fa fa-trash-o"></i></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>
<div id="box-note-modal"></div>
<div id="loading"><span class="loader"></soan></div>
<!-- <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.1/imagesloaded.pkgd.min.js"></script>
<script>
    $(function() {
        var editor = CKEDITOR.replace('add-note', {
            toolbar: [{
                name: 'colors',
                items: ['TextColor', 'BGColor']
            }, {
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat']
            }, {
                name: 'paragraph',
                items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
            }]
        });
        CKEDITOR.config.enterMode = CKEDITOR.ENTER_DIV
        editor.on("required", function(e) {
            editor.showNotification( 'Nội dung ghi chú bắt buộc phải nhập.', 'warning' );
            e.cancel();
        })

        function resizeMasonryItem(item) {
            /* Get the grid object, its row-gap, and the size of its implicit rows */
            var grid = document.getElementsByClassName('masonry')[0],
                rowGap = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-row-gap')),
                rowHeight = parseInt(window.getComputedStyle(grid).getPropertyValue('grid-auto-rows'));

            /*
             * Spanning for any brick = S
             * Grid's row-gap = G
             * Size of grid's implicitly create row-track = R
             * Height of item content = H
             * Net height of the item = H1 = H + G
             * Net height of the implicit row-track = T = G + R
             * S = H1 / T
             */
            var rowSpan = Math.ceil((item.querySelector('.masonry-content').getBoundingClientRect().height + rowGap) / (rowHeight + rowGap));

            /* Set the spanning as calculated above (S) */
            item.style.gridRowEnd = 'span ' + rowSpan;
        }

        /**
         * Apply spanning to all the masonry items
         *
         * Loop through all the items and apply the spanning to them using 
         * `resizeMasonryItem()` function.
         *
         * @uses resizeMasonryItem
         */
        function resizeAllMasonryItems() {
            // Get all item class objects in one list
            var allItems = document.getElementsByClassName('masonry-brick');

            /*
             * Loop through the above list and execute the spanning function to
             * each list-item (i.e. each masonry item)
             */
            for (var i = 0; i > allItems.length; i++) {
                resizeMasonryItem(allItems[i]);
            }
        }

        /**
         * Resize the items when all the images inside the masonry grid 
         * finish loading. This will ensure that all the content inside our
         * masonry items is visible.
         *
         * @uses ImagesLoaded
         * @uses resizeMasonryItem
         */
        function waitForImages() {
            var allItems = document.getElementsByClassName('masonry-brick');
            for (var i = 0; i < allItems.length; i++) {
                imagesLoaded(allItems[i], function(instance) {
                    var item = instance.elements[0];
                    resizeMasonryItem(item);
                });
            }
        }

        /* Resize all the grid items on the load and resize events */
        var masonryEvents = ['load', 'resize'];
        masonryEvents.forEach(function(event) {
            window.addEventListener(event, resizeAllMasonryItems);
        });

        /* Do a resize once more when all the images finish loading */
        waitForImages();

        var page = 1
        var has_data = true
        var scroll_to_bottom = false

        $(window).scroll(function() {
           if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                if (!scroll_to_bottom) {
                    scroll_to_bottom = true
                    page += 1

                    if (has_data) {
                        $.ajax({
                            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                            data : {
                                'cmd':'ajax_load_data',
                                'page':page
                            },
                            dataType: 'json',
                            success: function(data) {
                                if (data.success) {
                                    if (data.html != '') {
                                        $('.masonary-no-pins').append(data.html)
                                        // $('.masonary-no-pins .masonry-brick').removeAttr('style')
                                        waitForImages()
                                    } else {
                                        has_data = false
                                    }
                                }
                            },
                            complete: function() {
                                setTimeout(function() {
                                    scroll_to_bottom = false
                                }, 100)
                            }
                        });
                    }
                }
           }
        });

        $(document).on('click', '.pins', function(e) {
            var id = $(this).data('id');
            var is_pin = $(this).data("pin");
            $.ajax({
                method: "POST",
                url: "form.php?block_id=<?= Module::block_id(); ?>",
                data: {
                    cmd: "pin_note",
                    id: id,
                    is_pin: is_pin
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        window.location.reload(true)
                    } else {
                        alert("Có lỗi xảy ra. Bạn vui lòng thử lại sau.");
                        return false;
                    }
                }
            })
        })

        $(document).on('click', '.btn-delete-note', function(e) {
            var id = $(this).data('id');
            if (confirm("Bạn có chắc chắn muốn xóa ghi chú này không?")) {
                $.ajax({
                    method: "POST",
                    url: "form.php?block_id=<?= Module::block_id(); ?>",
                    data: {
                        cmd: "delete_note",
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.location.reload(true)
                        } else {
                            alert("Có lỗi xảy ra. Bạn vui lòng thử lại sau.");
                            return false;
                        }
                    }
                })
            }
        })

        $(document).on('click', '.box-google-keep', function(e) {
            var id = $(this).data('id')
            if (!$(e.target).closest(".btn-delete-note, .pins").length) {
                $.ajax({
                    url: "form.php?block_id=<?= Module::block_id(); ?>",
                    data: {
                        cmd: "show_modal_note",
                        id: id
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            $("#box-note-modal").empty().html(data.html)
                            var editorCk = CKEDITOR.replace('ckeditor', {
                                toolbar: [{
                                    name: 'colors',
                                    items: ['TextColor', 'BGColor']
                                }, {
                                    name: 'basicstyles',
                                    items: ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat']
                                }, {
                                    name: 'paragraph',
                                    items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                                }]
                            });
                            CKEDITOR.config.enterMode = CKEDITOR.ENTER_DIV
                            editorCk.on("required", function(e) {
                                editorCk.showNotification( 'Nội dung ghi chú bắt buộc phải nhập.', 'warning' );
                                e.cancel();
                            })
                            $("#notes-modal").modal("show")
                        } else {
                            alert("Có lỗi xảy ra. Bạn vui lòng thử lại sau.");
                            return false;
                        }
                    }
                })
            }
        })

        $(document).on("submit", "#frm-notes-modal", function(e) {
            e.preventDefault();
            var id = $(this).find("#note_id").val()
            var content = $(this).find("#ckeditor").val()
            var title = $(this).find("#title").val()
            var is_pin = 2
            if ($(this).find(".pin-note").is(":checked")) {
                is_pin = 1;
            }

            $('#loading').show()
            $.ajax({
                method: "POST",
                url: "form.php?block_id=<?= Module::block_id(); ?>",
                data: {
                    cmd: "update_note",
                    id: id,
                    content: content,
                    title: title,
                    is_pin: is_pin
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        window.location.reload(true)
                    } else {
                        alert("Có lỗi xảy ra. Bạn vui lòng thử lại sau.");
                        return false;
                    }
                },
                complete: function() {
                    $('#notes-modal').modal("hide")
                }
            })
        })
    })
</script>