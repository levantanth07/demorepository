<style>
    /*Megarow*/
    .megarow div.views-megarow-content {
        border: 3px solid #0074bd;
        padding-left: 5px;
        padding-right: 5px;
    }
    div.views-megarow-content .megarow-content .megarow-throbber-wrapper {
        text-align: center;
    }
    div.views-megarow-content .megarow-header {
        background-color: #FFFFFF;
        cursor: pointer;
        padding: 0px;
        text-align: center;
    }
    div.views-megarow-content .megarow-title {
        color: menutext;
        font-size: 12px;
        font-weight: bold;
        padding-left: 5px;
    }
    .content-detail{
        padding: 5px;
    }
    .megarow-header a.close{
        float: right;
        color: #000;
        font-size: 16px;
        padding-right: 5px;
        background: url(/packages/vissale/modules/AdminShop/images/close.png) no-repeat scroll left top rgba(0, 0, 0, 0);
        width: 16px;
        height: 16px;
        opacity: .6;
    }
    .error {
        background: #fff5f5;;
        border-color: red;
    }
</style>
<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<script>
    function check_selected() {
        var status = false;
        jQuery('form :checkbox').each(function(e){
            if(this.checked){
                status = true;
            }
        });
        return status;
    }
    function make_cmd(cmd){
        jQuery('#cmd').val(cmd);
        document.ListUserAdminForm.submit();
    }
   
</script>
<?php
    $packages = [[=acc_packages=]];
