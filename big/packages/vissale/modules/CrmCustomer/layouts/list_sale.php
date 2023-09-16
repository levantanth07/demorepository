<?php
$title = [[=title=]];
$action = (URL::get('do') == 'delete') ? 'delete' : 'list';
System::set_page_title(Portal::get_setting('company_name', '') . ' ' . $title);?>
<div class="container">
    <fieldset id="toolbar">
        <div class="col-md-8">
            <h3 class="title"><i class="fa fa-female"></i> <?php echo $title; ?></h3>
        </div>
        <div clas="col-md-4">
            <div class="pull-right">
                <a href="<?php echo Url::build_current(array('birth'=>'today', 'do' => 'search')); ?>" class="btn btn-default">
                    <i class="fa fa-calendar-o"></i> SN Hôm nay
                </a>
                <a href="<?php echo Url::build_current(array('birth'=>'week', 'do' => 'search')); ?>" class="btn btn-default">
                    <i class="fa fa-calendar-o"></i> SN Tuần này
                </a>
                <a  class="btn btn-primary" href="<?php echo Url::build_current(array('act', 'do' => 'add')); ?>">
                    <i class="fa fa-plus"></i> Thêm mới
                </a>
                <!--IF:cond(Session::get('admin_group'))-->
                <?php /*** ?><input type="button" value="<?php echo (URL::get('do')=='delete')?'Xác nhận xóa':'Xóa'?>" class="btn btn-danger"
                 * onclick="ListCrmCustomerForm.do.value='delete';ListCrmCustomerForm.submit();" /><?php ***/ ?>
                <!--/IF:cond-->
            </div>
        </div>
    </fieldset>
    <br>
    <style type="text/css">
        #search-box td a.full {
            width: 100%
        }
    </style>
    <div class="panel panel-default">
        <form method="GET" name="SearchCrmCustomerForm">
            <table class="table table-bordered" id="search-box">
                <tr>
                    <td align="right" width='150'>
                        <select name="branch_id" id="branch_id" class="form-control"></select>
                    </td>
                    <td width='100'>
                        <select name="level" id="level" class="form-control"
                                style="border: 1px dashed red"></select>
                    </td>
                    <td width='200' align="right">
                        <select name="status_id" id="status_id" class="form-control"
                                style="border: 1px dashed red"></select>
                    </td>
                    <td>
                        <span class="pull-right-container statistic">
                          <small class="label pull-left bg-yellow">Level 1: <?=!empty($this->map['level_count'][1]['count'])?$this->map['level_count'][1]['count']:0;?></small>
                          <small class="label pull-left bg-green">Level 2:  <?=!empty($this->map['level_count'][2]['count'])?$this->map['level_count'][2]['count']:0;?></small>
                          <small class="label pull-left bg-red">Level 3:  <?=!empty($this->map['level_count'][3]['count'])?$this->map['level_count'][3]['count']:0;?></small>
                          <small class="label pull-left bg-blue">Level 4:  <?=!empty($this->map['level_count'][4]['count'])?$this->map['level_count'][4]['count']:0;?></small>
                          <small class="label pull-left bg-purple">Level 5:  <?=!empty($this->map['level_count'][5]['count'])?$this->map['level_count'][5]['count']:0;?></small>
                          <small class="label pull-left bg-orange">Level 6:  <?=!empty($this->map['level_count'][6]['count'])?$this->map['level_count'][6]['count']:0;?></small>
                        </span>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td align="right">
                        <select name="schedule_filter" id="schedule_filter" class="form-control"
                                style=""></select>
                    </td>
                    <td>
                        <input name="from_date" type="text" id="from_date"
                               autocomplete="off" class="form-control" placeholder="Từ ngày">
                    </td>
                    <td align="right">
                        <input name="to_date" type="text" id="to_date"
                               autocomplete="off" class="form-control" placeholder="Đến ngày">
                    </td>
                    <td align="right">
                        <input name="customer_name" type="text" id="customer_name" class="form-control"
                               placeholder="Họ và tên"/>
                    </td>
                    <td align="right">
                        <input name="mobile" type="text" id="mobile" class="form-control" onchange="SearchCrmCustomerForm.submit();"
                               placeholder="Điện thoại"/>
                    </td>
                    <td align="right" width="22%">
                        <input type="submit" class="btn btn-success" value="Tìm kiếm" style='padding: 5px' />
                        <!--IF:export_excel(Session::get('account_type')==3)-->
                            <button type="submit" name="do" id="do" value="export_excel" class="btn btn-primary" style='padding: 5px'>Xuất excel</button>
                        <!--/IF:export_excel-->
                        <a title="Reset" href="<?php echo Url::build('customer'); ?>" style='padding: 5px'
                           class="btn btn-default">Reset</a>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="page_no" value="1">
            <input type="hidden" name="page" value="customer">
            <input type="hidden" name="do" value="sale">
        </form>
        <form method="post" name="ListCrmCustomerForm" enctype='multipart/form-data'>
            <div style='padding: 10px;'>
                <div colspan="13" title="Chọn tất cả" align="left">
                    [[|paging|]]

                    <button type='submit' value='submit'
                            style='width: auto;float: right;' class='btn btn-warning'>Import</button>
                    <input type="file" name="import_crm" required id="import_crm" class="form-control"
                           style='width: auto;float: right;' />

                </div>
            </div>
            <table class="table table-bordered table-striped" id="customerListTable">
                <thead>
                    <tr valign="middle">
                        <th width="10">STT</th>
                        <th width="200" align="left">Khách hàng</th>
                        <th width="100" align="left">Trạng thái</th>
                        <th width="100" align="left">Phân nhóm</th>
                        <th width="90" align="left">Phụ trách</th>
                        <th width="90" align="left">Người tạo</th>
                        <th width="20" align="center">Xem</th>
                        <th width="50" align="left">Hẹn</th>
                        <th width="50" align="left">Note</th>
                        <th width="50" align="left">C.gọi</th>
                        <th width="1%" align="left">Sửa</th>
                    </tr>
                </thead>
                <tbody>
                <?php $c = 0; ?>
                <!--LIST:items-->
                <?php
                    if(Url::get('do') != 'sale') {
                        [[=items.status_color=]] = '#666';
                    }
                ?>
                <tr id="customer_id_[[|items.no|]]" >
                    <td width="1%" valign="top">[[|items.no|]]</td>
                    <td align="left">
                        <!--IF:select_cond(Url::get('act')=='select')-->
                        <a href="#" lang="[[|items.id|]]" onclick="return false;"
                           class="btn btn-sm btn-warning select-item">Chọn</a>
                        <!--/IF:select_cond-->
                        <!--IF:w_cond([[=items.warning_note=]])-->
                        <i title="Cảnh báo: [[|items.warning_note|]]" class="fa fa-exclamation-triangle text-danger"
                           data-toggle="tooltip" data-placement="right"></i>
                        <!--/IF:w_cond-->
                        <strong id="name_[[|items.id|]]">[[|items.name|]]</strong><br>
                        <strong>[[|items.mobile|]]</strong> <br>
                        <small>Số tour: [[|items.count_process|]]</small><br>
                        <span class='small'>[[|items.code|]]</span><br>
                        <div class="small">
                            + CN: [[|items.group_name|]]
                        </div>
                        <!--IF:description_cond([[=items.description=]])-->
                        <span style="position:relative;" data-toggle="tooltip" data-placement="right"
                           title="ghi chú: [[|items.description|]]">
                            <i class="glyphicon glyphicon-file text-blue"></i></span> <br/>
                        <!--/IF:description_cond-->
                        <!--IF:job_title_cond([[=items.job_title=]])-->
                            <div class="small text-gray">+ CV: [[|items.job_title|]]</div><br/>
                        <!--/IF:job_title_cond-->
                        <!--IF:birth_date_cond([[=items.birth_date=]])-->
                            <div class="small text-gray">+ NS: [[|items.birth_date|]]</div><br/>
                        <!--/IF:birth_date_cond-->
                        <!--IF:address_cond([[=items.address=]])-->
                            <div class="small text-gray">+ DC: [[|items.address|]]</div><br/>
                        <!--/IF:address_cond-->
                    </td>
                    <td align="left" valign="top" style="color:[[|items.status_color|]]">
                        <small>[[|items.status_level|]] [[|items.status_name|]]</small>
                    </td>
                    <td align="left" valign="top">[[|items.crm_group_name|]]</td>
                    <td align="left" valign="top">[[|items.follow_user_name|]] </td>
                    <td align="left" valign="top">
                        [[|items.creator|]]<br>
                        <small class='text-gray'>[[|items.created_time|]]</small>
                    </td>
                    <td align="center" valign="top" bordercolor="#CCC">
                        <a class="btn btn-default"
                            href="<?php echo Url::build_current(array('cid'=>md5([[=items.id=]].CATBE),'do'=>'view', 'branch_id'));?>"
                             title="Xem chi tiết khách hàng">
                            <i class="glyphicon glyphicon-eye-open"></i>
                        </a>
                    </td>
                    <td align="left" valign="top" bordercolor="#CCC">
                        <a class="btn btn-default"
                          target="_blank"
                          href="<?php echo Url::build('lich-hen',array('cid'=>md5([[=items.id=]].CATBE),'cmd'=>'add'));?>"
                          title="Đặt lịch hẹn"><i class="fa fa-calendar text-danger"></i></a><br>
                        <small class='text-gray'>[[|items.appointed_time|]]</small>
                    </td>
                    <td align="left" valign="top" bordercolor="#CCC">
                        <a class="btn btn-default"
                          target="_blank"
                          href="<?php echo Url::build('ghi-chu-khach-hang',array('cid'=>md5([[=items.id=]].CATBE),'cmd'=>'add'));?>"
                          title="Ghi chú"><i class="fa fa-file-text text-warning"></i></a><br>
                        <small class='text-gray'>[[|items.noted_time|]]</small>
                    </td>
                    <td align="left" valign="top" bordercolor="#CCC">
                        <?php $url_call = Url::build('lich-su-cuoc-goi',array('cid'=>md5([[=items.id=]].CATBE),'cmd'=>'add', 'window'=>1)); ?>
                        <a class="btn btn-default" href='javascript:'
                                  onclick='popupCenterDual("<?=$url_call?>#EditCrmCustomerCallHistory", "Ghi cuộc gọi", 500, 768)'
                                  title="Lịch sử cuộc gọi">
                            <i class="fa fa-phone-square text-success"></i>
                        </a><br>
                        <small class='text-gray'>[[|items.called_time|]]</small><br>
                        <small class='text-gray'>[[|items.call_status|]]</small>
                    </td>
                    <td align="center" valign="top">
                        <?php
                        $cid = md5([[=items.id=]] . CATBE);
                      ?>
                        <a href="<?php echo URL::build_current(array('page_no', 'act', 'input_id', 'branch_id')); ?>&do=edit&cid=<?= $cid ?>"
                           class="btn btn-default"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
                <!--/LIST:items-->
                </tbody>
                <!--<tfoot></tfoot>-->
            </table>
            <!--<input type="hidden" name="do" value="delete"/>-->
            <!--IF:delete(URL::get('do')=='delete')-->
            <!--<input type="hidden" name="confirm" value="1"/>-->
            <!--/IF:delete-->
            <div class='padding-10px'>
                <div colspan="13" title="Chọn tất cả">Tổng cộng <strong class="text-info">[[|total|]]</strong> kh&aacute;ch
                    h&agrave;ng
                </div>
            </div>
            <div class='padding-10px'>
                <div title="Chọn tất cả" align="left">[[|paging|]]</div>
            </div>
        </form>
    </div>
    <div id="noteContent"
         style="position:absolute;display:none;border:2px solid #355C9B;background:#FFFFCC;float:left;max-width:500px;"></div>
