<script src="packages/core/includes/js/multi_items.js"></script>
<span style="display:none">
	<span id="mi_order_sample">
        <div id="input_group_#xxxx#" class="multi-item-group">
            <span class="multi-edit-input" style="width:80px;"><input  name="mi_order[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input readonly" style="width:80px;text-align:right;" value="(auto)" tabindex="-1" readonly><input  name="mi_order[#xxxx#][contact_type]" type="hidden" id="contact_type_#xxxx#" tabindex="-1" readonly></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][contact]" style="width:150px;" class="multi-edit-text-input readonly" type="text" id="contact_#xxxx#" readonly=""></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][email]" style="width:200px;" class="multi-edit-text-input" type="text" id="email_#xxxx#" readonly=""></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][phone]" style="width:150px;" class="multi-edit-text-input" type="text" id="phone_#xxxx#" readonly=""></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][product_codes]" style="width:100px;" class="multi-edit-text-input readonly" type="text" id="product_codes_#xxxx#" readonly=""></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][account_id]" style="width:120px;" class="multi-edit-text-input text-right readonly" type="text" id="account_id_#xxxx#" readonly=""></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][message]" style="width:200px;background:#fdffd4;" class="multi-edit-text-input" type="text" id="message_#xxxx#"></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][time]" style="width:130px;" class="multi-edit-text-input text-right readonly" type="text" id="time_#xxxx#" disabled></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][checked]" style="width:100px;" class="multi-edit-text-input checkbox" type="checkbox" id="checked_#xxxx#" /></span>
        </div>
    <br clear="all">
	</span>
</span>
<?php
$title = 'Danh sách chờ';
?>
<!-- Main content -->
<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="<?=Url::build_current()?>">Đơn hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?=$title?></li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box box-info">
        <form name="WaitingListForm" method="post" id="WaitingListForm">
            <div class="box-header with-border">
                <h4 class="title">Danh sách data số [[|group_name|]] - Giật về từ Landing page / Website</h4>
                <div class="box-tools pull-right form-inline">
                    <!--IF:cond1(User::is_admin() or Session::get('admin_group'))-->
                    <div class="form-group">
                        <input  name="make_order" type="submit" class="btn btn-danger btn-sm" value="Lên đơn" tabindex="-1">
                    </div>
                    <div class="form-group">
                        <select name="item_per_page" id="item_per_page" class="form-control"  onchange="WaitingListForm.submit();"></select>
                    </div>
                    <!--/IF:cond1-->
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-3 no-padding-l">
                            <div class="form-group">
                                <input name="keyword" type="text" id="keyword" class="form-control" placeholder="Họ tên khách hàng hoặc số DT">
                            </div>
                        </div>
                    </div>
                    <div id="searchBtnWrapper">

                    </div>
                </div>
                <div style="width:100%;height:20px;float:left;"></div>
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab">Chỉnh sửa nhanh</a></li>
                    <li><a href="<?php echo Url::build_current();?>">Danh sách đơn hàng</a></li>
                </ul>
                <div style="overflow: hidden;">
                    <div class="col-md-12 no-padding bor">
                        <div class="multi-item-wrapper">
                            <div id="mi_order_all_elems" style="width: 1400px;overflow: auto">
                                <div>
                                    <span class="multi-edit-input header" style="width:80px;">ID</span>
                                    <span class="multi-edit-input header" style="width:152px;">Tên khách hàng</span>
                                    <span class="multi-edit-input header" style="width:202px;">Email</span>
                                    <span class="multi-edit-input header" style="width:152px;">Điện thoại</span>
                                    <span class="multi-edit-input header" style="width:102px;">Mã sản phẩm</span>
                                    <span class="multi-edit-input header" style="width:122px;">Tài khoản</span>
                                    <span class="multi-edit-input header" style="width:202px;">Nội dung từ khách hàng</span>
                                    <span class="multi-edit-input header" style="width:132px;">Ngày tạo</span>
                                    <span class="multi-edit-input header" style="width:102px;text-align: center;"><input type="checkbox" onclick="$('.checkbox').prop('checked',this.checked)" title="Chọn tất cả"> </span>
                                    <br clear="all">
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <div class="col-md-12 total">
                        <div class="col-md-6">
                            [[|paging|]]
                        </div>
                        <div class="col-md-6 no-padding">
                            <div>Tổng số: <strong>[[|total|]]</strong></div>
                        </div>
                    </div>
                </div>
                <input  name="checked_all_orders" type="hidden" id="checked_all_orders" value="1">
                <input  name="checked_order" type="hidden" id="checked_order" value="">
            </div>
        </form>
    </div>
</div>
<script>
  mi_init_rows('mi_order',<?php if(isset($_REQUEST['mi_order'])){echo MiString::array2js($_REQUEST['mi_order']);}else{echo '[]';}?>);
</script>
<script>
    jQuery(document).ready(function(){
      $(document).keypress(function(e) {
          if(e.which == 13) {
            window.location='index062019.php?page=admin_orders&cmd=waiting_list&keyword='+jQuery('#keyword').val();
            return false;
          }
      });
    });
</script>