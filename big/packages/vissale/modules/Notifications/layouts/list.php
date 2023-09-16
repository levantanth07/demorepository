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
</style>

<div id="page">
    <section class="content-header">
        <h1 class="page-title"><i class="fa fa-bell-o"></i> <?= [[=title=]] ?></h1>
    </section>
    <section class="content">
        <div id="content">
            <div class="box box-solid">
                <div class="box-body">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home">Hệ thống</a></li>
                        <li><a data-toggle="tab" href="#menu1">Cá nhân</a></li>
                        <li><a data-toggle="tab" href="#menu_xuat_excel">Thông báo xuất excel</a></li>
                        <li><a data-toggle="tab" href="#menu_in_don">Thông báo in đơn</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <p>
                                <ul class="timeline">
                                <?php
                                $notifications = [[=p_notifications=]];
                                if (!empty($notifications)):
                                foreach ($notifications as $notification):
                                $link = 'javascript:void(0)';
                                if ($notification['notificationable_type'] == 1) {
                                $link = ''.Url::build('admin_orders').'&cmd=shipping_history&id=' . $notification['notificationable_id'];
                                }
                                ?>
                                <li>
                                    <i class="fa fa-clock-o bg-gray"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header"><span class="time text-bold"><?= date('d/m/Y H:i:s', strtotime($notification['created_at'])) ?></span></h3>
                                        <div class="timeline-body">
                                            <div><b><?= $notification['title'] ?></b></div>
                                            <?= $notification['content'] ?>
                                        </div>
                                    </div>
                                    <?php if ($notification['notificationable_type'] == 1): ?>
                                    <a href="<?= $link ?>" class="btn-abs" target="_blank">
                                    </a>
                                    <?php endif; ?>
                                </li>
                                <?php
                                endforeach;
                                endif;
                                ?>
                            </ul>
                            </p>
                        </div>
                        <div id="menu1" class="tab-pane fade">
                            <p>
                            <ul class="timeline">
                                <?php
                                $notifications = [[=u_notifications=]];
                                if (!empty($notifications)):
                                foreach ($notifications as $notification):
                                $link = 'javascript:void(0)';
                                if ($notification['notificationable_type'] == 1) {
                                $link = ''.Url::build('admin_orders').'&cmd=shipping_history&id=' . $notification['notificationable_id'];
                                }
                                ?>
                                <li>
                                    <i class="fa fa-clock-o bg-gray"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header"><span class="time text-bold"><?= date('d/m/Y H:i:s', strtotime($notification['created_at'])) ?></span></h3>
                                        <div class="timeline-body">
                                            <div><b><?= $notification['title'] ?></b></div>
                                            <?= $notification['content'] ?>
                                        </div>
                                    </div>
                                    <?php if ($notification['notificationable_type'] == 1): ?>
                                    <a href="<?= $link ?>" class="btn-abs" target="_blank">
                                    </a>
                                    <?php endif; ?>
                                </li>
                                <?php
                                endforeach;
                                endif;
                                ?>
                            </ul>
                            </p>
                        </div>
                        <div id="menu_xuat_excel" class="tab-pane fade">
                            <p>
                            <ul class="timeline">
                                <?php
                                $notifications = [[=e_notifications=]];
                                if (!empty($notifications)):
                                foreach ($notifications as $notification):
                                $link = 'javascript:void(0)';
                                if ($notification['notificationable_type'] == 1) {
                                $link = ''.Url::build('admin_orders').'&cmd=shipping_history&id=' . $notification['notificationable_id'];
                                }
                                ?>
                                <li>
                                    <i class="fa fa-clock-o  "></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header"><span class="time text-bold"><?= date('d/m/Y H:i:s', strtotime($notification['created_at'])) ?></span></h3>
                                        <div class="timeline-body">
                                            <div><b><?= $notification['title'] ?></b></div>
                                            <?= $notification['content'] ?>
                                        </div>
                                    </div>
                                    <?php if ($notification['notificationable_type'] == 1): ?>
                                    <a href="<?= $link ?>" class="btn-abs" target="_blank">
                                    </a>
                                    <?php endif; ?>
                                </li>
                                <?php
                                endforeach;
                                endif;
                                ?>
                            </ul>
                            </p>
                        </div>
                        <div id="menu_in_don" class="tab-pane fade">
                            <p>
                            <ul class="timeline">
                                <?php
                                $notifications = [[=pr_notifications=]];
                                if (!empty($notifications)):
                                foreach ($notifications as $notification):
                                $link = 'javascript:void(0)';
                                if ($notification['notificationable_type'] == 1) {
                                $link = ''.Url::build('admin_orders').'&cmd=shipping_history&id=' . $notification['notificationable_id'];
                                }
                                ?>
                                <li>
                                    <i class="fa fa-clock-o  "></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header"><span class="time text-bold"><?= date('d/m/Y H:i:s', strtotime($notification['created_at'])) ?></span></h3>
                                        <div class="timeline-body">
                                            <div><b><?= $notification['title'] ?></b></div>
                                            <?= $notification['content'] ?>
                                        </div>
                                    </div>
                                    <?php if ($notification['notificationable_type'] == 1): ?>
                                    <a href="<?= $link ?>" class="btn-abs" target="_blank">
                                    </a>
                                    <?php endif; ?>
                                </li>
                                <?php
                                endforeach;
                                endif;
                                ?>
                            </ul>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    var page = 1
    var has_data = true
    var _throttleTimer = null;
    var _throttleDelay = 100;
    var scroll_to_bottom = false

    $(function() {
        $(window).scroll(function() {
           if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                if (!scroll_to_bottom) {
                    scroll_to_bottom = true
                    page += 1

                    if (has_data) {
                        $.ajax({
                            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                            data : {
                                'do':'ajax_load_data',
                                'page':page
                            },
                            dataType: 'json',
                            success: function(data) {
                                // console.log(data)
                                if (data.success) {
                                    if (data.html != '') {
                                        $('.timeline').append(data.html)
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
    })
</script>