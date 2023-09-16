<link rel="stylesheet" href="assets/default/css/global.css" type="text/css" />
<div class="banner">HỆ THỐNG QUẢN TRỊ NỘI DUNG<span>Catbeloved Panel Version 2012</span></div><br clear="all" />
<div class="user-menu-list">
    <ul>
    	<!--IF:member(![[=check_member=]])-->
        <li class="user-main-menu <?php if(Url::sget('page')=='panel') echo 'selected';?>">
            <a href="<?php echo Url::build('panel','',false);?>"><div><div>[[.overview.]]</div></div></a>
        </li>
        <!--/IF:member-->
        <!--LIST:categories-->
        <li class="user-main-menu <?php if([[=categories.check_selected=]]) echo 'selected';?>">
            <!--IF:sub_cond([[=categories.check=]])-->
            <div><div style="cursor:default;">[[|categories.name|]]</div></div>
            <!--ELSE-->
            <a href="[[|categories.url|]]"><div><div>[[|categories.name|]]</div></div></a>
            <!--/IF:sub_cond-->
        </li>
        <!--IF:sub_cond([[=categories.check=]])-->
        <li class="user-sub-menu-bound">
        	<ul><?php $i = 1;?>
            	<!--LIST:categories.childs-->
            	<li class="user-sub-menu-item <?php if($i==1) echo 'user-sub-menu-item-1'; $i++;?>"><a href="[[|categories.childs.url|]]">[[|categories.childs.name|]]</a></li>
            	<!--/LIST:categories.childs-->
            </ul>
        </li>
        <!--/IF:sub_cond-->
        <!--/LIST:categories-->
		<li class="user-menu-home" style="float:right;" onclick="window.location='<?php echo Url::build('sign_out');?>'" title="Thoát"></li>
    </ul>
</div>
<div class="clear"></div>
<script type="text/javascript">
jQuery(function(){
	jQuery('.user-main-menu').hover(
		function(){
			jQuery('.user-sub-menu-bound').hide();
			var pos = jQuery(this).position();
			jQuery(this).next().show().css('left',pos.left);
		},
		function(){
			jQuery('.user-sub-menu-bound').hide();
		}
	);
	jQuery('.user-sub-menu-bound').hover(
		function(){
			jQuery(this).show();
		},
		function(){
			jQuery(this).hide();
		}
	);
});
</script>