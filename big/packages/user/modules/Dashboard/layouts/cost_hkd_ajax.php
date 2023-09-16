<style>
    .tableFixHead tr th { 
        position: sticky; top: 0; z-index: 1; 
    }
    table  { 
        border-collapse: collapse; width: 100%; 
    }
    .tableFixHead tr th { 
        background:#DDD; 
    }
</style>
<?php if(Form::has_flash_message(CostHkdForm::FLASH_MESSAGE_KEY)):?>
    <?php Form::draw_flash_message_error(CostHkdForm::FLASH_MESSAGE_KEY); ?>
<?php elseif(!$this->map['statistics']):?>
<p class="text-center" style="padding: 40px">Hệ thống chưa có dữ liệu </p>
<?php else:?>
<div class="table-responsive scroll" style="max-height: 800px; overflow: auto">
<table class="table table-bordered tableFixHead">
    <thead style="position: sticky; top: 0; z-index: 1">
        <tr>
            <th rowspan="2" group="stt">
                <div style="width: 60px; text-align:center; margin: auto">STT</div>
            </th>
            <th rowspan="2" group="index">
                <div style="width: 120px; text-align:center; margin: auto">Chỉ tiêu</div>
            </th>
            <th rowspan="2" group="sum">
                <div style="width: 200px; text-align:center; margin: auto">Tổng</div>
            </th>
            <th rowspan="2" group="sum">
                <div style="width: 200px; text-align:center; margin: auto">Tỉ lệ</div>
            </th>
            <?php foreach($this->map['days'] as $shortDay => $day):?>
            <th rowspan="2" column="sum_group_col">
                <div style="width: 120px; margin: auto"><?=$shortDay?></div>
            </th>
            <?php endforeach;?>
        </tr>                            
    </thead>
    <tbody>     
        <?php $i = 1;?>                  
        <?php foreach($this->map['statistics'] as $index => $cells):?>

        <tr>
            <td align="center"><?=$i++?></td>
            <?php foreach($cells as $index => $cell):?>

                <?php if($index != 'name'): ?>
                    <?php if($index == 'rate'): ?>
                        <?php if($cell != 0): ?>
                            <td align="right"><?=($this->numFormat($cell))?>%</td>
                        <?php else: ?>
                            <td></td>
                        <?php endif; ?>
                    <?php else: ?>
                            <td align="right"><?=($this->numFormat($cell))?></td>
                    <?php endif; ?>
                <?php else: ?>
                        <td align="left"><?=($cell)?></td>
                <?php endif; ?>
            
            <?php endforeach;?>
        </tr>
        <?php endforeach;?>
    </tbody>
</table>
</div>
<?php endif;?>

