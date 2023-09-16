<div class="faq-detail-bound">
	<div class="faq-detail-title-bound"><div class="faq-detail-tilte"><?php if(isset([[=category_name=]])){ echo [[=category_name=]]; }else{ echo Portal::language('news_detail');}?></div></div>
	<!--IF:cond(isset([[=name=]]))-->
	<div class="faq-detail-bound-content">
		<div class="faq-detail-name"><?php echo strip_tags(str_replace(array('&nbsp;','+','-'),'',[[=name=]])); ?></div>
		<div class="faq-detail-description">[[|description|]]</div>
		<div align="right" class="admin-edit">
		<?php if(User::can_edit(MODULE_NEWSADMIN,ANY_CATEGORY)){?>
			[ <a style="color:#FF0000" href="<?php echo Url::build('news_admin',array('cmd'=>'edit','type'=>'FAQ','id'=>[[=id=]]))?>" target="_blank">[[.edit.]]</a> ]
		<?php }?>
		</div>
		<div class="clear"></div>
	</div>
	<!--ELSE-->
	<div class="faq-detail-bound-content">
		<div class="not-exist-id">[[.id_dont_exist.]]</div>
	</div>
	<!--/IF:cond-->
	<!--IF:cond1(isset([[=item_related=]]) and [[=item_related=]])-->
	<div class="faq-detail-other-item-buond">
		<div class="faq-detail-other-item-title">[[.other_question.]]: </div>
		<!--LIST:item_related-->
		<div class="faq-detail-other-item">
			<ul>
				<li><a href="<?php echo Url::build_current(array('name_id'=>[[=item_related.name_id=]]),REWRITE);?>"><?php echo str_replace(array('&nbsp;','+','-'),'',[[=item_related.name=]]);?></a></li>
			</ul>
		</div>
		<!--/LIST:item_related-->
	</div>
	<!--/IF:cond1-->
	<div class="clear"></div>
</div>
