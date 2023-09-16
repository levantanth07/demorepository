<style>
    .btn-delete {
        color: red;
    }
    td a.btn {
    margin: 3px;
}
</style>
<script>
    function check_selected()
    {
        var status = false;
        jQuery('form :checkbox').each(function(e) {
            if(this.checked)
            {
                status = true;
            }
        });
        return status;
    }
    function make_cmd(cmd)
    {
        jQuery('#do').val(cmd);
        document.TestAdminProducts.submit();
    }
</script>
<script src="/packages/core/includes/js/helper.js"></script>
<fieldset id="toolbar">
    <div id="toolbar-title">
        [[|title|]]
    </div>
    <?php
                        if(Url::get('del')){
                            $displayDivDienGiai = 'block';
                        }else{
                            $displayDivDienGiai = 'none';
                        }
                    ?>
    <div id="toolbar-content" align="right" style="margin-right: 11px; margin-top: 10px;">
        <table align="right">
            <tbody>
                <tr>
                <?php if(Session::get('admin_group')){?>
                    <td id="toolbar-new-receive"  align="center">
                        <?php if(!ListAdminProductsBuildForm::$isOBD && empty(ListAdminProductsBuildForm::$system['f0'])): ?>
                        <a href="#" class="btn btn-default" data-toggle="modal" data-target="#ImportExcelModal"><span class="glyphicon glyphicon-floppy-open" aria-hidden="true"></span> Import Excel </a>
                        <?php endif; ?>
                        <a href="<?=Url::build_current(['do'=>'export_excel'])?>" class="btn btn-default"><span class="glyphicon fa fa-cloud-download" aria-hidden="true"></span> Export Excel </a>
                        <a data-step="1" data-intro="Thêm sản phẩm" class="btn btn-success"
                            href="<?php echo Url::build_current(array('do'=>'add'));?>"
                            role="button"> <i class="glyphicon glyphicon-plus"></i>Thêm Sản phẩm
                        </a>
                        </td>
                    <?php }?>
                </tr>
            </tbody>
        </table>
    </div>
