<table class="table" id="company-chart-table" style="border-collapse: separate;">
    <thead>
        <tr>
        <th rowspan="2" class="small text-center">#</th>
        <th rowspan="2" class="small text-center">Tên công ty</th>
        <th rowspan="2" class="text-center" class="small text-center">SL tài khoản</th>
        <th rowspan="2" class="text-center" class="small text-center">SL NV</th>
        <th colspan="3" class="small text-center">Tổng</th>
        <th colspan="3" class="small text-center">Đơn mới</th>
        <th colspan="3" class="small text-center">Đơn tối ưu</th>
        <th colspan="3" class="small text-center">Đơn CSKH</th>
    </tr>
    <tr>
        <!-- Tổng -->
        <th class="small text-center">Điểm</th>
        <th class="small text-center">Đơn</th>
        <th class="small text-center">SĐT</th>

        <!-- Đơn mới -->
        <th class="small text-center">Điểm</th>
        <th class="small text-center">Đơn</th>
        <th class="small text-center">SĐT</th>

        <!-- Tối ưu -->
        <th class="small text-center">Điểm</th>
        <th class="small text-center">Đơn</th>
        <th class="small text-center">SĐT</th>

        <!-- Đơn CSKH -->
        <th class="small text-center">Điểm</th>
        <th class="small text-center">Đơn</th>
        <th class="small text-center">SĐT</th>
    </tr>
    </thead>
    <tbody>
    <?php 
        $i=1;
        $total = 0;
        $total_order=0;
        $total_phone=0;
        $total_user=0;
        $total_parent_user=0
    ?>
    <!--LIST:groups-->
    <?php 
        $total_user += [[=groups.total_user=]];
        $total_parent_user += [[=groups.total_parent_user=]];
    ?>
    <tr class="row-<?=$i;?>" data-group-id="[[|groups.id|]]">
        <td><?=($i>1)?($i):'<i class="fa fa-trophy"></i>';?></td>
        <td class="group-name">
            [[|groups.name|]]
        </td>
        <td class="small text-center">[[|groups.total_user|]]</td>
        <td class="small text-center">[[|groups.total_parent_user|]]</td>
        
        <!-- Tổng -->
        <td class="text-right">
            <span class="money money_total">...</span>
        </td>
        <td class="small text-center order">...</td>
        <td class="small text-center phone">...</td>

        <!-- Đơn mới -->
        <td class="text-right">
            <span class="money money_somoi">...</span>
        </td>
        <td class="small text-center order_somoi">...</td>
        <td class="small text-center phone_somoi">...</td>

        <!-- Tối ưu -->
        <td class="text-right">
            <span class="money money_toiuu">...</span>
        </td>
        <td class="small text-center order_toiuu">...</td>
        <td class="small text-center phone_toiuu">...</td>

        <!-- Đơn CSKH -->
        <td class="text-right">
            <span class="money money_cskh">...</span>
        </td>
        <td class="small text-center order_cskh">...</td>
        <td class="small text-center phone_cskh">...</td>
    </tr>
    <?php $i++;?>
    <!--/LIST:groups-->
</tbody>
<tfoot>
    <tr class="row-total text-success" style="background: #fff;">
        <td></td>
        <td class="text-bold">Tổng</td>
        <td class="text-bold small text-center total_user"><?=$total_user?></td>
        <td class="text-bold small text-center total_user"><?=$total_parent_user?></td>
        
        <!-- Tổng -->
        <td class="text-bold text-right">
            <span class="money money_total">...</span>
        </td>
        <td class="text-bold small text-center order">...</td>
        <td class="text-bold small text-center phone">...</td>
        
        <!-- Đơn mới -->
        <td class="text-bold small text-right">
            <span class="money money_somoi">...</span>
        </td>
        <td class="text-bold small text-center order_somoi">...</td>
        <td class="text-bold small text-center phone_somoi">...</td>
        
        <!-- Tối ưu -->        
        <td class="text-bold small text-right">
            <span class="money money_toiuu">...</span>
        </td>
        <td class="text-bold small text-center order_toiuu">...</td>
        <td class="text-bold small text-center phone_toiuu">...</td>
        
        <!-- Đơn CSKH -->
        <td class="text-bold small text-right">
            <span class="money money_cskh">...</span>
        </td>
        <td class="text-bold small text-center order_cskh">...</td>
        <td class="text-bold small text-center phone_cskh">...</td>
    </tr>
</tfoot>
</table>