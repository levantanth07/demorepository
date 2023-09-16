<fieldset id="toolbar">
	<legend>[[.content_manage_system.]]</legend>
 	<div id="toolbar-title">
		[[.manage_news.]] <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content" align="right">
	<table>
	  <tbody>
		<tr>
		  <td id="toolbar-preview"  align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Move"> </span> [[.Preview.]] </a> </td>
		  <td id="toolbar-save"  align="center"><a onclick="EditNewsAdmin.submit();"> <span title="Edit"> </span> [[.Save.]] </a> </td>
		  <td id="toolbar-cancel"  align="center"><a href="<?php echo Url::build_current(array());?>"> <span title="New"> </span> [[.Cancel.]] </a> </td>
		  <td id="toolbar-help" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="Help"> </span> [[.Help.]] </a> </td>
		</tr>
	  </tbody>
	</table>
    </div>
</fieldset>
<br clear="all"/>
<fieldset id="toolbar">
	<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
	<form name="EditNewsAdmin" id="EditNewsAdmin" method="post" enctype="multipart/form-data">
		<table cellspacing="4" cellpadding="4" border="0" width="100%" style="background-color:#FFFFFF;">
		<tr>
		  <td valign="top">
	    <table cellpadding="4" cellspacing="0" border="0" width="100%" style="background-color:#F9F9F9;border:1px solid #D5D5D5">
					<tr>
					  <td width="16%" align="left">[[.category_id.]] (<span class="require">*</span>)</td>
					  <td width="28%" align="left"><select name="category_id" id="category_id" class="select-large"></select></td>
					  <td width="12%" align="left"><?php if(User::can_admin(false,ANY_CATEGORY)){?>[[.publish.]]<?php }?></td>
					  <td width="44%" align="left"><?php if(User::can_admin(false,ANY_CATEGORY)){?><input  name="publish" type="checkbox" value="1" id="publish" <?php if(Url::get('publish')==1){echo 'checked="checked"';}?>><?php }?></td>
				  </tr>
					<tr>
						<td align="left">[[.status.]]</td>
						<td align="left"><select name="status" id="status" class="select-large"></select></td>
						<td align="left">[[.position.]]</td>
						<td align="left"><input name="position" type="text" id="position" class="input"/></td>
					</tr>
				</table>
				<br/>
				<table  cellpadding="4" cellspacing="0" border="0" width="100%" style="background-color:#F9F9F9;border:1px solid #D5D5D5" align="center">
					<tr>
						<td>
						<div class="tab-pane-1" id="tab-pane-category">
						<!--LIST:languages-->
						<div class="tab-page" id="tab-page-category-[[|languages.id|]]">
							<h2 class="tab">[[|languages.name|]]</h2>
							<div class="form_input_label">[[.name.]] (<span class="require">*</span>)</div>
							<div class="form_input">
								 <input name="name_[[|languages.id|]]" type="text" id="name_[[|languages.id|]]" style="width:60%;border:1px solid #CCCCCC;"  />
							</div>
							<div class="form_input_label">[[.brief.]]</div>
							<div class="form_input">
								<textarea id="brief_[[|languages.id|]]" name="brief_[[|languages.id|]]" cols="75" rows="20" style="width:99%; height:200px;overflow:hidden"><?php echo Url::get('brief_'.[[=languages.id=]],'');?></textarea><br />
								<script type="text/javascript">simple_mce('brief_[[|languages.id|]]');</script>
							</div>
							<div class="form_input_label">[[.description.]]</div>
							<div class="form_input">
                                <?php
