<?php
/**
 * Created by PhpStorm.
 * User: trinhdinh
 * Date: 2019-01-17
 * Time: 17:01
 */
?>
<div class="container full"><br>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title" style='width:100%;'>
                <span>Lịch Hẹn Hôm Nay</span>
                <span class="pull-right-container">
                <small class="label  bg-yellow">Tổng: [[|total|]]</small>
                <small class="label bg-blue margin-left-5px">Khách đã đến: [[|total_arrival|]]</small>
                <small class="label bg-green margin-left-5px">Khách mới: [[|total_news_customers|]]</small>
                <small class="label bg-purple margin-left-5px">Khách cũ: [[|total_old_customers|]]</small>
                <a href="index062019.php?page=lich-hen" class="label label-default"> Xem tất cả</a>
            </span>
            </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <form class="form-inline" style="padding:0;margin-bottom: 20px;" action="/<?php echo Url::build_current(array('do'=>'search','cmd'=>'today_schedule'));?>">
                <input name="page" type="hidden" value="lich-hen" />
                <input name="page_no" type="hidden" />
                <input name="branch_id" type="hidden" />
                <input name="status_id" type="hidden" />
                <input name="page_no" type="hidden" value='1' />
                <input name="cmd" type="hidden" value="today_schedule" />
                <div class="form-group" style='padding-left: 0;'>
                    <input name="customer_text" type="text" id="customer_text" class="form-control" placeholder="Nhập tên KH, SĐT, ID ...">
                </div>
                <div class="form-group" style='padding: 0;'>
                    <div class='input-group'><input name="from_date" type="text" id="from_date" style='width: 130px;display: inline-block;'
                                                    autocomplete="off" class="form-control" placeholder="Từ ngày">
                        <input name="to_date" type="text" id="to_date" style='width: 130px;display: inline-block;'
                               autocomplete="off" class="form-control" placeholder="Đến ngày"></div>
                </div>
                <div class="form-group hidden">
                    <select name="branch_id" id="branch_id" class="form-control" style='max-width: 100%;'></select>
                </div>
                <div class="form-group">
                    <select name="status_id" id="status_id" class="form-control"></select>
                </div>
                <div class="form-group">
                    <select name="schedule_type" id="schedule_type" class="form-control"></select>
                </div>
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Tìm</button>
            </form>

            <table id='data_table' class="table table-bordered table-hover table-responsive">
                <thead>
                <tr>
                    <th width='20'>#</th>
                    <th width='150' class="hidden">Chi Nhánh</th>
                    <th width='180'>Khách Hàng</th>
                    <th class="hidden" width='100'>Phân Loại</th>
                    <th width='100'>Giờ Hẹn</th>
                    <th width='100'>Giờ Đã Đến</th>
                    <th width='100'>Ghi Chú</th>
                    <th width='80'>Người Tạo</th>
                    <th width='80'>Trạng Thái</th>
                    <th width='1%'>Sửa</th>
                </tr>
                </thead>
                <tbody>
                <tr id='add_new_schedule'>
                    <td>x</td>
                    <td class="hidden" id='branch_id_container'>
                        <select name="branch_id" id="branch_id" class="form-control" required='required'></select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input name="customer_name" id="customer_name" onchange="if(this.value==''){getId('customer_id').value='';}" class="form-control" autocomplete="off" readonly="" type="text" value="">
                            <span class="input-group-addon" title="Chọn khách hàng" onclick="window.open('index062019.php?page=customer&amp;act=select&branch_id='+get_branch_id());"><i class="fa fa-search"></i></span>
                            <input name="customer_id" id="customer_id" class="form-control" type="hidden" value="" required>
                        </div>
                    </td>
                    <td class="hidden">
                        x
                    </td>
                    <td>
                        <div  class='input-group'>
                            <input type='text' name='appointed_time_display' id='appointed_time_display' class="form-control" value=''>
                            <input type='hidden' name='appointed_time' id='appointed_time' value=''>
                        </div>
                    </td>
                    <td></td>
                    <td>
                        <div class='input-group'>
                            <input type='text' name='note' required  class="form-control"/>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                    <td class="text-right">
                        <input class="btn btn-warning" type='button' onclick='add_new_schedule()' name='' value=' + Đặt Nhanh'>
                        <input type='hidden' name='cmd' value='add'>
                        <input type='hidden' name='page' value='lich-hen'>
                        <input type='hidden' name='action' value='quick_schedule'>
                        <input type='hidden' name='schedule_type' value='1'>
                        <input type='hidden' name='status_id' value='1'>
                    </td>
                </tr>
                <?php $index = 1 + ($this->map['page_no']-1)*$this->map['item_per_page']; ?>
                <!--LIST:schedules-->
                <tr data-id="[[|schedules.id|]]">
                    <td>
                        <?=$index;?>
                        <?php $index++; ?>
                    </td>
                    <td class="hidden">
                        <small>[[|schedules.branch_name|]]</small>
                    </td>
                    <td>
                        <a target='_blank' href="<?php echo Url::build('customer',[ 'cid' => ([[=schedules.customer_id=]]), 'do' => 'view' ] )?>"> <strong class="text-blue"> [[|schedules.customer_name|]] </strong> </a>
                        <br>[[|schedules.customer_mobile|]]
                    </td>
                    <td class='hidden text-center'>
                        <strong>
                            <!--IF:new_old_customer([[=schedules.count_order=]]>0)-->
                            <small class="label bg-purple">Cũ</small>
                            <!--ELSE-->
                            <small class="label bg-green ">Mới</small>
                            <!--/IF:new_old_customer-->
                        </strong><small>([[|schedules.count_order|]])</small><br>
                        <?php
                        $color = isset($this->map['customer_statuses'][[[=schedules.customer_status_id=]]])?$this->map['customer_statuses'][[[=schedules.customer_status_id=]]]['color']:'';
                        $color = "color:$color"
                        ?>
                        <span style='<?=$color?>;'>
                                    <?=isset($this->map['customer_status_list'][[[=schedules.customer_status_id=]]])?$this->map['customer_status_list'][[[=schedules.customer_status_id=]]]:''  ;?></span>
                        <br>
                    </td>
                    <td><?php echo date('d/m/Y H:i\'', [[=schedules.appointed_time=]]); ?></td>
                    <td><!--IF:late_schedule(empty([[=schedules.arrival_time=]]) && [[=schedules.appointed_time=]] < time())-->
                        <span class="text-red text-bold">Trễ hẹn</span>
                        <!--ELSE-->
                        [[|schedules.arrival_time|]]
                        <!--/IF:late_schedule--></td>
                    <td>
                        [[|schedules.note|]] <br>
                        [[|schedules.note_services|]]
                    </td>
                    <td>[[|schedules.created_user_name|]]<br><small>
                            <?=date('d/m/Y H:i\'',[[=schedules.created_time=]])?></small></td>
                    <td>
                        <small><?php echo $this->map['status'][[[=schedules.status_id=]]]; ?></small>
                    </td>
                    <td class="text-right">
                        <?php if (Session::get('admin_group') || CrmCustomerScheduleDB::can_edit([[=schedules.id=]])) { ?>
                        <a role='button' class='btn btn-default' href="<?php echo Url::build_current(array('cid','sid'=>md5([[=schedules.id=]] . CATBE), 'cmd'=>'edit','today_schedule'=>1));?>">
                            Sửa
                        </a>
                        <?php } ?>
                    </td>
                </tr>
                <!--/LIST:schedules-->
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            [[|paging|]]
        </div>
    </div>
