<div class="ad-item-flash">
<!--LIST:items-->
<?php
$height = ''; $width = '';
if(isset([[=items.height=]]) and [[=items.height=]]) $height = 'height="'.[[=items.height=]].'px"';
if(isset([[=items.width=]]) and [[=items.width=]]) $width = 'width="'.[[=items.width=]].'px"';
?>
<?php
if(strpos([[=items.image_url=]],'.swf'))
{
	?>
	<embed src="[[|items.image_url|]]" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="[[|items.width|]]" height="[[|items.height|]]"></embed>
	<?php
}
else
{
	if([[=items.url=]]!="")
	{
		echo '<a target="_blank" href="'.Url::build_current(array('cmd'=>'click','id'=>[[=items.id=]])).'"><img src="'.[[=items.image_url=]].'" title="'.[[=items.name=]].'" alt="'.[[=items.name=]].'" '.$width.' '.$height.' style="'.Module::get_setting('internal_css').'"></a>';
	}
	else
	{
		echo '<img src="'.[[=items.image_url=]].'" title="'.[[=items.name=]].'" alt="'.[[=items.name=]].'" '.$width.' '.$height.' style="'.Module::get_setting('internal_css').'">';
	}
}
?>
<!--/LIST:items-->
</div>
<?php if(User::can_admin(MODULE_MEDIAADMIN,ANY_CATEGORY)){?>
<div align="center">[<a target="_blank" href="<?php echo Url::build('manage_advertisment',array('page_id'=>Module::$current->data['page_id'],'region'=>Module::$current->data['name']))?>">[[.adv_list.]]</a>]&nbsp;[<a target="_blank" href="<?php echo Url::build('manage_advertisment',array('cmd'=>'advertisment','page_id'=>Module::$current->data['page_id'],'region'=>Module::$current->data['name']))?>">[[.add_adv.]]</a>]</div>
<?php }?>
