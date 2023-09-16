<?php
    $bundles = MiString::array2js([[=bundles=]]);
    $masterProduct = [[=master_product=]];
    $defaultBundle = $masterProduct['bundle_id'];
?>

<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<script>
    const bundles = <?php echo $bundles; ?>;
    const defaultBundle = <?php echo $defaultBundle; ?>;
</script>

<div class="container-fluid" style="padding: 30px">
    <div class="row">
        <div class="col-xs-12">
            <form method="post" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong style="line-height: 30px">Sửa sản phẩm chuẩn hóa</strong>
                        <div class="pull-right">
                            <button class="btn btn-primary" type="submit" name="submit">Lưu sản phẩm</button>
                            <button class="btn btn-default"  data-link="<?=$this->map['redirect_after_update']?>" onclick="this.dataset.link ? (window.location.href = this.dataset.link) : history.back()"type="button">Quay lại</button>
                        </div>
                    </div>
                    <?php $master_product = [[=master_product=]] ?? 0;?>
                    <?php $product_system = [[=product_system=]] ?? 0;?>
                    <div class="panel-body">
                        <?php if(Form::$current->is_error()) echo Form::$current->error_messages();?>
                        <div class="form-group">
                            <label for="code">Mã sản phẩm / hàng hóa</label>
                            <input name="code" id="code" class="form-control" type="text" value="<?=$master_product['code']?>" readonly="true" placeholder="Mã sản phẩm (tự sinh)">
                        </div>
                        <div class="form-group">
                            <label for="name">
                                Tên sản phẩm chi tiết (*)
                                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Dành cho các lead hệ thống">
                                                            <i class="fa fa-question-circle"></i>
                                                        </a>
                            </label>
                            <input name="full_name" id="full_name" class="form-control" type="text" value="<?=$master_product['full_name']?>" placeholder="Tên sản phẩm chi tiết" autocomplete="off">
                            <div class="suggest" for="full_name">
                                <ul></ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">
                                Tên sản phẩm (*)
                                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Kí hiệu sản phẩm">
                                                            <i class="fa fa-question-circle"></i>
                                                        </a>
                            </label>
                            <input name="name" id="name" class="form-control" type="text" value="<?=$master_product['name']?>" placeholder="Tên sản phẩm" autocomplete="off">
                            <div class="suggest" for="name">
                                <ul></ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bundle_name">Phân loại sản phẩm (*)</label>
                            <select name="bundle_id" id="jsElBundleId" class="form-control" ></select>
                        </div>
                        <div class="form-group">
                            <label for="label_name">Nhãn sản phẩm (*)</label>
                            <input name="label_name" id="label_name" class="form-control" type="text"
                            value="<?=$master_product['label_name']?>" placeholder="Nhãn sản phẩm">
                        </div>
                        <div class="form-group">
                            <label for="unit_name">Đơn vị sản phẩm (*)</label>
                            <input name="unit_name" id="unit_name" class="form-control" type="text" value="<?=$master_product['master_unit_name']?>" placeholder="Đơn vị sản phẩm">
                        </div>
                        <div class="form-group">
                            <label for="cost_price">Giá vốn (*)</label>
                            <input name="cost_price" id="cost_price" class="form-control" type="text"
                            value="<?=$master_product['cost_price']?>" placeholder="Giá vốn">
                        </div>
                        <div class="form-group">
                            <label for="weight">Trọng lượng (*)</label>
                            <input name="weight" id="weight" class="form-control" type="text"
                                   value="<?=$master_product['weight']?>" placeholder="Trọng lượng">
                        </div>
                        <div class="form-group">
                            <label for="factory_product_code">Mã nhà máy</label>
                            <input name="factory_product_code" id="factory_product_code" class="form-control" type="text" value="<?=$master_product['factory_product_code']?>" placeholder="Mã nhà máy">
                        </div>
                        <div class="form-group">
                            <label>Tình trạng kinh doanh (*)</label><br>
                            <label class="checkbox-inline">
                                <input name="status" type="radio" value="1" <?=$master_product['status'] ? 'checked' : ''?>> Đang kinh doanh</label>
                            <label class="checkbox-inline">
                                <input name="status" type="radio" value="0"<?=!$master_product['status'] ? 'checked' : ''?>> Ngừng kinh doanh</label>
                        </div>

                        <div class="form-group">
                            <label for="note">Ghi chú</label>
                            <textarea name="note" id="note" class="form-control" placeholder="Ghi chú"><?=!empty($master_product['note']) ? $master_product['note'] : '' ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Ảnh đại diện</label><br>
                            <div class="thumb-wrapper">
                                <div class="thumb">
                                    <img src="<?=$master_product['image']?>" alt="">
                                </div>
                                <input name="image" type="file" accept="image/jpg,image/gif,image/png,image/jpeg,image/swf,image/ico">
                            </div>
                        </div>


                        <input type="hidden" name="method" value="put">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        let html = '';
        for (const bundleId in bundles) {
            let bundleName = bundles[bundleId].name;
            html += `<option value='${bundleId}'>${bundleName}</option>`;
        }//end for

        $('#jsElBundleId').html(html).val(defaultBundle).change();
        $('#jsElBundleId').select2();
    })
</script>
<?php require_once ROOT_PATH . 'packages/vissale/modules/AdminProductsBuild/layouts/statics/master_product.php'; ?>
