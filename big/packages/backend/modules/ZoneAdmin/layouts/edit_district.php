<div class="container">
    <br>
    <div class="box box-default">
        <div class="box-header">
            <div class="title">
                <h3>Update quận/huyện/thành phố/xã của tỉnh [[|province_name|]]</h3> <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
            </div>
            <div class="box-tools pull-right">
                <a class="btn btn-primary" onclick="EditCategoryForm.submit();" > <span title="save"> </span> Ghi lại </a>
                <a class="btn btn-default" href="<?php echo URL::build_current(['province_id']);?>"> <span title="Back"> </span> Quay lại </a>
            </div>
        </div>
        <div class="box-body">
            <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
            <form name="EditCategoryForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="confirm_edit" value="1" />
                <table cellspacing="4" cellpadding="4" border="0" width="100%" style="background-color:#FFFFFF;">
                    <tr>
                        <td valign="top">
                            <table class="table">
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <div>Tên (<span class="require">*</span>)</div>
                                            <input name="district_name" type="text" id="district_name" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <div>CODE (<span class="require">*</span>)</div>
                                            <input name="district_code" type="text" id="district_code" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <div>Tên Viettel (<span class="require">*</span>)</div>
                                            <input name="viettel_district_name" type="text" id="viettel_district_name" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <div>VIETTEL CODE</div>
                                            <input name="viettel_district_code" type="text" id="viettel_district_code" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <div>EMS CODE</div>
                                            <input name="ems_district_code" type="text" id="ems_district_code" class="form-control">
                                        </div>

                                    </td>
                                </tr>
                            </table>
                        </td><td valign="top" style="width:40%">
                            <div id="panel_1" style="margin-top:8px;">
                                <table class="table">
                                    <tr>
                                        <td>[[.type.]]</td>
                                        <td><select name="district_type" id="district_type" class="select-large"></select></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
                <input name="province_id" type="hidden" id="province_id">
            </form>
        </div>
    </div>
</div>