</div>
<style>
    .text-gray{color: #666 !important;}
    .statistic small{padding: 5px; margin-top: 3px;margin-right: 2px;}
    .padding-10px{
        padding: 10px;
    }
</style>
<link rel="stylesheet" href="assets/lib/DataTables/datatables.min.css"  type="text/css" />
<script type="text/javascript" src="assets/lib/DataTables/datatables.min.js"></script>
<script>
    function openNote(obj, content) {
        getId('noteContent').style.top = obj.offsetTop + 25 + 'px';
        getId('noteContent').style.left = obj.offsetLeft + 25 + 'px';
        getId('noteContent').innerHTML = '<div style="text-align:right;"><span style="padding:2px;background:#355C9B;color:#FFFFFF;border-left:1px solid #355C9B;border-bottom:1px solid #355C9B;cursor:pointer;" onclick="closeNote(\'noteContent\')">Đóng&nbsp;</div>';
        getId('noteContent').innerHTML += '<div style="padding:10px;float:left;">' + content + '</div>';
        jQuery('#noteContent').fadeIn();
    }

    function closeNote(element) {
        jQuery('#noteContent').fadeOut();
    }

    jQuery(document).ready(function () {
        $('#searchContact').click(function () {
            window.open('index062019.php?page=customer&act=select');
        });
        jQuery('.select-item').click(function () {
            var fromObj = getId('name_' + jQuery(this).attr('lang'));
            if (window.opener.document.getElementById("customer_id")) {
                var toObjName = window.opener.document.getElementById("customer_name");
                var toObjId = window.opener.document.getElementById("customer_id");
            } else if (window.opener.document.getElementById("contact_id")) {
                var toObjName = window.opener.document.getElementById("contact_name");
                var toObjId = window.opener.document.getElementById("contact_id");
            }
            toObjName.value = fromObj.innerHTML;
            toObjId.value = jQuery(this).attr('lang');
            window.close();
        });
    });
</script>
[[|customer_statuses_json|]]
<script>
    var selected_status_id = '<?=Url::get('status_id');?>';
</script>
<script>
    jQuery(document).ready(function () {
        init_dates();

        let from_date = jQuery('#from_date');
        let to_date = jQuery('#to_date');
        let schedule_filter = jQuery('#schedule_filter');
        from_date.datepicker({format: 'dd/mm/yyyy', language: 'vi'});
        to_date.datepicker({format: 'dd/mm/yyyy', language: 'vi'});

        schedule_filter.on('change', function (event) {
            init_dates();
        });

        function init_dates() {
            let from_date = jQuery('#from_date');
            let to_date = jQuery('#to_date');
            let schedule_filter = jQuery('#schedule_filter');
            if ( schedule_filter.val() ) {
                //from_date.attr('readonly', false);
                //to_date.attr('readonly', false);
                from_date.removeAttr('disabled');
                to_date.removeAttr('disabled');
                return false;
            }
            from_date.val('');
            to_date.val('');
            from_date.attr('disabled', true);
            to_date.attr('disabled', true);
            //from_date.removeAttr('disabled');
            //to_date.removeAttr('disabled');
        }
    });

    jQuery(document).ready( function () {
        jQuery('#customerListTable').DataTable({
            fixedHeader: true,
            paging: false,
            scrollY: 600,
            "searching": false,
            "ordering": false,
            "info":     false
        });
    });

    jQuery(document).ready(function () {

        init_statuses();

        $(`#level`).change(function (event) {
            init_statuses();
        });
    });
    function init_statuses() {
        let statues_display = $(`#status_id`);
        statues_display.empty();
        let selected_level = $(`#level`).val();
        let statues = _.filter(all_statuses, { 'level': selected_level });
        if (statues.length === 0) return false;

        _.forEach(statues, function(value, key) {
            let selected = '';
            if (parseInt(value.id)===parseInt(selected_status_id)) {
                selected='selected';
            }

            statues_display.append(`<option ${selected} value='${value.id}'>${value.name}</option>`);
        });
        let default_selected = '';
        if (selected_status_id.length===0){
            default_selected='selected';
        }
        let defaultOption = `<option ${default_selected} value=''>Tất cả</option>`;
        statues_display.append(defaultOption);
    }

</script>
