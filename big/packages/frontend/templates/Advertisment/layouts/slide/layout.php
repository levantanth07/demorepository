<link rel="stylesheet" href="<?php echo Portal::template();?>/slide/LightboxPanel.css" type="text/css" />
<script type="text/javascript" src="<?php echo Portal::template_js();?>/jquery/jquery.galleryview-1.1.js"></script>
<script type="text/javascript" src="<?php echo Portal::template_js();?>/jquery/jquery.timers-1.1.2.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#photos_<?php echo Module::block_id();?>').galleryView({
			panel_width: 624,
			panel_height: 199,
			transition_speed: 1500,
			transition_interval: 5000,
			nav_theme: 'dark',
			border: '0px solid white',
			pause_on_hover: true,
			path:'assets/default/images/slide/LightboxPanel/'
		});
	});
</script>
<div style="width:625px;<?php if(Module::get_setting('extend_css')){ echo Module::get_setting('extend_css'); }?>;overflow:hidden;" align="center">
	<div id="photos_<?php echo Module::block_id();?>" class="galleryview">
		<!--LIST:items-->
		<div class="panel">
			<a target="_blank" <?php if([[=items.url=]]){ echo 'href="'.[[=items.url=]].'"'; }?> >
				<img src="[[|items.image_url|]]" style="'<?php echo Module::get_setting('internal_css');?>" alt="<?php echo substr([[=items.image_url=]],20); ?>"/>
			</a>
		  </div>
		<!--/LIST:items-->
	</div>
</div>
<?php if(User::can_admin(MODULE_MEDIAADMIN,ANY_CATEGORY)){?>
<div align="center">[<a target="_blank" href="<?php echo Url::build('manage_advertisment',array('page_id'=>Module::$current->data['page_id'],'region'=>Module::$current->data['name']))?>">[[.adv_list.]]</a>]&nbsp;[<a target="_blank" href="<?php echo Url::build('manage_advertisment',array('cmd'=>'advertisment','page_id'=>Module::$current->data['page_id'],'region'=>Module::$current->data['name']))?>">[[.add_adv.]]</a>]</div>
<?php }?>
