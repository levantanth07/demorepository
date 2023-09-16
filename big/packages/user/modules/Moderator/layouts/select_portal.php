<script>
var data = <?php echo MiString::array2suggest([[=users=]],false);?>;
jQuery(document).ready(function(){
	jQuery("#account_id").autocomplete({source:data});
});
function check_selected()
{
	var status = false;
	jQuery('form :checkbox').each(function(e){
		if(this.checked)
		{
			status = true;
		}
	});
	return status;
}
function make_cmd(cmd)
{
	jQuery('#cmd').val(cmd);
	document.ListModeratorForm.submit();
}
</script>
<div class="container">
    <fieldset id="toolbar">
        <div id="toolbar-info">
            Phân quyền <span style="font-size:16px;color:#0B55C4;">[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
        </div>
        <div id="toolbar-content">
            <table align="right">
                <tbody>
                <tr>
                    <?php if(User::can_edit(false,ANY_CATEGORY)){?> <td id="toolbar-save"  align="center"><a onclick="GrantPrivilege.submit();"> <span title="Save"> </span> Save </a> </td><?php }?>
                    <?php if(User::can_view(false,ANY_CATEGORY)){?> <td id="toolbar-list"  align="center"><a href="<?php echo Url::build_current();?>#"> <span title="List"> </span> List </a> </td><?php }?>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <div style="height:8px;"></div>
    <fieldset id="toolbar">
        <a name="top_anchor"></a>
        <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
        <form name="GrantPrivilege" method="post">
            <table class="table table-bordered">
                <tr>
                    <th width="17%"  align="left">Account</th>
                    <th width="26%"  align="left">Quyền</th>
                </tr>
                <tr>
                    <td width="17%" valign="top"><input name="account_id" type="text" id="account_id" class="form-control"></td>
                    <td width="26%" valign="top">
                        <!--LIST:privilege-->
                        <div style="line-height:20px;">
                            <div style="float:left"><input name="privilege_id[]" type="checkbox" value="[[|privilege.id|]]" id="privilege_id_[[|privilege.id|]]"></div>
                            <div>&nbsp;[[|privilege.title|]]</div>
                        </div>
                        <!--/LIST:privilege-->
                        <script>
                            <?php if(Url::get('privilege_id')){?>
                            jQuery('#privilege_id_<?php echo Url::get('privilege_id');?>').attr('checked', true);
                            <?php }?>
                        </script>
                    </td>
                </tr>
            </table>
            <input name="portal_id" type="hidden" id="portal_id" size="20" readonly/>
        </form>
    </fieldset>
</div>