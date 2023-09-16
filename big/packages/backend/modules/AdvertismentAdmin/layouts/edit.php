<fieldset id="toolbar">
 	<div id="toolbar-title">
		[[.advertisment_admin.]] <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <td id="toolbar-save"  align="center"><a onclick="EditAdvertismentAdmin.submit();"> <span title="Save"> </span> [[.Save.]] </a> </td>
		  <td id="toolbar-cancel"  align="center"><a href="<?php echo Url::build_current();?>"> <span title="New"> </span> [[.Cancel.]] </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
 </fieldset>
  <br clear="all">
<fieldset id="toolbar">
	<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
	<form name="EditAdvertismentAdmin" id="EditAdvertismentAdmin" method="post" enctype="multipart/form-data">
	<div class="col-md-5">
					<table width="100%" style="border: 1px dashed silver;margin-top:-2px;" cellpadding="4" cellspacing="2">
					<tr>
						<td>[[.Hitcount.]]</td>
						<td><?php echo Url::get('hitcount','0');?></td>
					</tr>
					<tr>
						<td>[[.Created.]]</td>
						<td><?php echo date('hh:i d/m/Y',Url::get('time',time()));?></td>
					</tr>
					<tr>
						<td>[[.Modified.]]</td>
						<td><?php echo Url::get('last_time_update')?date('hh:i d/m/Y',Url::get('last_time_update')):'Not modified';?></td>
					</tr>
				</table>
				<div id="panel_1" style="margin-top:8px;">
					<span>[[.Parameters_properties.]]</span>
					<table class="table">
						<tr>
							<td align="right" width="30%">[[.image_url.]]</td>
							<td align="left">
              	<img src="<?php echo Url::get('image_url');?>" class="img-responsive" alt="" onerror="this.src='assets/default/images/no_image.gif'">
								<input name="image_url" type="file" id="image_url" class="file"><div id="delete_image_url"><?php if(Url::get('image_url') and file_exists(Url::get('image_url'))){?>[<a href="<?php echo Url::get('image_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('image_url')));?>" onclick="jQuery('#delete_image_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>
							</td>
						</tr>
						<tr>
							<td align="right">[[.url.]]</td>
							<td align="left"><input name="url" type="text" id="url" class="form-control"></td>
						</tr>
						<tr>
							<td align="right">[[.width.]]</td>
							<td align="left"><input name="width" type="text" id="width" class="form-control"></td>
						</tr>
						<tr>
							<td align="right">[[.height.]]</td>
							<td align="left"><input name="height" type="text" id="height" class="form-control"></td>
						</tr>
						<tr>
							<td align="right">[[.status.]]</td>
							<td align="left"><select name="status" id="status" class="form-control"></select></td>
						</tr>
					</table>
				</div>
      </div>
     <div class="col-md-7">
      <ul class="nav nav-tabs" role="tablist">
        <?php $i=0;?>
        <!--LIST:languages-->
          <li role="presentation" <?php echo ($i==0)?'class="active"':'';?>><a href="#info_tab_[[|languages.id|]]" aria-controls="home" role="tab" data-toggle="tab"><img src="[[|languages.icon_url|]]" alt="[[|languages.name|]]" /> [[|languages.name|]]</a></li>
        <?php $i++;?>
        <!--/LIST:languages-->
        </ul>
        <?php $i=0;?>
      <div class="tab-content">
      <!--LIST:languages-->
      <div role="tabpanel" class="tab-pane  <?php echo ($i==0)?'active':'';?>" id="info_tab_[[|languages.id|]]">
        <div class="form-group">
        	<label>[[.name.]] (<span class="require">*</span>)</label>
          <input name="name_[[|languages.id|]]" type="text" id="name_[[|languages.id|]]" class="form-control"/>
        </div>
        <div class="form-group">
        	<label>[[.description.]]</label>
          <textarea id="description_[[|languages.id|]]" name="description_[[|languages.id|]]" rows="12" class="form-control"><?php echo Url::get('description_'.[[=languages.id=]],'');?></textarea>
        </div>
      </div>
      <?php $i++;?>
      <!--/LIST:languages-->
      </div>
    </div>
	</form>
</fieldset>