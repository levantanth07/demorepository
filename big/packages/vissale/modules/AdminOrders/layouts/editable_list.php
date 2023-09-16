<script src="packages/core/includes/js/multi_items.js?v=23072020"></script>
<style type="text/css">
    .field_error{
        border: 1px solid red !important;
    }
    .multi-item-group input:not(:disabled):not(.readonly), .multi-item-group select:not(:disabled):not(.readonly){background:#fdffd4;}
    /* The alert message box */
    .alert {
        padding: 10px;
        background-color: #ffa29e; /* Red */
        color: white;
        margin-bottom: 15px;
    }

    /* The close button */
    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 22px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    /* When moving the mouse over the close button */
    .closebtn:hover {
        color: black;
    }
</style>
<?php
$arrError = [[=arrError=]];
?>
<span style="display:none">
	<span id="mi_order_sample">
        <div id="input_group_#xxxx#" class="multi-item-group">
            <span class="multi-edit-input" style="width:80px;"><input  name="mi_order[#xxxx#][id]" type="text" id="id_#xxxx#" class="multi-edit-text-input readonly" style="width:80px;text-align:right;" value="(auto)" tabindex="-1" readonly onclick="viewOrderRevision(this.value,false);"></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][user_assigned]" style="width:100px;" class="multi-edit-text-input readonly" type="text" id="user_assigned_#xxxx#" disabled></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][customer_name]" style="width:150px;" class="multi-edit-text-input" type="text" id="customer_name_#xxxx#"></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][mobile]" style="width:100px;color:#0c984d" class="form-control multi-edit-text-input" type="text" id="mobile_#xxxx#" disabled></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][total_price]" style="width:80px;" class="multi-edit-text-input text-right" type="text" id="total_price_#xxxx#" readonly></span>
            <span class="multi-edit-input"><input  name="mi_order[#xxxx#][note1]" style="width:100px;" class="form-control multi-edit-text-input" type="text" id="note1_#xxxx#"></span>

            <span class="multi-edit-input">
                <select onfocus="focusID(this)" onchange="changeID(this)" name="mi_order[#xxxx#][user_created]" style="width:110px;" class="form-control multi-edit-text-input" id="user_created_#xxxx#">[[|user_created_options|]]</select>
            </span>

            <span class="multi-edit-input">
                <input onfocus="focusTime(this)" onfocusout="focusOutTime(this)" onchange="changeTime(this)" name="mi_order[#xxxx#][created]" style="width:140px;" class="multi-edit-text-input date-input" type="text" id="created_#xxxx#" placeholder="00/00/0000 00:00:00"></span>

            <span class="multi-edit-input">
                <select onfocus="focusID(this)" onchange="changeID(this)"  disabled="true" validate="1" name="mi_order[#xxxx#][user_confirmed]" style="width:110px;" class="form-control multi-edit-text-input" id="user_confirmed_#xxxx#">[[|user_confirmed_options|]]</select>
            </span>

            <span class="multi-edit-input">
                <input onfocus="focusTime(this)" onfocusout="focusOutTime(this)" onchange="changeTime(this)"  disabled="true" validate="1" name="mi_order[#xxxx#][confirmed]" style="width:140px;" class="multi-edit-text-input date-input" type="text" id="confirmed_#xxxx#" placeholder="00/00/0000 00:00:00"></span>

            <span class="multi-edit-input">
                <select onfocus="focusID(this)" onchange="changeID(this)"  disabled="true" validate="1"  name="mi_order[#xxxx#][user_delivered]" style="width:110px;" class="form-control multi-edit-text-input" id="user_delivered_#xxxx#" placeholder="00/00/0000 00:00:00">[[|user_confirmed_options|]]</select></span>

            <span class="multi-edit-input">
                <input onfocus="focusTime(this)" onfocusout="focusOutTime(this)" onchange="changeTime(this)"  disabled="true" validate="1" name="mi_order[#xxxx#][delivered]" style="width:140px;" class="multi-edit-text-input date-input" type="text" id="delivered_#xxxx#" placeholder="00/00/0000 00:00:00"></span>

            <span class="multi-edit-input"><select name="mi_order[#xxxx#][status_id]" style="width:103px;" class="form-control multi-edit-text-input" id="status_id_#xxxx#" disabled>[[|status_options|]]</select></span>
            <span class="multi-edit-input">[<a href="#" onclick="showEditOrderModal();return false;"> <i class="fa fa-cog"></i> </a>]</span>
        </div>
    <br clear="all">
    </span>
