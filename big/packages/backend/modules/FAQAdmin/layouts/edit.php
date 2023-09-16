<script src="assets/default/css/tabs/tabpane.js" type="text/javascript"></script>
<fieldset id="toolbar">
 	<div id="toolbar-title">
		[[.faq_admin.]] <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <td id="toolbar-preview"  align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Move"> </span> [[.Preview.]] </a> </td>
		  <td id="toolbar-save"  align="center"><a onclick="EditFAQAdmin.submit();"> <span title="Edit"> </span> [[.Save.]] </a> </td>
		  <td id="toolbar-cancel"  align="center"><a href="<?php echo Url::build_current(array());?>"> <span title="New"> </span> [[.Cancel.]] </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
 </fieldset>
  <br clear="all">
<fieldset id="toolbar">
	<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
	<form name="EditFAQAdmin" id="EditFAQAdmin" method="post" enctype="multipart/form-data">
		<table cellspacing="4" cellpadding="4" border="0" width="100%" style="background-color:#FFFFFF;">
		<tr>
		  <td valign="top">
				<table  cellpadding="4" cellspacing="0" border="0" width="100%" style="background-color:#F9F9F9;border:1px solid #D5D5D5" align="center">
					<tr>
						<td>
						<div class="tab-pane-1" id="tab-pane-category">
						<!--LIST:languages-->
						<div class="tab-page" id="tab-page-category-[[|languages.id|]]">
							<h2 class="tab">[[|languages.name|]]</h2>
							<div class="form_input_label">[[.name_question.]] (<span class="require">*</span>)</div>
							<div class="form_input">
								 <input name="name_[[|languages.id|]]" type="text" id="name_[[|languages.id|]]" class="input" style="width:60%"  />
							</div>
							<div class="form_input_label">[[.brief_question.]]</div>
							<div class="form_input">
								<textarea id="brief_[[|languages.id|]]" name="brief_[[|languages.id|]]" cols="75" rows="20" style="width:99%; height:200px;overflow:hidden" ><?php echo Url::get('brief_'.[[=languages.id=]],'');?></textarea><br />
								<script>simple_mce('brief_[[|languages.id|]]');</script>
							</div>
							<div class="form_input_label">[[.description_question.]]</div>
							<div class="form_input">
								<textarea id="description_[[|languages.id|]]" name="description_[[|languages.id|]]" cols="75" rows="20" style="width:99%; height:350px;overflow:hidden"><?php echo Url::get('description_'.[[=languages.id=]],'');?></textarea><br />
								<script>advance_mce('description_[[|languages.id|]]');</script>
							</div>
						</div>
						<!--/LIST:languages-->
						</div>
						</td>
				   </tr>
				</table>
			</td>
			<td valign="top" style="width:320px;">
				<table width="100%" style="border: 1px dashed silver;" cellpadding="4" cellspacing="2">
				<tr>
					<td><strong>[[.Status.]]</strong></td>
					<td><?php echo Url::get('status','0');?></td>
				</tr>
				<tr>
				  <td><strong>[[.Rating.]]</strong></td>
				  <td><?php echo Url::get('rating','0');?></td>
				  </tr>
				<tr>
					<td><strong>[[.Hitcount.]]</strong></td>
					<td><?php echo Url::get('hitcount','0');?></td>
				</tr>
				<tr>
					<td><strong>[[.Created.]]</strong></td>
					<td><?php echo date('h\h:i d/m/Y',Url::get('time',time()));?></td>
				</tr>
				<tr>
					<td><strong>[[.Modified.]]</strong></td>
					<td><?php echo Url::get('last_time_update')?date('h\h:i d/m/Y',Url::get('last_time_update')):'Not modified';?></td>
				</tr>
				</table>
				<div id="panel">
					<div id="panel_1"  style="margin-top:8px;">
					<span>[[.Parameters_article.]]</span>
					<table cellpadding="6" cellspacing="0" width="100%" border="1" bordercolor="#E9E9E9">
						<tr>
							<td align="right" width="50%">[[.status.]]</td>
							<td align="left"><select name="status" id="status" class="select-large"></select></td>
						</tr>
						<tr>
							<td align="right">[[.position.]]</td>
							<td align="left"><input name="position" type="text" id="position" class="input-large"></td>
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