?>
<div class="panel panel-default">
    <div class="panel-body">
        <fieldset id="toolbar">
            <div class="row">
                <div class="col-xs-8">
                 <h3 class="title">
                     <?php echo (Url::get('cmd')=='delete')?'Xác nhận xóa shop':'Quản lý shop';?>
                     <?php echo (Url::get('status')==1)?' (Đăng ký từ Web)':'';?>
                 </h3>
                </div>
                <div class="col-xs-4 text-right">
                    <!--IF:cond6(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
                    <a href="<?php echo Url::build_current(array('cmd'=>'report'));?>" class="btn btn-default">Báo cáo</a>
                    <a href="<?php echo Url::build('user_admin', array('cmd'=>'add'));?>" class="btn btn-success">Thêm</a>
                    <?php if (Url::get('cmd')!='delete') {?>
                        <a class="btn btn-danger" onclick="if(confirm('Bạn có chắc muốn xóa?')){if(check_selected()){make_cmd('delete_shop')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}">Xoá </a>
                    <?php } else {?>
                            <a class="btn btn-danger" onclick="if(check_selected()){make_cmd('delete_shop')}"> Xác nhận xóa </a>
                    <?php }?>
                    <!--/IF:cond6-->
                </div>
            </div>
        </fieldset>
        <hr>
        <form name="ListShopForm" method="post" id="ListShopForm">
        <table class="table">
            <tr>
                <td width="15%">
                    <input name="keyword" type="text" id="keyword" class="form-control" placeholder="Tên shop hoặc tên tk chủ sở hữu">
                </td>
                
                <?php
                User::can_admin(MODULE_GROUPSSYSTEM, ANY_CATEGORY);
                ?>

                <!--IF:cond6(User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))-->
                <td width="15%">
                    <select name="account_type" id="account_type" class="form-control"></select>
                </td>
                <td width="15%">
                    <select name="is_crm" id="is_crm" class="form-control js-example-placeholder-single"></select>
                </td>
                <td width="15%">
                    <select name="status_palion" id="status_palion" class="form-control js-example-placeholder-single"></select>
                </td>
                <td width="20%">
                    <select name="system_group_id" id="system_group_id" class="form-control select2" onchange="ListShopForm.submit();"></select>
                </td>
                <td width="20%">
                    <select name="expired_month" id="expired_month" class="form-control" onchange="ListShopForm.submit();"></select>
                </td>
                
                <!--/IF:cond6-->
                <td width="20%" class="text-left">
                </td>
                <td><input type="submit" value="Tìm kiếm" class="btn btn-warning"></td>
            </tr>
        </table>
        <input type="hidden" name="page_no" value="1" />
    </form>
        <?php if (Session::get('user_id')=='PAL.khoand') {?>
            <!--LIST:items-->
            [[|items.id|]],
            <!--/LIST:items-->
        <?php }?>
    <form name="ListUserAdminForm" method="post">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Tổng số shop: [[|total|]]</h3>
                <div class="box-tools pull-right">
                    [[|good_shop|]] shop > 500 đơn,
                    [[|actived_shop|]] shop hoạt động, [[|expired_shop|]] shop hết hạn
                    <span class="label label-primary">Thống kê</span>
                </div>
                <!-- /.box-tools -->
            </div>
        </div>
        <table class="table table-bordered table-striped">
            <tr valign="middle" bgcolor="#F2F2CB">
                <th width="1%" title="[[.check_all.]]"><input type="checkbox" value="1" id="UserAdmin_all_checkbox" onclick="jQuery('.selected-ids').attr('checked',this.checked)"<?php if (URL::get('cmd')=='delete_shop') {
                    echo ' checked';
                                                                                                                                                                                  }?>></th>
                <th align="left" width="20%">Tên Shop</th>
                <th align="left" width="20%">Quản lý Shop</th>
                <th align="left" >Thông tin liên hệ</th>
                <th align="left" width="7%">Loại</th>
                <th>Gói cước</th>
                <th nowrap align="left" >Kích hoạt</th>
                <th nowrap align="left" ><a href="<?php echo Url::build_current(array('cmd','order_by'=>'user_counter','order_dir'=>((Url::get('order_dir')=='DESC')?'ASC':'DESC')));?>">Max user</a></th>
                <th nowrap align="left" ><a href="<?php echo Url::build_current(array('cmd','order_by'=>'page_counter','order_dir'=>((Url::get('order_dir')=='DESC')?'ASC':'DESC')));?>">Max pages</a></th>
                <th align="left" >Ngày tạo</th>
                <th align="left" ><a href="<?php echo Url::build_current(array('cmd','order_by'=>'expired_date','order_dir'=>((Url::get('order_dir')=='DESC')?'ASC':'DESC')));?>">Ngày hết hạn</a></th>
                <th align="left">Ngày hết hạn dùng Palion</th>
                <th align="left">Trạng thái dùng Palion</th>
            </tr>
            <?php $i = 1;?>
            <!--LIST:items-->
            <tr valign="middle" <?php Draw::hover('#F0ECC8');?> style="<?php if ($i%2) {
                echo 'background-color:#FFF';
                                }?>" id="UserAdmin_tr_[[|items.id|]]">
                <td>
                    <input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" class="selected-ids" onclick="" id="UserAdmin_checkbox" <?php if (URL::get('cmd')=='delete_shop') {
                        echo 'checked';
                                                                                                                                                }?>>
                <?php echo $i;?></td>
                <td align="left">
                    <a target="_blank" href="index062019.php?page=admin_group_info&group_id=[[|items.id|]]">
                        <strong>[[|items.name|]]</strong>
                    </a>
                    <div class="small">
                        Sở hữu bởi tài khoản: <strong>[[|items.code|]]</strong>
                    </div>
                    <div class="small">
                        (<!--IF:cond([[=items.master_group_name=]])-->Hệ thống: [[|items.master_group_name|]] | <!--/IF:cond-->[[|items.system_group_name|]], Group ID: [[|items.id|]], Số TK: [[|items.total_user|]])
                        <!--IF:cond([[=items.description=]])--><br>Ghi chú: [[|items.description|]]<!--/IF:cond-->
                        <!--IF:cond([[=items.phone_store_name=]])--><br>Kho số: <strong>[[|items.phone_store_name|]]</strong><!--/IF:cond-->
                    </div>
                </td>
                <td align="left">[[|items.admins|]]</td>
                <td align="left">
                    <span class="small">Email: [[|items.email|]]</span><br>
                    <span class="small">Điện thoại: [[|items.phone|]]</span>
                </td>
                <td class="text-center">
                    [[|items.account_type|]]
                    <!--IF:cond([[=items.status=]]==1)-->
                    <?php echo ([[=items.status=]]==1)?'<br>(Từ Web)':'';?>
                    <!--/IF:cond-->
                </td>
                <td class="text-center">
                    <div><b>[[|items.package_name|]]</b></div>
                    <div>
                        <?php if (!empty([[=items.package_name=]])) : ?>
                            <a href="#"
                                data-group-id="[[|items.id|]]"
                                data-expired-date="[[|items.expired_date|]]"
                                data-palion-price="[[|items.palion_price|]]"
                                data-palion-status="[[|items.palion_payment_status|]]"
                                data-palion-expired-at="[[|items.palion_expired_at|]]"
                                data-palion-at="[[|items.palion_paid_at|]]"
                                data-user-counter="[[|items.user_counter|]]"
                                data-page-counter="[[|items.page_counter|]]"
                                data-period-date="[[|items.period_date|]]"
                                data-page-months="[[|items.months|]]"
                                data-js-date-expired="[[|items.js_date_expired|]]"
                                data-package-id="[[|items.package_id|]]"
                                class="btn btn-info btn-sm btn-update-package" data-toggle="modal" data-target="#modal-update-package"
                            ><i class="fa fa-exchange"></i> Đổi gói</a>
                        <div><a href="#" class="btn btn-link btn-history" data-group-id="[[|items.id|]]">Xem lịch sử</a></div>
                        <?php else : ?>
                            <a href="#"
                                data-group-id="[[|items.id|]]"
                                data-expired-date="[[|items.expired_date|]]"
                                data-palion-price="[[|items.palion_price|]]"
                                data-palion-status="[[|items.palion_payment_status|]]"
                                data-palion-expired-at="[[|items.palion_expired_at|]]"
                                data-palion-at="[[|items.palion_paid_at|]]"
                                data-user-counter="[[|items.user_counter|]]"
                                data-page-counter="[[|items.page_counter|]]"
                                data-period-date="[[|items.period_date|]]"
                                data-page-months="[[|items.months|]]"
                                data-js-date-expired="[[|items.js_date_expired|]]"
                                class="btn btn-warning btn-sm btn-update-package"
                                data-toggle="modal" data-target="#modal-update-package"><i class="fa fa-hand-o-right"></i> Chọn gói</a>
                        <?php endif; ?>
                    </div>
                </td>
                <td align="center"><?=[[=items.active=]]?'<span class="label label-success">Đã kích hoạt</span>':'<span class="label label-danger">Chưa kích hoạt</span>'?></td>
                <td align="left">[[|items.user_counter|]]</td>
                <td align="left">[[|items.page_counter|]]</td>
                <td align="left"><?php echo ([[=items.created=]]!='0000-00-00 00:00:00' and [[=items.created=]])?date('d/m/Y', strtotime([[=items.created=]])):''; ?></td>
                <td align="left"><?=is_set_date([[=items.expired_date=]]) ? date('d/m/Y', strtotime([[=items.expired_date=]])) 
                . (strtotime([[=items.expired_date=]]) <= time() ? '<span style="color:#f00"> - Hết hạn</span>':'')
                :'Không thời hạn';?></td>
                
                <td align="left"><?=is_set_date([[=items.palion_expired_at=]]) ? date('d/m/Y', strtotime([[=items.palion_expired_at=]])) . (strtotime([[=items.palion_expired_at=]]) <=time() ? '<span style="color:#f00"> - Hết hạn</span>':'') : '';?></td>
                <td align="left">
                    <?php if ([[=items.palion_payment_status=]] == 1) : ?>
                        <span>Đã thanh toán</span>
                    <?php elseif ([[=items.palion_payment_status=]] == 2) : ?>
                        <span>Dùng thử</span>
                    <?php else : ?>
                        <span></span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php $i++;?>
            <!--/LIST:items-->
        </table>
        <table  width="100%" cellpadding="6" cellspacing="0" style="background-color:#F0F0F0;border:1px solid #E7E7E7;#width:99%" align="center">
            <tr>
                <td>[[|paging|]]</td>
            </tr>
        </table>
        <input type="hidden" name="cmd" value="delete_shop"/>
        <input type="hidden" name="page_no" value="1"/>
        <!--IF:delete(URL::get('cmd')=='delete_shop')-->
        <input type="hidden" name="confirm" value="1" />
        <!--/IF:delete-->
    </form>
    </div>
