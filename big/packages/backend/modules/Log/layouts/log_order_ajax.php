<?php
    $logs = [[=logs=]];
?>
<style>
    .tableFixHead  { 
        overflow: auto; height: 100px; 
    }
    .tableFixHead th { 
        position: sticky; top: 0; z-index: 1; 
    }
    table  { 
        border-collapse: collapse; width: 100%; 
    }
    th, td { 
        padding: 8px 16px; 
    }
    th { 
        background:#eee; 
    }
    .scroll {
        height: 450px;
        overflow: scroll;
    }
</style>
<div class="scroll">
    <table class="table table-bordered table-striped tableFixHead">
        <tr class="text-center">
           <th class="text-center">STT</th>
           <th class="text-center">Thời gian</th>
           <th class="text-center">Tên tài khoản</th>
           <th class="text-center">Nội dung</th>
        </tr>
        <?php if(sizeof($logs) > 0): ?>
            <?php $i = 1; ?>
            <?php foreach($logs as $key => $value): ?>
            <tr valign="middle" class="text-center">
                <td style="width: 5%"><?php echo $i; ?></td>
                <td style="width: 10%"><?php echo $value['created']; ?></td>
                <td style="width: 10%"><?php echo $value['user_created_name']; ?></td>
                <?php if($value['data']): ?>
                    <td class="text-left" style="width: 35%"><?php echo $value['data']; ?></td>
                <?php elseif( $value['before_order_status'] && $value['order_status'] ): ?>
                    <td class="text-left" style="width: 35%">Chuyển trạng thái từ: <b><?php echo $value['before_order_status']; ?></b> thành <b><?php echo $value['order_status']; ?></b></td>
                <?php endif; ?>
            </tr>

            <?php $i++; ?>

            <?php endforeach; ?>
        <?php else: ?>
            <tr valign="middle" class="text-center">
                <td colspan="4" class="text-danger">Không có dữ liệu</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
<br>
<?php if(sizeof($logs) > 0): ?>
<p>Lưu ý: <span class="text-danger">Lịch sử đơn hàng lưu tối đa 100 bản ghi!</span></p>
<?php endif; ?>