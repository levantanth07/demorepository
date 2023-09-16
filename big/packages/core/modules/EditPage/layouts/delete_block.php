<table width="100%" border="1" cellpadding="5" cellspacing="0">
  <tr>
    <td width="631" bgcolor="#FFFFCC"><p>[[.delete_block_title.]]</p>
      <hr>
	  <?php echo Form::$current->error_messages();?>      <form name="form1" method="post">
        <table width="100%" border="0">
          <tr>
            <td width="99">[[.db_module_name.]]</td>
            <td width="10">:</td>
            <td width="496">[[|name|]]</td>
          </tr>
          <tr>
            <td height="31">&nbsp;</td>
            <td>:</td>
            <td><input type="submit" name="Submit" value="   [[.Delete.]]   "></td>
          </tr>
        </table>
      </form>
      <p><a href="<?php echo URL::build_current();?>&id=[[|page_id|]]&container_id=[[|container_id|]]">N&#7897;i dung trang [[|page_name|]]</a></p>
      <hr></td>
  </tr>
</table>
