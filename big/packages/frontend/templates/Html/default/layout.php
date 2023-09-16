<object id="pingbox55ud52iciog×?" type="application/x-shockwave-flash" data="http://wgweb.msg.yahoo.com/badge/Pingbox.swf" width="193" height="420" style="margin-bottom:5px;">
	<param name="movie" value="http://wgweb.msg.yahoo.com/badge/Pingbox.swf" />
    <param name="allowScriptAccess" value="always" />
    <param name="wmode" value="transparent" />
    <param name="flashvars" value="wid=z0sEfVavUXfp_Zz1uJE1lr9zeSM.uUOGxjh4" />
</object>
<?php if(User::is_admin()){?>
<div align="right">[ <a class="edit" href="<?php echo Url::build('block_setting',array('block_id'=>Module::$current->data['id']));?>"  target="_blank"><?php echo Portal::language('edit');?></a>]</div>
<?php }?>