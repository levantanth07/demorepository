<script type="text/javascript">
jQuery(function(){
	jQuery('.slide-image').css('opacity','0.3');
	<!--LIST:items-->
	var img[[|items.id|]] = new Image(); img[[|items.id|]].src = '[[|items.image_url|]]';
	if(jQuery('.slide-image-view').attr('id')==jQuery('#_'+[[|items.id|]]).attr('id')){
		jQuery('#__'+[[|items.id|]]).css('opacity','1');
	}
	<!--/LIST:items-->
	jQuery('#image_list').jCarouselLite({
		btnPrev:'#right',
		btnNext:'#left',
		mouseWheel:true,
		visible:6,
		scroll:3,
		speed: 500
	});
	jQuery('.slide-image').hover(
		function(){
			jQuery(this).css('opacity','1');
		},
		function(){
			if(jQuery(this).attr('id') != '_'+jQuery('.slide-image-view').attr('id')){
				jQuery(this).css('opacity','0.5');
			}
		}
	).click(function(){
		var img_lenght = (jQuery(this).attr('id')).length;
		jQuery('.slide-image-view').attr('id',(jQuery(this).attr('id')).substr(1,img_lenght));
		jQuery('.slide-image').css('opacity','0.5');
		jQuery(this).css('opacity','1');
		eval('var src = img'+this.lang+'.src');
		jQuery('.slide-image-view').attr('src',src);
		jQuery('.slide-image-view').animate({
			opacity: 0.5
		},1).animate({
			opacity: 1
		},500);
	});
});
</script>
<div class="slide-bound">
	<div class="slide-image-view-bound"><img id="_[[|id|]]" src="[[|image_url|]]" class="slide-image-view" /></div>
	<div class="scroll-left"><img src="<?php echo Portal::template();?>/images/slide/home/scroll_button_left.gif" id="left" /></div>
	<div class="slide-scroll" id="image_list">
		<ul class="slide-">
		<!--LIST:items-->
			<li class="slide-image-bound"><img id="__[[|items.id|]]" src="[[|items.image_url|]]" class="slide-image" lang="[[|items.id|]]" /></li>
		<!--/LIST:items-->
		</ul>
	</div>
	<div class="scroll-right"><img src="<?php echo Portal::template();?>/images/slide/home/scroll_button_right.gif" id="right" /></div>
</div>