</div>

<div class="modal fade" id="modal-update-package" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form action="" id="form-update-package" name="form_add_address">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Cập nhật gói cước</h4>
                </div>
                <?php if (!empty($packages)) : ?>
                <div class="modal-body">
                    <input type="hidden" name="group_id" id="package-group-id">
                    <div class="form-group">
                        <label for="billing_at">Ngày cập nhật đơn hàng</label>
                        <input type="text" class="form-control" value="<?= date('d/m/Y') ?>" name="billing_at" id="billing_at" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="package_id">Chọn gói cước</label>
                        <select  class="form-control" name="package_id" id="package_id" required>
                            <option value="">Chọn gói cước</option>
                            <?php
                            foreach ($packages as $package) :
                                ?>
                                <option value="<?= $package['id'] ?>"
                                    data-price="<?= $package['price'] ?>"
                                    data-max-user="<?= $package['max_user'] ?>"
                                    data-max-page="<?= $package['max_page'] ?>"
                                    data-number-months="<?= $package['number_months'] ?>"
                                    data-percent-discount="<?= $package['percent_discount'] ?>"
                                >
                                <?= $package['name'] ?> (<?= number_format($package['price']) ?>/tháng, Tối đa: <?= $package['max_user'] ?> user, <?= $package['max_page'] ?> page)
                                </option>
                                <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user_counter">Số Tài khoản được tạo</label>
                        <input type="text" class="form-control" name="user_counter" id="user_counter">
                    </div>
                    <div class="form-group">
                        <label for="page_counter">Số Fanpage được đăng ký</label>
                        <input type="text" class="form-control" name="page_counter" id="page_counter">
                    </div>
                    <div class="form-group">
                        <label for="">Số tháng sử dụng</label>
                        <select class="form-control" name="months" id="months" required>
                            <option value="">Số tháng sử dụng</option>
                        </select>
                    </div>
                    <input type="hidden" id="months_update" value="">
                    <input type="hidden" id="palion_expired_at_update" value="">
                    <div class="form-group">
                        <label for="update_expired_date">Cập nhật lại ngày hết hạn</label>
                        <input name="update_expired_date" type="text" id="update_expired_date" class="form-control">
                        <div class="text-danger">
                            <span class="period-time-old"></span>
                            <span class="period-time-new"></span>
                            <input type="hidden" id="expired-date" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Chiết khấu (VNĐ)</label>
                        <input type="text" class="form-control" name="discount" id="discount" placeholder="Chiết khấu" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Thành tiền (*)</label>
                        <input type="text" class="form-control" name="total_price" id="total_price" placeholder="Thành tiền" required>
                    </div>
                    <p style="font-weight: bold;">Thông tin thanh toán Palion</p>
                    <div class="form-group">
                        <label for="">Trạng thái sử dụng</label>
                        <select class="form-control" name="palion_status" id="palion_status">
                            [[|status_id_list|]]
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Số tiền</label>
                        <input type="text" class="form-control" name="palion_price" id="palion_price" placeholder="Số tiền">
                    </div>
                    <div class="form-group">
                        <label for="palion_at">Ngày thanh toán</label>
                        <input type="text" class="form-control" value="" name="palion_at" id="palion_at" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="palion_expired_at">Ngày hết hạn dùng Palion</label>
                        <input class="form-control" value="" name="palion_expired_at" id="palion_expired_at" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Trở lại</button>
                    <button type="submit" class="btn btn-primary">Hoàn thành</button>
                </div>
                <?php else : ?>
                    <div class="modal-body">
                        <div class="text-center"><a href="index062019.php?page=admin-shop&cmd=manager-packages" class="btn btn-primary">Thêm gói cước</a></div>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<div id="loading"><span class="loader"></soan></div>

