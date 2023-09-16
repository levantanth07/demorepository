<script src="assets/admin/scripts/tinymce/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#note_services',
        language:'vi',
        height: 300,
        theme: 'modern',
        plugins: [
            'advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern imagetools'
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: 'print preview media | forecolor backcolor emoticons',
        theme_advanced_buttons1 : "openmanager",
        image_advtab: true,
        content_css: [
            'assets/admin/scripts/tinymce/skins/lightgray/skin.min.css'
        ],
        automatic_uploads: false,
        external_filemanager_path:"/filemanager/filemanager/",
        filemanager_title:"Quản lý FILE pro" ,
        filemanager_access_key:"5998805fbc81d7335a602f65ade654fd",
        external_plugins: { "filemanager" : "/filemanager/filemanager/plugin.min.js"}
    });
</script>

<div class="container">
    <style>
        .img-print-template img{
            max-width: 100%;
        }
    </style>
    <fieldset id="toolbar">
        <div id="toolbar-title">
            Quản lý lịch hẹn
            <span>[ <?php if(Url::get('cmd')=='add'){echo 'Thêm mới';} if(Url::get('cmd')=='edit')
                {echo 'Sửa';}?> ]</span>
        </div>
        <div id="toolbar-content" align="right">
            <table align="right" style="height: 58px;">
                <tbody>
                <tr>
                    <td align="center">
                        <a class="btn btn-primary" onclick="EditCrmCustomerSchedule.submit();">
                            <i class="glyphicon glyphicon-floppy-disk"></i> Ghi lại </a>
                    </td>

                    <td align="center">
                        <a class="btn btn-default" href="<?=($from_customer_id=Url::get('from_customer'))?Url::build('customer',array('do'=>'view', 'cid' => $from_customer_id),false,false,'#lichhen'):Url::build_current(array('cmd'=>'today_schedule', 'cid' => [[=cid=]]));?>">
                            <i class="glyphicon glyphicon-log-out"></i> Quay lại </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <br clear="all"/>
    <fieldset id="add_receive_form" class="row">
        <div class="col-md-8">
            <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <strong>Chi tiết lịch hẹn:</strong>
                </div>
                <div class="panel-body">
                    <form name="EditCrmCustomerSchedule" id="EditCrmCustomerSchedule" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="schedule_id">Mã lịch hẹn</label>
                            <input name="schedule_id" type="text" id="schedule_id" class="form-control" readonly placeholder="(auto)">
                        </div>
                        <div class="form-group">
                            <label for="status_id">Trạng thái:</label>
                            <select name="status_id" id="status_id" class="form-control"></select>
                        </div>
                        <div class="form-group hidden">
                            <label for="customer_status">Phân Loại KH (level):</label>
                            <select name="customer_status_id" id="customer_status_id" class="form-control"></select>
                            <input  name='old_customer_status_id' type='hidden' id='old_customer_status_id' value='<?=isset($this->map['old_customer_status_id'])?$this->map['old_customer_status_id']:0;?>'>
                        </div>
                        <div class="form-group">
                            <label for="received_full_name">Tên khách hàng</label>
                            <input name="customer_id" type="hidden" id="customer_id">
                            <input name="customer_name" type="text" id="customer_name" readonly class="form-control" placeholder="Tên khách hàng">
                        </div>
                        <div class="form-group">
                            <label for="customer_mobile">SĐT khách hàng</label>
                            <input name="customer_mobile" type="text" id="customer_mobile" readonly class="form-control" placeholder="SĐT khách hàng">
                        </div>
                        <div class="form-group">
                            <label for="appointed_time_display" class="text-red text-bold">Thời gian hẹn</label>
                            <!--IF:sid_cond(Url::sget('sid'))-->
                            <input name="appointed_time_display" type="text" id="appointed_time_display" readonly class="form-control" />
                            <!--ELSE-->
                            <input name="appointed_time_display" type="text" id="appointed_time_display" class="form-control" />
                            <!--/IF:sid_cond-->
                            <input name="appointed_time" type="hidden" id="appointed_time" />
                            <span class="small"><i>(Không thể sửa thời gian lịch hẹn sau khi đã tạo.)</i></span>
                        </div>
                        <?php if ( URL::get('cmd')=='edit' && !empty(URL::get('sid')) ) { ?>
                            <div class="form-group">
                                <label for="arrival_time_display"><span class="text-blue text-bold">Thời gian khách đã đến</span><span class="text-red">*</span></label>
                                <input name="arrival_time_display" type="text" id="arrival_time_display" class="form-control">
                                <input name="arrival_time" type="hidden" id="arrival_time" class="form-control">
                            </div>
                        <?php } ?>
                        <div class="form-group hidden">
                            <label for="branch_id">Tại chi nhánh:</label>
                            <!--IF:branch_id_cond(Url::sget('sid'))-->
                                <select name="branch_id" id="branch_id" disabled class="form-control"></select>
                            <!--ELSE-->
                                <select name="branch_id" id="branch_id" class="form-control"></select>
                            <!--/IF:branch_id_cond-->
                            <span class="small"><i>(Không thể sửa chi nhánh sau khi đã tạo.)</i></span>
                        </div>
                        <div class="form-group hidden">
                            <label for="staff_name"><span class="text-blue">Nhân viên sale</span></label>
                            <div class="input-group">
                                <input name="sale_staff_id" type="hidden" id="sale_staff_id">
                                <input name="sale_staff_name" type="text" id="sale_staff_name" class="form-control" placeholder="Chọn nhân viên sale" readonly>
                                <span class="input-group-addon" id="search-sale-staff"
                                      title="Chọn nhân viên sale">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group hidden">
                            <label for="staff_name"><span class="text-blue">Nhân viên phụ trách</span></label>
                            <div class="input-group">
                                <input name="staff_id" type="hidden" id="staff_id">
                                <input name="staff_name" type="text" id="staff_name" class="form-control" placeholder="Chọn nhân viên" readonly>
                                <span class="input-group-addon" id="search-staff"
                                      title="Chọn nhân viên">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="bill_date">Loại lịch hẹn:</label>
                            <select name="schedule_type" id="schedule_type" class="form-control"></select>

                        </div>
                        <div class="form-group">
                            <label for="note">Nội dung lịch hẹn:</label>
                            <textarea name="note" type="text" id="note" class="form-control" rows="5"
                                   placeholder="Nội dung lịch hẹn"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="note">Sản phẩm / dịch vụ quan tâm:</label>
                            <textarea name="note_services" type="text" id="note_services" class="form-control" rows="5"
                                      placeholder="Nội dung lịch hẹn"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="glyphicon glyphicon-time"></i> Lịch sử</strong>
                </div>
                <div class="panel-body" style="max-height: 865px;overflow-y: auto">
                    <ul class="list-group">
                        <!--LIST:logs-->
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-12"><i class="fa fa-clock-o"></i>
                                    <?php echo date("d/m/Y H:i'", [[=logs.time=]]); ?>
                                </div>
                                <div class="col-md-12">
                                    <strong>[[|logs.user_id|]]</strong>
                                    <span class="small">[[|logs.description|]]</span>
                                </div>
                            </div>
                        </li>
                        <!--/LIST:logs-->
                    </ul>
                </div>
            </div>
        </div>
    </fieldset>
