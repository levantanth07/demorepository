<div class="container">
    <br>
    <div class="box box-info">
        <div class="box-header">
            <div id="toolbar-info">
                [[.privilege.]] <span style="font-size:16px;color:#0B55C4;">[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
            </div>
            <div id="toolbar-content">
                <table align="right">
                    <tbody>
                    <tr>
                        <?php if(User::can_edit()){?> <td id="toolbar-save"  align="center"><a onclick="EditPrivilegeForm.submit();"> <span title="Save"> </span> [[.Save.]] </a> </td><?php }?>
                        <?php if(User::can_view()){?><td id="toolbar-cancel"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'list'));?>#"> <span title="Hủy"> </span> Hủy </a> </td><?php }?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-body">
            <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
            <form name="EditPrivilegeForm" method="post"  action="?<?php echo htmlentities($_SERVER['QUERY_STRING']);?>">
                <div class="pd10">
                    Mã quyền <input name="code" type="text" id="code">
                </div>
                <hr>
                <div class="tab-pane-1" id="tab-pane-ecommerce_product">
                    <!--LIST:languages-->
                    <div class="tab-page" id="tab-page-ecommerce_product-[[|languages.id|]]">
                        <h2 class="tab">[[|languages.name|]]</h2>
                        <div class="form-group_label">Tiêu đề:</div>
                        <div class="form-group">
                            <input name="title_[[|languages.id|]]" type="text" id="title_[[|languages.id|]]" class="input"  style="width:300px;" >
                        </div><div class="form-group_label">Mô tả:</div>
                        <div class="form-group">
                            <textarea id="description_[[|languages.id|]]" name="description_[[|languages.id|]]" cols="75" rows="20" style="width:99%; height:295px;overflow:hidden"><?php echo Url::get('description_'.[[=languages.id=]],'');?></textarea><br />
                        </div>
                    </div>
                    <!--/LIST:languages-->
                </div>
                <div class="form-group_label" style="clear:both">Package:</div>
                <div class="form-group">
                    <select name="package_id" id="package_id" class="form-control"></select>
                </div>
                <div class="form-group_label">Trạng thái:</div>
                <div class="form-group">
                    <select name="status" id="status" class="form-control"></select>
                </div>
                <div class="form-group_label">Hàm:</div>
                <div class="form-group">
                    <select name="category_id" id="category_id" class="form-control"></select>
                </div>
                <input type="hidden" value="1" name="confirm_edit"/>
            </form>
        </div>
    </div>
</div>