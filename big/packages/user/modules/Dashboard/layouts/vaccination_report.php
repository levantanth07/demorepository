<?php if(Form::has_flash_message(VaccinationForm::FLASH_MESSAGE_KEY)):?>
    <?php Form::draw_flash_message_error(VaccinationForm::FLASH_MESSAGE_KEY); ?>
<?php elseif(!$this->map['statistics']):?>
<p class="text-center" style="padding: 40px">Hệ thống chưa có dữ liệu </p>
<?php else:?>
    <script type="text/javascript">
        $('html body table span').css({"display":"none"});
    </script>
<table class="table table-bordered ">
    <thead>
        <tr>
            <th rowspan="2" group="stt">
                <div style="width: 30px; text-align:center; margin: auto">STT</div>
            </th>
            <th rowspan="2" group="index">
                <div style="width: 120px; text-align:center; margin: auto">Tên</div>
            </th>
            <th rowspan="2" group="sum">
                <div style="width: 60px; text-align:center; margin: auto">NV</div>
            </th>
            <th colspan="5" group="sum">
                Tình trạng tiêm vắc xin Covid 19
            </th>
            <th colspan="7" group="sum">
                Tình trạng sức khỏe
            </th>
        </tr>    
        <tr>
            <!-- Tỉ lệ tiêm  -->
            <th>Chưa xác định</th>
            <th>Chưa tiêm</th>
            <th>Mũi 1</th>
            <th>Mũi 2</th>
            <th>Mũi 3</th>

            <!-- Tình trạng sức khỏe  -->
            <th>Chưa xác định</th>
            <th>Bình thường</th>
            <th col="f0">F0</th>
            <th col="f1">F1</th>
            <th>F2</th>
            <th>F3</th>
            <th>Khác</th>
        </tr>                        
    </thead>
    <tbody>     
        <?php $i = 1;?>                  
        <?php foreach($this->map['statistics'] as $index => $cells):?>
        <tr>
            <td align="center"><?=$i++?></td>
            <td><?=$cells['name']?></td>
            <td><?=$cells['num_users']?></td>

            <!-- Tỉ lệ tiêm  -->
            <td><?=$cells['chua_xac_dinh']?> <br> <span>(<?=$cells['chua_xac_dinh_pc']?>%)</span>  </td>
            <td><?=$cells['chua_tiem']?> <br> <span>(<?=$cells['chua_tiem_pc']?>%)</span></td>
            <td><?=$cells['mui_1']?> <br> <span>(<?=$cells['mui_1_pc']?>%)</span></td>
            <td><?=$cells['mui_2']?> <br> <span>(<?=$cells['mui_2_pc']?>%)</span></td>
            <td><?=$cells['mui_3']?> <br> <span>(<?=$cells['mui_3_pc']?>%)</span></td>

            <!-- Tình trạng sức khỏe  -->
            <td><?=$cells['sk_chua_xac_dinh']?><br><span>(<?=$cells['sk_chua_xac_dinh_pc']?>%)</span></td>
            <td><?=$cells['sk_binh_thuong']?><br><span>(<?=$cells['sk_binh_thuong_pc']?>%)</span></td>
            <td col="f0"><?=$cells['sk_f0']?><br><span>(<?=$cells['sk_f0_pc']?>%)</span></td>
            <td col="f1"><?=$cells['sk_f1']?><br><span>(<?=$cells['sk_f1_pc']?>%)</span></td>
            <td><?=$cells['sk_f2']?><br><span>(<?=$cells['sk_f2_pc']?>%)</span></td>
            <td><?=$cells['sk_f3']?><br><span>(<?=$cells['sk_f3_pc']?>%)</span></td>
            <td><?=$cells['sk_khac']?><br><span>(<?=$cells['sk_khac_pc']?>%)</span></td>
        </tr>
        <?php endforeach;?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" align="center">Tổng</td>
            <td><?=$this->map['sum']['num_users']?></td>

            <!-- Tỉ lệ tiêm  -->
            <td><?=$this->map['num']['chua_xac_dinh']?> <br> <span>(<?=$this->map['sum']['chua_xac_dinh_pc']?>%)</span></td>
            <td><?=$this->map['num']['chua_tiem']?> <br> <span>(<?=$this->map['sum']['chua_tiem_pc']?>%)</span></td>
            <td><?=$this->map['num']['mui_1']?><br> <span>(<?=$this->map['sum']['mui_1_pc']?>%)</span></td>
            <td><?=$this->map['num']['mui_2']?> <br> <span>(<?=$this->map['sum']['mui_2_pc']?>%)</span></td>
            <td><?=$this->map['num']['mui_3']?> <br> <span>(<?=$this->map['sum']['mui_3_pc']?>%)</span></td>

            <!-- Tình trạng sức khỏe  -->
            <td><?=$this->map['num']['sk_chua_xac_dinh']?> <br> <span>(<?=$this->map['sum']['sk_chua_xac_dinh_pc']?>%)</span></td>
            <td><?=$this->map['num']['sk_binh_thuong']?><br> <span>(<?=$this->map['sum']['sk_binh_thuong_pc']?>%)</span></td>
            <td col="f0"><?=$this->map['num']['sk_f0']?> <br> <span>(<?=$this->map['sum']['sk_f0_pc']?>%)</span></td>
            <td col="f1"><?=$this->map['num']['sk_f1']?> <br> <span>(<?=$this->map['sum']['sk_f1_pc']?>%)</span></td>
            <td><?=$this->map['num']['sk_f2']?> <br> <span>(<?=$this->map['sum']['sk_f2_pc']?>%)</span></td>
            <td><?=$this->map['num']['sk_f3']?> <br> <span>(<?=$this->map['sum']['sk_f3_pc']?>%)</span></td>
            <td><?=$this->map['num']['sk_khac']?> <br> <span>(<?=$this->map['sum']['sk_khac_pc']?>%)</span></td>
        </tr>
    </tfoot>
</table>
<?php endif;?>