</div>
<?php
    foreach ($this->map['branch_id_list'] as $key => $item) {
        $md5_branch_id = md5($key.CATBE);
        echo "<input type='hidden' id='branch_id_{$key}' value='{$md5_branch_id}' />";
    }
?>
<script>
    $(document).ready(function(){
        $('#arrival_time_display').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            useCurrent:false

        });

        $('#appointed_time_display').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            useCurrent:false
        });

        $('#appointed_time_display').on('dp.change', function (e) {
            /*console.log('new=>',e.date.format('DD-MM-YYYY'), e.date.unix());
            console.log('old=>',e.oldDate.format('DD-MM-YYYY'));*/
            jQuery('#appointed_time').val(e.date.unix());
        });
        jQuery('#arrival_time_display').on('dp.change', function (e) {
            /*console.log('new=>',e.date.format('DD-MM-YYYY'), e.date.unix());
            console.log('old=>',e.oldDate.format('DD-MM-YYYY'));*/
            jQuery('#arrival_time').val(e.date.unix());
        });

        jQuery(`#search-staff`).click(function () {
            let branch_id = jQuery('#branch_id').val();
            let gid = jQuery(`#branch_id_${branch_id}`).val();
            console.log('chi nhanh id =>', branch_id);
            let url = `/?page=staff&act=select&gid=${gid}&type=follower`;
            window.open(url);
        });
        jQuery(`#search-sale-staff`).click(function () {
            let branch_id = jQuery('#branch_id').val();
            let gid = jQuery(`#branch_id_${branch_id}`).val();
            console.log('chi nhanh id =>', branch_id);
            let url = `/?page=staff&act=select&gid=${gid}&type=sale`;
            window.open(url);
        });
    });
</script>
