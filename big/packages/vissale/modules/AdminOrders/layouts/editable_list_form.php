<div id="item-list">
<div class="col-md-12 no-padding bor">
    <div class="multi-item-wrapper">
        <div id="mi_order_all_elems">
            <div>
                <span class="multi-edit-input header" style="width:80px;">Mã ĐH</span>
                <span class="multi-edit-input header" style="width:102px;">NV</span>
                <span class="multi-edit-input header" style="width:202px;">Khách hàng</span>
                <span class="multi-edit-input header" style="width:152px;">Điện thoại</span>
                <span class="multi-edit-input header" style="width:102px;">Tổng tiền</span>
                <span class="multi-edit-input header" style="width:402px;">Ghi chú</span>
                <span class="multi-edit-input header" style="width:152px;">Trạng thái</span>
                <br clear="all">
            </div>
        </div>
    </div>
</div>
<div class="col-md-12 total">
    <div class="col-md-6">
        [[|paging|]]
    </div>
    <div class="col-md-6 no-padding">
        <!--IF:cond([[=total=]]>=0)-->
        <ul class="list-inline">
            <li>* Tổng Số Đơn Hàng: <strong>[[|total|]]</strong></li>
            <li> * Tổng tiền: <strong>[[|total_amount|]]</strong></li>
        </ul>
        <!--/IF:cond-->
    </div>
</div>
<script>
    mi_init_rows('mi_order',<?php if(isset($_REQUEST['mi_order'])){echo MiString::array2js($_REQUEST['mi_order']);}else{echo '[]';}?>);
</script>
</div>
