<table width="100%" border="1" cellpadding="5" cellspacing="0">
  <tr>
    <td width="631" bgcolor="#FFFFCC"><p>[[.edit_container_layout_title.]]</p>
      <hr>
	  <?php echo Form::$current->error_messages();?>      <form name="form1" method="post">
		<textarea name="layout" rows="30" style="width:100%" id="content">[[|layout|]]</textarea>
		<script type="text/javascript">editor_generate("layout");</script>
		<input type="submit" name="Submit" value="[[.change.]]">
      </form>
      <hr></td>
  </tr>
</table>
<hr>
