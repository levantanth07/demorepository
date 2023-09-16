<?php
    $items = [[=order_revisions=]];
?>
<div class="box-header with-border">
    <h4 class="box-title"><i class="glyphicon glyphicon-time"></i> Lịch sử đơn hàng </h4>
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
</div>
<div class="box-body row">
    <div id="lich_su_do_hang" class="col-xs-12" style="height: 500px !important;overflow-y:auto;">
        <div class="page">
            <div class="page__demo">
                <div class="main-container page__container">
                    <div class="timeline" id="result">
                        <?php foreach($items as $key => $item): ?>
                        <div class="timeline__group">
                            <span class="timeline__year"><?php echo date('d/m/Y',$item['id']) ?></span>
                            <?php foreach($item['arr'] as $k => $v): ?>
                            <div class="timeline__box">
                                <div class="timeline__date" >
                                    <span class="timeline__month" >
                                        <?php if(!($v['before_order_status'])): ?>
                                            <i class="fa fa-folder-open"></i>
                                        <?php else: ?>
                                            <i class="fa fa-info-circle"></i>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="timeline__post">
                                    <div class="timeline__content">
                                        <div class="box box-default box-solid">
                                            <div class="box-header">
                                                <div class="box-title">
                                                    <span class="label label-warning small" style="padding: 2px;"><?php echo $v['user_created_name'] ?></span>
                                                </div>
                                                <div class="box-tools pull-right" style="position: absolute;top:-12px;">
                                                    <span class="label label-default">
                                                        <i class="fa fa-clock-o"></i>
                                                        <?php echo date('H:i:s',strtotime($v['created'])) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="box-body" style="padding:5px;">
                                                <?php if(($v['before_order_status'])): ?>
                                                    Chuyển trạng thái từ: <strong><?php echo $v['before_order_status'] ?></strong> thành <strong><?php echo $v['order_status'] ?></strong>
                                                <?php else: ?>
                                                    <div><?php echo $v['data'] ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="text-center">
    <div class="label label-danger">Lịch sửa đơn hàng lưu tối đa 3 tháng.</div>
</div>
