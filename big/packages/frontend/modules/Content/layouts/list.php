<div class="newsdetail-bound">
	<!--IF:cond([[=name=]] and isset([[=name=]]))-->
	<div class="newsdetail-bound-content">
    	<div class="newsdetail-time"><?php echo Portal::language(date('D',[[=time=]])).', '.Portal::language('day').' '.date('d/m/Y H:s A',[[=time=]]);?></div>
		<div class="newsdetail-name">[[|name|]]</div>
		<div class="newsdetail-brief"><?php echo strip_tags([[=brief=]]);?></div>
		<div class="newsdetail-description">[[|description|]]</div>
		<div align="right" class="admin-edit">
		<?php if(User::can_edit(MODULE_NEWSADMIN,ANY_CATEGORY)){?>
			[ <a style="color:#FF0000" href="<?php echo Url::build('news_admin',array('cmd'=>'edit','type'=>'NEWS','category_id'=>[[=category_id=]],'id'=>[[=id=]]))?>" target="_blank">[[.edit.]]</a> ]
		<?php }?>
		<?php if(User::can_delete(MODULE_NEWSADMIN,ANY_CATEGORY)){?>
			<span>|</span>[ <a style="color:#FF0000" href="<?php echo Url::build('news_admin',array('cmd'=>'delete_id','type'=>'NEWS','category_id'=>[[=category_id=]],'id'=>[[=id=]]))?>" target="_blank">[[.delete.]]</a> ]
		<?php }?>
		</div>
		<div class="clear"></div>
	</div>
	<!--IF:cond_url(isset([[=file=]]) and file_exists([[=file=]]))-->
	<div class="newsdetail-attachment"><a href="[[|file|]]">< [[.download_attachment.]] ></a></div>
	<!--/IF:cond_url-->
	<!--ELSE-->
	<div class="newsdetail-bound-content">
		<div class="not-exist-id">[[.data_is_updating.]]</div>
	</div>
	<!--/IF:cond-->
	<div class="clear"></div>
	<!--IF:cond1(isset([[=item_newer=]]) and [[=item_newer=]])-->
	<div class="newsdetail-other-item-buond">
		<div class="newsdetail-other-item-title">[[.news_newer.]]: </div>
		<!--LIST:item_newer-->
		<div class="newsdetail-other-item">
			<ul>
				<li><a href="<?php echo Url::build('xem-trang-tin',array('name_id'=>[[=item_newer.name_id=]]),REWRITE);?>"><?php echo str_replace(array('&nbsp;','+','-'),'',[[=item_newer.name=]]);?></a> (<?php echo date('d/m/y',[[=item_newer.time=]])?>)</li>
			</ul>
		</div>
		<!--/LIST:item_newer-->
	</div>
	<!--/IF:cond1-->
	<!--IF:cond1(isset([[=item_related=]]) and [[=item_related=]])-->
	<div class="newsdetail-other-item-buond">
		<div class="newsdetail-other-item-title">[[.old_news.]]: </div>
		<!--LIST:item_related-->
		<div class="newsdetail-other-item">
			<ul>
				<li><a href="<?php echo Url::build('xem-trang-tin',array('name_id'=>[[=item_related.name_id=]]),REWRITE);?>"><?php echo str_replace(array('&nbsp;','+','-'),'',[[=item_related.name=]]);?></a> (<?php echo date('d/m/y',[[=item_related.time=]])?>)</li>
			</ul>
		</div>
		<!--/LIST:item_related-->
	</div>
	<!--/IF:cond1-->
</div>
