<script>
    $(function() {
        $('#from_date').datetimepicker({defaultDate: new Date(),format:'YYYY-MM-DD 00:00:00'});
        $('#to_date').datetimepicker({defaultDate: new Date(),format:'YYYY-MM-DD 23:59:59'});
    });
</script>
<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-8">
                    <div class="text-bold"><i class="fa fa-flag"></i> Lịch sử thay đổi trạng thái đơn hàng ([[|total|]])</div>
                </div>
                <div class="col-md-4 text-right">

                </div>
            </div>
        </div>
        <div class="panel-body">
            <form name="LogStatusForm" method="post" class="form-inline">
                <div class="panel">
                    <div class="row">
                        <div class="col-xs-2">
                            <div class="form-group">
                                <label for="keyword">Mã đơn hàng</label>
                                <input name="keyword" type="text" id="keyword" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="form-group">
                                <label for="from_date">Từ ngày</label>
                                <input name="from_date" type="text" id="from_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="form-group">
                                <label for="to_date">Đến ngày</label>
                                <input name="to_date" type="text" id="to_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="form-group">
                                <label for="before_order_status_id">Trạng thái cũ</label>
                                <select name="before_order_status_id" id="before_order_status_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="form-group">
                                <label for="order_status_id">Trạng thái mới</label>
                                <select name="order_status_id" id="order_status_id" class="form-control"></select>
                            </div>
                        </div>
                        <div class="col-xs-2">
                            <div class="form-group pull-right">
                                <br>
                                <button name="search" type="submit" id="search" class="btn btn-default"><i class="fa fa-search"></i> Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="15%" align="left">Mã đơn hàng</th>
                        <th width="15%" align="left">Tên Khách Hàng</th>
                        <th width="15%" align="left">Điện thoại</th>
                        <th width="20%" align="left">Trạng thái cũ</th>
                        <th width="20%" align="left">Trạng thái mới</th>
                        <th width="10%" align="left">Tài khoản</th>
                        <th width="7%" align="left"><a>Thời gian</a></th>
                    </tr>
                    <?php $i=0;?>
                    <!--LIST:items-->
                    <tr valign="middle" <?php Draw::hover(Portal::get_setting('crud_item_hover_bgcolor','#FFFFDD'));?> style="cursor:hand;<?php if($i%2){echo 'background-color:#F9F9F9';}?>" id="Category_tr_[[|items.id|]]">
                        <td>[[|items.order_id|]]</td>
                        <td>[[|items.customer_name|]]</td>
                        <td>[[|items.mobile|]]</td>
                        <td><span class="label label-default">[[|items.before_order_status|]]</span></td>
                        <td><span class="label label-danger">[[|items.order_status|]]</span></td>
                        <td>[[|items.user_created_name|]]</td>
                        <td nowrap="nowrap"><?php echo date('H:i\' d/m/Y',strtotime([[=items.created=]]));?></td>
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
