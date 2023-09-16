<div class="container full" id="editOrderFormWrapper">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('admin_group_info')?>">Tỷ lệ doanh thu</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cập nhật</li>
        </ol>
    </nav>

    <form name="CostDeclaration" method="post" autocomplete="off" role="presentation">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Cập nhật tỷ lệ ước chừng các Chi phí</h3>
                <div class="box-tools pull-right">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> Lưu</button>
                    <a href="<?=URL::build_current(['do'=>'cost_declaration', 'act' => 'list'])?>" class="btn btn-default btn-sm">Thoát</a>
                </div>
            </div>
            <div class="box-body" style="max-width: 700px;">
                <div class="row">
                    <div class="col-xs-12">
                        <?php $this->renderErrors();?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        Chọn thời gian
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <select name="month" type="text" id="month" class="form-control">
                                <option value="12">Tháng 12</option>
                                <option value="11">Tháng 11</option>
                                <option value="10">Tháng 10</option>
                                <option value="9">Tháng 9</option>
                                <option value="8">Tháng 8</option>
                                <option value="7">Tháng 7</option>
                                <option value="6">Tháng 6</option>
                                <option value="5">Tháng 5</option>
                                <option value="4">Tháng 4</option>
                                <option value="3">Tháng 3</option>
                                <option value="2">Tháng 2</option>
                                <option value="1">Tháng 1</option>
                            </select>                                
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            <select name="year" type="text" id="year" class="form-control">
                                <option value="2021">Tháng 2021</option>
                                <option value="2022">Tháng 2022</option>
                            </select>                              
                        </div>
                    </div>
                </div>

                <!-- <div class="row">
                    <div class="col-xs-4">
                        Giá vốn (*)
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group pc-prefix">
                            <input name="gia_von" type="text" class="form-control">                                
                        </div>
                    </div>
                </div> -->

                <div class="row">
                    <div class="col-xs-4">
                        Chi phí Lương (*)
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group pc-prefix">
                            <input name="chi_phi_luong" type="text" class="form-control">                                
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-4">
                        Cước COD (*)
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group pc-prefix">
                            <input name="cuoc_cod" type="text" class="form-control">                                
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-4">
                        Cước ĐT (*)
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group pc-prefix">
                            <input name="cuoc_dt" type="text" class="form-control">                                
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-4">
                        Cước hoàn (*)
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group pc-prefix">
                            <input name="cuoc_hoan" type="text" class="form-control">                                
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-4">
                        Cước khác (*)
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group pc-prefix">
                            <input name="cuoc_khac" type="text" class="form-control">                                
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-4">
                        Cước tiền nhà (*)
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group pc-prefix">
                            <input name="cuoc_tien_nha" type="text" class="form-control">                                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php require_once ROOT_PATH . 'packages/user/modules/AdminUserInfo/layouts/cost_declaration_static.php'; ?>