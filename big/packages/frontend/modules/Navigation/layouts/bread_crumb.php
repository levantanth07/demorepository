<?php $i=0;?>
<!--LIST:items-->
<?php if($i!=0){?>
&raquo;
<?php }$i++;?>
<a class="bread-crumb-item" href="<?php echo Url::build([[=url=]]?[[=url=]]:strtolower([[=items.type=]]),array('category_id'=>[[=items.id=]]));?>">[[|items.name|]]</a>
<!--/LIST:items-->