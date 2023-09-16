<style type="text/css">
    .normal-text{font-style: normal; color: #333; font-weight: normal}
    .thumb-wrapper {display: flex; }
    .thumb {    width: 80px; height: 45px; overflow: hidden; border-radius: 3px; margin: 0 20px 0 0; justify-content: center; align-items: center; display: flex; box-shadow: 0 0 3px 0 #c1c1c1; background: #f3f3f3;}
    #product-system-wrapper img {display: block; max-width: 100%; }
    .nav-tabs>li:first-child{ margin-left: 15px; }
    .pagenav {display: flex; align-items: center; }

    .pagenav b {margin: 0 10px; }
    .pagenav .pagination {    flex-grow: 1; justify-content: flex-end; display: flex;}
    td.history-edited i{color: #888}
    td.history-edited p{margin: 0; font-size: 14px}
</style>
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<script src="/packages/core/includes/js/helper.js"></script>
<?php
    $master_products = [[=master_products=]];
    $master_units = [[=master_units=]];
    $master_bundles = [[=master_bundles=]];
    $bundle_options = [[=bundle_options=]];
    $labels = [[=labels=]];
    $filters = MasterProductForm::$filters;
    $tableNumRow = 0;
?>
<script>
    const bundle_options = <?php echo $bundle_options; ?>;
</script>
<div id="product-system-wrapper" class="container-fluid" style="padding: 30px">
    <div class="row">
        <div class="col-xs-12">
            <form method="get" enctype="multipart/form-data" action="/index062019.php" id="masterProductForm">
                <input type="hidden" name="page" value="product_admin">
                <input type="hidden" name="do" value="master_product">
                <input type="hidden" name="status" value="<?=$filters['status']?>">
                <input type="hidden" name="method" value="delete" disabled>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong style="line-height: 30px">Danh sách sản phẩm chuẩn hóa</strong>
                        <div class="pull-right">
                            <a class="btn btn-primary" href="/index062019.php?page=product_admin&do=master_product&view=create">Thêm sản phẩm</a>
                            <!-- <a class="btn btn-success" href="#" data-toggle="modal" data-target=".import-excel-modal">Import sản phẩm</a> -->
                            <a class="btn btn-success" href="/form.php?submit=1&do=export_excel_master_product&block_id=<?=Module::block_id()?>">Export sản phẩm</a>
                        </div>
                    </div>

                    <div class="panel-body form-inline">
                        <?php if(Form::$current->is_error()) echo Form::$current->error_messages();?>
                        <?php Form::draw_flash_message_success('update_success');?>
                        <?php Form::draw_flash_message_success('delete_message');?>
                        <?php Form::draw_flash_message_success('create_success');?>
                        <div class="nav">
                            <div class="form-group">
                                <input name="code" id="code" class="form-control" type="text"
                                value="<?=isset($_REQUEST['code']) ? $_REQUEST['code'] : '' ?>" placeholder="Nhập mã sản phẩm">
                            </div>
                            <div class="form-group">
                                <input name="pname" id="name" class="form-control" type="text"
                                value="<?=isset($_REQUEST['pname']) ? $_REQUEST['pname'] : '' ?>" placeholder="Nhập tên sản phẩm">
                            </div>
                            <div class="form-group">
                                <input name="full_name" id="name" class="form-control" type="text"
                                value="<?=isset($_REQUEST['full_name']) ? $_REQUEST['full_name'] : '' ?>" placeholder="Nhập tên sản phẩm chi tiết">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="bundle" id="bundle"></select>
                            </div>
                            <div class="form-group">
                                <!-- Custom tag làm điểm gắn kết SELECT -->
                                <JSHELPER id="select_label"></JSHELPER>
                            </div>

                            <div class="form-group">
                                <!-- Custom tag làm điểm gắn kết SELECT -->
                                <JSHELPER id="select_unit"></JSHELPER>
                            </div>

                            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                            <a href="/index062019.php?page=product_admin&do=master_product" class="btn btn-success" type="reset">Làm mới</a>
                        </div>
                    </div>

                    <ul class="nav nav-tabs">
                        <li role="presentation" class="<?=$filters['status'] ? 'active' : ''?>">
                            <a href="/index062019.php?page=product_admin&do=master_product&status=1">Đang kinh doanh</a>
                        </li>
                        <li role="presentation" class="<?=!$filters['status'] ? 'active' : ''?>">
                            <a href="/index062019.php?page=product_admin&do=master_product&status=0">Ngừng kinh doanh</a>
                        </li>
                    </ul>

                    <!-- Table -->
                    <table class="table table-bordered ">
                        <thead>
                            <tr>
                                <th width="80px">Code</th>
                                <th width="80px">Ảnh</th>
                                <th width="200px">Tên sản phẩm chi tiết</th>
                                <th width="200px">Tên sản phẩm</th>
                                <th>Phân loại</th>
                                <th>Đơn vị</th>
                                <th>Trọng lượng</th>
                                <th>Nhãn</th>
                                <th>Lịch sử chỉnh sửa</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($master_products as $product):?>
                            <tr id="product-<?=$product['id']?>">
                                <th><?=$product['code']?></th>
                                <td>
                                    <div class="thumb">
                                        <img onerror="imageErrorHandle(this)" src="<?=$product['image'] ? $product['image'] : '/assets/standard/images/no_image.png'?>" />
                                    </div>
                                </td>
                                <td><?=$product['full_name']?></td>
                                <td><?=$product['name']?></td>
                                <td><?=$product['master_bundle_name']?></td>
                                <td><?=$product['master_unit_name']?></td>
                                <td><?=number_format($product['weight'])?>g</td>
                                <td><?=$product['label_name']?></td>
                                <td class="history-edited">
                                    <?php if($product['created_username']):?>
                                    <p>
                                        <i class="fa fa-plus-circle"></i>
                                        <strong><?=$product['created_username']?></strong> &bull;
                                        <?=date('d/m/Y H:i', strtotime($product['created_at']))?>
                                    </p>
                                    <?php endif;?>

                                    <?php if($product['updated_username'] && $product['all_fields_updated_at']):?>
                                    <p><i class="fa fa-pencil-square"></i>
                                        <strong><?=$product['updated_username']?></strong> &bull;
                                        <?=date('d/m/Y H:i', strtotime($product['all_fields_updated_at']))?>
                                    </p>
                                    <?php endif;?>
                                </td>
                                <td>
                                    <a class="btn btn-warning btn-xs" href="/index062019.php?page=product_admin&do=master_product&view=update&id=<?=$product['id']?>">Sửa</a>

                                    <a style="margin: 0 5px; display: none" onclick="!confirm('Bạn có chắc chắn muốn xóa không ?') ? event.preventDefault() : document.querySelector('input[value=delete]').disabled = false;" class="btn btn-danger btn-xs" href="/index062019.php?page=product_admin&do=master_product&method=delete&id=<?=$product['id']?>&form_block_id=<?=Module::block_id();?>">Xóa</a></td>
                            </tr>
                        <?php endforeach;?>
                        <tr> <td colspan="8">
                        <?php if(empty($master_products)): ?>
                            <div class="well well-sm" style="margin-bottom: 0">Không có sản phẩm nào !</div>
                        <?php else:?>
                            <div class="pagenav">
                                Hiển thị: <input type="text"
                                value="[[|item_per_page|]]"
                                name="item_per_page"
                                id="itemPerPage"
                                style="border: 1px solid #ccc; padding: 3px; outline: none; border-radius: 3px; width: 60px; margin-left: 5px; margin-right: 5px;"
                                onkeyup="this.value = this.value.replace(/\D/g, '');">
                                Tổng sản phẩm: <b>[[|count|]]</b>
                                 [[|paging|]]
                            </div>
                        <?php endif;?>
                        </td></tr>
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Import excel modal -->
            <div class="modal fade import-excel-modal" tabindex="-1" role="dialog" aria-labelledby="importExcelModal">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content" style="border-radius: 6px">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" id="myLargeModalLabel">Import excel sản phẩm hệ thống</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <form class="" id="import-excel-form">
                                        <div class="form-group">
                                            <label for="excel_file">Chọn file excel</label>
                                            <input name="excel_file" id="excel_file" class="form-control" type="file" value="" placeholder="Chọn file excel">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success">Import</a>
                                            <button type="button" class="btn btn-warning pull-right">Reset</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Import excel modal -->

        </div>
    </div>
</div>

<script type="text/javascript">
    const MODULE_ID = <?=Module::block_id()?>;
    $(function(){
        $('#import-excel-form').submit(async function(event){
            try{
                event.preventDefault();

                let data = new FormData();
                data.append('excel_file', $('#excel_file').prop('files')[0]);

                let options = {
                    method: 'post',
                    body: data
                };
                let result = await fetch('/form.php?submit=1&do=import_excel_master_product&block_id='+MODULE_ID, options).then(res => res.json());
                console.log(result);
            }catch(error){
                console.log(error);
            }
        });

        // let optionsData = {0: 'Tất cả hệ thống'},
        //     systems = <//json_encode(array_column($system_childs, 'name', 'id'))>;
        // dữ liệu từ php có dạng [
        // 0 => [
        //      id => 111,
        //      name => aaa
        // ],
        // 1 => [
        //      id => 222,
        //      name => bbb
        // ],
        //  ....]
        // => chuyển về dạng [111 => aaa, 222 => bbb, ...]
        // Khi render se thanh:
        // <option value="111">aaa</option>
        // <option value="222">bbb</option>
        // Thậm chí còn có nhiều tính năng nâng cao vui lòng xem doc lib

        // merge 2 object => {0: 'tat ca ..', 111: 'aaa', 222: 'bbb', ...}
        // Object.assign(optionsData, systems);

        // JSHELPER.render.select({
        //     data: {0: 'Phân loại', ...<?=json_encode(array_column($master_bundles, 'name', 'id'))?>},
        //     selected: <?=$filters['bundle']?>,
        //     selectAttrs: {class: 'form-control', name: "bundle", id: 'bundle', style: 'max-width: 250px'},
        // }).mount('#select_bundle');

        $('#bundle').select2({
            "data": bundle_options,
            "pagination": {
                "more": true
            }
        });
        JSHELPER.render.select({
            data: {0: 'Nhãn', ...<?=json_encode(array_column($labels, 'name', 'id'))?>},
            selected: <?=$filters['label']?>,
            selectAttrs: {class: 'form-control', name: "label", id: 'label', style: 'max-width: 250px'},
        }).mount('#select_label');

        JSHELPER.render.select({
            data: {0: 'Đơn vị', ...<?=json_encode(array_column($master_units, 'name', 'id'))?>},
            selected: <?=$filters['unit']?>,
            selectAttrs: {class: 'form-control', name: "unit", id: 'unit'}, style: 'max-width: 250px',
        }).mount('#select_unit');
        
    });

    function imageErrorHandle(img){
        if(!img.dataset.error){
            img.src = '/assets/standard/images/no_image.png';
            img.dataset.error = 1;
        }
    }
    $('body').on('keyup', '#itemPerPage', function(e){
        if (e.key === 'Enter' || e.keyCode === 13) {
            $('#masterProductForm').submit();
        }
    });
    $('body').on('change', '#itemPerPage', function(){
        $('#masterProductForm').submit();
    })
</script>
