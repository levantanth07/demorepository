<?php if(Module::get_setting('use_round_frame')){?>
<!--IF:cond([[=estore_use_frame=]])-->[[|begin_frame|]]<!--ELSE-->
<div class="[[|div_container_class|]]">
<div class="[[|category_title_class|]]">[[|title|]]</div>
<div class="[[|div_content_class|]]">
<!--/IF:cond-->
<?php }?>