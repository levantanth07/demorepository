<?php
    $items = [[=items=]];
    $total = [[=total=]];
    $item_per_page = [[=item_per_page=]];
    $message = [[=message=]];
?>
<style>
    .tableFixHead          { overflow: auto; height: 100px; }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; }

table  { border-collapse: collapse; width: 100%; }
th, td { padding: 8px 16px; }
th     { background:#eee; }
</style>
<form name="ListUserAdminInforForm">
    <div class="scroll" style="
  height: 800px;
  overflow: scroll;">
    <table class="table table-bordered tableFixHead">
        <thead>
            <tr valign="middle">
                <th width="1%" class="text-center">STT</th>
                <th class="text-center">Ngày</th>
                <th class="text-center">Tài khoản</th>
                <th class="text-center">Thao tác</th>
                <th class="text-center">IP</th>
            </tr>
        </thead>
        <tbody>
        <?php if(sizeof($items) > 0): ?>
        <?php foreach($items as $key => $value) : ?>
            <tr>
                <td width="1%" class="text-center"><?php echo $value['index'];?></td>
                <td class="text-center"><?php echo $value['created_at']; ?></td>
                <td class="text-center"><?php echo $value['username']; ?></td>
                <td class="text-center"><?php echo $value['action']; ?></td>
                <td class="text-left">
                    <?php echo $value['ip_address']; ?>
                    <br>
                    <?php echo $value['user_agent']; ?>
                    <br>
                    <?php echo $value['device_type']; ?>
                    <br>
                    <?php echo $value['content']; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php else: ?>
                <td class="text-center text-danger" colspan="5">Không có dữ liệu!</td>
        <?php endif; ?>
        </tbody>
    </table>
   <?php if($message == '') : ?>
    <?php if(sizeof($items) > 0): ?>
        <div class="row">
            <div class="col-md-2">
                <div class="input-group">
                    <input onchange="ReloadList(1)" name="item_per_page" id="item_per_page" class="form-control" min="15" max="500" style="width: 80px;" value="15" placeholder="Dòng hiển thị" type="number"/>
                    <span class="input-group-addon" style="border: none;" id="total">Tổng : <?php echo $total; ?></span>
                </div>
            </div>
            <div class="col-md-6 text-center">
                [[|paging|]]
            </div>
        </div>
        <input  name="page_no" type="hidden" id="page_no" value="[[|page_no|]]"/>
    <?php endif; ?>
    <?php endif; ?>
     </div>
</form>
<script type="text/javascript">
    var item_per_page = '<?php echo $item_per_page; ?>';
    $('#item_per_page').val(item_per_page)
</script>