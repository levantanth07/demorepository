<div class="container">
    <br>
    <div class="box box-info">
        <div class="box-header">
            <div class="box-title">
                Đối tác <span style="font-size:16px;color:#0B55C4;">[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
            </div>
            <div class="box-tools pull-right">
                <a class="btn btn-primary" onclick="EditPartnerAdmin.submit();"> <i class="fa fa-floppy-o"></i> [[.Save.]] </a>
                <a class="btn btn-danger" href="<?php echo Url::build_current(array());?>"> [[.Cancel.]] </a>
            </div>
        </div>
        <div class="box-body">
            <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
            <form name="EditPartnerAdmin" id="EditPartnerAdmin" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <table class="table">
                            <tr>
                                <td>
                                    <div class="tab-pane-1" id="tab-pane-category">
                                        <div class="tab-page" id="tab-page-category">
                                            <div class="form_input_label"><h3>&nbsp;&nbsp;Tên (<span class="require">*</span>)</h3></div>
                                            <div class="form_input">
                                                <input name="name" type="text" id="name" class="input form-control" style="width:95%;margin:10px 10px 20px; 10px;"  />
                                            </div>

                                            <div class="form_input_label"><h3>&nbsp;&nbsp;Link (<span class="require">*</span>)</h3></div>
                                            <div class="form_input">
                                                <input name="link" type="text" id="link" class="input form-control" style="width:95%;margin:10px 10px 20px; 10px;"  />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table">
                            <tr>
                                <td width="40%" align="right"><strong>[[.Status.]]</strong></td>
                                <td><?php echo Url::get('status','0');?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>[[.Created.]]</strong></td>
                                <td><?php echo date('h\h:i d/m/Y',Url::get('time',time()));?></td>
                            </tr>
                            <tr>
                                <td align="right"><strong>[[.Modified.]]</strong></td>
                                <td><?php echo Url::get('last_time_update')?date('h\h:i d/m/Y',Url::get('last_time_update')):'Not modified';?></td>
                            </tr>
                            <tr>
                                <td align="right">[[.status.]]</td>
                                <td align="left"><select name="status" id="status" class="form-control"></select></td>
                            </tr>
                            <tr>
                                <td align="right">[[.position.]]</td>
                                <td align="left"><input name="position" type="text" id="position" class="form-control"></td>
                            </tr>
                            <tr>
                                <td align="right">[[.images.]]</td>
                                <td align="left">
                                    <input name="small_thumb_url" type="file" id="small_thumb_url" class="form-control">
                                    <div id="delete_small_thumb_url"><?php if (Url::get('small_thumb_url') and file_exists(Url::get('small_thumb_url'))) { ?>[
                                            <a href="<?php echo Url::get('small_thumb_url'); ?>" target="_blank"
                                               style="color:#FF0000">[[.view.]]</a>]&nbsp;[<a
                                                    href="<?php echo Url::build_current(array('cmd' => 'unlink', 'link' => Url::get('small_thumb_url'))); ?>"
                                                    onclick="jQuery('#delete_small_thumb_url').html('');"
                                                    target="_blank" style="color:#FF0000">[[.delete.]]</a>]<?php } ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>