</span>
<div class="container full">
    <br>
    <form name="EditableListForm" method="post" id="EditableListForm" class="form-inline">
        <div class="box box-info">
            <div class="box-header with-border">
                <h4 class="box-title"> <i class="fa fa-cogs"></i> Chỉnh sửa nhanh đơn hàng</h4>
                <div class="box-tools pull-right">
                    <!--IF:owner_cond(Session::get('admin_group') and is_group_owner())-->
                    <input name="update" type="submit" class="btn btn-primary" value="Cập nhật">
                    <!--/IF:owner_cond-->
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-danger pull-right text-right mb-1">
                            (Chỉ tài khoản <strong>sở hữu</strong> mới có quyền sửa đổi)<br>
                            <i>Không chọn điều kiện lọc sẽ hiển thị DS đơn 6 tháng gần nhất!</i>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input name="term_sdt" type="text" id="term_sdt" class="form-control" value="[[|term_sdt|]]" style="width: 160px;" placeholder="SDT (Tối thiểu [[|min_search_phone_number|]] số)">
                                </div>
                                <div class="form-group">
                                    <input name="term_order_id" type="text" id="term_order_id" class="form-control" value="[[|term_order_id|]]" style="width: 120px;" placeholder="Mã đơn hàng">
                                </div>
                                <div class="form-group">
                                    <input name="term_ship_id" type="text" id="term_ship_id" class="form-control" value="[[|term_ship_id|]]" style="width: 100px;" placeholder="Mã vận đơn">
                                </div>

                                <div class="form-group">
                                    <input name="customer_name" type="text" id="customer_name" placeholder="Họ tên khách hàng" class="form-control"  style="width: 120px;">
                                </div>
                                <div class="form-group">
                                    <input name="ngay_tao_from" type="text" id="ngay_tao_from" class="form-control" style="width: 120px;" placeholder="Từ ngày" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <input name="ngay_tao_to" type="text" id="ngay_tao_to" class="form-control" style="width: 120px;" placeholder="Đến ngày" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <select name="status_id" id="status_id" class="form-control"></select>
                                </div>
                                <div class="form-group">
                                    <select name="item_per_page" id="item_per_page" class="form-control" onchange="EditableListForm.submit();"></select>
                                    <input type="submit" value="-o- Tìm kiếm" class="btn btn-default">
                                    <input name="act" type="hidden" id="act">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <!--IF:cond([[=arrError=]])-->
                <div class="alert">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    <?php
                    foreach ($arrError as $message => $value) {
                        echo $message .  '<br>';
                    }
                    ?>
                </div>
                <!--/IF:cond-->
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab">Chỉnh sửa nhanh</a></li>
                    <li><a href="/get_customers.php" target="_blank">Tra cứu data số đã tạo</a></li>
                    <li><a data-toggle="tab" class="text-warning">(* Nhấn vào <strong>Mã ĐH</strong> để xem nhanh lịch sử đơn hàng)</a></li>
                </ul>
                <div class="col-md-12 no-padding bor">
                    <div class="multi-item-wrapper" style="overflow-x: auto">
                        <div id="mi_order_all_elems" style="width: max-content;float:left;overflow:auto;border: 1px solid #CCC;margin-top: -1px;">
                            <div>
                                <span class="multi-edit-input header" style="width:80px;">Mã ĐH</span>
                                <span class="multi-edit-input header" style="width:102px;">NV</span>
                                <span class="multi-edit-input header" style="width:152px;">Khách hàng</span>
                                <span class="multi-edit-input header" style="width:102px;">Điện thoại</span>
                                <span class="multi-edit-input header" style="width:82px;">Tổng tiền</span>
                                <span class="multi-edit-input header" style="width:102px;">Ghi chú</span>
                                <span class="multi-edit-input header" style="width:112px;">Người Tạo</span>
                                <span class="multi-edit-input header" style="width:142px;">Ngày Tạo</span>
                                <span class="multi-edit-input header" style="width:112px;">Người XN</span>
                                <span class="multi-edit-input header" style="width:142px;">Ngày XN</span>
                                <span class="multi-edit-input header" style="width:112px;">Người chuyển</span>
                                <span class="multi-edit-input header" style="width:142px;">Ngày chuyển</span>
                                <span class="multi-edit-input header" style="width:105px;">Trạng thái</span>
                                <span class="multi-edit-input header" style="52px;">.....</span>
                                <br clear="all">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 total">
                    <div class="col-md-6">
                        [[|paging|]]
                    </div>
                    <div class="col-md-6 pull-right">
                        <!--IF:cond([[=total=]]>=0)-->
                        <ul class="list-inline">
                            <li>* Tổng Số Đơn Hàng: <strong>[[|total|]]</strong></li>
                            <li> * Tổng tiền: <strong>[[|total_amount|]]</strong></li>
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
</div>
<script>
    const ROW_CALLBACKS = function(itemEl, value, index)
    {
        let confirmedEl = itemEl.querySelector('input#confirmed_' + index);

    }

    mi_init_rows('mi_order',<?php if(isset($_REQUEST['mi_order'])){echo MiString::array2js($_REQUEST['mi_order']);}else{echo '[]';}?>, {ROW_CALLBACKS});
