<?php
    $warehouse = [[=warehouse=]];
    $isExportExcel = url::get('type') == 'EXPORT_EXCEL';
    $isDeleteOrder = url::get('type') == 'DELETE';
    $isSendMailToCarrier = url::get('type') == 'SEND_EMAIL_TO_CARRIER';
    $isSendMailToWarehouse = url::get('type') == 'SEND_EMAIL_TO_WAREHOUSE';
    $isUpdateShopSetting = url::get('type') == 'UPDATE_SHOP_SETTING';
    $isPrint = url::get('type') == 'PRINT';
    $message = '';
    if(User::is_admin()){
        if($isExportExcel){
            $message = '(Lịch sử xuất Excel xem trong 10 ngày)';
        } else if ($isDeleteOrder){
            $message = '(Lịch sử xóa Đơn hàng xem trong 30 ngày)';
        } else if ($isPrint){
            $message = '(Lịch sử In Đơn hàng xem trong 30 ngày)';
        }
    }   
?>
<script>
    function check_selected()
    {
        var status = false;
        jQuery('form :checkbox').each(function(e){
            if(this.checked && this.id=='ListLogForm_checkbox')
            {
                status = true;
            }
        });
        return status;
    }
    function make_cmd(cmd)
    {
        jQuery('#cmd').val(cmd);
        document.ListLogForm.submit();
    }