</fieldset>
<br>
<fieldset id="toolbar" style="background-color: #fff;">
    <form class="form-inline" style="padding: 15px 0 0 0;" action="/<?php echo Url::build_current(array('do'=>'search'));?>">
        <?php if($this->map['num_product_need_update']): ?>
        <div id="alert-warning-updated" class="alert alert-warning" style="margin: 15px">
            Bạn có <span id="num_product_need_update"><?=$this->map['num_product_need_update']?></span> sản phẩm thay đổi, vui lòng bấm <button type="button" class="btn btn-primary btn-sm" onclick="onClickUpdateAll(this)">Cập nhật</button>
        </div>
        <?php endif;?>
        <input name="page" type="hidden" value="<?= Datafilter ::removeXSSinHtml($_GET['page']) ?>" />
        <input name="page_no" type="hidden" />
        <input name="do" type="hidden" value="search" />
        <div class="form-group col-md-3">
            <input name="search_text" type="text" id="search_text" class="form-control" placeholder="Nhập mã sản phẩm, tên sản phẩm">
        </div>
        <div class="form-group">
            <select name="bundle_id" id="bundle_id" class="form-control"></select>
        </div>

        <div class="form-group">
            <select name="unit_id" id="unit_id" class="form-control"></select>
        </div>

        <?php if(ListAdminProductsBuildForm::$isOBD): ?>
        <div class="form-group">
            <select name="label_id" id="label_id" class="form-control" style="max-width: 250px"></select>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <select name="del" id="del" class="form-control"></select>
        </div>

        <div class="form-group">
            <!-- Custom tag làm điểm gắn kết SELECT -->
            <JSHELPER id="standardized"></JSHELPER>
        </div>
        <script type="text/javascript">
            JSHELPER.render.select({
                data: {[-1]: 'Tất cả', 0: 'Chưa chuẩn hóa', 1: 'Đã chuẩn hóa'},
                selected: <?=!isset($_REQUEST['standardized']) ? -1 : $_REQUEST['standardized'];?>,
                selectAttrs: {class: 'form-control', name: "standardized", id: 'standardized'},
            }).mount('#standardized');
        </script>
        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Tìm kiếm</button>
    </form>


    <form name="TestAdminProducts" method="post" action="index062019.php?page=test-products">
        <div class="list-item" style="padding:15px; ">
            <div class="row">
                <div class="col-lg-12">
                    <ul class="nav nav-tabs">
                        <li <?php echo !Url::get('del')?' class="active"':'';?>><a href="<?php echo Url::build_current(array('keyword'))?>">Danh sách sản phẩm hàng hoá</a></li>
                        <li <?php echo Url::get('del')?' class="active"':'';?>><a style="color:#f00;" href="<?php echo Url::build_current(array('keyword','del'=>1))?>">Sản phẩm ngừng kinh doanh (Ẩn)</a></li>
                    </ul>
                    <!--IF:report_cond(!empty([[=items=]]))-->
                    <?php
                        $items = [[=items=]];
                        $current_url = sprintf(
                            '%s://%s/%s',
                            System::getProtocol(),
                            $_SERVER['HTTP_HOST'],
                            $_SERVER['REQUEST_URI']
                        );
                    ?>
                    <table class="table table-hover table-striped table-bordered" cellpadding="6" cellspacing="0" width="100%" style="#width:99%;margin-top:8px;" border="1" bordercolor="#E7E7E7" align="center">
                        <thead>
                            <tr>
                                <th width="1%" title="[[.check_all.]]">
                                    <input type="checkbox" value="1" id="TestAdminProducts_all_checkbox" onclick="select_all_checkbox(this.form,'TestAdminProducts',this.checked,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');"<?php if(URL::get('do')=='delete') echo ' checked';?>>
                                </th>
                                <th>STT</th>
                                <th width="1%">Ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Loại</th>
                                <th class="text-right">Giá bán</th>

                                <th>Thuộc tính</th>
                                <th>Đơn vị</th>
                                <th>Nhãn</th>
                                <th>Tồn kho</th>
                                <!-- <th>Số ĐH</th> -->
                                <th width="2%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                require_once('packages/vissale/lib/php/vissale.php');
                                $i = 1;
                                $group_id = Session::get('group_id');
                                $warehouse_id = get_default_warehouse($group_id);
                                foreach ($items as $item):
                                    $product_id = $item['id'];
                                    $on_hand = get_product_remain($product_id,$warehouse_id);
                            ?>
                            <tr id="product-<?=$item['id']?>">
                                <td width="1%"><input name="selected_ids[]" type="checkbox" value="<?= $item['id'] ?>" onclick="select_checkbox(this.form,'TestAdminProducts',this,'<?php echo Portal::get_setting('crud_list_item_selected_bgcolor','#FFFFEC');?>','<?php echo Portal::get_setting('crud_item_bgcolor','white');?>');" class="TestAdminProducts_checkbox" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td >
                                <td><?= $i++ ?></td>
                                <td><img src="<?= $item['image_url'] ?>" alt="" width="50"></td>
                                <td>
                                    <span class="product_name"><?= $item['name'] ?></span>
                                    <div class="small">
                                        Mã: <?= $item['code'] ?>
                                    </div>
                                </td>
                                <td class="bundle_name"><?= $item['bundles_name'] ?> </td>
                                <td class="text-right text-bold"><?= System::display_number($item['price']) ?></td>

                                <td>
                                    <ul>
                                        <li>Màu sắc: <?= $item['color'] ?></li>
                                        <li>Kích cỡ: <?= $item['size'] ?></li>
                                        <li>Trọng lượng: <span class="weight"><?= $item['weight'] ?></span> Gram</li>
                                    </ul>
                                </td>
                                <td class="unit_name"><?= $item['units_name'] ?></td>
                                <td class="label_name"><?= $item['label_name'] ?></td>
                                <td><?= System::calculate_number($on_hand); ?></td>
                                <!-- <td><?= $item['total_order'] ?></td> -->
                                <td align="left" width="2%">
                                <?php if(Session::get('admin_group')):?>
                                    <a class="btn btn-warning btn-sm" href="<?php echo Url::build_current(array(
                                        'id'=> $item['id'],
                                        'do'=>'edit','page_no','search_category_id'));?>" title="Sửa">
                                        Sửa
                                    </a>
                                    <?php if($item['standardized'] && $item['master_updated_at'] != $item['updated_at']):?>
                                    <a class="btn btn-primary btn-sm btn-update" href="javascript:void(0)" data-id="<?=$item['id']?>" title="Cập nhật" onclick="updateProduct(this)">
                                        Cập nhật
                                    </a>
                                    <?php endif;?>

                                    <!--IF:cond(Url::get('del') && !$item['total_order'])-->
                                        <a class="btn btn-danger btn-sm" href="<?php echo Url::build_current(array(
                                            'id'=> $item['id'],
                                            'do'=>'delete','page_no','search_category_id',
                                            'destination' => $current_url
                                        ));?>" class="btn-delete" title="Xóa" onClick="return confirm('Bạn có chắc chắn muốn xóa không? Thao tác này không thể phục hồi.')">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i> Xóa
                                        </a>
                                    <!--/IF:cond-->
                                <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!--ELSE-->
                        <div class="alert text-center">Không có dữ liệu!</div>
                    <!--/IF:report_cond-->
                </div>
            </div>
        </div>
        <div class="paging">
            <div class="row" style="display: flex;justify-content: center;">
                [[|paging|]]
            </div>
        </div>
        <div class="box" style="display: <?= $displayDivDienGiai ?>;">
         <!-- <div class="box"> -->
            <div class="box-header with-border">
                <h3 class="box-title">Diễn giải</h3>
                <div class="box-tools pull-right">
                    <!-- Buttons, labels, and many other things can be placed here! -->
                    <!-- Here is a label for example -->
                    <span class="label label-warning">Chú ý</span>
                </div>
                <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                - <strong>Số ĐH</strong>: Số lượng đơn hàng đã thêm sản phẩm không phân biệt trạng thái đơn hàng.<br>
                - Chỉ <span class="label label-danger">xoá</span> được sản phẩm khi Số ĐH = 0

            </div>
            <!-- /.box-body -->

            <!-- box-footer -->
        </div>
        <input type="hidden" name="do" value="" id="do">
    </form>