</script>
<script>
    let statuses = [[|statuses|]];
    let currentTimeMs = new Date().getTime();
    let XAC_NHAN_CHOT_DON = 2, CHUYEN_HANG = 3;

    let idFieldsType = ['user_created', 'user_confirmed'];
    let timeFieldsType = ['created', 'confirmed'];
    let rows = [];

    addEventListener('load', function(){
        rows = [].slice.call(document.querySelectorAll('div[id^=input_group_]'))
        .filter(function(e){
            return e.id && e.id.match(/input_group_\d+/);
        });

        rows.map(function(e){
            let statusID = e.querySelector('[id^=status_id_]').value;
            e.querySelectorAll('[validate]').forEach(function(element){
                return element.disabled = false;
                // bo qua validate
                if(statuses[statusID]['level'] >= XAC_NHAN_CHOT_DON && ['confirmed', 'user_confirmed'].includes(getFieldName(element))){
                    element.disabled = false;
                }

                if(statuses[statusID]['level'] >= CHUYEN_HANG && ['delivered', 'user_delivered'].includes(getFieldName(element))){
                    element.disabled = false;
                }
            })
        })
    })

    /**
     * Removes a value.
     *
     * @param      {<type>}  ID      { parameter_description }
     */
    Array.prototype.removeValue = function(ID) {
        let index =this.indexOf(ID);
        if (index !== -1) {
            this.splice(index, 1);
        }
    };

    /**
     * Gets the field name.
     *
     * @param      {<type>}  el      { parameter_description }
     * @return     {<type>}  The field name.
     */
    function getFieldName(el){
        return el.name.match(/\[(\w+)]$/)[1];
    }

    function getOrderID(el){
        return document.querySelector('input[id=id_' + el.name.match(/\[(\d+)]/)[1] + ']').value;
    }


    /**
     * { function_description }
     *
     * @param      {<type>}  el      { parameter_description }
     */
    function defaultFocus(el){
        if(!el.hasOwnProperty('initValue')){
            el.initValue = el.value;
        }
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  el      { parameter_description }
     */
    function focusID(el){
        defaultFocus(el);
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  el      { parameter_description }
     */
    function focusTime(el){
        defaultFocus(el);
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  el      { parameter_description }
     */
    function focusOutTime(el){
        changeTime(el);
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  el      { parameter_description }
     */
    function changeID(el){
        validateID(el);
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  el      { parameter_description }
     */
    function changeTime(el){
        validateTime(el);
    }

    /**
     * { function_description }
     *
     * @param      {<type>}   el         { parameter_description }
     * @param      {<type>}   filedName  The filed name
     * @return     {boolean}  { description_of_the_return_value }
     */
    function validateID(el){
        return;
        let filedName = getFieldName(el);

        if(!el.hasOwnProperty('initValue') || el.initValue <= 0){
            return true;
        }

        if(el.value <= 0){
            rollbackInitValueValue(el);
        }

        return el.classList.remove('field_error'), true;
    }

    /**
     * { function_description }
     *
     * @param      {<type>}   el      { parameter_description }
     * @return     {boolean}  { description_of_the_return_value }
     */
    function validateTime(el)
    {
        let filedName = getFieldName(el);
        let fieldNames = {
            confirmed: 'Thời gian xác nhận',
            created: 'Thời gian tạo',
            assigned: 'Thời gian chia',
            delivered: 'Thời gian chuyển',
        }

        el.time = Date.parse(el.value.replace(/(\d{2})\/(\d{2})\/(\d{4})(.+)/, '$3-$2-$1$4'));

        if(!el.hasOwnProperty('initValue') || isNaN(el.time)){
            return true;
        }

        if(el.time > currentTimeMs){
            alert(`Lỗi: ${fieldNames[filedName]} của mã đơn hàng ${getOrderID(el)} không hợp lệ !`);
            rollbackInitValueValue(el);
        }

        return el.classList.remove('field_error'), true;
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  el      { parameter_description }
     * @return     {<type>}  { description_of_the_return_value }
     */
    function rollbackInitValueValue(el){
        return el.value = el.initValue, true;
    }

    /**
     * Gets the end of day by milliseconds.
     *
     * @param      {<type>}  ms      The milliseconds
     * @return     {<type>}  The end of day by milliseconds.
     */
    function getEndOfDayByMs(ms){
        let date  = new Date(ms);
        date.setHours(0, 0, 0, 0);

        return date.getTime()/1000;
    }

    jQuery(document).ready(function(){
        $('#ngay_tao_from').datepicker({format:'dd/mm/yyyy'});
        $('#ngay_tao_to').datepicker({format:'dd/mm/yyyy'});
        $('.date-input').datetimepicker({
                format:'DD/MM/YYYY HH:mm:ss',
                collapse:false,
                useCurrent: false
            });

        $('input[name="update"]').click(function(clickEvent){
            try{
                let error = {
                    user_created: [],
                    confirmed: [],
                    created: [],
                    delivered: [],
                    confirmed_created: [],
                    confirmed_delivered: [],
                };
                let valid = true;
                let currentDay = getEndOfDayByMs(new Date().getTime());
                rows.map(function(el){
                    try{
                        // order ID
                        let orderID = el.querySelector('input[id^=id_]').value;
                        let statusID = el.querySelector('select[id^=status_id_]').value;

                        if(statuses[statusID]['level'] < XAC_NHAN_CHOT_DON){
                            return;
                        }

                        let created = el.querySelector('input[id^=created]');
                        let createdTime = created.value ? Date.parse(created.value.replace(/(\d{2})\/(\d{2})\/(\d{4})(.+)/, '$3-$2-$1$4')) : 0;
                        let createdDay = getEndOfDayByMs(createdTime);

                        let confirmed = el.querySelector('input[id^=confirmed]');
                        let confirmedTime = confirmed.value ? Date.parse(confirmed.value.replace(/(\d{2})\/(\d{2})\/(\d{4})(.+)/, '$3-$2-$1$4')) : 0;
                        let confirmedDay = getEndOfDayByMs(confirmedTime);

                        let delivered = el.querySelector('input[id^=delivered_]');
                        let deliveredTime = delivered.value ? Date.parse(delivered.value.replace(/(\d{2})\/(\d{2})\/(\d{4})(.+)/, '$3-$2-$1$4')) : 0;
                        let deliveredDay = getEndOfDayByMs(deliveredTime);

                        // if(confirmedDay > currentDay){
                        //     error.confirmed.push(orderID);
                        // }

                        // if(createdDay > currentDay){
                        //     error.created.push(orderID);
                        // }

                        // if(deliveredDay > currentDay){
                        //     error.created.push(orderID);
                        // }
                        let errorsCount = {confirmed_created: 0, confirmed_delivered: 0};

                        if(
                           (created.hasOwnProperty('initValue') && created.initValue != created.value)
                           || (confirmed.hasOwnProperty('initValue') && confirmed.initValue != confirmed.value)
                           || (delivered.hasOwnProperty('initValue') && delivered.initValue != delivered.value)
                           ){
                            console.log([createdDay, confirmedDay])
                            if(statuses[statusID]['level'] >= XAC_NHAN_CHOT_DON){
                                if(createdDay > confirmedDay){
                                    errorsCount.confirmed_created++;
                                    error.confirmed_created.push(orderID);
                                    confirmed.classList.add('field_error');
                                    created.classList.add('field_error');
                                }
                            }

                            if(statuses[statusID]['level'] >= CHUYEN_HANG){
                                if(deliveredDay < confirmedDay){
                                    errorsCount.confirmed_delivered++;
                                    error.confirmed_delivered.push(orderID);
                                    confirmed.classList.add('field_error');
                                    delivered.classList.add('field_error');
                                }
                            }
                        }

                        if(!errorsCount.confirmed_created)
                            created.classList.remove('field_error');
                        if(!errorsCount.confirmed_created && !errorsCount.confirmed_delivered)
                            confirmed.classList.remove('field_error');
                        if(!errorsCount.confirmed_delivered)
                            delivered.classList.remove('field_error');
                    }catch(e){
                        console.log(e);
                        clickEvent.preventDefault();
                    }
                });

                let message = "";
                if(error.user_created.length)
                    message += "\nNgười tạo đơn (" + error.user_created.join(',') + ") không được để trống. ";

                if(error.created.length)
                    message += "\nĐịnh dạng thời gian tạo đơn hàng (" + error.created.join(',') + ") không hợp lệ. ";

                if(error.confirmed.length)
                    message += "\nĐịnh dạng thời gian xác nhận đơn hàng (" + error.confirmed.join(',') + ") không hợp lệ. ";

                if(error.confirmed_created.length)
                    message += "\nNgày tạo và xác nhận đơn (" + error.confirmed_created.join(',') + ") không hợp lệ";

                if(error.confirmed_delivered.length)
                    message += "\nNgày xác nhận và ngày chuyển đơn (" + error.confirmed_delivered.join(',') + ") không hợp lệ";

                if(message){
                    clickEvent.preventDefault();
                    alert(message);
                }
            }catch(err){
                clickEvent.preventDefault();
            }
        })
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
</script>
