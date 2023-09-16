<div class="view-bound">
	<div class="view">
    	<span>Lượt xem: <b>
            <?php echo $this->map['total_visit'];?>
        </b></span> &nbsp;|&nbsp;
        <span>Đang Online: <b>
			<?php for($i=1;$i<=[[=leng_user_online=]];$i++){ ?>
				<?php echo $this->map['total_online'][$i];?>
			<?php }?>
        </b></span>
    </div>
</div>
<!--
<script type="text/javascript">document.write(unescape("%3Cscript src=%27http://s10.histats.com/js15.js%27 type=%27text/javascript%27%3E%3C/script%3E"));</script>
<div align="center">
<script  type="text/javascript" >
try {Histats.start(1,1805869,4,443,142,101,"00011111");
Histats.track_hits();} catch(err){};
</script>
</div>
-->