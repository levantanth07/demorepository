<fieldset id="toolbar">
 	<div id="toolbar-title">
		Bình luận của khách hàng <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <td id="toolbar-save"  align="center"><a onclick="EditManageComment.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
		  <td id="toolbar-cancel"  align="center"><a href="<?php echo Url::build_current(array());?>"> <span title="New"> </span> [[.Cancel.]] </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
 </fieldset>
  <br clear="all">
<fieldset id="toolbar">
	<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
	<form name="EditManageComment" id="EditManageComment" method="post" enctype="multipart/form-data">
		<table cellspacing="4" cellpadding="4" border="0" width="100%" style="background-color:#FFFFFF;">
		<tr>
		  <td valign="top">
				<table  cellpadding="4" cellspacing="0" border="0" width="100%" style="background-color:#F9F9F9;border:1px solid #D5D5D5" align="center">
					<tr>
						<td>
						<div class="tab-pane-1" id="tab-pane-category">
						<div class="tab-page" id="tab-page-category-1">
							<div class="form_input_label">Tiêu đề(<span class="require">*</span>)</div>
							<div class="form_input">
								 <input name="title" type="text" id="title" class="input" style="width:60%"  />
							</div>
							<div class="form_input_label">Nội dung(<span class="require">*</span>)</div>
							<div class="form_input">
								<textarea id="content" name="content" cols="75" rows="20" style="width:99%; height:350px;overflow:hidden"><?php echo Url::get('content','');?></textarea><br />
								<script>advance_mce('content');</script>
							</div>
						</div>
						<!--/LIST:languages-->
						</div>
						</td>
				   </tr>
				</table>
			</td>
			<td valign="top" style="width:320px;">
				<div id="panel">
					<div id="panel_1"  style="margin-top:8px;">
					<span>[[.Parameters_article.]]</span>
					<table cellpadding="6" cellspacing="0" width="100%" border="1" bordercolor="#E9E9E9">
						<tr>
							<td align="right" width="50%">Hiển thị</td>
							<td align="left"><input  name="publish" type="checkbox" id="publish" value="1" <?php echo Url::get('publish')?'checked':''?>></td>
						</tr>
						<tr>
							<td align="right">[[.position.]]</td>
							<td align="left"><input name="position" type="text" id="position" class="input-large"></td>
						</tr>
            <tr>
							<td align="right" width="50%">Điện thoại</td>
							<td align="left"><input name="phone" type="text" id="phone" /></td>
						</tr>
            <tr>
							<td align="right" width="50%">Email</td>
							<td align="left"><input name="email" type="text" id="email" /></td>
						</tr>
            <tr>
							<td align="right" width="50%">Họ và tên</td>
							<td align="left"><input name="full_name" type="text" id="full_name" /></td>
						</tr>
					</table>
					</div>
				</div>
			</td>
			</tr>
			</table>
		</tr>
		</table>
	</form>
</fieldset>