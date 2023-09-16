<style type="text/css">
    .fail, .pass {color: #fff; margin: -6px; padding: 6px; border-radius: 3px; }
    .fail {background: #e95959; }
    .pass {background: #3f8f3f; }
    a[data-toggle="tooltip"]{color:  #ffe700}
</style>
<?php if (Form::has_flash_message(CostMktForm::FLASH_MESSAGE_KEY)) :?>
    <?php Form::draw_flash_message_error(CostMktForm::FLASH_MESSAGE_KEY); ?>
<?php elseif (!$this->map['statistics']) :?>
<p class="text-center" style="padding: 40px">Hệ thống chưa có dữ liệu </p>

<?php else :?>
    <?php
    // dd($this->map['columns'], false);
    ?>
<table class="table table-bordered ">
    <thead>
        <tr>
            <th rowspan="2" col="stt">
                <div style="width: 30px; text-align:center; margin: auto">STT</div>
            </th>

            <th rowspan="2" col="name">
                <div style="width: 120px; text-align:center; margin: auto">Tên HKD</div>
            </th>
            <?php foreach ($this->map['columns']['childs'] as $block => $cols) :?>
                <?php if ($block === 'cpqc') :?>
                <th rowspan="2" col="cpqc">
                    <div style="width: 60px; text-align:center; margin: auto">
                        CPQC
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Chi phí quảng cáo">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'sdt') :?>
                <th rowspan="2" col="sdt">
                    <div style="width: 60px; text-align:center; margin: auto">
                        Tổng SĐT
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Tổng đơn đã tạo">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'cpqc/sdt') :?>
                <th rowspan="2" col="rate_sdt">
                    <div style="width: 60px; text-align:center; margin: auto">
                        CPQC / SĐT
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Chi phí quảng cáo / Tổng đơn đã tạo">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'block_so_moi') :?>
                <th colspan="<?=count($cols['childs'])?>" col="rate_sdt">
                    <div style="text-align:center; margin: auto">
                        Đơn mới
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Khối đơn sale mới (Gồm đơn có loại là Sale mới hoặc đơn không có loại đơn)">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'block_toi_uu') :?>
                <th colspan="<?=count($cols['childs'])?>" col="rate_sdt">
                    <div style="width: 100px; text-align:center; margin: auto">
                        Đơn tối ưu
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Khối đơn tối ưu (Gồm đơn có loại đơn là Tối ưu)">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'block_cskh') :?>
                <th colspan="<?=count($cols['childs'])?>" col="rate_sdt">
                    <div style="width: 100px; text-align:center; margin: auto">
                        Đơn CSKH
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Khối đơn CSKH (Gồm đơn CSKH và các đơn Đặt lại 1,2,...20, trên 20)">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'block_total') :?>
                <th colspan="<?=count($cols['childs'])?>" col="rate_sdt">
                    <div style="text-align:center; margin: auto">
                        Tổng
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Tổng (Sale mới + Tối ưu + CSKH)">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'so_nv_mkt') :?>
                <th rowspan="2" col="rate_sdt">
                    <div style="width: 100px; text-align:center; margin: auto">
                        NV MKT
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Nhân viên MKT">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'so_nv_sale') :?>
                <th rowspan="2" col="rate_sdt">
                    <div style="width: 100px; text-align:center; margin: auto">
                        NV SALE
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Nhân viên Sale">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'block_mkt') :?>
                <th colspan="<?=count($cols['childs'])?>" col="rate_sdt">
                    <div style="text-align:center; margin: auto">
                        MKT
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Khối thông tin liên quan MKT">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'block_sale') :?>
                <th colspan="<?=count($cols['childs'])?>" col="rate_sdt">
                    <div style="text-align:center; margin: auto">
                        SALE
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Khối thông tin liên quan Sale">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>

                <?php if ($block === 'id') :?>
                <th rowspan="2" col="rate_sdt">
                    <div style="text-align:center; margin: auto">
                        Lợi nhuận
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Lợi nhuận (Dữ liệu được lấy từ Báo Cáo Doanh Thu Ước Chừng)">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                <?php endif;?>
            <?php endforeach; ?>
        </tr>       
        <tr>
            <?php foreach ($this->map['columns']['childs'] as $block => $cols) :
                ?>
            <!-- Đơn mới -->
                <?php if ($block === 'block_so_moi') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'so_don_sale_moi') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">
                            Đơn
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Số đơn sale mới">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                        <?php endif;?>
                    
                            <?php if ($col === 'doanh_thu_sale_moi') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">
                            Điểm
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Doanh thu đơn sale mới/1.000.000">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                            <?php endif;?>
                    
                            <?php if ($col === 'cpqc/so_don_sale_moi') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">
                            CPQC / Đơn
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Chi phí quảng cáo / Số đơn sale mới">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                            <?php endif;?>
                    
                            <?php if ($col === 'cpqc/doanh_thu_sale_moi') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">
                            CPQC / Điểm
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Chi phí quảng cáo / Doanh thu đơn sale mới">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                            <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            <!----/Đơn mới -->

            <!-- Đơn tối ưu -->
                <?php if ($block === 'block_toi_uu') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'so_don_toi_uu') :?>
                <th>
                    <div style="width: 100px; text-align:center; margin: auto">
                        Số đơn
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Số đơn tối ưu">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                        <?php endif;?>
                
                        <?php if ($col === 'doanh_thu_toi_uu') :?>
                <th>
                    <div style="width: 100px; text-align:center; margin: auto">
                        Điểm
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Doanh thu đơn tối ưu / 1.000.000">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            <!----/Đơn tối ưu -->

            <!-- Đơn CSKH -->
                <?php if ($block === 'block_cskh') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'so_don_cskh') :?>
                <th>
                    <div style="width: 100px; text-align:center; margin: auto">
                        Số đơn
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Số đơn CSKH">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                        <?php endif;?>
                
                        <?php if ($col === 'doanh_thu_cskh') :?>
                <th>
                    <div style="width: 100px; text-align:center; margin: auto">
                        Điểm
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Doanh thu đơn CSKH / 1.000.000">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            <!----/Đơn CSKH -->

            <!-- Tổng -->
                <?php if ($block === 'block_total') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'tong_so_don') :?>
                <th>
                    <div style="width: 100px; text-align:center; margin: auto">
                         Số Đơn
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Tổng số đơn Sale mới + Tối ưu + CSKH">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                        <?php endif;?>
                
                        <?php if ($col === 'tong_doanh_thu') :?>
                <th>
                    <div style="width: 100px; text-align:center; margin: auto">
                        Điểm
                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Tổng doanh thu đơn Sale mới + Tối ưu + CSKH / 1.000.000">
                            <i class="fa fa-question-circle"></i>
                        </a>
                    </div>
                </th>
                        <?php endif;?>
                
                        <?php if ($col === 'cpqc/tong_so_don') :?>
                <th>
                    <div style="width: 100px; text-align:center; margin: auto">
                    CPQC / Đơn
                    <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Chi phí quảng cáo / Tổng Đơn">
                        <i class="fa fa-question-circle"></i>
                    </a>
                </div>
                </th>
                        <?php endif;?>
                
                        <?php if ($col === 'cpqc/tong_doanh_thu') :?>
                <th>
                    <div style="width: 100px; text-align:center; margin: auto">
                    CPQC / Điểm
                    <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Chi phí quảng cáo / Tổng Điểm">
                        <i class="fa fa-question-circle"></i>
                    </a>
                </div>
                </th>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            <!----/Tổng -->

            <!-- MKT -->
                <?php if ($block === 'block_mkt') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'sdt/so_nv_mkt') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">
                            SĐT/MKT
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Số đơn / Số nv MKT">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                        <?php endif;?>

                        <?php if ($col === 'cpqc/sdt') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">
                            CPQC/Data
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Chi phí quảng cáo / Số đơn">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                        <?php endif;?>

                        <?php if ($col === 'doanh_thu_sale_moi/so_nv_mkt') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">DT mới/MKT
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Doanh thu sale mới / Số nv MKT">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            <!----/MKT -->

            <!-- SALE -->
                <?php if ($block === 'block_sale') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'sdt/so_nv_sale') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">SĐT/Sale
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Số đơn / Số nv Sale">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                        <?php endif;?>

                        <?php if ($col === 'tong_doanh_thu/so_nv_sale') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">Điểm /Sale
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Doanh thu tổng / Số nv Sale">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                        <?php endif;?>

                        <?php if ($col === 'doanh_thu_cskh/tong_doanh_thu') :?>
                    <th>
                        <div style="width: 100px; text-align:center; margin: auto">%CSKH
                            <a href="javascript:void(0);" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Doanh thu CSKH / Doanh thu tổng">
                                <i class="fa fa-question-circle"></i>
                            </a>
                        </div>
                    </th>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            <!----/SALE -->
            <?php endforeach; ?>
        </tr>                         
    </thead>
    <tbody>     
        <?php $i = 1;?>                  
        <?php foreach ($this->map['statistics'] as $index => $cells) :?>
        <tr>
            <td align="center"><?=$i++?></td>
            <td align="center"><?=$cells['name']?></td>
            <?php foreach ($this->map['columns']['childs'] as $block => $cols) :?>
                <?php if ($block === 'cpqc') :?>
                <td key="cpqc" align="center"><?=$this->numFormat($cells['cpqc'], 2)?></td>
                <?php endif;?>

                <?php if ($block === 'sdt') :?>
                <td key="sdt" align="center"><?=$this->numFormat($cells['sdt'], 2)?></td>
                <?php endif;?>

                <?php if ($block === 'cpqc/sdt') :?>
                <td key="cpqc/sdt" align="center"><?=$this->numFormat($cells['cpqc/sdt'], 2)?></td>
                <?php endif;?>

                <!-- Đơn mới -->
                <?php if ($block === 'block_so_moi') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'so_don_sale_moi') :?>
                        <td key="so_don_sale_moi" align="center"><?=$this->numFormat($cells['so_don_sale_moi'], 2)?></td>
                        <?php endif;?>

                        <?php if ($col === 'doanh_thu_sale_moi') :?>
                        <td key="doanh_thu_sale_moi" align="center"><?=$this->numFormat($cells['doanh_thu_sale_moi'], 2)?></td>
                        <?php endif;?>

                        <?php if ($col === 'doanh_thu_sale_moi') :?>
                        <td key="cpqc/so_don_sale_moi" align="center"><?=$this->numFormat($cells['cpqc/so_don_sale_moi'], 2)?></td>
                        <?php endif;?>

                        <?php if ($col === 'doanh_thu_sale_moi') :?>
                        <td key="cpqc/doanh_thu_sale_moi" align="center"><?=$this->numFormat($cells['cpqc/doanh_thu_sale_moi'], 2)?></td>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
                <!----/Đơn mới -->

                <!-- Đơn tối ưu -->
                <?php if ($block === 'block_toi_uu') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'so_don_toi_uu') :?>
                        <td key="so_don_toi_uu" align="center"><?=$this->numFormat($cells['so_don_toi_uu'], 2)?></td>
                        <?php endif;?>
                        
                        <?php if ($col === 'doanh_thu_toi_uu') :?>
                        <td key="doanh_thu_toi_uu" align="center"><?=$this->numFormat($cells['doanh_thu_toi_uu'], 2)?></td>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
                <!----/Đơn tối ưu -->        
                        
                <!-- Đơn CSKH -->
                <?php if ($block === 'block_cskh') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'so_don_cskh') :?>
                        <td key="so_don_cskh" align="center"><?=$this->numFormat($cells['so_don_cskh'], 2)?></td>
                        <?php endif;?>
                        
                        <?php if ($col === 'doanh_thu_cskh') :?>
                        <td key="doanh_thu_cskh" align="center"><?=$this->numFormat($cells['doanh_thu_cskh'], 2)?></td>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>                        
                <!----/Đơn CSKH -->        
                        
                <!-- Tổng -->
                <?php if ($block === 'block_total') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'tong_so_don') :?>
                        <td key="tong_so_don" align="center"><?=$this->numFormat($cells['tong_so_don'], 2)?></td>
                        <?php endif;?>
                        
                        <?php if ($col === 'tong_doanh_thu') :?>
                        <td key="tong_doanh_thu" align="center"><?=$this->numFormat($cells['tong_doanh_thu'], 2)?></td>
                        <?php endif;?>
                        
                        <?php if ($col === 'cpqc/tong_so_don') :?>
                        <td key="cpqc/tong_so_don" align="center"><?=$this->numFormat($cells['cpqc/tong_so_don'], 2)?></td>
                        <?php endif;?>
                        
                        <?php if ($col === 'cpqc/tong_doanh_thu') :?>
                        <td key="cpqc/tong_doanh_thu" align="center"><?=$this->numFormat($cells['cpqc/tong_doanh_thu'], 2)?></td>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
                <!----/Tổng -->                        
                        
                        

                <!-- NV MKT -->
                <?php if ($block === 'so_nv_mkt') :?>
                <td><?=$this->numFormat($cells['so_nv_mkt'], 2)?> </td>
                <?php endif;?>
                <!----/NV MKT -->

                <!-- NV SALE -->
                <?php if ($block === 'so_nv_sale') :?>
                <td><?=$this->numFormat($cells['so_nv_sale'], 2)?> </td>
                <?php endif;?>
                <!----/NV SALE -->

                <!-- MKT -->
                <?php if ($block === 'block_mkt') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'sdt/so_nv_mkt') :?>
                        <td>
                            <div class="<?=$this->passOrFail($cells[$col], function ($v) {
    return $v >= 20;
                                        })?>">
                                <?=$this->numFormat($cells[$col], 2)?>
                            </div>
                        </td> 
                        <?php endif;?>

                        <?php if ($col === 'cpqc/sdt') :?>
                        <td>
                            <div class="<?=$this->passOrFail($cells[$col], function ($v) {
    return $v <= 0.15;
                                        })?>">
                                <?=$this->numFormat($cells[$col], 2)?>
                            </div>
                        </td> 
                        <?php endif;?>

                        <?php if ($col === 'doanh_thu_sale_moi/so_nv_mkt') :?>
                        <td>
                            <div class="<?=$this->passOrFail($cells[$col], function ($v) {
    return $v >= 10;
                                        })?>">
                                <?=$this->numFormat($cells[$col], 2)?>
                            </div>
                        </td> 
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
                
                <!-- SALE -->
                <?php if ($block === 'block_sale') :?>
                    <?php foreach ($cols['childs'] as $col => $name) :?>
                        <?php if ($col === 'sdt/so_nv_sale') :?>
                        <td>
                            <div class="<?=$this->passOrFail($cells[$col], function ($v) {
    return $v >= 10;
                                        })?>">
                                <?=$this->numFormat($cells[$col], 2)?>
                            </div>
                        </td>
                        <?php endif;?>

                        <?php if ($col === 'tong_doanh_thu/so_nv_sale') :?>
                        <td>
                            <div class="<?=$this->passOrFail($cells[$col], function ($v) {
    return $v >= 5;
                                        })?>">
                                <?=$this->numFormat($cells[$col], 2)?>
                            </div>
                        </td>
                        <?php endif;?>

                        <?php if ($col === 'doanh_thu_cskh/tong_doanh_thu') :?>
                        <td>
                            <div class="<?=$this->passOrFail($cells[$col], function ($v) {
    return $v >= 30;
                                        })?>">
                                <?=$this->numFormat($cells[$col], 2)?>
                            </div>
                        </td>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
                <!----/SALE -->

                <!-- Lợi nhuận -->
                <?php if ($block === 'id') :?>
                <td profit="<?=$cells['id'] ?? 'total'?>"> ... </td>
                <?php endif;?>
                <!----/Lợi nhuận -->
            <?php endforeach;?>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
<script>
    window.__REPORT_EXPIRED = <?=($REPORT_EXPIRED = time())?>;
    window.__REPORT_DATA = <?=($REPORT_DATA = json_encode($this->map['statistics']))?>;
    window.__REPORT_HASH = '<?=md5($REPORT_EXPIRED . $REPORT_DATA . $REPORT_EXPIRED)?>';
</script>
<?php endif;?>
