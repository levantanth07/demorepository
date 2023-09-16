<div class="statistic-bound">
	<div class="statistic-title">[[.visitors.]]:</div>
    <div class="statistic-content-bound">
		<?php for($i=5;$i>=1;$i--){ ?>
		<div class="statistic-item"><?php echo $this->map['visit'][7-$i];?></div>
		<?php }?>
		<div class="clear"></div>
    </div>
	<div class="clear"></div>
	<div class="statistic-title">[[.online.]]:</div>
	<div class="statistic-content-bound">
		<?php for($i=1;$i<=[[=leng_user_online=]];$i++){ ?>
		<div class="statistic-item"><?php echo $this->map['total_online'][$i];?></div>
		<?php }?>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>