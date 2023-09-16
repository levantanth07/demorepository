<?php
/**
 * Created by PhpStorm.
 * User: trinhdinh
 * Date: 2019-01-08
 * Time: 18:56
 */
?>
<section class="container">
    <!-- Info boxes -->
    <div class="row">
        <div class="box box-danger" style='margin-top: 10px;'>
            <div class="box-header with-border"></div>
            <form name='CallHistoryCrmCustomer' id='CallHistoryCrmCustomer'>
                <input type='hidden' name='page' value='customer'>
                <input type='hidden' name='do' value='call_history'>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Loại</label>
                                <select name="type" id="type" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Chi nhánh</label>
                                <select name="branch_id" id="branch_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Từ ngày:</label>

                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input name="from_date" type="text" id="from_date" class="form-control pull-right">
                                </div>
                                <!-- /.input group -->
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Đến ngày:</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input name="to_date" type="text" id="to_date" class="form-control pull-right">
                                </div>
                                <!-- /.input group -->
                            </div>
                        </div>
                        <div class='col-md-2'>
                            <div class="form-group">
                                <label>Tên tài khoản:</label>
                                <input name="account_name" type="text" id="account_name" class="form-control my-colorpicker1 colorpicker-element">
                            </div>
                        </div>
                        <div class='col-xs-2'>
                            <label style='color: #fff;'>Select</label><br/>
                            <button type='submit' class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
            <!-- /.box-body -->
        </div>

        <div class="box box-info" style='margin-top: 10px;'>
            <div class="box-header with-border">
                <h3 class="box-title">Danh sách [[|title|]] <span class="label label-warning pull-right">[[|count|]]</span></h3>
                <div class="box-tools pull-right">
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="listTable">
                        <thead>
                        <tr>
                            <th width='20'>STT</th>
                            <th width='50'>Người Tạo</th>
                            <th width='30'>Thời Gian</th>
                            <th width='30'>Tên KH</th>
                            <th width='20'>SDT KH</th>
                            <th width='50'>Nội Dung</th>
                            <th width='30'>Trạng Thái</th>
                            <th width='20'>Sửa</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--LIST:items-->
                            <tr>
                                <td>[[|items.index|]]</td>
                                <td>
                                    <small>[[|items.created_full_name|]]<br/>
                                        [[|items.created_user_name|]]
                                    </small>
                                </td>
                                <td><?=date('d/m/Y H:i\'', [[=items.created_time=]])?></td>
                                <td>
                                    <a href='<?php echo Url::build('customer',['do'=>'view', 'cid'=>md5([[=items.customer_id=]].CATBE)]); ?>'>[[|items.customer_name|]]</a>
                                    <br><small>[[|items.code|]]</small>
                                </td>
                                <td>[[|items.mobile|]]</td>
                                <td><small>[[|items.content|]]</small></td>
                                <td>[[|items.status_name|]]</td>
                                <td>
                                    <!--IF:cond_editable( [[=items.editable=]] )-->
                                    <?php $editLink = Url::build('lich-su-cuoc-goi',array('cmd'=>'edit','cid'=>md5([[=items.customer_id=]].CATBE),'nid'=>md5([[=items.id=]].CATBE),'window'=>1)); ?>
                                    <a href='javascript:' onclick='popupCenterDual("<?=$editLink?>#EditCrmCustomerCallHistory", "Cập nhật cuộc gọi", 500, jQuery(window).height())'>sửa</a>
                                    <!--ELSE-->
                                    <!--/IF:cond_editable-->
                                </td>
                            </tr>
                        <!--/LIST:items-->
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                [[|paging|]]
                <!--<a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>-->
            </div>
            <!-- /.box-footer -->
        </div>
    </div>
</section>
<link rel="stylesheet" href="assets/lib/DataTables/datatables.min.css"  type="text/css" />
<script type="text/javascript" src="assets/lib/DataTables/datatables.min.js"></script>
<script type='text/javascript'>
    jQuery(document).ready(function(){
        jQuery('#from_date').datepicker({format:'dd/mm/yyyy',language:'vi'});
        jQuery('#to_date').datepicker({format:'dd/mm/yyyy',language:'vi'});

        jQuery('#listTable').DataTable({
            fixedHeader: true,
            paging: false,
            scrollY: 600,
            "searching": false,
            "ordering": false,
            "info":     false
        });
    });
</script>
