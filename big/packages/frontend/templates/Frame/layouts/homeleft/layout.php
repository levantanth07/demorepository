<div class="frame-home-bound" <?php if(Module::get_setting('extra_css_bound')){ echo 'style="'.Module::get_setting('extra_css_bound').'"'; }?> >
    <div class="frame-home-title">
        <div class="fr-ht-bound">
            <div class="fr-ht-b-left">
               <?php { echo Portal::language('title_frame_'.Module::block_id());} ?>
               <!--
               <?php //if(Module::get_setting('frame_title_link')){?>
        			<a href="<?php //echo Module::get_setting('frame_title_link');?>">{{-title-}}</a>
        		<?php //}else{ ?>
        			{{-title-}}
        		<?php //}?>
                -->
            </div>
            <div class="fr-ht-b-right"></div>
        </div><!--End .fr-ht-bound-->
    </div><!--End .frame-home-title-->
    <div style="clear: both;"></div>
    <div class="frame-home-content">{{-content-}}</div>
</div>