</div>
<form name="EditCrmCustomerSchedule" id="EditCrmCustomerSchedule" action='index062019.php?page=lich-hen&cmd=add' method='post'>

</form>
<script>
    function add_new_schedule() {
        let form = jQuery("#EditCrmCustomerSchedule");
        form.empty();
        let formInputs = jQuery(`#add_new_schedule input`);
        let check = true;
        //
        let selects = jQuery(`#add_new_schedule select`);
        let branch = selects[0];
        check = alertMissingField(branch);
        if (check === false) {
            return false
        }
        form.append(branch);

        //
        for (let i=0;i<formInputs.length;i++){
            check = alertMissingField(formInputs[i]);
            if (check === false) {
                jQuery(`#branch_id_container`).append(branch);
                return false;
            }
            jQuery(formInputs[i]).clone().appendTo(form);
        }

        // console.log('submit form');
        //console.log(form);
        //return;
        form.submit();
    }

    //
    function alertMissingField(field) {
        let element = jQuery(field);
        /*if (element.attr('name') == 'branch_id' && element.val().length==0){
            alert('Bạn chưa chọn chi nhánh !');
            return false;
        }*/

        if (element.attr('name') == 'customer_name' && element.val().length==0){
            alert('Bạn chưa chọn khách hàng !');
            return false;
        }
        if (element.attr('name') == 'appointed_time' && element.val().length==0){
            alert('Bạn chưa chọn giờ hẹn !');
            return false;
        }
        if (element.attr('name') == 'note' && element.val().length==0){
            alert('Bạn chưa điền ghi chú !');
            return false;
        }
    }

    $(document).ready(function(){
        $('#from_date').datetimepicker({
            inline: false,
            sideBySide: true,
            format: 'DD/MM/YYYY'
        });
        $('#to_date').datetimepicker({
            inline: false,
            sideBySide: true,
            format: 'DD/MM/YYYY'
        });

        $('#appointed_time_display').datetimepicker({
            inline: false,
            sideBySide: true,
            format: 'DD/MM/YYYY HH:mm'
        });

        jQuery('#appointed_time_display').on('dp.change', function (e) {
            jQuery('#appointed_time').val(e.date.unix());
        });
    });

    function get_branch_id() {
        return '';//$(`#data_table #branch_id`).val();
    }
</script>