</fieldset>
<div class="modal fade" id="ImportExcelModal" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form name="ImportExcelForm" id="ImportExcelForm" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Import sản phẩm từ Excel</h4>
                </div>
                <div class="modal-body">
                    <div class="pd10 text-center">----------------Chọn file Excel-----------------</div><br>
                    <div class="error"><?php echo Form::error_messages();?></div>
                    File mẫu: <a title="Tải về file mẫu" href="assets/vissale/images/excel_sp_mau.xlsx?v=10022022" target="_blank"><img src="assets/vissale/images/file_excel_sp_mau.png?v=10022022" width="90%" alt=""></a>
                    <hr>
                    <div class="form-group">
                        <input  name="excel_file" type="file" id="excel_file" required>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <input name="_do" type="hidden" class="btn btn-primary" value="import_excel">
                    <input name="importExcelBtn" type="submit" class="btn btn-primary" value="Import">
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    Number.prototype.format = function(n, x, s, c) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = this.toFixed(Math.max(0, ~~n));

        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
    };

    /**
     * { function_description }
     *
     * @param      {<type>}  btn     The button
     */
    function updateProduct(btn)
    {
        if(!confirm('Có chắc chắn muốn Cập nhật thông tin sản phẩm mới nhất không?')){
            return;
        }

        $(btn).prop('disabled', true);
        $(btn).text('Updating ..');
        const ID = btn.dataset.id;

        $.post('/index062019.php?page=product_admin&do=update-from-master', {
            id: btn.dataset.id, form_block_id: <?=Module::block_id()?>
        })
        .then(function(response){
            $(btn).remove();
            let tr = document.querySelector('tr#product-' + ID);
            let num = $('#num_product_need_update').text() - 0;

            $('#num_product_need_update').text(--num);
            !num && $('#alert-warning-updated').remove();

            let bundle_name = tr.querySelector('.bundle_name')
            if (bundle_name) bundle_name.innerText = response.master_bundle_name;

            let unit_name = tr.querySelector('.unit_name')
            if (unit_name) unit_name.innerText = response.master_unit_name;

            let label_name = tr.querySelector('.label_name')
            if (label_name) label_name.innerText = response.label_name;

            let import_price = tr.querySelector('.import_price')
            if (import_price) import_price.innerText = (response.import_price - 0).format(0, 3, ',', '.');

            let weight = tr.querySelector('.weight')
            if (weight) weight.innerText = (response.weight - 0).format(0, 3, ',', '.');

            let product_name = tr.querySelector('.product_name')
            if (product_name) product_name.innerText = response.name;

            let img = tr.querySelector('img');
            if (img && response.image_url) img.src = response.image_url;
        })
        .fail(function(err){
            alert('Cập nhật thất bại, vui lòng thử lại.')
            $(btn).text('Cập nhật');
            $(btn).prop('disabled', false);
        });

    }

    /**
     * Called on click update all.
     *
     * @param      {<type>}  btn     The button
     */
    function onClickUpdateAll(btn)
    {
        if(!confirm('Việc này sẽ cập nhật toàn bộ sản phẩm có thay đổi, bạn chắc chắn ?')){
            return;
        }

        $(btn).prop('disabled', true);
        $(btn).text('Updating ..');

        $.ajax({
            type: 'POST',
            url: '/index062019.php?page=product_admin&do=update-all',
            data: {
                id: btn.dataset.id, form_block_id: <?=Module::block_id()?>
            },
            async: false
        }).done(function(response){
            $(btn).remove();
            location.reload();
        })
        .fail(function(err){
            alert('Cập nhật thất bại, vui lòng thử lại.')
            $(btn).text('Cập nhật');
            $(btn).prop('disabled', false);
        });
    }

    jQuery(document).ready(function(){
        jQuery('#from_bill_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
        jQuery('#to_bill_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
    });
</script>
