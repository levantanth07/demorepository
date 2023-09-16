<script src="packages/core/includes/js/multi_items.js" type="text/javascript"></script>
<span style="display:none">
	<span id="mi_module_table_sample">
		<span id="input_group_#xxxx#" style="width:100%;text-align:left;">
			<input  name="mi_module_table[#xxxx#][id]" type="hidden" id="id_#xxxx#">
			<span class="multi_input">
					<input  name="mi_module_table[#xxxx#][table]" style="width:200px;" type="text" id="table_#xxxx#" >
			</span>
			<span class="multi_input"><span style="width:20;">
				<img src="assets/default/images/buttons/delete.gif" onClick="mi_delete_row($('input_group_#xxxx#'),'mi_module_table','#xxxx#');if(document.all)event.returnValue=false; else return false;" style="cursor:pointer;"/>
			</span></span><br>
		</span>
	</span>
</span>
<?php
$title = (URL::get('cmd')=='edit')?'Sửa module':'Thêm module';
$action = (URL::get('cmd')=='edit')?'edit':'add';
System::set_page_title(Portal::get_setting('website_title','').' '.$title);?>[[|type|]]
<div class="container">
    <table class="table">
        <tr>
            <td width="90%"><h3 class="title"><?php echo $title;?></h3></td>
            <td class="form_title_button"><a href="javascript:void(0)" onclick="EditModuleAdminForm.submit();" class="btn btn-primary">save</a></td>
            <td class="form_title_button">
                <a href="javascript:void(0)" onclick="location='<?php echo URL::build_current();?>';" class="btn btn-default">back</a></td>
            <?php if($action=='edit'){?><td class="form_title_button">
                <a href="javascript:void(0)") onclick="location='<?php echo URL::build_current(array('cmd'=>'delete','id'));?>';" class="btn btn-danger">Delete</a></td><?php }?>
        </tr>
    </table>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="col-xs-8">
                <?php if(Form::$current->is_error())
                {
                    ?>		<strong>B&#225;o l&#7895;i</strong><br>
                    <?php echo Form::$current->error_messages();?><br>
                    <?php
                }
                ?>
                <form name="EditModuleAdminForm" method="post" enctype="multipart/form-data">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">Tên module</span>
                        <input name="name" type="text" id="name" class="form-control" aria-describedby="basic-addon1">
                    </div>
                    <hr>
                    <ul class="nav nav-tabs" role="tablist">
                        <?php $i=0;?>
                        <!--LIST:languages-->
                        <li role="presentation" <?php echo ($i==0)?'class="active"':'';?>><a href="#info_tab_[[|languages.id|]]" aria-controls="home" role="tab" data-toggle="tab"><img src="[[|languages.icon_url|]]" alt="[[|languages.name|]]" /></a></li>
                        <?php $i++;?>
                        <!--/LIST:languages-->
                    </ul>
                    <?php $i=0;?>
                    <div class="tab-content">
                        <!--LIST:languages-->
                        <div role="tabpanel" class="tab-pane  <?php echo ($i==0)?'active':'';?>" id="info_tab_[[|languages.id|]]">
                            <div class="form_input_label">title:</div>
                            <div class="form_input">
                                <input name="title_[[|languages.id|]]" type="text" id="title_[[|languages.id|]]" class="form-control">
                            </div><div class="form_input_label">description:</div>
                            <div class="form_input">
                                <textarea name="description_[[|languages.id|]]" id="description_[[|languages.id|]]" class="form-control" rows="5"></textarea><br />
                            </div>
                        </div>
                        <?php $i++;?>
                        <!--/LIST:languages-->
                    </div>
                    <div class="form_input_label">package:</div>
                    <div class="form_input">
                        <select name="package_id" id="package_id" class="form-control"></select>
                    </div>
                    <div class="form_input_label">type:</div>
                    <div class="form_input">
                        <select name="type" id="type" onchange="if(this.value=='PLUGIN' || this.value=='WRAPPER')$('action_info').style.display='';else $('action_info').style.display='none';if(this.value=='PLUGIN')$('plugin_action_info').style.display='';else $('plugin_action_info').style.display='none';" class="form-control"></select>
                    </div>
                    <div id="action_info" <?php if(URL::get('type')!='PLUGIN' or URL::get('type')!='WRAPPER')echo 'style="display:none"';?>>
                        <div id="plugin_action_info" <?php if(URL::get('type')!='PLUGIN')echo 'style="display:none"';?>>
                            <div class="form_input_label">action:</div>
                            <select name="action" id="action" class="form-control"></select> </div>
                        on<select name="action_module_id" id="action_module_id"></select>
                    </div>
                    <div class="form_input_label">use_dblclick:</div>
                    <div class="form_input">
                        <input name="use_dblclick" id="use_dblclick" type="checkbox" value="1" <?php echo (URL::get('use_dblclick')?'checked':'');?>>
                    </div>
                    <div class="form-group hide">
                        <div class="form_input_label">Update_setting_code:</div>
                        <div class="form_input">
                            <textarea name="update_setting_code" id="update_setting_code" cols="80" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="form-group hide">
                        <div class="form_input_label">Create_block_code:</div>
                        <div class="form_input">
                            <textarea name="create_block_code" id="create_block_code" cols="80" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="form-group hide">
                        <div class="form_input_label">Destroy_block_code:</div>
                        <div class="form_input">
                            <textarea name="destroy_block_code" id="destroy_block_code" cols="80" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="form-group hide">
                        <fieldset><legend>module_table</legend>
                            <span id="mi_module_table_all_elems" style="text-align:left;">
					<span>
						<span class="multi_input_header"><span style="width:200;">table</span></span>
						<span class="multi_input_header"><span style="width:20;"><img src="assets/default/images/spacer.gif"/></span></span>
						<br>
					</span>
				</span>
                            <input type="button" value="   Add   " onclick="mi_add_new_row('mi_module_table');">
                        </fieldset>
                    </div>
                    <div class="form_input_label">image_url:
                        <!--IF:cond(Url::get('id') and ([[=image_url=]]))-->
                        <br><img src="[[|image_url|]]">
                        <!--/IF:cond-->
                    </div>
                    <div class="form_input">
                        <input name="image_url" type="file" id="image_url">
                    </div>
                    <hr>
                    <input type="hidden" value="1" name="confirm_edit"/>
                </form>
            </div>
            <div class="col-xs-4">
                <strong>Đang cắm trong các trang:</strong>
                <ul class="list-group">
                    <!--LIST:using_pages-->
                    <li class="list-group-item"><a target="_blank" href="?page=[[|using_pages.name|]]" class="card-link"><strong>[[|using_pages.name|]]</strong></a> [<a target="_blank" href="<?php echo URL::build_current(array('cmd'=>'delete_block','block_id'=>[[=using_pages.id=]]));?>">delete block</a>]</li>
                    <!--/LIST:using_pages-->
                </ul>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
mi_init_rows('mi_module_table',
	<?php if(isset($_REQUEST['mi_module_table']))
	{
		echo MiString::array2js($_REQUEST['mi_module_table']);
	}
	else
	{
		echo '{}';
	}
	?>);
</script>