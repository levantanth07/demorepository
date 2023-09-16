<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<script src="/packages/core/includes/js/helper.js"></script>
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
    #master-product-select-box{}
    .__product_wrapper {
        display: flex;
        flex-direction: row;
    }

    .__product_wrapper .thumb {
        width: 40px;
        height: 40px;
        border-radius: 3px;
        margin-right: 10px;
        overflow: hidden;
        background: #f1f1f1;
    }

    .__product_wrapper .thumb img {
        width: 100%;
        height: 100%;
        display: block;
    }

    .__product_wrapper .infomation .name {
        font-weight: bold;
    }

    .__product_wrapper .infomation .meta {
        color: #666;
        font-size: 11px;
    }
    .select2-results__option--highlighted .__product_wrapper .infomation .meta {
        color: #fff;
        font-size: 11px;
    }
    button.btn-change-code {
        padding: 3px 20px;
        outline: none;
        border: none;
        border-radius: 3px;
    }
    button.btn-change-code img {
        width: 20px;
    }
    option[standardized="1"] {
        background: #dbf4fd
    }
    .bg-success {
        background-color: #f2dede;
        padding: 15px;
        border-radius: 3px;
        color: #f10000;
    }
    input.error{
        border-color: red;
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
                        <td align="center" data-step="6" data-intro="Hoàn thành">
                            <a class="btn btn-primary" onclick="confirmSave(event)" style="margin-right: 5px">
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
                            <?php if(Form::$current->is_error()) echo Form::$current->error_messages();?>
                             <!-- Điều kiện khá lằng nhằng   -->
                            <?php if([[=is_obd=]] || intval([[=system_f0_id=]])):?>
                            <div class="form-group" id="master-product">
                                <label for="code">Sản phẩm hệ thống</label>
                                <select class="form-control" id="master-product-select-box" name="master_product">
                                    <option value="0">Chọn sản phẩm hệ thống</option>
                                </select>
                            </div>
                            <?php endif;?>

                            <input type="hidden" name="master_product_id" value="<?=URL::iget('master_product_id')?>">

                            <p class="bg-success">
                            Lưu ý quan trọng:<br>
                            - Sau khi Lưu xong sẽ không được chọn lại sản phẩm chuẩn hóa<br>
                            - Hãy chắc chắn bạn đã rà soát phần kho, sau khi chuẩn hóa xong các phần liên quan (nhập, xuất kho, thẻ kho, tồn kho) sẽ coi là sản phẩm mới
                            </p>

                            <div class="form-group" data-step="1" data-intro="Nhập mã hàng (Không sử dụng trùng mã)">
                                <label for="code">Mã sản phẩm / hàng hóa (*)</label>
                                <input name="code" type="text" id="code" class="form-control">
                            </div>
                            <div class="form-group" data-step="2" data-intro="Nhập tên hàng">
                                <label for="name">Tên sản phẩm / hàng hóa (*)</label>
                                <input name="name" type="text" id="name" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" data-step="3" data-intro="Nhập giá bán">
                                        <label for="price">Giá bán (*)</label>
                                        <input name="price" type="text" id="price" class="form-control" />
                                    </div>
                                    <!-- <div class="form-group" data-step="4" data-intro="Nhập giá vốn">
                                        <label for="import_price">Giá vốn (*)</label>
                                        <input name="import_price" type="text" id="import_price" class="form-control" value="<?=empty($_REQUEST['import_price']) ? 0 : $_REQUEST['import_price'];?>"/>
                                    </div> -->
                                    <!--IF:ton_kho_cond(Url::get('do')=='add')-->
                                    <div class="form-group" data-step="5" data-intro="Nhập tồn đầu kỳ">
                                        <label for="on_hand">Tồn kho đầu kỳ (*) </label>
                                        <input name="on_hand" type="number" id="on_hand" class="form-control">
                                        <div class="alert alert-warning-custom">
                                            Phiếu nhập kho đầu kỳ sẽ tự động sinh khi bạn nhập số tồn đầu kỳ
                                        </div>
                                    </div>
                                    <!--ELSE-->
                                    <div class="form-group">
                                        <label for="on_hand">Tồn kho bán hàng: </label>
                                        <strong class="text-danger">[[|on_hand|]]</strong>
                                    </div>
                                    <!--/IF:ton_kho_cond-->
                                    <div class="form-group">
                                        <label for="unit_id">Đơn vị</label>
                                        <UNIT id="units_select_box"></UNIT>

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
                                        <label for="weight">Trọng lượng (Gram) (*)</label>
                                        <input name="weight" type="text" id="weight" class="form-control" placeholder="Trường này không được để trống và phải có giá trị dương">
                                    </div>
                                    <div class="form-group">
                                        <label for="bundle_id">Loại</label>
                                        <BUNDLE id="bundles_select_box"></BUNDLE>
                                    </div>
                                    <div class="form-group">
                                        <label for="label_id">Nhãn</label>
                                        <input name="label_name" type="text" id="label_name" class="form-control">
                                        <input name="label_id" type="hidden" id="label_id" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="del">Tình trạng</label>
                                        <DEL id="del_select_box"></DEL>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="registration_certificate_id">Số hiệu giấy đăng kí công bố sản phẩm </label>
                                        <input name="registration_certificate_id" type="text" id="registration_certificate_id" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="registration_certificate_by">Nơi cấp giấy đăng kí công bố sản phẩm </label>
                                        <input name="registration_certificate_by" type="text" id="registration_certificate_by" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="registration_certificate_at">Ngày cấp giấy đăng kí công bố sản phẩm </label>
                                        <input name="registration_certificate_at" type="text" id="registration_certificate_at" class="form-control">
                                    </div>
                                    <?php if(Url::get('do')=='edit'): ?>
                                        <input type="hidden" name="standardized" value="<?php echo $this->map['standardized']; ?>">
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <div class="box box-default">
                                            <div class="box-header with-border">
                                                <div class="box-title">Ảnh sản phẩm</div>
                                            </div>
                                            <div class="box-body">
                                                <div class="wrapper-image text-center">
                                                    <!--IF:report_cond(Url::get('image_url'))-->
                                                    <img src="<?=Url::get('image_url')?>" alt="Ảnh sản phẩm" width="200">
                                                    <!--/IF:report_cond-->
                                                </div>
                                                <div class="box-image-upload">
                                                    <br>
                                                    <input type="file" class="form-control" name="image_url" accept="image/*">
                                                </div>
                                            </div>
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
    Number.prototype.format = function(n, x, s, c) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = this.toFixed(Math.max(0, ~~n));

        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
    };

    const DEL_SELECT_BOX = JSHELPER.render.select({
        data: {0: 'Kinh doanh', 1: 'Không kinh doanh'},
        selectAttrs: {name: 'del', id: 'del_select_box', class: 'form-control'},
        selected: '<?=URL::iget('del')?>'
    }).mount('#del_select_box')

    const UNITS_SELECT_BOX = JSHELPER.render.select({
        HTML_COLUMN: 'name',
        data: {0: 'Chọn', ...<?=json_encode([[=unit_id_list=]])?>},
        selectAttrs: {name: 'unit_id', id: 'units_select_box', class: 'form-control'},
        selected: '<?=URL::iget('unit_id')?>'
    }).mount('#units_select_box')

    const BUNDLES_SELECT_BOX = JSHELPER.render.select({
        HTML_COLUMN: 'name',
        data: {0: 'Chọn', ...<?=json_encode([[=bundle_id_list=]])?>},
        selectAttrs: {name: 'bundle_id', id: 'bundles_select_box', class: 'form-control'},
        selected: '<?=URL::iget('bundle_id')?>'
    }).mount('#bundles_select_box')

    const STANDARDIZED = <?=Url::iget('standardized')?>;

    Number.prototype.numberFormatNew = function(decimals, dec_point, thousands_sep) {
        dec_point = typeof dec_point !== 'undefined' ? dec_point : '.';
        thousands_sep = typeof thousands_sep !== 'undefined' ? thousands_sep : ',';

        var parts = this.toFixed(decimals).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

        return parts.join(dec_point);
    }

    const currentDate = new Date();
    $('#registration_certificate_at').change(function(){
        let methodName = new Date(this.value.replace(/^(\d{2})\/(\d{2})/, '$2/$1')).getTime() > currentDate.getTime() ? 'addClass' : 'removeClass';
        $(this)[methodName]('error')
    })

    $('#registration_certificate_at').datepicker({
        format:'dd/mm/yyyy',language:'vi'
    });

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

	    $('#weight').on('change', function(e) {
		    $(e.target).val($(e.target).val().replace(/[^\d]/g, ''))
	    })
	    $('#weight').on('keypress', function(e) {
		    keys = ['0','1','2','3','4','5','6','7','8','9']
		    return keys.indexOf(event.key) > -1
	    })

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

        format_number_money("#price");
        format_number_money("#import_price");
    })

    if(STANDARDIZED || (document.querySelector('input[name=master_product_id]').value - 0)){
        $('#code').prop('readonly', true);
        $('#name').prop('readonly', true);
        $('#weight').prop('readonly', true);
        $('#import_price').prop('readonly', true);

        BUNDLES_SELECT_BOX.disabled = true;
        UNITS_SELECT_BOX.disabled = true;
        // DEL_SELECT_BOX.disabled = true;
    }

    $('#label_name').prop('readonly', true);

    // HKD: Khi thêm mới sản phẩm
    // Disable các trường Mã sản phẩm, Tên sản phẩm, Giá vốn , Giá bán, Đơn vị, Phân loại, Nhãn sản phẩm
    if(<?=![[=is_edit=]] && ([[=is_obd=]] || intval([[=system_f0_id=]])) ? 'true' : 'false'?>){
        $('#code').prop('readonly', true);
        $('#name').prop('readonly', true);
        $('#weight').prop('readonly', true);
        $('#import_price').prop('readonly', true);
        BUNDLES_SELECT_BOX.disabled = true;
        UNITS_SELECT_BOX.disabled = true;
    } else {
        $('#code').prop('readonly', true);
        $('#name').prop('readonly', true);
        $('#weight').prop('readonly', true);
        $('#import_price').prop('readonly', true);
        BUNDLES_SELECT_BOX.disabled = true;
        UNITS_SELECT_BOX.disabled = true;
    }


    function confirmSave(e)
    {
        if($('input[name=master_product_id]').val() - 0 && !confirm('Bạn có chắc chắn muốn lưu lại ?')){
            return e.preventDefault();
        }

        $('#EditAdminProducts').submit();
    }
    $(function(){
        const AJAX_SEARCH_RESULT_LIMIT = 50;
        <?=intval([[=system_f0_id=]] || [[=is_obd=]])?> && $('#master-product-select-box').select2({
            ajax: {
                url: 'form.php?block_id=<?php echo Module::block_id(); ?>&do=search_master_product',
                data: function (params) {
                    return {
                        code: params.term,
                        name: params.term,
                        system: <?=intval([[=system_f0_id=]])?>,
                        status: 1,
                        limit: AJAX_SEARCH_RESULT_LIMIT,
                        p: params.page || 1
                    }
                },
                method: 'post',
                dataType: 'json',
                delay: 500,
                allowClear: true,
                cache: true,
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: Object.values(data.master_products),
                        pagination: {
                            more: 0//data.count - params.page * AJAX_SEARCH_RESULT_LIMIT
                        }
                    };
                },
            },
            placeholder: 'Tìm sản phẩm hàng hóa',
            minimumInputLength: 0,
            templateResult: templateResult,
            templateSelection: templateSelection,
        });

        function templateResult(product)
        {
            if(product.loading){
                return product.text;
            }

            return $(`<div class="__product_wrapper" ${product['status'] == 0 ? 'style="background: #ffb2b2;"' : ''}>
                <div class="thumb"><img src="${product['image']}" onerror="imageErrorHandle(this)"></div>
                <div class="infomation">
                    <div class="name">${product['name']}</div>
                    <div class="meta">${product['code']} - ${product['master_unit_name']} - ${product['master_bundle_name']}</div>
                </div>
              </div>`);
        }

        function templateSelection(product)
        {
            if(product.id === 0 || product.id ==='0'){
                return ;
            }

            $('input[name=master_product_id]').val(product.id);

            $('#code').val(product.code);
            $('#code').prop('readonly', true);

            $('#name').val(product.name);
            $('#name').prop('readonly', true);

            $('#import_price').val((product.cost_price - 0 || 0).format(0, 3, ',', '.'));
            $('#import_price').prop('readonly', true);

            $('#weight').val((product.weight - 0 || 0).format(0, 3, ',', '.'));
            $('#weight').prop('readonly', true);

            $('#label_id').val(product.label_id || 0);
            $('#label_name').val(product.label_name);
            $('#label_name').prop('readonly', true);

            BUNDLES_SELECT_BOX.innerHTML = `<option value="${product.master_bundle_id}">${product.master_bundle_name}</option>`;
            BUNDLES_SELECT_BOX.disabled = true;

            UNITS_SELECT_BOX.innerHTML = `<option value="${product.master_unit_id}">${product.master_unit_name}</option>`;
            UNITS_SELECT_BOX.disabled = true;

            // DEL_SELECT_BOX.innerHTML = `<option value="${product.status == '1' ? 0 : 1}">${product.status ? 'Kinh doanh' : 'Ngừng kinh doanh'}</option>`;
            // DEL_SELECT_BOX.disabled = true;

            return product.name;
        }
    });

    async function changeCode(e)
    {
        let el = e.target,
            code = el.dataset.code,
            loadingUrl = '/assets/vissale/images/loading_circle.gif',
            data = new FormData();

        el.innerHTML = '<img src="'+loadingUrl+'">';
        data.append('do', 'change_product_code');
        data.append('code', code);
        data.append('block_id', <?=Module::block_id()?>);

        try{
            let res = await fetch('/form.php', {method: 'post', body: data }).then(e => e.json())
            switch(res.status){
                case 'success':
                    alert(res.message);
                    $(el.parentElement.parentElement).next('br').remove()
                    return $(el.parentElement.parentElement).remove();

                case 'error':
                    alert(res.message);
                    el.innerHTML = 'đổi';
                    return '';
            }
        }catch(err){
            el.innerHTML = 'đổi';
        }
    }

    function imageErrorHandle(img){
        if(!img.dataset.error){
            img.src = '/assets/standard/images/no_image.png';
            img.dataset.error = 1;
        }
    }


</script>
<div id="loader_ajax"></div>
