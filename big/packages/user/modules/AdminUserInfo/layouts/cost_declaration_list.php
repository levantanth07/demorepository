<div class="container full" id="editOrderFormWrapper">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('admin_group_info')?>">Tỷ lệ doanh thu</a></li>
        </ol>
    </nav>

    <form name="CostDeclaration" method="post" autocomplete="off" role="presentation">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Danh sách Tỷ lệ ước chừng</h3>
                <div class="box-tools pull-right">
                    <a href="<?=URL::build_current(['do'=>'cost_declaration', 'act' => 'add'])?>" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Thêm mới</a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-12">
                        <?php $this->renderSuccess();?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Thời gian</th>
                                    <!-- <th>Giá vốn</th> -->
                                    <th>Chi phí Lương</th>
                                    <th>Cước COD</th>
                                    <th>Cước ĐT</th>
                                    <th>Cước hoàn</th>
                                    <th style="text-align: center;">CP khác <br><small style="font-weight: normal;">(CP via BM, CP truyền thông ...)</small></th>
                                    <th>Cước tiền nhà</th>
                                    <th>Lịch sử</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->map['rows'] as $key => $row) : ?>
                                <tr>
                                    <td><?=++$key?></td>
                                    <td><?=date('m-Y', strtotime($row['time']))?></td>
                                    <td><?=$row['chi_phi_luong']?>%</td>
                                    <td><?=$row['cuoc_cod']?>%</td>
                                    <td><?=$row['cuoc_dt']?>%</td>
                                    <td><?=$row['cuoc_hoan']?>%</td>
                                    <td><?=$row['cuoc_khac']?>%</td>
                                    <td><?=$row['cuoc_tien_nha']?>%</td>
                                    <td>
                                        <span>Người tạo: <i><?=$row['created_name']?></i></span>
                                        <span>Ngày tạo: <i><?=date('d-m-Y H:i:s', strtotime($row['created_at']))?></i></span>
                                        <?php if ($row['updated_name']) :?>
                                        <span>Người cập nhật: <i><?=$row['updated_name']?></i></span>
                                        <span>Ngày cập nhật: <i><?=date('d-m-Y h:i:s', strtotime($row['updated_at']))?></i></span>
                                        <?php endif;?>
                                    </td>
                                    <td>
                                        <?php if (date('Ym') - date('Ym', strtotime($row['time'])) <= 0) :?>
                                        <a href="<?=URL::build_current(['do'=>'cost_declaration', 'act' => 'edit', 'id' => $row['id']])?>" class="btn btn-warning btn-xs btn-edit">Sửa</a>
                                        <a href="<?=URL::build_current(['do'=>'cost_declaration', 'act' => 'delete', 'id' => $row['id']])?>" class="btn btn-danger btn-xs btn-delete">Xóa</a>
                                        <?php endif;?>
                                    </td>
                                </tr>   
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php require_once ROOT_PATH . 'packages/user/modules/AdminUserInfo/layouts/cost_declaration_static.php'; ?>