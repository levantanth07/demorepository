<style>
#adv-up{
}
.adv-up-image-bound{
}
.adv-up-image-bound img{
    width: 258px;
    height: 193px;
    padding: 9px 0 14px 9px;
}
.adv-up-content{
    list-style: none;
}
</style>
<script type="text/javascript" src="packages/core/includes/js/jquery/jCarousel.js"></script>
	<div id="adv-up">
        <ul class="adv-up-content">
            <!--LIST:items-->
            <li class="adv-up-item">
                <div class="adv-up-image-bound" style="<?php echo Module::get_setting('internal_css');?>"><img src="[[|items.image_url|]]" alt="<?php echo substr([[=items.image_url=]],20); ?>"/></div>
				<!--<div class="partner-name"><a href="<?php //echo Url::build('xem-tin-tuc',array('name_id'=>[[=items.name_id=]]),REWRITE)?>" title="[[|items.name|]]">[[|items.name|]]</a></div>-->
				<!--<div class="partner-image-frame"><a href="<?php //echo Url::build('xem-tin-tuc',array('name_id'=>[[=items.name_id=]]),REWRITE)?>" title="[[|items.name|]]"><img src="assets/hotel/images/partner_frame.png" alt="phan-mem-quan-ly" /></a></div>-->
			</li>
            <!--/LIST:items-->
        </ul>
    </div>
<?php if(User::can_admin(MODULE_MEDIAADMIN,ANY_CATEGORY)){?>
<div align="center">[<a target="_blank" href="<?php echo Url::build('manage_advertisment',array('page_id'=>Module::$current->data['page_id'],'region'=>Module::$current->data['name']))?>">[[.adv_list.]]</a>]&nbsp;[<a target="_blank" href="<?php echo Url::build('manage_advertisment',array('cmd'=>'advertisment','page_id'=>Module::$current->data['page_id'],'region'=>Module::$current->data['name']))?>">[[.add_adv.]]</a>]</div>
<?php }?>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#adv-up').jCarouselLite({
		  vertical:true,
			speed:2500,
			btnNext: '.partner-slide-next',
			btnPrev: '.partner-slide-prev',
			visible: 1,
			auto:true
		});
	})
</script>
