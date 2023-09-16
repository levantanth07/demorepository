<div class="frame-default-bound" <?php if(Module::get_setting('extra_css_bound')){ echo 'style="'.Module::get_setting('extra_css_bound').'"'; }?> >
    <div class="frame-default-title">
        <div class="frame-default-title-left">
	        <div class="frame-default-title-right">
            	<div class="frame-default-title-center">
					<?php echo Url::get('name'); ?>
                </div>
	        </div>
        </div>
	</div>
    <div class="frame-default-content">
		<div class="frame-default-content-left">
			<div class="frame-default-content-right">{{-content-}}</div></div></div>
    <div class="frame-default-bottom">
		<div class="frame-default-bottom-left"><div class="frame-default-bottom-right"><div class="frame-default-bottom-center"></div></div></div>
	</div>
</div>