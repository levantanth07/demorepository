
<div class="supportonline-bound" <?php if(Module::get_setting('extra_css_bound')){ echo 'style="'.Module::get_setting('extra_css_bound').'"'; }?>>
	<div class="supportonline-title">
    	<span><?php { echo Portal::language('title_frame_'.Module::block_id());} ?></span>
    </div>
    <div class="supportonline-content-bound">
    	{{-content-}}
    </div>
</div>
