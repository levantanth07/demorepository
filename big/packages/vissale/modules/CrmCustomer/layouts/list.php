<?php
$title = [[=title=]];
$action = (URL::get('do') == 'delete') ? 'delete' : 'list';
System::set_page_title(Portal::get_setting('company_name', '') . ' ' . $title);?>
<div class="container full"><br>
    <style type="text/css">
        #search-box td a.full {
            width: 100%
        }
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
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title"><i class="fa fa-female"></i> <?php echo $title; ?></h3>
            <div class="box-tools pull-right">
                <a href="<?php echo Url::build_current(array('birth'=>'today', 'do' => 'search')); ?>" class="btn btn-default">
                    <i class="fa fa-calendar-o"></i> SN Hôm nay
                </a>
                <a href="<?php echo Url::build_current(array('birth'=>'week', 'do' => 'search')); ?>" class="btn btn-default">
                    <i class="fa fa-calendar-o"></i> SN Tuần này
                </a>
               <!--  <a  class="btn btn-primary" href="#">
                    <i class="fa fa-plus"></i> Thêm mới
                </a> -->
                <!--IF:cond(Session::get('admin_group'))-->
                <?php /*** ?><input type="button" value="<?php echo (URL::get('do')=='delete')?'Xác nhận xóa':'Xóa'?>" class="btn btn-danger"
                 * onclick="ListCrmCustomerForm.do.value='delete';ListCrmCustomerForm.submit();" /><?php ***/ ?>
                <!--/IF:cond-->
            </div>
        </div>
        <div class="box-body">
            <form method="GET" name="SearchCrmCustomerForm">
                <input type="hidden" name="page" value="customer">
                <input type="hidden" name="page_no" value="1">
                <input type="hidden" name="act" value="<?= !empty($_REQUEST['act'])?$_REQUEST['act']:''?>">
                <input type="hidden" name="page_no" value="1">
                <table class="table table-bordered" id="search-box">
                    <tr>
                        <td align="right">
                            <select name="schedule_filter" id="schedule_filter" class="form-control" style="border: 1px dashed red"></select>
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
                            <input name="customer_id" type="text" value="<?=isset($_GET['customer_id'])?$_GET['customer_id'] : '';?>" onkeyup="this.value = this.value.replaceAll(/[\D]+/g, '')" onchange="onChangeCustomerID(this)" id="customer_id" class="form-control"
                                   placeholder="Mã KH"/>
                        </td>
                        <td align="right">
                            <input name="customer_name" type="text" id="customer_name" class="form-control"
                                   placeholder="Họ và tên"/>
                        </td>
                        <td align="right">
                            <input name="mobile" type="text" id="mobile" class="form-control" onchange="SearchCrmCustomerForm.submit();"
                                   placeholder="Điện thoại"/>
                        </td>
                        <td align="right">
                            <select name="crm_group_id" id="crm_group_id" class="form-control"></select>
                        </td>
                        <td align="right" width="20%">
                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Tìm kiếm</button>
                            <!--IF:export_excel(Session::get('account_type')==3)-->
                            <button type="submit" name="do" id="do" value="export_excel" class="btn btn-success" style='padding: 5px'>Xuất excel</button>
                            <!--/IF:export_excel-->
                            <a title="Reset" href="<?php echo Url::build('customer'); ?>" style='padding: 5px'
                               class="btn btn-default"><i class="fa fa-refresh"></i> Tìm lại</a>
                        </td>
                    </tr>
                </table>
            </form>
            <form method="post" name="ListCrmCustomerForm" enctype='multipart/form-data'>
                <div class="row">
                    <div class="col-md-12 text-right" title="Chọn tất cả" align="left">
                        [[|paging|]]
                        <div class="hidden">
                            <button type='submit' value='submit'
                                    style='width: auto;float: right;' class='btn btn-warning'>Import</button>
                            <input type="file" name="import_crm" required id="import_crm" class="form-control"
                                   style='width: auto;float: right;' />

                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped" id="customerListTable">
                    <thead>
                    <tr>
                        <th width="1%">STT</th>
                        <th width="30%">Khách hàng</th>
                        <th>ĐH mới nhất</th>
                        <th align="left">Phân nhóm</th>
                        <th align="left">Phụ trách</th>
                        <th align="left">Người tạo</th>
                        <th align="center">Xem</th>
                        <th align="left">Hẹn</th>
                        <th align="left">Note</th>
                        <th>C.gọi</th>
                        <th>Sửa</th>
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
                    <tr id="customer_id_[[|items.id|]]" >
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
                            <span class="text-bold" id="name_[[|items.id|]]">
                            [[|items.id|]] - [[|items.name|]]
                        </span><br>
                            <span class='small'>SHOP: [[|items.group_name|]]</span><br>
                            <span>
                                <?php if (Session::get('callio_payload')) { ?>
                                    <a class="fa fa-phone callio-call-customer" data-phonenumber="[[|items.mobile_show|]]" data-phonename="[[|items.mobile|]]"></a>
                                <?php } elseif (Session::get('voip24h_payload')) { ?>
                                    <a class="fa fa-phone voip24h-call-customer" data-phonenumber="[[|items.mobile_show|]]" data-phonename="[[|items.mobile|]]"></a>
                                <?php } else { ?>
                                    <i class="fa fa-phone-square"></i>
                                <?php } ?>
                             [[|items.mobile|]]
                        </span><br>
                            <span>
                            + Đơn hàng thành công: <span class="badge small">[[|items.success_total_quantity|]]</span>
                        </span>
                            <span class='small'>[[|items.code|]]</span>
                            <!--IF:description_cond([[=items.description=]])-->
                            <span style="position:relative;" data-toggle="tooltip" data-placement="right"
                                  title="ghi chú: [[|items.description|]]">
                            <i class="glyphicon glyphicon-file text-blue"></i>
                        </span> <br/>
                            <!--/IF:description_cond-->
                            <!--IF:job_title_cond([[=items.job_title=]])-->
                            <div class="small text-gray">+ Chức vụ: [[|items.job_title|]]</div><br/>
                            <!--/IF:job_title_cond-->
                            <!--IF:birth_date_cond([[=items.birth_date=]])-->
                            <div class="small text-gray">+ Ngày Sinh: [[|items.birth_date|]]</div><br/>
                            <!--/IF:birth_date_cond-->
                            <!--IF:address_cond([[=items.address=]])-->
                            <div class="small text-gray">+ ĐC: [[|items.address|]]</div><br/>
                            <!--/IF:address_cond-->
                        </td>
                        <td align="center" valign="top">
                            <a href="index062019.php?page=admin_orders&cmd=edit&id=[[|items.last_order_id|]]" target="_blank">[[|items.last_order_id|]]</a><br><span class="label" style="float: left;width:100% !important;border: 1px solid #CCC; color:<?=[[=items.last_order_status_color=]]?[[=items.last_order_status_color=]]:'#999'?>">[[|items.last_order_status|]]</span>
                        </td>
                         <td align="left" valign="top"><?= [[=items.crm_group_name=]] ?></td>
                        <td align="left" valign="top">[[|items.follow_user_name|]] </td>
                        <td align="left" valign="top">
                            [[|items.creator|]]<br>
                            <small class='text-gray'>[[|items.created_time|]]</small>
                        </td>
                        <td align="center" valign="top" bordercolor="#CCC">
                            <a class="btn btn-default"
                               href="<?php echo Url::build_current(array('cid'=>([[=items.id=]]),'do'=>'view', 'branch_id'));?>"
                               title="Xem chi tiết khách hàng">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </a>
                        </td>
                        <td align="left" valign="top" bordercolor="#CCC">
                            <a class="btn btn-default"
                               target="_blank"
                               href="<?php echo Url::build('lich-hen',array('cid'=>[[=items.id=]],'cmd'=>'add'));?>"
                               title="Đặt lịch hẹn"><i class="fa fa-calendar text-danger"></i></a><br>
                            <small class='text-gray'>[[|items.appointed_time|]]</small>
                        </td>
                        <td align="left" valign="top" bordercolor="#CCC">
                            <a class="btn btn-default"
                               target="_blank"
                               href="<?php echo Url::build('ghi-chu-khach-hang',array('cid'=>[[=items.id=]],'cmd'=>'add'));?>"
                               title="Ghi chú"><i class="fa fa-file-text text-warning"></i></a><br>
                            <small class='text-gray'>[[|items.noted_time|]]</small>
                        </td>
                        <td align="left" valign="top" bordercolor="#CCC">
                            <?php $url_call = Url::build('lich-su-cuoc-goi',array('cid'=>[[=items.id=]],'cmd'=>'add', 'window'=>1)); ?>
                            <a class="btn btn-default" href='javascript:'
                               onclick='popupCenterDual("<?=$url_call?>#EditCrmCustomerCallHistory", "Ghi cuộc gọi", 500, 768)'
                               title="Lịch sử cuộc gọi">
                                <i class="fa fa-phone-square text-success"></i>
                            </a><br>
                            <small class='text-gray'>[[|items.called_time|]]</small><br>
                            <small class='text-gray'>[[|items.call_status|]]</small>
                        </td>
                        <td>
                      <?php
                        $cid = [[=items.id=]];
                        $orderId = [[=items.last_order_id=]]
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
                <div valign="middle">
                    <div colspan="13" title="Chọn tất cả">Tổng cộng <strong class="text-info">[[|total|]]</strong> kh&aacute;ch
                        h&agrave;ng
                    </div>
                </div>
                <div valign="middle">
                    <div colspan="13" title="Chọn tất cả" align="left">[[|paging|]]</div>
                </div>
            </form>
        </div>
    </div>
    <div id="noteContent"
         style="position:absolute;display:none;border:2px solid #355C9B;background:#FFFFCC;float:left;max-width:500px;"></div>
