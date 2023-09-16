<div class="home-menu-bound">
	<table cellpadding="15" cellspacing="0" width="100%" border="0" bordercolor="#CCCCCC" class="table-bound">
		<tr>
			<td width="65%"  class="form-title"> [[.component.]] <?php echo [[=name=]]; ?></td>
			<td width="35%" align="right">
			</td>
		</tr>
	</table>
	<div class="home-menu-function-bound">
		<?php $group = false; $i = 0; ?>
		<!--LIST:child-->
			<?php
				if(!isset([[=child.group_name=]]))
				{
					[[=child.group_name=]] = $group;
				}
				if($group != [[=child.group_name=]])
				{
					$group = [[=child.group_name=]];
					$i = 0;
			?>
			<div class="home-menu-group-name"> [[|child.group_name|]] </div>
			<?php
				}
			?>
			<!--IF:cond10(IDStructure::have_child('function',[[=child.structure_id=]]))-->
			<!--ELSE-->
			<div class="home-menu-item" onclick="window.location='[[|child.url|]]'" title="<?php echo Portal::language()==1?[[=child.name_1=]]:[[=child.name_3=]]; ?>">
				<div class="home-menu-item-img">
				<!--IF:cond([[=child.icon_url=]] and file_exists([[=child.icon_url=]]))-->
				<img src="[[|child.icon_url|]]" />
				<!--ELSE-->
				<img src="assets/default/images/menu/folder.png" />
				<!--/IF:cond-->
				</div>
				<div class="home-menu-item-name">
					<a href="<!--IF:cond3([[=child.url=]])-->[[|child.url|]]<!--ELSE-->javascript:void(0)<!--/IF:cond3-->"><?php echo Portal::language()==1?[[=child.name_1=]]:[[=child.name_3=]]; ?></a>
				</div>
			</div>
			<!--/IF:cond10-->
		<?php $i++; if(!($i%5)) echo '<div style="clear:both; font-size:0px; height:0px;"></div>';?>
		<!--/LIST:child-->
	</div>
</div>