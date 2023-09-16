<div class="advertisment-bound" style=" <?php if(Module::get_setting('extend_css')){ echo Module::get_setting('extend_css'); }?>">
<!--LIST:items-->
<?php
if([[=items.url=]])
{
	$url = [[=items.url=]];
}
else
{
	$url = '';
}
if(strpos($url,'.swf'))
{
	?>
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="100%" height="400">
	<param name="movie" value="<?php echo $url;?>" />
	<param name="quality" value="high" />
	<embed src="<?php echo $url;?>" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="100%"></embed>
</object>
	<?php
}
else
{
	if($url){
		echo '<div><a target="_blank" href="'.$url.'"><img src="'.[[=items.image_url=]].'" title="'.[[=items.name=]].'" alt="'.[[=items.name=]].'"></a></div>';
	}else{
		echo '<div><img src="'.[[=items.image_url=]].'" title="'.[[=items.name=]].'" alt="'.[[=items.name=]].'"></div>';
	}
}
?>
<!--/LIST:items-->
</div>