/*
$sBasePath = $_SERVER['PHP_SELF'] ;
echo $sBasePath;
include_once "fckeditor/fckeditor.php";
$sBasePath =  "/fckeditor/";
//$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "_samples" ) ) ;
$oFCKeditor = new FCKeditor('description_'.[[=languages.id=]]);
$oFCKeditor->BasePath	= $sBasePath ;
$oFCKeditor->Value		= 'fhfdhdfh' ;
$oFCKeditor->Create() ;
*/
                               /*
                                include_once "packages/ckeditor222/ckeditor.php";
                                // Create a class instance.
                                $CKEditor = new CKEditor();
                                // Path to the CKEditor directory.
                                $CKEditor->basePath = 'packages/ckeditor222/';
                                // Create a textarea element and attach CKEditor to it.
                                $CKEditor->editor("description_".[[=languages.id=]], Url::get('description_'.[[=languages.id=]]) );
                                */
                                ?>
                                <textarea id="description_[[|languages.id|]]" name="description_[[|languages.id|]]" cols="75" rows="20" style="width:99%; height:350px;overflow:hidden"><?php echo Url::get('description_'.[[=languages.id=]],'');?></textarea><br />
								<script> advance_mce('description_[[|languages.id|]]');</script>
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
					<span>[[.images.]]</span>
					<table cellpadding="4" cellspacing="0" width="100%" border="1" bordercolor="#E9E9E9">
						<tr>
							<td width="30%" align="right">[[.small_thumb.]]</td>
						    <td width="70%" align="left"><input name="small_thumb_url" type="file" id="small_thumb_url" class="file" size="18"><div id="delete_small_thumb_url"><?php if(Url::get('small_thumb_url') and file_exists(Url::get('small_thumb_url'))){?>[<a href="<?php echo Url::get('small_thumb_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('small_thumb_url')));?>" onclick="jQuery('#delete_small_thumb_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div></td>
						</tr>
						<tr>
							<td width="30%" align="right">[[.image_url.]]</td>
						    <td width="70%" align="left"><input name="image_url" type="file" id="image_url" class="file" size="18"><div id="delete_image_url"><?php if(Url::get('image_url') and file_exists(Url::get('image_url'))){?>[<a href="<?php echo Url::get('image_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('image_url')));?>" onclick="jQuery('#delete_image_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div></td>
						</tr>
						<tr>
							<td width="30%" align="right">[[.attach_file.]]</td>
						    <td width="70%" align="left"><input name="file" type="file" id="file" class="file" size="18"><div id="delete_file"><?php if(Url::get('file') and file_exists(Url::get('file'))){?>[<a href="<?php echo Url::get('file');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('file')));?>" onclick="jQuery('#delete_file').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div></td>
						</tr>
						<tr>
							<td width="30%" align="right">[[.published_date.]]</td>
						    <td width="70%" align="left">
                                <input name="published_date" type="text" id="published_date" />
                            </td>
						</tr>
						<tr>
							<td width="30%" align="right">[[.type_text.]]</td>
						    <td width="70%" align="left">
                                <select name="type_text" id="type_text" class="select-large"></select>
                            </td>
						</tr>
					</table>
					</div>
					<div id="panel_1" style="margin-top:8px;">
					<span>[[.Parameters_article.]]</span>
					<table cellpadding="4" cellspacing="0" width="100%" border="1" bordercolor="#E9E9E9" style="margin-top:2px;">
						<tr>
							<td width="49%" align="right">[[.author.]]</td>
						  <td width="51%" align="left"><input name="author" type="text" id="author" class="input"></td>
						</tr>
						<tr>
							<td width="49%" align="right">[[.hitcount.]]</td>
						  <td width="51%" align="left"><input name="hitcount" type="text" id="hitcount" class="input"></td>
						</tr>
						<!--<tr>
							<td width="49%" align="right">[[.front_page.]]</td>
						  <td width="51%" align="left"><select name="front_page" id="front_page" class="select"></select></td>
						</tr>
						<tr>
							<td width="49%" align="right">[[.show_image_in_detail.]]</td>
						  <td width="51%" align="left"><select name="show_image" id="show_image" class="select"></select></td>
						</tr>
						<tr>
							<td width="49%" align="right">[[.show_print.]]</td>
						    <td width="51%" align="left"><select name="show_print" id="show_print" class="select"></select></td>
						</tr>
						<tr>
							<td width="49%" align="right">[[.show_send_email.]]</td>
						    <td width="51%" align="left"><select name="show_email" id="show_email" class="select"></select></td>
						</tr>
						<tr>
							<td width="49%" align="right">[[.show_time.]]</td>
						  <td width="51%" align="left"><select name="show_time" id="show_time" class="select"></select></td>
						</tr>
						<tr>
							<td width="49%" align="right">[[.show_author.]]</td>
						  <td width="51%" align="left"><select name="show_author" id="show_author" class="select"></select></td>
						</tr>
						<tr>
							<td width="49%" align="right">[[.show_comment.]]</td>
						  <td width="51%" align="left"><select name="show_comment" id="show_comment" class="select"></select></td>
						</tr>-->
					</table>
					</div>
					<div id="panel_1"  style="margin-top:8px;">
					<span>[[.Metadata_information.]]</span>
					<table cellpadding="4" cellspacing="0" width="100%" border="1" bordercolor="#E9E9E9">
						<tr>
							<td width="30%" align="right" valign="top">[[.keywords.]]</td>
						  <td width="70%" align="left"><input name="keywords" type="text" id="keywords" class="input-large" style="height:80px"></td>
						</tr>
						<tr>
							<td width="30%" align="right" valign="top">[[.tags.]]</td>
						  <td width="70%" align="left"><input name="tags" type="text" id="tags" class="input-large" style="height:80px"></td>
						</tr>
					</table>
					</div>
				</div>
			</td>
		</tr>
		</table>
	</form>
</fieldset>
<!--Datepicker-->
<script type="text/javascript">
	jQuery(function() {
		jQuery( "#published_date" ).datepicker();
	});
</script>
