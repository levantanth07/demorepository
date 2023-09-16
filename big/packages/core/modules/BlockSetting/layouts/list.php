<?php
$title = (URL::get('cmd')=='delete')?Portal::language('delete_title'):Portal::language('list_title');
$action = (URL::get('cmd')=='delete')?'delete':'list';
System::set_page_title(Portal::get_setting('website_title','').' '.$title);?>
<TEXTAREA ID="holdtext" STYLE="display:none;"></TEXTAREA>
<h1>Thiết lập [[|name|]] </h1>
<table width="100%"><tr><td style="font-size:16px;"><a target="_blank" href="<?php echo URL::build([[=page_name=]]);?>"><?php echo ucfirst([[=region=]]);?> [[.of.]] [[|page_name|]]</a></td><td align="right"><a target="_blank" href="<?php echo URL::build('module_setting',array('module_id'=>[[=module_id=]]));?>">[[.module_setting.]]</a></td></tr></table><br />
<div class="row">
    <div class="col-md-12">
        <form name="ListBlockSettingForm" method="post">
            <?php if(Form::$current->is_error())
            {
                ?>		<strong>B&#225;o l&#7895;i</strong><br>
                <?php echo Form::$current->error_messages();?><br>
                <?php
            }
            ?>
            <table class="table">
                <tr>
                    <td id="notice" align="right" style="color:#FF3300;font-weight:bold;display:none;">Dữ liệu đã được lưu...!</td>
                    <td align="right">
                        <input name="save" type="submit" value="Cập nhật" class="btn btn-primary">
                    </td>
                </tr>
            </table>
            <div class="input-group">
                <span class="input-group-addon">Nhập Tên:</span> <input name="name" type="text" id="name" class="form-control" />
            </div>
            <hr>
            <?php $column = 1;?>
            <div class="row">
                <div class="tab-pane-1 col-md-12" id="tab-pane-item_type_field">
                    <!--LIST:groups-->
                    <div class="tab-page" id="tab-page-item_type_field-[[|groups.name|]]" style="height:100%;z-index:10;">
                        <h3 class="tab">[[|groups.name|]]</h3>
                        <?php
                        $first = true;
                        ?>
                        <div>
                                    <!--LIST:groups.items-->
                                    <?php if([[=groups.items.group_column=]] != 1)
                            {
                                echo '</div>';
                            }elseif(!$first)
                            {
                                echo '</div>';
                                echo '<div>';
                            }
                            else
                            {
                                $first = false;
                            }
                            ?>
                            <p>
                                <a id="anchor_[[|groups.items.id|]]"></a>
                                <!--IF:inline([[=groups.items.style=]]==1)-->
                            <div style="display:inline;width:250px;" title="[[|groups.items.id|]]" onclick="holdtext.innerText = '[[|groups.items.id|]]';Copied = holdtext.createTextRange(); Copied.execCommand('Copy');">
                                <strong>[[|groups.items.name|]]</strong>
                            </div>
                            <!--ELSE-->
                            <span style="font-weight:bold;font-size:14px" title="[[|groups.items.id|]]" onclick="holdtext.innerText = '[[|groups.items.id|]]';Copied = holdtext.createTextRange(); Copied.execCommand('Copy');">+ [[|groups.items.name|]]</span><br />
                            <!--IF:description([[=groups.items.description=]]!="")-->
                            <p>[[|groups.items.description|]]</p>
                            <!--/IF:description-->
                            <!--/IF:inline-->
                            <pre>
                                <?php echo [[=groups.items.value=]];?>
                            </pre>
                            <!--IF:inline([[=groups.items.style=]]==1)-->
                            <!--IF:description([[=groups.items.description=]]!="")-->
                            <p>[[|groups.items.description|]]</p>
                            <!--/IF:description-->
                            <!--/IF:inline-->
                            </p>
                            <!--/LIST:groups.items-->
                    </div>
                    <!--/LIST:groups-->
                </div>
            </div>
        </form>
    </div>
</div>
<?php if(Url::get('suss')){?>
    <script type="text/javascript">
        jQuery(function(){
            /*jQuery('#ListBlockSettingForm').submit(function(){
             jQuery('#notice').show().fadeOut(3000);
             });*/
        });
    </script>
<?php } ?>