</div>
<style>
    .text-gray{color: #666 !important;}
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

    $(document).ready(function () {
        $('#searchContact').click(function () {
            window.open('index062019.php?page=customer&act=select');
        });
        jQuery('.select-item').click(function () {
            let customer_id=jQuery(this).attr('lang');
            let fromObj = getId('name_' + customer_id);
            if (window.opener.document.getElementById("customer_id")) {
                var toObjName = window.opener.document.getElementById("customer_name");
                var toObjId = window.opener.document.getElementById("customer_id");
                var card_discount_rate = window.opener.document.getElementById("card_discount_rate");
                var card_name = window.opener.document.getElementById("card_name");
                var card_start_date = window.opener.document.getElementById("card_start_date");
                var card_end_date = window.opener.document.getElementById("card_end_date");
                var note = window.opener.document.getElementById("note");
                var note_message = window.opener.document.getElementById("note_message");
            } else if (window.opener.document.getElementById("contact_id")) {
                var toObjName = window.opener.document.getElementById("contact_name");
                var toObjId = window.opener.document.getElementById("contact_id");
            }
            toObjName.value = fromObj.innerHTML;
            toObjId.value = customer_id;
            let discount_rate = jQuery(`#card_discount_rate_${customer_id}`).val();
            if ( discount_rate > 0 ){
                card_discount_rate.value = discount_rate;
                card_name.value = jQuery(`#card_name_${customer_id}`).val();
                card_start_date.value = jQuery(`#card_start_date_${customer_id}`).val();
                card_end_date.value = jQuery(`#card_end_date_${customer_id}`).val();
                note.value = `KH ${fromObj.innerHTML} được áp dụng thẻ VIP ${card_name.value} từ ${card_start_date.value} đến ${card_end_date.value}`;
                note_message.innerHTML = note.value;
            }

            window.close();
        });
    });
</script>
<script>
    $(document).ready(function () {
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
    /**
     * Called on change customer id.
     *
     * @param      {<type>}  el      { parameter_description }
     * @return     {<type>}  { description_of_the_return_value }
     */
    function onChangeCustomerID(el)
    {
        if((el.value && el.value > 0) || el.value == ''){
            return ReloadList(1);
        }

        alert('Vui lòng nhập mã khách hàng > 0');
    }
</script>
