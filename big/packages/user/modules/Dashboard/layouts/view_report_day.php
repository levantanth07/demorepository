<?php
    $sales = [[=sales=]];
    $marketing = [[=marketing=]];
    $arrayAvatar = [[=arrayAvatar=]];
    $top1 = $sales[1]['avatar'] ?? '';
    $top2 = $sales[2]['avatar'] ?? '';
    $top3 = $sales[3]['avatar'] ?? '';
    $top11 = $marketing[1]['avatar'] ?? '';
    $top21 = $marketing[2]['avatar'] ?? '';
    $top31 = $marketing[3]['avatar'] ?? '';
?>
<div class="charts-sale">
    <div class="rank-top">
        <img class="images" src="assets/vissale/BXHHTML/images/rank-1.png">
        <div class="top-1">
            <?php  if($top1 != ''): ?>
                <img class="avatar" src="<?php echo $top1 ?>">
            <?php  endif; ?>
        </div>
        <div class="top-2">
            <?php  if($top2 != ''): ?>
                <img class="avatar" src="<?php echo $top2 ?>">
            <?php  endif; ?>
        </div>
        <div class="top-3">
            <?php  if($top3 != ''): ?>
                <img class="avatar" src="<?php echo $top3 ?>">
            <?php  endif; ?>
        </div>
    </div>
    <h3 class="charts-sale-title">BẢNG XẾP HẠNG SALE</h3>
    <div class="table-report">
        <table class="table-sale text-center">
            <thead>
                <tr>
                  <th scope="col">Avatar</th>
                  <th scope="col">Name</th>
                  <th scope="col">Order</th>
                  <th scope="col">Rank Avatar</th>
                  <th scope="col">Rev/ ngày</th>
                </tr>
            </thead>
            <tbody>
                <?php  if (sizeof($sales) > 0):?>
                    <?php foreach($sales as $key => $sale): ?>
                        <?php if($key < 5): ?>
                            <tr align="center" class="bg-color-<?php echo $key; ?>">
                        <?php elseif($key >= 5): ?>
                            <tr align="center" style="background:white">
                        <?php endif; ?>
                          <td><img class="avatar_" src="<?php echo $sale['avatar'] ?>"></td>
                          <td><?php echo $sale['name']; ?></td>
                          <td><?php echo $sale['qty']; ?></td>
                          <td>
                            <?php if($key < 5): ?>
                                <div class="img-rank"><img src="<?php echo $arrayAvatar[$key]['url'] ?>"></div>
                            <?php elseif($key >= 5): ?>
                                <div class="img-rank"><img src="<?php echo $arrayAvatar[5]['url'] ?>"></div>
                            <?php endif; ?>
                          </td>
                          <td style="color: #c82c2c; font-weight: bold;"><?php echo $sale['total']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif(sizeof($sales) <= 0): ?>
                    <tr align="center" class="bg-color-1">
                        <td colspan="5"><p class="text-danger text-center">Chưa có dữ liệu</p></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>       
</div>
<div class="charts-marketting">
    <div class="rank-top">
        <img class="images" src="assets/vissale/BXHHTML/images/rank-2.png">
        <div class="top-1">
            <?php  if($top11 != ''): ?>
                <img class="avatar" src="<?php echo $top11 ?>">
            <?php  endif; ?>
        </div>
        <div class="top-2">
            <?php  if($top31 != ''): ?>
                <img class="avatar" src="<?php echo $top31 ?>">
            <?php  endif; ?>
        </div>
        <div class="top-3">
            <?php  if($top21 != ''): ?>
                <img class="avatar" src="<?php echo $top21 ?>">
            <?php  endif; ?>
        </div>
    </div>
    <h3 class="charts-marketting-title">BẢNG XẾP HẠNG MKT</h3>
    <div class="table-report">
        <table class="table-sale text-center">
            <thead>
                <tr>
                  <th scope="col">Avatar</th>
                  <th scope="col">Name</th>
                  <th scope="col">Order</th>
                  <th scope="col">Rank Avatar</th>
                  <th scope="col">Rev/ ngày</th>
                </tr>
            </thead>
            <tbody id="load-data">
                <?php  if (sizeof($marketing) > 0):?>
                    <?php foreach($marketing as $key => $mkt): ?>
                        <?php if($key < 5): ?>
                            <tr align="center" class="bg-color-<?php echo $key; ?>">
                        <?php elseif($key >= 5): ?>
                            <tr align="center" style="background:white">
                        <?php endif; ?>
                          <td><img class="avatar_" src="<?php echo $mkt['avatar'] ?>"></td>
                          <td><?php echo $mkt['name']; ?></td>
                          <td><?php echo $mkt['qty']; ?></td>
                          <td>
                            <?php if($key < 5): ?>
                                <div class="img-rank"><img src="<?php echo $arrayAvatar[$key]['url'] ?>"></div>
                            <?php elseif($key >= 5): ?>
                                <div class="img-rank"><img src="<?php echo $arrayAvatar[5]['url'] ?>"></div>
                            <?php endif; ?>
                          </td>
                          <td style="color: #c82c2c; font-weight: bold;"><?php echo $mkt['total']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif(sizeof($marketing) <= 0): ?>
                    <tr align="center" class="bg-color-1">
                          <td colspan="5"><p class="text-danger text-center">Chưa có dữ liệu</p></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>