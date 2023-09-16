<script src="assets/default/css/tabs/tabpane.js" type="text/javascript"></script>
<?php
$title = (URL::get('cmd')=='edit')?'Chỉnh sửa trang':'Thêm trang';
$action = (URL::get('cmd')=='edit')?'edit':'add';
System::set_page_title(Portal::get_setting('website_title','').' '.$title);?>
<div class="container">
    <table class="table">
        <tr>
            <td width="80%"><h2><?php echo $title;?></h2></td>
            <td class="form_title_button"><a href="javascript:void(0)" onclick="EditPageAdminForm.submit();" class="btn btn-primary">save</a></td>
            <td><a href="javascript:void(0)" onclick="location='<?php echo URL::build_current(array('portal_id','package_id'));?>';" class="btn btn-default">back</a></td>
            <?php if($action=='edit'){?>
                <td><a href="javascript:void(0)" onclick="location='<?php echo URL::build_current(array('portal_id','package_id','cmd'=>'delete','id'));?>';" class="btn btn-danger">Delete</a></td><?php }?>
        </tr>
    </table>
    <hr>
    <div class="form_content">
        <?php if(Form::$current->is_error())
        {
            ?>		<strong>B&#225;o l&#7895;i</strong><br>
            <?php echo Form::$current->error_messages();?><br>
            <?php
        }
        ?>
        <form name="EditPageAdminForm" method="post">
            <div class="input-group">
                <span class="input-group-addon">Tên trang</span>
                <input name="name" type="text" id="name" class="form-control">
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
            </div><div class="form_input_label">layout:</div>
            <div class="form_input">
                <select name="layout" id="layout" class="form-control"></select>
            </div>
            <hr>
            <div class="form_input_label">params:</div>
            <div class="form_input">
                <input name="params" type="text" id="params" class="form-control">
            </div>
            <div class="form_input_label">type:</div>
            <div class="form_input">
                <select name="type" id="type" class="form-control"></select>
            </div>
            <div class="form_input_label">cachable:</div>
            <div class="form_input">
                <input name="cachable" id="cachable" type="checkbox" value="1" <?php echo (URL::get('cachable')?'checked':'');?>>
            </div>
            <div class="form_input_label">is_use_sapi:</div>
            <div class="form_input">
                <input name="is_use_sapi" id="is_use_sapi" type="checkbox" value="1" <?php echo (URL::get('is_use_sapi')?'checked':'');?>>
            </div>
            <div class="form_input_label">cache_param:</div>
            <div class="form_input">
                <input name="cache_param" type="text" id="cache_param" style="width:300">
            </div>
            <div class="form_input_label">condition:</div>
            <div class="form_input">
                <textarea name="condition" id="condition" style="width:300px;height:100px"></textarea>
            </div>
            <input type="hidden" value="1" name="confirm_edit"/>
        </form>
    </div>
</div>