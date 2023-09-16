<div class="container full">
    <br>
    <form name="EditableListForm" method="post" id="EditableListForm" class="form-inline">
        <div class="box box-info">
            <div class="box-header with-border">
                <h4 class="box-title"> <i class="fa fa-cogs"></i> Đơn hàng cần đánh giá chất lượng sale/cskh</h4>
                <div class="box-tools pull-right">
                    <!--IF:cond1(1==1)-->
                    <button class="btn btn-default btn-sm" type="button" data-toggle="modal" data-target='#assignOrderModal' onclick="updateTotalNotAssigned();"><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i> Chia đơn nhanh </button>
                    <!--/IF:cond1-->
                    <input name="update" type="submit" class="btn btn-primary" value="Cập nhật">
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input  name="keyword" type="text" id="keyword" value="<?=Url::get('keyword')?>" class="form-control" style="margin-right:2px;width: 205px;" placeholder="Số điện thoại (Tối thiểu [[|min_search_phone_number|]] số)"><br>
                                    <input name="customer_name" type="text" id="customer_name" placeholder="Tên khách hàng" class="form-control"  style="width: 205px;">
                                </div>
                                <div class="form-group">
                                    <input name="ngay_from" type="text" id="ngay_from" class="form-control" style="width: 100px;" placeholder="Từ ngày" autocomplete="off"><br>
                                    <input name="ngay_to" type="text" id="ngay_to" class="form-control" style="width: 100px;" placeholder="Đến ngày" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <select name="cs_status_id" id="cs_status_id" class="form-control" style="width: 150px;"></select><br>
                                    <select name="order_status_id" id="order_status_id" class="form-control" style="width: 150px;"></select>
                                </div>
                                <div class="form-group">
                                    <!--IF:cond(AdminOrders::$admin_group)-->
                                    <select name="search_user_assigned_id" id="search_user_assigned_id" class="form-control" style="width: 150px;"></select><br>
                                    <!--/IF:cond-->
                                    <select name="item_per_page" id="item_per_page" class="form-control" onchange="EditableListForm.submit();" style="width: 150px;"></select>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="-o- Tìm kiếm" class="btn btn-warning" style="width: 100px;"><br>
                                    <a href="<?=Url::build_current(['cmd'])?>" class="btn btn-default" style="width: 100px;"><i class="fa fa-refresh"></i> Tìm lại</a>
                                    <input name="act" type="hidden" id="act">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="nav nav-tabs">
                    <li <?=Url::get('act')?'':' class="active"'?>><a href="<?=Url::build_current(['cmd','keyword','customer_name','ngay_from','ngay_to','search_user_assigned_id'])?>">Tổng hợp</a></li>
                    <li <?=(Url::get('act')=='need_rate')?' class="active"':''?>><a href="<?=Url::build_current(['cmd','act'=>'need_rate','keyword','customer_name','ngay_from','ngay_to','search_user_assigned_id'])?>">Đơn chưa xử lý</a> </li>
                    <li <?=(Url::get('act')=='rated')?' class="active"':''?>><a href="<?=Url::build_current(['cmd','act'=>'rated','keyword','customer_name','ngay_from','ngay_to','search_user_assigned_id'])?>">Đơn đã xử lý</a> </li>
                    <li <?=(Url::get('act')=='overdue')?' class="active text-danger"':'class="tab tab-danger"'?>><a href="<?=Url::build_current(['cmd','act'=>'overdue','keyword','customer_name','ngay_from','ngay_to','search_user_assigned_id'])?>">Đơn quá hạn</a> </li>
                    <li><a data-toggle="tab" class="text-warning">(* Nhấn vào <strong>Mã ĐH</strong> để xem nhanh lịch sử đơn hàng)</a></li>
                </ul>
                <div class="col-md-12 no-padding bor">
                    <div class="multi-item-wrapper">
                        <div>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th style="width:80px;">Mã ĐH</th>
                                    <th style="width:152px;">Khách hàng</th>
                                    <th style="width:102px;">Điện thoại</th>
                                    <th style="width:102px;">Ghi chú</th>
                                    <th style="width:112px;">Người XN</th>
                                    <th style="width:142px;">Ngày XN</th>
                                    <th style="width:142px;">Chuyển hàng</th>
                                    <th style="width:142px;">Thành công</th>
                                    <th style="width:105px;">Trạng thái</th>
                                    <th style="52px;">Chia cho</th>
                                    <th></th>
                                </tr>
                                <!--LIST:items-->
                                <tr>
                                    <td>[[|items.id|]]</td>
                                    <td>
                                        <div style="width: 150px;overflow: auto">[[|items.customer_name|]]</div>
                                    </td>
                                    <td><span class="badge badge-success">[[|items.mobile|]]</span></td>
                                    <td>[[|items.note1|]]</td>
                                    <td>[[|items.user_confirmed_name|]]</td>
                                    <td>[[|items.confirmed|]]</td>
                                    <td>
                                        <div class="badge badge-success">[[|items.delivered|]]</div>
                                        <div class="label label-default">
                                            [[|items.user_delivered_name|]]
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge badge-success">[[|items.update_successed_time|]]</div>
                                        <div class="label label-default">
                                            [[|items.user_successed_name|]]
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-default">[[|items.status_name|]]</span>
                                        <div class="badge small">[[|items.cs_status_name|]]</div>
                                        <div class="small text-left text-warning">[[|items.cs_note|]]</div>
                                    </td>
                                    <td class="text-center">[[|items.user_assigned|]]</td>
                                    <td>
                                        <!--IF:rated_cond([[=items.rated=]])-->
                                        <a target="_blank" href="<?=Url::build_current(['cmd'=>'care_detail','order_id'=>[[=items.id=]]])?>" title="Đánh giá SALE / CSKH">
                                            [[|items.rated|]] <i class="fa fa-star"></i>
                                        </a>
                                        <br>
                                        <span class="small">bởi [[|items.rating_user|]] lúc [[|items.rating_time|]]</span>
                                        <!--ELSE-->
                                        <a target="_blank" href="<?=Url::build_current(['cmd'=>'care_detail','order_id'=>[[=items.id=]]])?>" class="btn btn-default" title="Đánh giá SALE / CSKH">Đánh giá <i class="fa fa-star"></i></a>
                                        <!--IF:cond_([[=items.can_rate=]])-->
                                        <!--ELSE-->
                                        <span class="text-danger">Quá hạn</span>
                                        <!--/IF:cond_-->
                                        <!--/IF:rated_cond-->
                                    </td>
                                </tr>
                                <!--/LIST:items-->
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 total">
                    <div class="col-md-6">
                        [[|paging|]]
                    </div>
                    <div class="col-md-4 pull-right text-right">
                        <div class="text-warning">Chỉ là việc với đơn thành công trong 30 ngày gần nhất</div>
                    </div>
                    <div class="col-md-2 pull-right">
                        <!--IF:cond([[=total=]]>=0)-->
                        <ul class="list-inline">
                            <li>* Tổng Số Đơn Hàng: <strong>[[|total|]]</strong></li>
                            <li class="hidden"> * Tổng tiền: <strong>[[|total_amount|]]</strong></li>
                        </ul>
                        <!--/IF:cond-->
                    </div>
                </div>
                <hr>
                <input  name="checked_all_orders" type="hidden" id="checked_all_orders" value="1">
                <input  name="checked_order" type="hidden" id="checked_order" value="">
                <div class="modal fade modal-default" id="orderRevisionModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Lịch sử đơn hàng <span id="orderId" class="text-info"></span></h4>
                            </div>
                            <div class="modal-body">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#orderRevisionTab" onclick="viewOrderRevision($('#orderId').html(),false);">Hiện tại</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div id="orderRevisionTab" class="tab-pane fade in active">
                                        <p id="orderRevisionModalContent"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">x Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade modal-default" id="editOderModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title">Sửa đơn hàng <span id="editOrderId" class="text-info"></span></h4>
                            </div>
                            <div class="modal-body">
                                <p>
                                    Tính năng này đang được cập nhật...
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">x Đóng</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!--IF:cond11(1==1)-->
    <form name="assignOrderForm" method="post">
        <div class="modal fade" id="assignOrderModal" tabindex="-1" role="dialog" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title text-primary"><i class="glyphicon glyphicon-list-alt" aria-hidden="true"></i> <strong>Chia đơn nhanh</strong></div>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <!--IF:cond([[=account_type=]]==TONG_CONG_TY)-->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <label>Kho số</label><br>
                                        <select name="phone_store_id" id="phone_store_id" class="form-control" onchange="updateTotalNotAssigned($('#ass_bundle_id').val(),$('#ass_source_id').val())"></select>
                                    </div>
                                </div>
                            </div>
                            <!--/IF:cond-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label>Tiêu chí chia</label>
                                        <select  name="assign_option" type="text" id="assign_option" class="form-control">
                                            <option value="">Ưu tiên số mới</option>
                                            <option value="1">Ưu tiên số cũ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label>Nhập số đơn cần chia</label>
                                        <input type="hidden" id="max_assigned_total" value="0">
                                        <input  name="assigned_total" type="text" id="assigned_total" class="form-control" value="[[|total_not_assigned_order|]]" onchange="if(to_numeric(this.value)>to_numeric($('#max_assigned_total').val())){alert('Có tối đa '+$('#max_assigned_total').val()+' số tồn');this.value=$('#max_assigned_total').val();}" placeholder="Nhập số đơn cần chia">
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-warning-custom">* Tổng <span id="total_not_assigned_order" style="color:#336699; font-weight: bold;">[[|total_not_assigned_order|]]</span> đơn chưa được gán sẽ tự động chia đều cho tất cả các tài khoản được đặt tùy chọn gán đơn.</div>
                            <label><i class="fa fa-users"></i> Chọn nhân viên (Giữ shift hoặc ctrl để chọn nhiều tài khoản)</label>
                            <select name="all_ass_account_id[]" id="all_ass_account_id" class="form-control" multiple="" data-toggle="tooltip" title="Áp dụng với nhiều nhân viên" style="height: 200px;width:100%;"></select>
                        </div>
                    </div>
                    <div class="modal-footer text-center">
                        <div class="pull-left text-danger">
                            (Đơn thành công và chuyển hàng trong 30 ngày)
                        </div>
                        <input  name="autoAssignOrder" type="submit" id="autoAssignOrder" class="btn btn-success btn-lg" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing Order" value=" + Gán đơn">
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!--/IF:cond11-->
</div>
<script>
    var blockId = <?php echo Module::block_id(); ?>;
    $(document).ready(function(){
        $('#ngay_from').datepicker({format:'dd/mm/yyyy'});
        $('#ngay_to').datepicker({format:'dd/mm/yyyy'});
        $('.date-input').datetimepicker({
                format:'YYYY-MM-DD HH:mm:ss',
                collapse:false,
                useCurrent: false
            });
    });
    function viewOrderRevision(orderId,old){
        let data;
        if(old == true){
            data = {
                'cmd':'get_order_history',
                'order_id': orderId,
                'act':'old'
            };
        }else{
            data = {
                'cmd':'get_order_history',
                'order_id': orderId
            }
        }
        $.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : data,
            beforeSend: function(){
            },
            success: function(content){
                $('#orderRevisionModal').modal();
                if(content != 0){
                    $('#orderId').html(orderId);
                    if(old == true){
                        $('#orderRevisionModalContentOld').html(content);

                    }else{
                        $('#orderRevisionModalContent').html(content);
                    }
                }
                else{
                    console.log('no result');
                }
            }
        });
    }
    function showEditOrderModal(){
        $('#editOderModal').modal();
    }
    function updateTotalNotAssigned(){
        $.ajax({
            method: "POST",
            url: '<?=Url::build_current()?>&cmd=get_not_assigned_order_by_cs',
            data : {
                'block_id':blockId
            },
            beforeSend: function(){
                //$('#chatBodyWrapper').html('Đang tải ...');
            },
            success: function(content){
                //alert(content);
                content = content.trim();
                $('#total_not_assigned_order').html(content);
                $('#assigned_total').val(content);
                $('#max_assigned_total').val(content);
            },
            error: function(){
                alert('Lỗi...Bạn vui lòng kiểm tra lại kết nối!');
            }
        });
    }
</script>
