<fieldset>
	<legend>[[.content_manage_system.]]</legend>
 	<div>
		[[.manage_news.]] <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div align="right">
	<table>
		<tr>
		  <td id="toolbar-trash" align="center"><a onclick="DeleteNewsAdmin.submit();"> <span title="Delete"> </span> [[.delete.]] </a> </td>
		  <td id="toolbar-cancel" align="center"><a href="<?php echo Url::build_current();?>"> <span title="Cancel"> </span> [[.Cancel.]] </a> </td>
		  <td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td>
		</tr>
	</table>
    </div>
</fieldset>
<br clear="all">
<form method="post" name="DeleteNewsAdmin">
<div>
	<h3><?php echo $this->map['item']['name']?></h3>
    <strong><?php echo $this->map['item']['brief']?></strong>
    <p><?php echo $this->map['item']['description']?></p>
</div>
</form>