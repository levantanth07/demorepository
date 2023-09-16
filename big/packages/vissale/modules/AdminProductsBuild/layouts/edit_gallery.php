<style>
    .wrapper-image ul {
        padding-left: 0px;
    }
    .wrapper-image ul li {
        position: relative;
        float: left;
        width: 27%;
        margin: 0 1% 2%;
        height: 66px;
        list-style-type: none;
        border: 1px dotted #ccc;
    }
    .box-image-upload, .lb-images {
        margin-left: 1%;
        clear: both;
    }
    .image-empty {
        border: 1px dotted #ccc;
    }
    .wrapper-image ul li img {
        width: 100%;
        height: 100%;
        vertical-align: middle;
        border-radius: 3px;
    }
    .imgDel {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 99;
        font-size: 20px;
        display: none;
    }
    .clearfix:after {
        clear: both;
        display: block;
        content: ""
    }
    #box-errors {
        margin-top: 5px;
    }
    .wrapper-image ul li:hover .imgDel {
        display: block;
    }
</style>
<div class="container">
    <fieldset id="toolbar">
        <div id="toolbar-title">
            Quản lý sản phẩm
            <span>[ <?php if(Url::get('do')=='add'){echo 'Thêm mới';} if(Url::get('do')=='edit')
            {echo 'Sửa ' . Url::get('name');}?> ]</span>
        </div>
        <div id="toolbar-content" align="right">
            <table align="right" style="height: 58px;">
                <tbody>
                    <tr>
                        <td align="center">
                            <a class="btn btn-primary" onclick="EditAdminProducts.submit();" style="margin-right: 5px">
                            <i class="glyphicon glyphicon-floppy-disk"></i> Ghi lại </a>
                        </td>
                        <td align="center">
                            <a class="btn btn-default" href="<?php echo Url::build_current(array('do'=>'list'));?>#">
                            <i class="glyphicon glyphicon-log-out"></i> Quay lại </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <br clear="all"/>
    <form name="EditAdminProducts" id="EditAdminProducts" method="POST" enctype="multipart/form-data">
        <fieldset id="add_test_products_form" class="row">
            <div class="col-md-12">
                <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>
                            <?php
                                if (Url::get('do')=='add') {
                                    echo 'Thêm mới';
                                } else if(Url::get('do')=='edit') {
                                    echo 'Sửa ' . Url::get('name');
                                }
                            ?>
                        </strong>
                    </div>
                    <div class="panel-body">
                            <div class="form-group">
                                <label for="code">Mã sản phẩm (*)</label>
                                <input name="code" type="text" id="code" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="name">Tên sản phẩm (*)</label>
                                <input name="name" type="text" id="name" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price">Giá (*)</label>
                                        <input name="price" type="text" id="price" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Giá vốn (*)</label>
                                        <input name="import_price" type="text" id="import_price" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label for="on_hand">Tồn kho (*)</label>
                                        <input name="on_hand" type="number" id="on_hand" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="color">Màu</label>
                                        <input name="color" type="text" id="color" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="size">Kích cỡ</label>
                                        <input name="size" type="text" id="size" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="bundle_id">Loại</label>
                                        <select name="bundle_id" id="bundle_id" class="form-control">
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="unit_id">Đơn vị</label>
                                        <select name="unit_id" id="unit_id" class="form-control">
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="del">Tình trạng</label>
                                        <select name="del" id="del" class="form-control">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="" class="lb-images">Ảnh sản phẩm</label>
                                        <div class="wrapper-image clearfix">
                                            <ul>
                                            <!--IF:report_cond(!empty([[=gallery_images=]]))-->
                                            <?php
                                                $gallery_images = [[=gallery_images=]];
                                                foreach ($gallery_images as $k => $gallery) {
                                            ?>
                                                <li>
                                                    <img src="<?= $gallery ?>" class="img-responsive" />
                                                    <a href="javascript:void(0)" class="imgDel icon" title="Xóa ảnh">
                                                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                                                    </a>
                                                    <input type="hidden" name="gallery_images[]" value="<?= $gallery ?>" />
                                                </li>
                                            <?php
                                                }
                                            ?>
                                            <!--ELSE-->
                                                <li class="image-empty"></li>
                                                <li class="image-empty"></li>
                                                <li class="image-empty"></li>
                                                <li class="image-empty"></li>
                                                <li class="image-empty"></li>
                                                <li class="image-empty"></li>
                                            <!--/IF:report_cond-->
                                            </ul>
                                        </div>
                                        <div class="box-image-upload">
                                            <input type="file" id="gallery_image" class="hidden" multiple accept="image/*">
                                            <a href="#" class="btn btn-default" id="upload-image">Chọn ảnh</a>
                                        </div>
                                        <div id="box-errors"></div>
                                        <div id="hidden-delete-files" class="hidden"></div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
