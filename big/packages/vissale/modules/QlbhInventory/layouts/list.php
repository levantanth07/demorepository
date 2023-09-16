<div class="panel panel-default">
    <form name="ListQlbhInventoryForm" method="post">
        <table class="table">
            <tr>
                <td width="70%" class="form-title"><h2>[[|title|]]</h2></td>
                <td width="30%" align="right" nowrap="nowrap">
                    <input type="button" value="Thêm" onclick="window.location='<?php echo Url::build_current(array('cmd'=>'add','type'));?>'" class="btn btn-primary">
                    <input type="button" value="Xóa" id="delete_button" class="btn btn-danger">
                </td>
            </tr>
        </table>
        <div class="row">
          <div class="col-lg-12">
          <fieldset>
            <legend class="title">Tìm kiếm</legend>
            <div class="col-lg-3">
              <div class="input-group">
               <span class="input-group-addon">Kho</span><select name="warehouse_id" id="warehouse_id" class="form-control"></select>
              </div>
            </div>
            <div class="col-lg-3">
             <div class="input-group">
               <input type="submit" name="search" value="Tìm kiếm" class="btn btn-default">
              </div>
            </div>
          </fieldset><br />
          <table class="table table-bordered">
            <tr class="table-header">
              <th width="1%"><input type="checkbox" id="all_item_check_box"></th>
              <th width="1%">STT</th>
               <th width="20%" align="left">Mã hàng</th>
               <th width="20%" align="left">Mặt hàng</th>
               <th width="20%" align="left">Tồn đầu kỳ</th>
              <th width="1%">&nbsp;</th>
                <th width="1%">&nbsp;</th>
            </tr>
            <?php $qlbh_warehouse = '';?>
            <!--LIST:items-->
               <?php if($qlbh_warehouse != [[=items.warehouse_id=]]){ $qlbh_warehouse = [[=items.warehouse_id=]];?>
            <tr class="category-group">
              <td colspan="8">[[|items.qlbh_warehouse_name|]]</td>
            </tr>
             <?php }?>
            <tr>
              <td width="1%"><input name="item_check_box[]" type="checkbox" class="item-check-box" value="[[|items.id|]]"></td>
              <td style="cursor:pointer;">[[|items.i|]]</td>
              <td style="cursor:pointer;">[[|items.product_code|]]</td>
              <td style="cursor:pointer;">[[|items.product_name|]]</td>
              <td style="cursor:pointer;">[[|items.opening_stock|]]</td>
              <td><a class="btn btn-warning btn-sm" href="<?php echo Url::build_current(array('cmd'=>'edit','type','id'=>[[=items.id=]]));?>">Sửa</a></td>
                <td><a class="btn btn-danger btn-sm"" href="<?php echo Url::build_current(array('cmd'=>'delete','type','id'=>[[=items.id=]]));?>">Xoá</td>
            </tr>
            <!--/LIST:items-->
          </table>
                <div class="paging">[[|paging|]]</div>
            </div>
      </div><input name="cmd" type="hidden" value="">
    </form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#edit_button").click(function (){
            ListQlbhInventoryForm.cmd.value = 'edit';
            ListQlbhInventoryForm.submit();
        });
        jQuery("#delete_button").click(function (){
            ListQlbhInventoryForm.cmd.value = 'delete';
            ListQlbhInventoryForm.submit();
        });
        jQuery(".delete-one-item").click(function (){
            if(!confirm('[[.are_you_sure.]]')){
                return false;
            }
        });
        jQuery("#all_item_check_box").click(function (){
            var check  = this.checked;
            jQuery(".item-check-box").each(function(){
                this.checked = check;
            });
        });
    });
</script>