<style>
    .loader {
        border: 5px solid #f3f3f3;
        -webkit-animation: spin 1s linear infinite;
        animation: spin 1s linear infinite;
        border-top: 5px solid #555;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        position: absolute;
        top: 45%;
        left: 50%;
    }
    #loading {
        position: fixed;
        top: 0px;
        left: 0px;
        width: 100%;
        height:100%;
        z-index: 2000;
        background:rgba(255,255,255,.5) no-repeat center center;
        text-align:center;
        display: none;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
    $(document).ready(function(){
        $('.select2').select2({
            dropdownAutoWidth : true
        });
        $(".js-example-placeholder-single").select2({
            placeholder: "Đồng bộ CRM",
            allowClear: true
        });

        $("#status_palion").select2({
            placeholder: "Trạng thái sử dụng palion",
            allowClear: true
        });
    })
    $(document).on('keypress','#palion_price',function(e){
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });
    $('#palion_price').change(function(){
        var palion_price = numberFormat($(this).val());
        $('#palion_price').val(palion_price);
    })
    var current_url = window.location.href
    Number.prototype.numberFormat = function(decimals, dec_point, thousands_sep) {
        dec_point = typeof dec_point !== 'undefined' ? dec_point : '.';
        thousands_sep = typeof thousands_sep !== 'undefined' ? thousands_sep : ',';

        var parts = this.toFixed(decimals).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

        return parts.join(dec_point);
    }

    function calculate_price_package() {
        var price = 0
        var months = 0
        var percent_discount = 0
        $('.period-time-new').html('');
        var month_update = 0;
        if (!isNaN(parseInt($('#package_id').val()))) {
            var package_id_selected = $('#package_id').find(':selected')
            price = parseInt(package_id_selected.data('price'))
            percent_discount = parseInt(package_id_selected.data('percent-discount'))
        }
        if (!isNaN(parseInt($('#months').val()))) {
            months = parseInt($('#months').val())
        }

        if (!isNaN(parseInt($('#months_update').val()))) {
            month_update = parseInt($('#months_update').val())
        }

        var temp_price = price * months
        var discount = (temp_price * percent_discount) / 100;
        var total_price = temp_price - discount

        var expired_date_current = $('#expired-date').val();
        if (months > 0 && month_update > 0) {
            var current_time = new Date().getTime()
            var date_expired = addMonths(expired_date_current, months)
            $('.period-time-new').html('<span class="pull-right label label-warning">Ngày hết hạn dự kiến: ' + date_expired + '</span>')
            $('#update_expired_date').val(date_expired);
            $('#update_expired_date').trigger('change')
        }
        $('#discount').val(discount.numberFormat());
        $('#total_price').val(total_price.numberFormat());
    }

    function isLeapYear(year) { 
        return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0)); 
    }

    function getDaysInMonth(year, month) {
        return [31, (isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
    }

    function addMonths(date, value) {
        return moment(date, 'DD/MM/YYYY').add(value, 'M').format('DD/MM/YYYY');
    }

    function convert_date(timestamp) {
        var date = new Date(timestamp)
        var year = date.getFullYear();
        var month = date.getMonth() + 1; 
        var day = date.getDate();
        if(month.toString().length == 1) {
            month = '0' + month;
        }

        if(day.toString().length == 1) {
            day = '0' + day;
        }

        return day + '-' + month + '-' + year
    }
    function convert_date_new(timestamp) {
        var date = new Date(timestamp)
        var year = date.getFullYear();
        var month = date.getMonth() + 1; 
        var day = date.getDate();
        if(month.toString().length == 1) {
            month = '0' + month;
        }

        if(day.toString().length == 1) {
            day = '0' + day;
        }

        return year + '-' + month + '-' + day
    }

    function ajax_megarow(id, url, self, colspan) {
        if ($('tr.megarow-'+id).length) {
            $('tr.megarow-'+id).remove();
        } else {
            self.closest('tr').after('<tr class="megarow megarow-'+id+'"><td colspan="'+ colspan +'">\
                <div class="views-megarow-content views-megarow-content-'+id+'">\
                    <div class="megarow-header clearfix">\
                        <span class="megarow-title"> Đang tải dữ liệu...</span>\
                        <a class="close" href="#"></a>\
                    </div>\
                    <div class="megarow-content">\
                        <div class="megarow-throbber">\
                            <div class="megarow-throbber-wrapper">\
                                <img src="/packages/vissale/modules/AdminShop/images/ajax-loader.gif" title="Loading...">\
                            </div>\
                        </div>\
                    </div>\
                </div>\
            </td>\
            ');

            $.ajax({
                url: url,
                dataType: 'json',
                success: function(data){
                    if (data.success == true) {
                        $('tr.megarow-'+id).remove();
                        self.closest('tr').after('<tr class="megarow megarow-'+id+'"><td colspan="'+ colspan +'">\
                            <div class="views-megarow-content views-megarow-content-'+id+'">\
                                <div class="megarow-header clearfix">\
                                    <span class="megarow-title"></span>\
                                    <a class="close" href="#"></a>\
                                </div>\
                                <div class="megarow-content ">\
                                    '+data.data+'\
                                </div>\
                            </div>\
                        </td>\
                        ');
                    } else {
                        alert('Có lỗi xảy ra. Bạn vui lòng thử lại sau !');
                    }
                }
            });
        }
    }

    $(function() {
        $('#billing_at').datepicker({
            format: 'dd/mm/yyyy'
        });

        $('#palion_at').datepicker({
            format: 'dd/mm/yyyy'
        });

        const peaEl = $('#palion_expired_at');

        /**
         * Called on change palion expired at.
         *
         * @param      {<type>}  e       { parameter_description }
         * @return     {<type>}  { description_of_the_return_value }
         */
        const validatePalionExpiredAt = function(e) {
            let current = moment(peaEl.val(), 'DD/MM/YYYY');
            
            let status = parseInt($('#palion_status').val() || 0);
            if(status && !current.isValid()) {
                peaEl.addClass('error');
                return Swal.fire({
                  title: 'Error!',
                  text: 'Vui lòng nhập ngày hết hạn hợp lệ !',
                  icon: 'error',
                  confirmButtonText: 'Ok'
                });
            }

            let update_expired_date = moment($('#update_expired_date').val(), 'DD/MM/YYYY');
            if(update_expired_date.isValid() && current.isValid() && current.isAfter(update_expired_date)){
                
                return Swal.fire({
                      title: 'Error!',
                      text: 'Ngày hết hạn dùng Palion phải <= ngày hết hạn Tuha',
                      icon: 'error',
                      confirmButtonText: 'Ok',
                      didOpen: function() {
                        peaEl.addClass('error');
                      }
                    });
            }

            peaEl.removeClass('error');
        }

        

        peaEl.datepicker({
            format: 'dd/mm/yyyy'
        })
        .on('clearDate changeDate changeMonth changeYear', validatePalionExpiredAt);

        $('#palion_status').change(function(e) {
            const currentVal = $(this).val();
            if(currentVal <= 0) {
                return peaEl.removeClass('error');
            }

            const palion_expired_at = $('#update_expired_date').val();

            peaEl.val(palion_expired_at)
            peaEl.datepicker('setDate', palion_expired_at);
        })


        $('#update_expired_date').datepicker({format: 'dd/mm/yyyy'});
        $('#update_expired_date').datepicker('setDate', $(this).data('js-date-expired'));
        $('#update_expired_date').change(function(e) {
            let status = parseInt($('#palion_status').val() || 0);
            if(status) {
                peaEl.datepicker('setDate', $(this).val());
            }
        })

        $(document).on('click', '.btn-delete-group', function() {
            var $this = $(this)
            if (confirm('Bạn có chắc chắn muốn xóa không? Thao tác này không thể phục hồi.')) {
                $.ajax({
                    url: current_url + '&do=delete_history_package&id=' + $(this).data('id'),
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            $this.closest('tr').remove()
                        } else {
                            return Swal.fire({
                              title: 'Error!',
                              text: 'Có lỗi xảy ra. Bạn vui lòng thử lại sau.',
                              icon: 'error',
                              confirmButtonText: 'Ok'
                            });
                        }
                    }
                })
            }
        })

        $('.btn-history').click(function(e) {
            e.preventDefault();
            var group_id = $(this).data('group-id');
            var url = current_url + '&do=get_history_package&group_id=' + group_id;
            ajax_megarow(group_id, url, $(this), 10);
        });

        $('#form-update-package').submit(function(e) {
            e.preventDefault();

            if($(this).has('input.error').length) {
                return Swal.fire({
                  title: 'Error!',
                  text: 'Vui lòng kiểm tra trường đánh dấu đỏ',
                  icon: 'error',
                  confirmButtonText: 'OK'
                });
            }

            $('#loading').show();
            let formData = $(this).serialize();
            $.ajax({
                url: current_url + '&do=save_package',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(data) {
                    if (typeof data === 'object' && data.status === 'success') {
                        return Swal.fire({
                            text: 'Lưu thành công',
                            icon: 'success',
                            timer: 1000,
                            didDestroy: () => {
                                return window.location.reload(true);
                            }
                        });
                        
                    }

                    return Swal.fire({
                      title: 'Error!',
                      text: typeof data === 'object' && data.status === 'error' ? data.message : 'Có lỗi xảy ra. Bạn vui lòng thử lại sau.',
                      icon: 'error',
                      confirmButtonText: 'Ok'
                    });
                },
                complete: function() {
                    $('#loading').hide();
                }
            })
        })

        $('.btn-update-package').click(function() {
            $('form#form-update-package')[0].reset();
            const self = $(this);

            $('#package-group-id').val(self.data('group-id'))

            if (self.data('package-id') !== undefined) {
                $('#package_id').val(self.data('package-id'));
                $('#months').empty().append('<option value="">Số tháng sử dụng</option>')
                let package_id_selected = $('#package_id').find(':selected')
                let number_months = parseInt(package_id_selected.data('number-months'))
                for (var i = number_months; i <= 60; i += number_months) {
                    $('#months').append('<option value="'+ i +'">'+ i +' tháng</option>')
                }

                if (self.data('expired-date') != "") {
                    $('.period-time-old').html('Ngày hết hạn: ' + self.data('expired-date') + ' ('+ self.data('period-date') +')')
                }
            } else {
                $('#package_id').val('');
                $('#months').empty().append('<option value="">Số tháng sử dụng</option>');
                $('.period-time-old').empty();
                $('.period-time-new').empty();
            }

            $('#update_expired_date').val(self.data('js-date-expired'));
            $('#expired-date').val(self.data('js-date-expired'));
            $('#user_counter').val(self.data('user-counter'));
            $('#page_counter').val(self.data('page-counter'));
            $('#palion_price').val(numberFormat(self.data('palion-price')));
            $('#palion_status').val(self.data('palion-status'));
            $('#months').val(self.data('page-months'));

            if(self.data('palion-at') !== '0000-00-00'){
                $('#palion_at').datepicker('setDate', self.data('palion-at'));
            }
            
            const palionExpiredAtInit = self.data('palion-expired-at');
            if(self.data('palion-expired-at') !== '<?=NULL_TIME?>'){
                $('#palion_expired_at').datepicker('setDate', palionExpiredAtInit);
                $('#palion_expired_at_update').val(self.data('palion-expired-at'));
            }   
            calculate_price_package();
        });
        $('#months').change(function() {
            var months = $(this).val();
            $('#months_update').val(months);
            calculate_price_package()
        })

        $('#package_id').change(function() {
            $('#months').empty().append('<option value="">Số tháng sử dụng</option>')
            if (!isNaN(parseInt($(this).val()))) {
                var package_id_selected = $('#package_id').find(':selected')
                var number_months = parseInt(package_id_selected.data('number-months'))
                for (var i = number_months; i <= 60; i += number_months) {
                    $('#months').append('<option value="'+ i +'">'+ i +' tháng</option>')
                }
                $('#months').val(parseInt(package_id_selected.data('number-months')));
                $('#months_update').val(parseInt(package_id_selected.data('number-months')));
            }

            calculate_price_package()
        })

        $(document).on('click', '.megarow-header .close', function (e) {
            e.preventDefault()
            $(this).closest('tr').remove()
        })
    })
</script>