<table width="100%"  border="0" cellpadding="3" bgcolor="#FFFFFF"
		ondragenter="event.returnValue = false;event.dataTransfer.dropEffect = 'copy';"
		ondrop="if(!block_moved){event.returnValue = false;event.dataTransfer.dropEffect = 'copy';if(event.dataTransfer.getData('Text')<0){ location = '<?php echo Url::build_current(array('id','container_id'));?>&region=[[|name|]]&module_id='+(-event.dataTransfer.getData('Text'));}else location = '<?php echo Url::build_current(array('id','container_id','cmd'=>'move_block'));?>&region=[[|name|]]&block_id='+event.dataTransfer.getData('Text');}"
		ondragover="event.returnValue = false;event.dataTransfer.dropEffect = 'copy';">
  <tr valign="top">
    <td>
	<?php echo Draw::begin_round_table();?>	<span style="text-transform:uppercase;color:#000066">&nbsp;&nbsp;::&nbsp;&nbsp;&nbsp;<b>[[|name|]]</b>&nbsp;&nbsp;&nbsp;&nbsp;::</span>
	<fieldset>
	<table width="100%" cellpadding="5" cellspacing="0" >
      <tr valign="top">
        <td valign="top" bgcolor="#FFFFFF">
		<table width="100%" border="0" cellpadding="3" cellspacing="0">
            <tr valign="top">
              <td height="16" colspan="6" valign="top">&nbsp;</td>
            </tr>
			<!--LIST:items-->
            <tr valign="top" <?php echo Draw::hover('#CCCCCC');?>>
              <td height="19" valign="top" nowrap><strong><font color="black">&raquo;&nbsp;&nbsp;</font></strong></td>
              <td align="left" valign="top" nowrap><strong><a href="[[|items.href|]]" ondragstart="event.dataTransfer.setData('Text', '[[|items.id|]]');event.dataTransfer.effectAllowed = 'copy'; ">[[|items.name|]]<!--IF:name([[=items.block_name=]])--><span style="font-size:10px"> - [[|items.block_name|]]</span><!--/IF:name--></a>&nbsp;&nbsp;&nbsp;</strong></td>
              <td valign="top"><a href="<?php echo URL::build_current(array('cmd'=>'delete'));?>&id=[[|items.id|]]"><strong><img src="assets/default/images/buttons/delete.gif" width="12" height="12" border="0" ></strong></a></td>
              <td valign="top"><strong>[[|items.move_up|]]</strong></td>
              <td valign="top"><strong>[[|items.move_down|]]</strong></td>
            </tr>
			<!--IF:regions([[=items.regions=]])-->
			<tr>
				<td height="16" colspan="5" valign="top">
					[[|items.regions|]]
				</td>
			</tr>
			<!--/IF:regions-->
			<!--/LIST:items-->
            <tr valign="top">
              <td height="16" colspan="5" valign="top">&nbsp;</td>
            </tr>
          </table>
         </td>
      </tr>
	  <tr><td nowrap>
	  <a href="<?php echo Url::build('module',array('page_id'=>$_REQUEST['id'],'container_id'));?>&region=[[|name|]]" class="btn btn-warning">[[.Add_block.]]</a>
	  </td></tr>
    </table>
	</fieldset>
<?php echo Draw::end_round_table();?></td></tr>
    </table>
