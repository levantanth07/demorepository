<fieldset id="toolbar">
 	<div id="toolbar-title">
		<?php echo Portal::language(Url::get('page'));?> <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		 <?php if(User::can_edit(false,ANY_CATEGORY)){?> <td id="toolbar-save"  align="center"><a onclick="EditMediaAdmin.submit();"> <span title="Save"> </span> [[.save.]] </a> </td><?php }?>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-cancel"  align="center"><a href="<?php echo Url::build_current(array());?>"> <span title="New"> </span> [[.cancel.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
 </fieldset>
  <br clear="all">
<fieldset id="toolbar">
	<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
	<form name="EditMediaAdmin" id="EditMediaAdmin" method="post" enctype="multipart/form-data">
	<div class="row">
  	<div class="col-md-6">
					<table width="100%" style="border: 1px dashed silver;margin-top:-2px;" cellpadding="4" cellspacing="2">
					<tr>
					  <td><b>[[.Rating.]]</b></td>
					  <td><?php echo Url::get('rating','0');?></td>
					  </tr>
					<tr>
						<td><b>[[.Hitcount.]]</b></td>
						<td><?php echo Url::get('hitcount','0');?></td>
					</tr>
					<tr>
						<td><b>[[.Created.]]</b></td>
						<td><?php echo date('h:i d/m/Y',Url::get('time',time()));?></td>
					</tr>
					<tr>
						<td><b>[[.Modified.]]</b></td>
						<td><?php echo Url::get('last_time_update')?date('hh:i d/m/Y',Url::get('last_time_update')):'Not modified';?></td>
					</tr>
				</table>
				<div id="panel_1" style="margin-top:8px;">
					<table cellpadding="4" cellspacing="0" width="100%" border="1" bordercolor="#E9E9E9">
						<tr>
							<td align="right">[[.category.]]</td>
							<td align="left"><select name="category_id" id="category_id" class="form-control"></select></td>
						</tr>
                        <?php if(Url::get('page')=='clip_admin'){?>
						<tr>
							<td align="right">[[.product.]]</td>
							<td align="left"><select name="product_id" id="product_id" class="form-control"></select></td>
						</tr>
                        <?php }?>
						<tr>
							<td align="right">[[.image_url.]]</td>
							<td align="left">
								<input name="image_url" type="file" id="image_url" class="form-control"><div id="delete_image_url"><?php if(Url::get('image_url') and file_exists(Url::get('image_url'))){?>[<a href="<?php echo Url::get('image_url');?>" target="_blank" style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('image_url')));?>" onclick="jQuery('#delete_image_url').html('');" target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php }?></div>
							</td>
						</tr>
                        <tr style="display:none;">
                        	<td align="right">[[.image_url_multiple.]]</td>
                            <td align="left">
								<input name="image_url_detail[]" type="file" id="image_url_detail[]" class="form-control" multiple>
							</td>
                        </tr>
                        <!--LIST:temp2-->
                       <?php if ((Url::get('cmd')=='edit') and Url::get('id') and file_exists([[=temp2.image_url=]]) ){ ?>
                        <tr id="delete_image_detail_[[|temp2.id|]]">
                        	<td>
                        	</td>
                            <td>
                            <div>
                                <a href="[[|temp2.image_url|]]" style="color:#FF0000" target="_blank" />[[[.view.]]] </a>
                                <a href="<?php echo Url::build_current(array('cmd'=>'unlink2','id_img'=>[[=temp2.id=]],'link'=>[[=temp2.image_url=]]));?>" onclick="jQuery('#delete_image_detail_[[|temp2.id|]]').html('');" target="_blank" style="color:#FF0000" />[[[.delete.]]]</a>
                                <?php echo substr([[=temp2.image_url=]],20) ?>
                            </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <!--/LIST:temp2-->
						<tr>
							<td align="right">[[.url.]]</td>
							<td align="left"><input name="url" type="text" id="url" class="form-control"></td>
						</tr>
            <tr>
						  <td align="right">Mã nhúng</td>
						  <td align="left"><textarea name="embed" id="embed" class="form-control" style=";height:100px;"></textarea></td>
					  </tr>
						<tr>
							<td align="right">[[.status.]]</td>
							<td align="left"><select name="status" id="status" class="select"></select></td>
						</tr>
						<tr style="display:none">
							<td align="right">[[.position.]]</td>
							<td align="left"><input name="position" type="text" id="position" class="form-control" size="17"></td>
						</tr>
						<tr style="display:none">
							<td align="right">[[.hitcount.]]</td>
							<td align="left"><input name="hitcount" type="text" id="hitcount" class="form-control" size="17"></td>
						</tr>
					</table>
				</div>
				<div class="row">
        	<div class="col-md-12">
					<h4>Cấu hình SEO</h4>
					<div>
          <ul class="nav nav-tabs" role="tablist">
          <?php $i=0;?>
          <!--LIST:languages-->
            <li role="presentation" <?php echo ($i==0)?'class="active"':'';?>><a href="#seo_tab_content_[[|languages.id|]]" aria-controls="home" role="tab" data-toggle="tab"><img src="[[|languages.icon_url|]]" alt="[[|languages.name|]]" /> [[|languages.name|]]</a></li>
          <?php $i++;?>
          <!--/LIST:languages-->
          </ul>
          <!-- Tab panes -->
          <div class="tab-content"><br>
          <?php $i=0;?>
           <!--LIST:languages-->
            <div role="tabpanel" class="tab-pane  <?php echo ($i==0)?'active':'';?>" id="seo_tab_content_[[|languages.id|]]">
              <div class="input-group" style="width:100%;">
                <span class="input-group-addon" style="width:30%;">Meta title [[|languages.name|]](60 ký tự)</span>
                <input name="seo_title_[[|languages.id|]]" type="text" id="seo_title_[[|languages.id|]]" class="form-control">
              </div>
              <div class="input-group" style="width:100%;">
                <span class="input-group-addon" style="width:30%;">Meta keywords [[|languages.name|]]</span>
                <input name="seo_keywords_[[|languages.id|]]" type="text" id="seo_keywords_[[|languages.id|]]" class="form-control">
              </div>
              <div class="input-group" style="width:100%;">
                <span class="input-group-addon textarea-addon" style="width:30%;">Meta description [[|languages.name|]]<br>(150 ký tự)</span>
                <textarea name="seo_description_[[|languages.id|]]" id="seo_description_[[|languages.id|]]" class="form-control" rows="5" cols="22"></textarea>
              </div>
            </div>
             <?php $i++;?>
            <!--/LIST:languages-->
          </div>
          <div style="margin-top:8px;">
            <div class="input-group" style="width:100%;">
              <span class="input-group-addon textarea-addon" style="width:30%;">Tags</span>
              <textarea name="tags" id="tags" class="form-control" rows="5" cols="22"></textarea>
            </div>
          </div>
        </div>
        </div>
				</div>
      </div>
			<div class="col-md-6">
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
        	<h2></h2>
          <div class="form-group">
          	 <label>[[.name.]] (<span class="require">*</span>)</label>
             <input name="name_[[|languages.id|]]" type="text" id="name_[[|languages.id|]]" class="form-control"  />
          </div>
          <div class="form-group">
          	<label>[[.description.]]</label>
            <textarea id="description_[[|languages.id|]]" name="description_[[|languages.id|]]" cols="75" rows="20" class="form-control"><?php echo Url::get('description_'.[[=languages.id=]],'');?></textarea>
          </div>
        </div>
         <?php $i++;?>
        <!--/LIST:languages-->
       </div>
			</div>
		</div>	
	</form>
</fieldset>