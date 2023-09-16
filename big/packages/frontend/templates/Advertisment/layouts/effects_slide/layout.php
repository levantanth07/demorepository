<script type="text/javascript" src="packages/core/includes/js/contentslider.js"></script>
<div class="advertisment-bound" style=" <?php if(Module::get_setting('extend_css')){ echo Module::get_setting('extend_css'); }?>">
<?php
$height = ''; $width = '';
if(isset([[=items.meta=]])) eval('$meta='.[[=items.meta=]].';');
if(isset($meta['height']) and $meta['height']) $height = 'height="'.$meta['height'].'px"';
if(isset($meta['width']) and $meta['width']) $width = 'width="'.$meta['width'].'px"';
if([[=total=]] > 1){
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="advertisment-slide-image-bound">
			<div id="slider1" class="contentslide" align="center">
				<div class="opacitylayer">
					<!--LIST:items-->
					<div class="contentdiv">
						<?php if([[=items.url=]]!=''){ ?><a href="[[|items.url|]]" target="_blank"><?php }?>
						<img id="ctl00_ContentPlaceHolder1_UcQuangcaoGiua1_Repeater1_ct[[|items.id|]]_abc" <?php echo $width;?> <?php echo $height;?> src="[[|items.image_url|]]" class="advertisment-slide-image" /></a>
					</div>
					<!--/LIST:items-->
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td align="center" class="pagination" id="paginate-slider1" style="display:none;">
			<script type="text/javascript">
				//ContentSlider("slider_ID", [autorotate_miliseconds], [custompaginatelinkstext], [customnextlinktext])
				//ContentSlider("slider1")
				ContentSlider("slider1", <?php echo Module::get_setting('speed')?Module::get_setting('speed'):6000;?>)
				//OR ContentSlider("slider1", 3000, linktextarray)
				//OR ContentSlider("slider1", 3000, linktextarray, "Foward")
				//OR ContentSlider("slider1", "", linktextarray)
				//OR ContentSlider("slider1", "", "", "Foward")
			</script>
		</td>
	</tr>
</table>
<?php }else{ ?>
	<!--LIST:items-->
<?php
	if([[=items.url=]]!=""){
		echo '<div><a target="_blank" href="'.[[=items.url=]].'"><img src="'.[[=items.image_url=]].'" title="'.[[=items.name=]].'" alt="'.[[=items.name=]].'" '.$width.' '.$height.' style="'.Module::get_setting('internal_css').'"></a></div>';
	}else{
		echo '<div><img src="'.[[=items.image_url=]].'" title="'.[[=items.name=]].'" alt="'.[[=items.name=]].'" '.$width.' '.$height.' style="'.Module::get_setting('internal_css').'"></div>';
	}
?>
	<!--/LIST:items-->
<?php }?>
</div>