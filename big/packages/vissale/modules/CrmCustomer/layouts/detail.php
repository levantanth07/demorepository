<?php
$title = 'Khách hàng '.[[=name=]];
System::set_page_title(Portal::get_setting('company_name','').' '.$title);?>
<style type="text/css">
    .callio-call-customer{
        cursor: pointer;
        align-items: center;
        background: #6B66C4;
        color: #fff;
        padding: 2px 4px;
        border-radius: 3px;
    }
    .voip24h-call-customer{
        cursor: pointer;
        align-items: center;
        background: #3c8dbc;
        color: #fff;
        padding: 2px 4px;
        border-radius: 3px;
    }
</style>
<div class="container full">
    <br>
	<div class="box box-info" id="print">
        <div class="box-header">
            <h3 class="box-title">
                Thông tin khách hàng <span class="label label-info" style="margin: 0 5px"><?=Url::get('cid')?></span> <span class="text-primary">[[|name|]]</span>
            </h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-warning" onclick="window.location='<?php echo Url::build_current(array('do'=>'edit','branch_id'));?>&cid=<?=([[=id=]]);?>';">
                    <i class="fa fa-pencil-square"></i> SỬA
                </button>
                <button type="button" class="btn btn-default" onclick="window.location='<?php echo Url::build_current(['branch_id']);?>';">
                    <i class="fa fa-hand-o-right"></i> Về danh sách
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-4">
                    <div class="text-center">
                        <div>
                            <img src="[[|image_url|]]" width="150" onerror="this.src='assets/standard/images/no_avatar.webp'" style="border:1px solid #CCCCCC;padding:5px;border-radius: 10px;">
                        </div>
                    </div><br>
                    <table class="table table-striped">
                        <tr>
                            <td width="120" align="right">Tên:</td>
                            <td width="150"><strong class="text-blue">[[|name|]]</strong> [[|customer_age|]]</td>
                        </tr>
                        <tr>
                            <td align="right">Đơn hàng mới nhất:</td>
                            <td align='left'>
                                Mã ĐH: [[|last_order_id|]]<br>
                                <span style='color: [[|last_order_status_color|]]'>[[|last_order_status|]]</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">Nhóm phân loại:</td>
                            <td>[[|crm_group_name|]]</td>
                        </tr>
                        <tr>
                            <td align="right">Di động:</td>
                            <td><strong>[[|mobile|]]</strong>
                                <?php if (Session::get('callio_payload')) { ?>
                                    <a class="fa fa-phone callio-call-customer" data-phonenumber="[[|mobile|]]"></a>
                                <?php } ?>
                                <?php if (Session::get('voip24h_payload')) { ?>
                                    <a class="fa fa-phone voip24h-call-customer" data-phonenumber="[[|mobile|]]"></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td width="120" align="right">Nghề nghiệp:</td>
                            <td nowrap="nowrap" width="150">[[|career|]]</td>
                        </tr>
                        <tr>
                            <td width="120" align="right">Chức vụ:</td>
                            <td nowrap="nowrap" width="150">[[|job_title|]]</td>
                        </tr>
                        <tr>
                            <td align="right">Cân nặng:</td>
                            <td>[[|weight|]] kg</td>
                        </tr>
                        <tr>
                            <td align="right">Ngày sinh:</td>
                            <td>[[|birth_date|]]</td>
                        </tr>
                        <tr>
                            <td align="right">Email:</td>
                            <td>[[|email|]]</td>
                        </tr>
                        <tr>
                            <td align="right">Khu vực: </td>
                            <td>[[|zone_name|]]</td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">Địa chỉ:</td>
                            <td colspan="3">[[|address|]]</td>
                        </tr>
                        <tr class="hidden">
                            <td align="right" valign="top">Người phụ trách:</td>
                            <td>[[|follow_user_name|]]</td>
                        </tr>
                        <tr class="hidden">
                            <td align="right" valign="top">Người giới thiệu:</td>
                            <td><a href="<?php echo Url::build_current(array('do'=>'view','id'=>md5([[=contact_id=]].CATBE)))?>" target="_blank">[[|contact_name|]]</a></td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">Thuộc:</td>
                            <td>[[|group_name|]]</td>
                        </tr>
                        <tr class="text-danger">
                            <td align="right" valign="top">Ghi chú cảnh báo:</td>
                            <td colspan="3">[[|warning_note|]]</td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">Ghi chú chung:</td>
                            <td colspan="3">[[|description|]]</td>
                        </tr>
                    </table>
                    <div class="panel panel-default">
                        <div class="panel-heading">Ngân hàng</div>
                        <div class="panel-body">
                            <div>Tên ngân hàng: [[|bank_name|]]</div>
                            <div>Số tài khoản ngân hàng: [[|bank_account_number|]]</div>
                            <div>Tên tài khoản ngân hàng: [[|bank_account_name|]]</div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-8">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#donhang"> <i class="fa fa-tag"></i> Đơn hàng</a></li>
                        <li class="hidden"><a data-toggle="tab" href="#thegiamgia">Thẻ giảm giá</a></li>
                        <li><a data-toggle="tab" href="#lichhen"><i class="fa fa-calendar"></i> Lịch hẹn</a></li>
                        <li><a data-toggle="tab" href="#ghichu"><i class="fa fa-file-text"></i> Ghi chú</a></li>
                        <li><a data-toggle="tab" href="#cuocgoi"><i class="fa fa-phone-square"></i> Cuộc gọi</a></li>
                        <li><a data-toggle="tab" href="#benhly"><i class="fa  fa-flask"></i> Bệnh lý</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="donhang" class="tab-pane fade in active">
                            <div class="panel panel-default">
                                <div class="panel-heading">Đơn hàng</div>
                                <div class="panel-body">
                                    <table class="table">
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã ĐH</th>
                                            <th>Trạng thái</th>
                                            <th>Thời gian</th>
                                            <th width="300">Ghi chú đơn</th>
                                            <th class="text-right">Tổng tiền</th>
                                        </tr>
                                        <?php $i=0?>
                                        <?php $total=0?>
                                        <!--LIST:orders-->
                                        <tr>
                                            <td><?= ++$i;?></td>
                                            <td><a target="_blank" href="index062019.php?page=admin_orders&cmd=edit&id=[[|orders.id|]]">[[|orders.id|]]</a></td>
                                            <td>
                                                [[|orders.status_name|]]
                                            </td>
                                            <td>
                                                <span class="small">Tạo: [[|orders.created|]]</span><br>
                                                <span class="small">Chốt: [[|orders.confirmed|]]</span>
                                            </td>
                                            <td>
                                                <p class="small" style="background-color: #EEEE99; border-radius:3px; border:1px solid #CCC;padding:2px;width: 100%;"><strong>Note chung</strong>: [[|orders.note1|]]</p>
                                                <p class="small" style="background-color: #EEEE99;border-radius:3px;border:1px solid #CCC;padding:2px;margin-top: 5px;width: 100%;"><strong>Ghi chú 2</strong>: [[|orders.note2|]]</p>
                                            </td>
                                            <td class="text-right">
                                                [[|orders.total_price|]]đ
                                                <?php $total += System::calculate_number([[=orders.total_price=]]);?>
                                            </td>
                                        </tr>
                                        <!--/LIST:orders-->
                                    </table>
                                    <div class="text-right"><span class="text-bold">Tổng tiền: <?=System::display_number($total);?> đ</span></div>
                                </div>
                            </div>
                        </div>
                        <div id="thegiamgia" class="tab-pane fade hidden">
                            <div class="panel panel-default">
                                <div class="panel-heading">Thẻ VIP</div>
                                <div class="panel-body">
                                    <table class="table">
                                        <tr>
                                            <th>Thẻ</th>
                                            <th>Giảm giá</th>
                                            <th>Bắt đầu</th>
                                            <th>Kết thúc</th>
                                        </tr>
                                        <!--LIST:cards-->
                                        <tr>
                                            <td>[[|cards.name|]]</td>
                                            <td>[[|cards.discount_rate|]]%</td>
                                            <td>[[|cards.start_date|]]</td>
                                            <td>[[|cards.end_date|]]</td>
                                        </tr>
                                        <!--/LIST:cards-->
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="ghichu" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-heading">Ghi chú</div>
                                <div class="panel-body">
                                    <ul class="timeline">
                                        <!--LIST:notes-->
                                        <li style="padding-left: 40px;margin-bottom: 0;">
                                            <table class="table table-striped" style="margin-bottom: 0;">
                                                <tr>
                                                    <td style="width:85%"><?php echo date('H:i\' d/m/y',[[=notes.created_time=]])?></td>
                                                    <td style="width:15%">[[|notes.created_user_name|]]</td>
                                                </tr>
                                                <tr>
                                                    <td>[[|notes.content|]]</td>
                                                    <td>
                                                        [[|notes.emotion|]] <br>
                                                        <?php if([[=can_edit_schedule=]]) { ?>
                                                        <a href="<?php echo Url::build('ghi-chu-khach-hang',array('cmd'=>'edit','cid'=>(Url::iget('cid')),'nid'=>md5([[=notes.id=]].CATBE)));?>" type="button"
                                                           class="btn btn-default btn-xs"><i class="fa fa-pencil"></i> Cập nhật</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </li>
                                        <!--/LIST:notes-->
                                    </ul>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="<?php echo Url::build('ghi-chu-khach-hang',array('cmd'=>'add','cid'=>(Url::iget('cid')),'branch_id'));?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Thêm</a>
                                <a href="<?php echo Url::build('ghi-chu-khach-hang',array('cmd'=>'list','cid'=>(Url::iget('cid')),'branch_id'));?>" class="btn btn-warning btn-sm"><i class="fa fa-file-text"></i> Tất cả</a>
                            </div>
                        </div>
                        <div id="lichhen" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-heading">Lịch hẹn</div>
                                <div class="panel-body">
                                    <!--LIST:schedules-->
                                    <ul class="timeline">
                                        <li class="time-label">
                                            <span class="<?=([[=schedules.appointed_time=]]<time())?'bg-red':'bg-green';?>">
                                                Hẹn lúc: <span class="text-bold">[[|schedules.appointed_time_display|]]</span>
                                            </span>
                                        </li>
                                        <li>
                                            <i class="fa fa-envelope bg-blue"></i>
                                            <div class="timeline-item">
                                                <span class="time"><i class="fa fa-clock-o"></i>
                                                    Đã đến lúc: <span class="text-bold">
                                                    <?php echo !empty([[=schedules.arrival_time=]])? [[=schedules.arrival_time_display=]] : 'Chưa đến' ?>
                                                </span>
                                                </span>
                                                <h3 class="timeline-header">
                                                    <span class="small text-blue text-bold"><?php echo $this->map['schedule_status'][ [[=schedules.status_id=]] ] ?></span>
                                                </h3>

                                                <div class="timeline-body text-warning">
                                                    [[|schedules.note|]]
                                                </div>

                                                <div class="timeline-footer">
                                                    <?php if (check_user_privilege('CUSTOMER') || Session::get('admin_group') || is_group_owner() || [[=schedules.created_user_id=]] == get_user_id()): ?>
                                                        <a  href="<?php echo Url::build('lich-hen',array('cmd'=>'edit','from_customer'=>[[=id=]],'cid'=>(Url::iget('cid')),'sid'=>md5([[=schedules.id=]].CATBE)));?>" type="button"
                                                       class="btn btn-default btn-xs"><i class="fa fa-pencil"></i> Cập nhật</a>
                                                   <?php else: ?>
                                                        <a disabled href="<?php echo Url::build('lich-hen',array('cmd'=>'edit','from_customer'=>[[=id=]],'cid'=>(Url::iget('cid')),'sid'=>md5([[=schedules.id=]].CATBE)));?>" type="button"
                                                       class="btn btn-default btn-xs"><i class="fa fa-pencil"></i> Cập nhật</a>
                                                    <?php endif; ?>
                                                    Tạo bởi: [[|schedules.created_user_name|]] -
                                                    [[|schedules.branch_name|]]
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <!--/LIST:schedules-->
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="<?php echo Url::build('lich-hen',array('cmd'=>'add','cid'=>(Url::iget('cid')),'branch_id','from_customer'=>[[=id=]]));?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Thêm</a>
                                <a href="<?php echo Url::build('lich-hen',array('cmd'=>'list','cid'=>(Url::iget('cid')),'branch_id','from_customer'=>[[=id=]]));?>" class="btn btn-warning btn-sm"><i class="fa fa-calendar"></i> Tất cả</a>
                            </div>
                        </div>
                        <div id="cuocgoi" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-heading">Cuộc gọi</div>
                                <div class="panel-body">
                                    <ul class="timeline">
                                        <!--LIST:calls-->
                                        <li style="padding-left: 40px;margin-bottom: 0;">
                                            <table class="table table-striped" style="margin-bottom: 0;">
                                                <tr>
                                                    <td style="width:85%"><?php echo date('H:i\' d/m/y',[[=calls.created_time=]])?></td>
                                                    <td style="width:15%">[[|calls.created_user_name|]]</td>
                                                </tr>
                                                <tr>
                                                    <td>[[|calls.content|]]</td>
                                                    <td>
                                                        [[|calls.emotion|]] <br>
                                                        <?php if([[=can_edit_schedule=]]) { ?>
                                                        <a href="<?php echo Url::build('lich-su-cuoc-goi',array('cmd'=>'edit','cid'=>(Url::iget('cid')),'nid'=>md5([[=calls.id=]].CATBE)));?>" type="button"
                                                           class="btn btn-default btn-xs"><i class="fa fa-pencil"></i> Cập nhật</a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </li>
                                        <!--/LIST:calls-->
                                    </ul>
                                </div>
                            </div>
                            <div class="text-right">
                                <?php $url_call = Url::build('lich-su-cuoc-goi',array('cmd'=>'add','cid'=>(Url::iget('cid')),'branch_id','window'=>1)); ?>
                                <a href="javascript:" class="btn btn-success btn-sm"
                                   onclick='popupCenterDual("<?=$url_call;?>#EditCrmCustomerCallHistory", "Ghi cuộc gọi", 500, 768)'
                                ><i class="fa fa-plus"></i> Thêm</a>
                                <a href="<?php echo Url::build('lich-su-cuoc-goi',array('cmd'=>'list','cid'=>(Url::iget('cid')),'branch_id'));?>" class="btn btn-warning btn-sm"><i class="fa fa-phone-square"></i> Tất cả</a></div>
                        </div>
                        <div id="benhly" class="tab-pane fade">
                            <div class="panel panel-default">
                                <div class="panel-heading">Bệnh lý</div>
                                <div class="panel-body">
                                    <table class="table">
                                        <tr>
                                            <th>Tên bệnh</th>
                                            <th>Tình trạng</th>
                                            <th>Ngày tạo</th>
                                            <th>Người tạo</th>
                                            <th>Sửa</th>
                                        </tr>
                                        <!--LIST:pathology-->
                                        <tr>
                                            <td>[[|pathology.name|]]</td>
                                            <td>[[|pathology.note|]]</td>
                                            <td>[[|pathology.created_time|]]</td>
                                            <td>[[|pathology.created_user_name|]]</td>
                                            <td>
                                                <?php if([[=can_edit_schedule=]]) { ?>
                                                <a href="<?php echo Url::build('benh-ly',array('cmd'=>'edit','cid'=>(Url::iget('cid')),'nid'=>md5([[=pathology.id=]].CATBE)));?>" type="button"
                                                   class="btn btn-default btn-xs"><i class="fa fa-pencil"></i> Cập nhật</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <!--/LIST:pathology-->
                                    </table>
                                </div>
                            </div>
                            <div class="text-right">
                                <a href="<?php echo Url::build('benh-ly',array('cmd'=>'add','cid'=>(Url::iget('cid')),'branch_id'));?>" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Thêm</a>
                                <a href="<?php echo Url::build('benh-ly',array('cmd'=>'list','cid'=>(Url::iget('cid')), 'branch_id'));?>" class="btn btn-warning btn-sm"><i class="fa  fa-flask"></i> Tất cả</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        let hash = document.location.hash;
        let prefix = "";
        if (hash) {
            $('.nav-tabs a[href="'+hash.replace(prefix,"")+'"]').tab('show');
        }
        // Change hash for page-reload
        $('.nav-tabs a').on('shown', function (e) {
            window.location.hash = e.target.hash.replace("#", "#" + prefix);
        });
    });
</script>
