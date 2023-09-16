<script src="packages/core/includes/js/language_tab.js" type="text/javascript"></script>
<table width="100%" border="0" cellpadding="1" cellspacing="0">
  <tr>
    <td width="631" bgcolor="white">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
        <tr>
          <td><img src="<?php echo Portal::template('core');?>/images/OK_1.gif" /></td>
		  <td nowrap="nowrap" width="100%" align="center" background="<?php echo Portal::template('core');?>/images/OK_2.gif"><span style="text-transform:uppercase;color:#000066">&nbsp;&nbsp;::&nbsp;&nbsp;&nbsp;[[.export_module.]]&nbsp;&nbsp;&nbsp;&nbsp;::</span></td>
          <td><img src="<?php echo Portal::template('core');?>/images/OK_3.gif" /></td>
        </tr>
      </table>
	  <?php echo Form::$current->error_messages();?>      <form  method="post" name="addModule" id="addModule">
        <table width="100%" border="0">
          <tr>
            <td width="196" class="lable">[[.module.]]</td>
            <td width="10">:</td>
            <td width="412"><select name="module_id" id="module_id">
            	            </select></td>
          </tr>
		  <tr>
            <td class="lable">[[.code.]]</td>
            <td>:</td>
            <td><textarea cols="80" rows="15">[[|code|]]</textarea></td>
		  </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td><input type="submit" name="Submit" value="[[. export.]]"></td>
          </tr>
        </table>
      </form>
      <hr size="1">
      <p><a style="color:#FF6600" href="<?php echo URL::build_current(array('package_id'));?>" class="lable">Danh s&aacute;ch c&aacute;c module </a></p>
    </td>
  </tr>
</table>
