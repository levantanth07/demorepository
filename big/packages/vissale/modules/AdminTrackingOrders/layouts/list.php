<div class="container full">
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> Tra cứu đơn hàng</h3>
        </div>
        <div class="box-body">
            <form class="d-flex pb-2r" method="POST" action="[[|search_url|]]">
                <input name="phone_number" id="phone_number" minlength="[[|configs_phone_min|]]" maxlength="[[|configs_phone_max|]]" required class="form-control d-inline-block w-20" placeholder="Nhập số điện thoại" type="text" value="[[|configs_phone_val|]]">
                <button class="btn btn-primary" name="search" type="submit">
                    <i class="fa fa-search search-ico"></i>
                    Tìm kiếm
                </button>
                <a class="btn ml-1r btn-default" href="[[|reset_filter_url|]]">Reset</a>
                <span class="ml-1r"> - Tìm thấy <b>[[|total_orders|]]</b> đơn hàng </span>
            </form>
            <table class="table table-striped table-hover table-responsive table-bordered table-orders">
                <thead>
                    <tr class="bg-blue">
                        <th class="text-center">STT</th>
                        <th class="text-center">Thuộc HKD</th>
                        <th class="text-center">Tạo bởi</th>
                        <th class="text-center">Gán cho</th>
                        <th class="text-center">Xác nhận chốt đơn bởi</th>
                    </tr>
                </thead>
                <tbody>
                    [[|orders_block|]]
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="11">
                            [[|paging|]]
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>