</script>
<div class="container-fluid">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-8">
                    <div class="text-bold"><i class="fa fa-flag"></i> Quản lý log [[|logTitle|]] ([[|total|]])  </div>
                    <p class="text-danger"><?php echo $message ?? '' ?></p>
                </div>
                <div class="col-md-4 text-right">
                    <!--IF:cond(User::is_admin())-->
                    <td id="toolbar-cancel"  align="center"><a onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_atleast_item');?>');}}" class="btn btn-danger"> Delete </a> </td>
                    <!--/IF:cond-->
                </div>
            </div>
        </div>
        <div class="panel-body">
            <form name="ListLogForm" method="post">
                <div class="panel">
                    <div class="row">
                        <div class="col-xs-1">
                            <input name="keyword" type="text" id="keyword" class="form-control" placeholder="Từ khoá">
                        </div>
                        <?php
                        if ($isExportExcel || $isSendMailToCarrier || $isUpdateShopSetting || $isPrint || $isSendMailToWarehouse || $isDeleteOrder) {
                            ?>
                            <div class="col-xs-1 no-padding">
                                <input name="from_date" type="text" id="from_date"
                                       autocomplete="off" class="form-control" placeholder="Từ ngày"
                                       style="max-width: 150px;">
                            </div>
                            <div class="col-xs-1 no-padding">
                                <input name="to_date" type="text" id="to_date"
                                       autocomplete="off" class="form-control" placeholder="Đến ngày"
                                       style="max-width: 150px;">
                            </div>
                            <?php
                        }
                        $paddingTop = 0;
                        if ($isExportExcel || $isSendMailToCarrier || $isPrint || $isSendMailToWarehouse) {
                            $textOrderId = 'Loại xuất Excel';
                            if ($isSendMailToCarrier || $isSendMailToWarehouse) {
                                $paddingTop = 10;
                                $textOrderId = 'Loại gửi email';
                                ?>
                                <?php if ($isSendMailToCarrier): ?>
                                    <div class="col-xs-2">
                                        <select name="to_carrier" id="to_carrier" class="form-control">
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <?php if ($isSendMailToWarehouse): ?>
                                    <div class="col-xs-2">
                                        <select name="to_warehouse" id="to_warehouse" class="form-control">
                                            <?php foreach ($warehouse as $key => $value): ?>
                                                <?php if (isset($_GET['to_warehouse']) && $_GET['to_warehouse'] === $value['name']): ?>
                                                    <option selected=""><?php echo $value['name'] ?></option>
                                                <?php else: ?>
                                                    <option><?php echo $value['name'] ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                
                            <?php } else if ($isPrint) {
                                $textOrderId = 'Loại in';
                            } ?>
                            <div class="col-xs-2">
                                <select name="user_export" id="user_export" class="form-control">
                                </select>
                            </div>
                            <div class="col-xs-1">
                                <input name="order_id" type="text" id="order_id" class="form-control"
                                       placeholder="Mã đơn hàng">
                            </div>
                            <?php if ($isSendMailToWarehouse): ?>
                                <div class="col-xs-2">
                                    <select class="form-control" id="export_type" name="export_type">
                                        <option value=""><?= $textOrderId ?></option>
                                       <option <?php if ($_GET['export_type'] == '0') { ?>selected="true" <?php }; ?>value="0">Ẩn số điện thoại</option>
                                       <option <?php if ($_GET['export_type'] == '1') { ?>selected="true" <?php }; ?>value="1">Không ẩn số điện thoại</option>
                                    </select>
                                </div>

                                <div class="col-xs-2">
                                    <select class="form-control" id="log_type_extra" name="log_type_extra">
                                        <option value="">Đính kèm phiếu xuất kho ?</option>
                                        <option <?php if ($_GET['log_type_extra'] == '0') { ?>selected="true" <?php }; ?>value="0">Không</option>
                                        <option <?php if ($_GET['log_type_extra'] == '1') { ?>selected="true" <?php }; ?>value="1">Có</option>
                                    </select>
                                </div>
                            <?php else: ?>
                                <div class="col-xs-2">
                                    <select class="form-control" id="export_type" name="export_type">
                                        <option value=""><?= $textOrderId ?></option>
                                        <option value="0">Không ẩn số điện thoại</option>
                                        <option value="1">Ẩn số điện thoại</option>
                                    </select>
                                </div>
                            <?php endif; ?>
                        <?php } ?>
                        <!--IF:cond(User::is_admin())-->
                        <div class="col-xs-1 text-right">SHOP</div>
                        <div class="col-xs-2">
                            <input name="group_name" type="text" id="group_name" class="form-control">
                        </div>
                        <!--/IF:cond-->
                        <div class="col-xs-2">
                            <button name="search" type="submit" id="search" class="btn btn-default"><i
                                        class="fa fa-search"></i> Tìm kiếm
                            </button>
                            <a title="Reset" href="<?php echo Url::build('log',array(), false, false,'',url::get('type')); ?>"
                               class="btn btn-default">
                                <i class="fa fa-refresh"></i> Tìm lại
                            </a>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th align="left" style="width: 200px"><a>Tiêu đề</a></th>
                        <th align="left"><a>Chi tiết</a></th>
                        <?php
                        if($isExportExcel || $isSendMailToCarrier || $isPrint || $isSendMailToWarehouse){
                            ?>
                            <th align="left"><a>Mã đơn hàng</a></th>
                        <?php } ?>
                        <th align="left"><a>Tài khoản</a></th>
                        <th align="left">IP</th>
                        <th align="left" style="display: none;"><a>Phân loại</a></th>
                        <?php
                        if ($isExportExcel) {
                            ?>
                            <th align="left"><a>Loại xuất excel</a></th>
                        <?php } else if ($isSendMailToCarrier || $isSendMailToWarehouse) {
                            ?>
                            <th align="left"><a>Loại gửi email</a></th>
                            <th align="left"><a>Kèm phiếu xuất kho</a></th>
                            <?php
                        } else if($isPrint){
                            ?>
                            <th align="left"><a>Loại in</a></th>
                            <?php
                        } ?>
                        <th align="left"><a>SHOP</a></th>
                        <?php
                        if($isSendMailToCarrier){
                            ?>
                            <th align="left"><a>Nhà vận chuyển</a></th>
                            <th align="left"><a>Email Nhà vận chuyển</a></th>
                            <?php
                        }
                        ?>
                        <?php
                        if($isSendMailToWarehouse){
                            ?>
                            <th align="left"><a>Kho</a></th>
                            <th align="left"><a>Email Kho</a></th>
                            <?php
                        }
                        ?>
                        <th align="left"><a>Thời gian</a></th>
                    </tr>
                    <?php $i=0;?>
                    <!--LIST:items-->
                    <tr valign="middle">
                        <td>[[|items.title|]]</td>
                        <td><div style="overflow: auto;width: 200px;max-height: 200px">[[|items.description|]]</div></td>
                        <?php
                        if($isExportExcel || $isSendMailToCarrier || $isPrint || $isSendMailToWarehouse){
                            ?>
                            <td>
                                <div style="max-height: 200px;overflow-y: scroll;">
                                    [[|items.list_export_order_id|]]
                                </div>
                            </td>
                        <?php } ?>
                        <td>[[|items.user_id|]]</td>
                        <td>[[|items.ip|]]</td>
                        <td style="display: none;">[[|items.type|]]</td>
                        <?php
                        if($isExportExcel || $isSendMailToCarrier || $isPrint || $isSendMailToWarehouse){
                            ?>
                            <td><?php echo intval($this->map['items']['current']['censored_phone_number']) === 1 ? 'Ẩn số điện thoại' : 'Không ẩn số điện thoại'; ?></td>
                            <!-- <td>
                                <?php echo ([[=items.log_type_extra=]] == 1) ? 'Có' : 'Không'; ?>   
                            </td> -->
                        <?php } ?>
                        <td>[[|items.group_name|]]</td>
                        <?php
                        if($isSendMailToCarrier || $isSendMailToWarehouse){
                            ?>
                            <td>[[|items.carrier|]]</td>
                            <td>[[|items.carrier_email|]]</td>
                            <?php
                        }
                        ?>
                        <td nowrap="nowrap"><?php echo date('H:i d/m/Y',[[=items.time=]]);?></td>
                    </tr>
                    <!--/LIST:items-->
                </table>
                <div class="pt">
                    [[|paging|]]
                </div>
                <input type="hidden" name="cmd" value="" id="cmd"/>
            </form>
        </div>
    </div>
</div>
<script>
    $.fn.datepicker.defaults.format = "dd/mm/yyyy";
    $('#from_date').datetimepicker({format: 'DD/MM/YYYY'});
    //jQuery('#create_date_to').datepicker();
    $('#to_date').datetimepicker({format: 'DD/MM/YYYY'});
</script>
