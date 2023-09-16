<div class="frame-default-bound" <?php if(Module::get_setting('extra_css_bound')){ echo 'style="'.Module::get_setting('extra_css_bound').'"'; }?> >
    <div class="frame-default-title">
        <div class="frame-default-title-left">
            <div class="frame-default-title-right">
            	<div class="frame-default-title-center">
				<?php if(Module::get_setting('frame_icon_title') and file_exists(Module::get_setting('frame_icon_title'))){?><span class="frame-default-icon"><img src="<?php echo Module::get_setting('frame_icon_title');?>" /></span><?php }?>
                <?php if(Module::get_setting('frame_title_link')){?>
                    <a href="<?php echo Module::get_setting('frame_title_link');?>" class="frame-title-link">{{-title-}}</a>
                <?php }else{ ?>
                    {{-title-}}
                <?php }?>
                </div>
            </div>
        </div>
    </div>
    <div class="frame-default-content">
        <div class="frame-default-content-left">
            <div class="frame-default-content-right">{{-content-}}</div></div></div>
    <div class="frame-default-bottom">
        <div class="frame-default-bottom-left"><div class="frame-default-bottom-right"></div></div>
    </div>
</div>