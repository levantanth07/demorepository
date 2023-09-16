<?php
$admin_tuha = (User::can_admin(MODULE_GROUPSSYSTEM,ANY_CATEGORY))?true:false;
$admin_group = (Session::get('admin_group'))?true:false;
$admin_mkt = check_user_privilege('ADMIN_marketing')?true:false;
?>
<div class="container full">
    <br>
    <form name="EditUser" method="post" id="EraseDataForm" enctype="multipart/form-data">
        <div class="box box-default">
            <div class="box-header bg-gray-light">
                <h3 class="box-title"> <i class="fa fa-laptop" aria-hidden="true"></i> GIẢI PHÓNG DỮ LIỆU</h3>
                <div class="box-tools pull-right">
                    <!--IF:cond($admin_tuha)-->
                    <a class="btn btn-default" href="<?=Url::build('admin-shop');?>">Danh sách shop</a>
                    <!--/IF:cond-->
                </div>
            </div>
            <div class="box-body bg-gray-light">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel">
                            <div class="panel-heading">
                                <h3 class="title">Thống số shop [[|name|]]</h3>
                            </div>
                            <div class="panel-body">
                                <table class="table">
                                    <tr>
                                        <th>Hạng mục</th>
                                        <th>Số lượng</th>
                                    </tr>
                                    <tr>
                                        <td>Tổng số đơn hàng thành công</td>
                                        <td>[[|total_order|]]</td>
                                    </tr>
                                    <tr>
                                        <td>Tổng số sản phẩm</td>
                                        <td>[[|total_product|]]</td>
                                    </tr>
                                    <tr>
                                        <td>Tổng số users</td>
                                        <td>[[|total_user|]]</td>
                                    </tr>
                                    <tr class="bg-aqua">
                                        <td>Ngày tạo shop</td>
                                        <td>[[|created|]]</td>
                                    </tr>
                                    <tr class="bg-orange">
                                        <td>Khởi tạo lại thời gian tạo shop từ</td>
                                        <td><select name="month_before" id="month_before"></select></td>
                                    </tr>
                                </table>
                                <div class="alert alert-warning-custom">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            Đơn hàng <strong>Thành công</strong> sẽ tự động chuyển về trạng thái <strong>Khai thác lại</strong>, doanh số sẽ reset về 0
                                        </li>
                                        <li class="list-group-item">
                                            Tất cả các mốc thời gian của các hoạt động trước mốc thời gian đã chọn ở trên sẽ được khởi tạo lại từ khoảng thời gian này.
                                        </li>
                                        <li class="list-group-item">
                                            Thời gian khởi tạo shop sẽ đổi lại tương ứng thời gian đã chọn trên.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="footer text-center">
                                1 + 1 = <input name="number_1" type="number" style="width: 50px;text-align: center;border:0px;" placeholder="?" onchange="if(this.value==2){$('#btnSubmit').attr('disabled',false)}else{$('#btnSubmit').attr('disabled',true)}">
                                <a id="btnSubmit" class="btn btn-primary btn-lg text-bold" onclick="EraseDataForm.submit();" disabled=""><i class="fa fa-floppy-o"></i>
                                    Enter
                                </a>
                                <hr>
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i> Tất cả thông tin sau khi đã xử lý sẽ không khôi phục lại được hiện trạng ban đầu. SẾP VUI LÒNG NGHĨ KĨ TRƯỚC KHI ẤN.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <img src="assets/vissale/images/danger_image.jpg" alt="" width="100%">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function(e) {

        $('#date_established').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        $('#expired_date').datetimepicker({
            format: 'YYYY-MM-DD'
        });
        <!--IF:cond(Url::get('active'))-->
        $('#active').attr('checked',true);
        <!--/IF:cond-->
        <!--IF:cond(Url::get('integrate_shipping'))-->
        $('#integrate_shipping').attr('checked',true);
        <!--/IF:cond-->
        $('#master_group_id').val(<?php echo Url::iget('master_group_id')?>);
    });
</script>