</div>
<script>
    Number.prototype.numberFormatNew = function(decimals, dec_point, thousands_sep) {
        dec_point = typeof dec_point !== 'undefined' ? dec_point : '.';
        thousands_sep = typeof thousands_sep !== 'undefined' ? thousands_sep : ',';

        var parts = this.toFixed(decimals).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

        return parts.join(dec_point);
    }

    $(function() {
        var imageUploads = [];
        var size_accept = 2097152 // 2 Mb
        var errors = []
        var list_images = []
        var current_url = window.location.href
        var isUploading = false

        function ajax_upload_image() {
            if (list_images.length > 0) {
                if (isUploading) {
                    return;
                }

                isUploading = true
                $('#loader_ajax').show()
                var formData = new FormData();
                $.each(list_images, function (i, item) {
                    formData.append('image_files[]', item)
                })
                
                $.ajax({
                    url: current_url + '&type=ajax&action=upload_image',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        if ('success' in data) {
                            $.each(data.success, function (i, val) {
                                var htmlOnly = `
                                    <img src="`+ val +`" class="img-responsive" />
                                    <a href="javascript:void(0)" class="imgDel icon" title="Xóa ảnh">
                                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                                    </a>
                                    <input type="hidden" name="gallery_images[]" value="`+ val +`" />
                                `;
                                if ($('.image-empty:nth-child('+ (i + 1) +')').length) {
                                    $('.image-empty:nth-child('+ (i + 1) +')').html(htmlOnly).removeClass('image-empty')
                                } else {
                                    $('.wrapper-image ul').append('<li>'+ htmlOnly +'</li>');
                                }
                            })
                        }
                    },
                    complete: function() {
                        isUploading = false
                        $('#gallery_image').val('')
                        $('#loader_ajax').hide()
                        list_images = []
                        errors = []
                    }
                })
            }
        }

        var imagesPreview = function(input) {
            errors = []
            if (input.files) {
                var filesAmount = input.files.length;

                for (i = 0; i < filesAmount; i++) {
                    if (input.files[i].size <= size_accept) {
                        list_images.push(input.files[i])
                    } else {
                        errors.push('Dung lượng file <b>'+ input.files[i].name +'</b> không được lớn quá 2Mb')
                    }
                }
            }

            ajax_upload_image()
            $('#box-errors').empty().removeClass('alert alert-danger')
            if (errors.length) {
                $('#box-errors').addClass('alert alert-danger');
                $.each(errors, function(i, val) {
                    $('#box-errors').append('<div>- '+ val +'</div>')
                })
            }
        };

        $('#gallery_image').change(function() {
            imagesPreview(this)
        })

        $('#upload-image').click(function(e) {
            e.preventDefault()
            $('#gallery_image').trigger('click')
        })

        $(document).on('click', '.imgDel', function() {
            if (confirm('Bạn có chắc chắn muốn xóa ảnh này không? Thao tác này không thể phục hồi.')) {
                if ($('.wrapper-image ul li').length > 1) {
                    $('#hidden-delete-files').append('<input type="hidden" name="delete_images[]" value="'+ $(this).prev().attr('src') +'">')
                    $(this).closest('li').remove()
                } else {
                    $(this).closest('li').empty().addClass('image-empty')
                }
            }
        })

        function format_number_money(element){
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

                calculate_order_price()
            });
        }

        format_number_money("#price")
    })
    
</script>
<div id="loader_ajax"></div>