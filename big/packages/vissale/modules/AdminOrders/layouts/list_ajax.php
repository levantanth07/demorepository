<div id="item-list" class="row">
    <div class="col-md-12 bor">
        <table id="orderListTable" class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 30px;"></th>
                    <th style="width: 30px;"><input  type="checkbox" value="1" id="ListAdminOrdersForm_all_checkbox" onclick="selectAll(this);" style="font-size:36px;font-weight:bold;"></th>
                    <!--LIST:columns-->
                    <!--IF:cond([[=columns.id=]]=='products' or [[=columns.id=]]=='source' or [[=columns.id=]]=='page')-->
                    <th nowrap="" style="width:180px;color:#000;font-weight: normal;font-size:14px;color:#111;cursor: not-allowed;">[[|columns.name|]]</th>
                    <!--ELSE-->
                    <!--IF:cond_([[=columns.id=]]=='status_name')-->
                    <th nowrap="" style="color:#000;font-weight: normal;font-size:14px;color:#111;cursor: not-allowed;">[[|columns.name|]]</th>
                    <!--ELSE-->
                    <!--IF:cond__([[=columns.id=]]=='customer_name')-->
                    <th nowrap="" style="width:120px;">
                        [[|columns.name|]]
                    </th>
                    <!--ELSE-->
                    <!--IF:cond___([[=columns.id=]]=='note1')-->
                    <th nowrap="" style="width:250px;">
                        [[|columns.name|]]
                    </th>
                    <!--ELSE-->
                    <!--IF:cond____([[=columns.id=]]=='note2' or [[=columns.id=]]=='shipping_note')-->
                    <th nowrap="" style="width:250px;">
                        [[|columns.name|]]
                    </th>
                    <!--ELSE-->
                    <!--IF:cond_____([[=columns.id=]]=='id')-->
                    <th nowrap="" style="width:85px;">
                        [[|columns.name|]]
                    </th>
                    <!--ELSE-->
                    <th nowrap="" style="<?php echo ([[=columns.id=]]=='address' or [[=columns.id=]]=='products')?'width:180px;':'';?>color:#000;font-weight: normal;font-size:14px;color:#12295B;cursor: pointer;">[[|columns.name|]]</th>
                    <!--/IF:cond_____-->
                    <!--/IF:cond____-->
                    <!--/IF:cond___-->
                    <!--/IF:cond__-->
                    <!--/IF:cond_-->
                    <!--/IF:cond-->
                    <!--/LIST:columns-->
                </tr>
            </thead>
            <tbody>
            <?php
            $items = [[=items=]];
            foreach ($items as $item):
                if (!empty($item['id'])):
            ?>
            <?php
                $url = 'index062019.php?page=admin_orders&cmd=edit&id='.$item['id'];
                $bg_color = $item['label'] ? $item['label'] : '#FFF';
            ?>
            <tr class="rows" bgcolor="<?php echo $bg_color;?>" onclick="updateSelectedRow(this,<?= $item['id']?>,'<?php echo $bg_color;?>');">
                <td class="text-center">
                    <?php
                    $quyen_sua_don_nhanh = [[=quyen_sua_don_nhanh=]];
                    $isOwner = [[=isOwner=]];
                    if ($quyen_sua_don_nhanh || $isOwner):
                    ?>
                    <a title="Sửa nhanh" style="margin-bottom: 5px;" href="#" onclick="showQuickEditModal(<?= $item['id']?>);return false;" class="btn btn-warning btn-sm" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#quickEditModal"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <?php endif;?>

                    <?php if ($item['fb_conversation_id']): ?>
                    <a title="Trả lời khách hàng" href="#" onclick="getVichatHistory('<?= $item['fb_conversation_id']?>','<?= $item['fb_page_id']?>');" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#chatModal"><i class="glyphicon glyphicon-comment" aria-hidden="true"></i></a>
                    <?php endif;?>
                </td>
                <td>
                    <?php if ($item['editting']):?>
                    <span class="glyphicon glyphicon-lock" style="color:#F00;" title="Đang thao tác bởi <?= $item['editting']?>"></span>
                    <?php endif;?>
                    <input  name="selected_ids[]" type="checkbox" id="checkbox_<?= $item['id']?>" value="<?= $item['id']?>" onclick="selectOne(this);" class="order-checkbox" style="font-size:36px;font-weight:bold;">
                </td>
                <?php
                $columns = [[=columns=]];
                $device = [[=device=]];
                foreach ($columns as $column):
                    $itemValue = $item[$column['id']] ?? 0;
                    if ($device == 'DESKTOP'):
                ?>
                <td id="<?= $column['id']?>_<?= $item['id']?>" ondblclick="window.open('index062019.php?page=admin_orders&cmd=<?=Url::get('cmd')=='list_pos'?'pos':'edit'?>&id=<?= $item['id']?>','EDIT_ORDER');" class="column-<?= $column['id']?>"><?php echo ($column['id']=='total_price')?'<span style="color:#F00;font-weight:bold;">'.System::display_number($itemValue).'</span>':$itemValue;?>
                </td>
                <?php  else:?>
                <td onclick="window.open('index062019.php?page=admin_orders&cmd=<?=Url::get('cmd')=='list_pos'?'pos':'edit'?>&id=<?= $item['id']?>','EDIT_ORDER');" class="column-<?= $column['id']?>"><?php echo ($column['id']=='total_price')?'<span style="color:#F00;font-weight:bold;">'.System::display_number($itemValue).'</span>':$itemValue;?>
                </td>
                <?php endif;?>
                <?php endforeach;?>
            </tr>
            <?php endif; endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="col-md-12 total">
        <div class="col-md-6">
            [[|paging|]]
        </div>
        <div class="col-md-6 no-padding">
            <!--IF:cond([[=total=]]>=0)-->
            <ul class="list-inline">
                <li>* Tổng Số Đơn Hàng: <strong id="totalOrderByList">[[|total|]]</strong></li>
                <?php if(Session::get('admin_group') or !(get_group_options('hide_total_amount')==true and check_user_privilege('MARKETING'))){?>
                <li>* Tổng tiền: <strong>[[|total_amount|]]</strong></li>
                <?php }?>
            </ul>
            <!--/IF:cond-->
        </div>
    </div>
</div>
<input  name="page_no" type="hidden" id="page_no" value="[[|page_no|]]"/>
<script type="text/javascript">
    jQuery(document).ready(function(){
        $('[data-toggle="popover"]').popover({
            container: 'body',
            placement: 'auto'
        });
        $('#orderListTable').DataTable( {
            paging: false,
            scrollY: 380,
            "scrollX": true,
            "searching": false,
            "bInfo" : false,
            "order": []
        });
    });

    // setting cho phép xem lịch sử đơn hàng
    window.HAS_SHOW_HISTORY_ORDER = parseInt('<?=intval([[=has_show_history_order=]] || [[=isOwner=]])?>');
    document.querySelector('.HAS_SHOW_HISTORY_ORDER').style.display = window.HAS_SHOW_HISTORY_ORDER ? '' : 'none' ;

    ORDERS_STATISTICS = {
        total: "[[|total|]]".toInt(), 
        total_amount: "[[|total_amount|]]".toInt(),
        shipping_price: "[[|shipping_price|]]".toInt(),
    }
</script>
