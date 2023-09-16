 <span style="display:none">
	<span id="mi_privilege_module_sample">
		<div id="input_group_#xxxx#">
			<div class="row">
                <div class="col-md-12" style="border-bottom: 2px solid #FFF;">
                    <div class="row">
                        <input  name="mi_privilege_module[#xxxx#][id]" type="hidden" id="id_#xxxx#">
                        <span class="col-md-3">
                            <input type="text" name="mi_privilege_module[#xxxx#][module_name]"  class="form-control"  id="module_name_#xxxx#" />
                        </span>
                        <span class="col-md-1">
                                <input  type="checkbox" value="1" name="mi_privilege_module[#xxxx#][view]" id="view_#xxxx#">
                        </span>
                        <span  class="col-md-1">
                                <input  type="checkbox" value="1" name="mi_privilege_module[#xxxx#][view_detail]" id="view_detail_#xxxx#">
                        </span>
                        <span  class="col-md-1">
                                <input  type="checkbox" value="1" name="mi_privilege_module[#xxxx#][add]" id="add_#xxxx#">
                        </span>
                        <span  class="col-md-1">
                                <input  type="checkbox" value="1" name="mi_privilege_module[#xxxx#][edit]" id="edit_#xxxx#">
                        </span>
                        <span  class="col-md-1">
                                <input  type="checkbox" value="1" name="mi_privilege_module[#xxxx#][delete]" id="delete_#xxxx#">
                        </span>
                        <span  class="col-md-1">
                                <input  type="checkbox" value="1" name="mi_privilege_module[#xxxx#][special]" id="special_#xxxx#">
                        </span>
                        <span  class="col-md-1">
                                <input  type="checkbox" value="1" name="mi_privilege_module[#xxxx#][reserve]" id="reserve_#xxxx#">
                        </span>
                        <span class="col-md-1">
                                <input  type="checkbox" value="1" name="mi_privilege_module[#xxxx#][admin]" id="admin_#xxxx#"  onclick="select_all_column('#xxxx#');">
                        </span>
                        <span class="col-md-1">
                            <a class="btn btn-danger btn-sm" onClick="mi_delete_row(getId('input_group_#xxxx#'),'mi_privilege_module','#xxxx#');if(document.all)event.returnValue=false; else return false;">Bỏ</a>
                        </span>
                    </div>
                </div>
            </div>
		</div>
	</span>
</span>
 <div class="container">
     <br>
     <div class="box">
        <div class="box-header">
            <div class="box-title">
                privilege <span style="font-size:16px;color:#0B55C4;">[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
            </div>
            <div class="box-tools pull-right">
                <table align="right">
                    <tbody>
                    <tr>
                        <?php if(User::can_edit()){?> <td id="toolbar-save"  align="center"><a onclick="GrantPrivilegeForm.submit();"> <span title="Save"> </span> Save </a> </td><?php }?>
                        <?php if(User::can_view()){?><td id="toolbar-back"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'list'));?>#"> <span title="Back"> </span> Back </a> </td><?php }?>
                        <?php if(User::can_view()){?><td id="toolbar-list"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'list'));?>#"> <span title="List"> </span> List </a> </td><?php }?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
         <hr>
        <div class="box-body">
            <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
            <form name="GrantPrivilegeForm" method="post"  action="?<?php echo htmlentities($_SERVER['QUERY_STRING']);?>">
                <input name="deleted_ids" type="hidden" id="deleted_ids"/>
                <?php if(Form::$current->is_error())
                {
                ?>
                <table class="table">
                    <tr bgcolor="#EFEFEF" valign="top">
                        <td bgcolor="#C8E1C3"><?php echo Form::$current->error_messages();?></td>
                    </tr>
                </table>
                    <?php
                }
                ?>
                <label>Nhóm quyền</label>
                <select name="id" id="id" class="form-control"> onchange="location='<?php echo URL::build_current(array('cmd'));?>&id='+this.value;"></select>
                <br>
                <fieldset id="toolbar">
                    <div id="mi_privilege_module_all_elems">
                        <div class="bg-gray-light with-border">
                            <span class="col-md-3">Module ID</span>
                            <span class="col-md-1"><a href="javascript:void(0)" onclick="select_all_module('view');">view</a></span>
                            <span class="col-md-1"><a href="javascript:void(0)" onclick="select_all_module('view_detail');">detail</a></span>
                            <span class="col-md-1"><a href="javascript:void(0)" onclick="select_all_module('add');">Add</a></span>
                            <span class="col-md-1"><a href="javascript:void(0)" onclick="select_all_module('edit');">Edit</a></span>
                            <span class="col-md-1"><a href="javascript:void(0)" onclick="select_all_module('delete');">Delete</a></span>
                            <span class="col-md-1"><a href="javascript:void(0)" onclick="select_all_module('special');">Moderator</a></span>
                            <span class="col-md-1"><a href="javascript:void(0)" onclick="select_all_module('reserve');">reserve</a></span>
                            <span class="col-md-1"><a href="javascript:void(0)" onclick="select_all_module('admin');">admin</a></span>
                            <span class="col-md-1"><img src="<?php echo Portal::template('core');?>/images/spacer.gif"/></span>
                        </div>
                    </div>
                    <input type="button" value="  Thêm mới  " onclick="mi_add_new_row('mi_privilege_module');">
                </fieldset>
            </form>
        </div>
        <script type="text/javascript">
            mi_init_rows('mi_privilege_module',
                <?php if(isset($_REQUEST['mi_privilege_module']))
                {
                    echo MiString::array2js($_REQUEST['mi_privilege_module']);
                }
                else
                {
                    echo '{}';
                }
                ?>
            );
            function select_all_module(action)
            {
                if(typeof(all_forms['mi_privilege_module'])!='undefined')
                {
                    var checked = -1;
                    for(var i=0;i<all_forms['mi_privilege_module'].length;i++)
                    {
                        if(checked==-1)
                        {
                            checked = !getId(action+'_'+all_forms['mi_privilege_module'][i]).checked;
                        }
                        getId(action+'_'+all_forms['mi_privilege_module'][i]).checked=checked;
                    }
                }
            }
            function select_all_column(index)
            {
                getId('add_'+index).checked=getId('admin_'+index).checked;
                getId('edit_'+index).checked=getId('admin_'+index).checked;
                getId('delete_'+index).checked=getId('admin_'+index).checked;
                getId('view_'+index).checked=getId('admin_'+index).checked;
                getId('view_detail_'+index).checked=getId('admin_'+index).checked;
                getId('special_'+index).checked=getId('admin_'+index).checked;
                getId('reserve_'+index).checked=getId('admin_'+index).checked;
            }
        </script>

    </div>
</div>