<?php 
$title = 'Danh mục phân loại khách hàng';
$action = (URL::get('do')=='edit')?'edit':'add';
System::set_page_title(Portal::get_setting('company_name','').' '.$title);
?>
<div class="container">
	<fieldset id="toolbar">
	 	<div class="col-md-8">
			<h3 class="title"><span class="glyphicon glyphicon-th-large" aria-hidden="true"></span> <?php echo $title;?></h3>
		</div>
	    <div clas="col-md-4">
	    	<div class="pull-right">
	    		<input type="button" value="Thêm mới" class="btn btn-primary" onclick="window.location='index062019.php?page=customer-group&do=add<?php echo Url::get('org')?'&org=1':'';?>';" />
				<!--IF:cond(Session::get('admin_group'))--><input type="button" value="Xóa" class="btn btn-danger" onclick="ListCrmCustomerGroupForm.do.value='delete';ListCrmCustomerGroupForm.submit();return false;" /><!--/IF:cond-->		
	    	</div>
	    </div>
	</fieldset><br>
	<div class="list">
        <table class="table">
            <form name="ListCrmCustomerGroupForm" method="post">
                <thead>
                <tr valign="middle" bgcolor="#EFEFEF">
                    <td width="1%" title="Chọn tất cả">
                        <input type="checkbox" value="1" id="CrmCustomerGroup_all_checkbox" onclick="select_all_checkbox(this.form,'CrmCustomerGroup',this.checked,'#FFFFEC','white');"<?php if(URL::get('do')=='delete') echo ' checked';?>></td />
                    <td nowrap align="left" >
                        <label for="CrmCustomerGroup_all_checkbox">Chọn tất cả</label>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                </thead>
                <tbody>
                    <!--LIST:items-->
                    <tr bgcolor="<?php if((URL::get('just_edited_id',0)==[[=items.id=]]) or (is_numeric(array_search(MAP['items']['current']['id'],MAP['just_edited_ids'])))){ echo '#EFFFDF';} else {echo 'white';}?>" valign="middle" <?php Draw::hover('#E2F1DF');?> style="cursor:pointer;" id="CrmCustomerGroup_tr_[[|items.id|]]">
                        <td>
                            <!--IF:cond([[=items.group_id=]]!=1 or User::is_admin())-->
                            <input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" onclick="select_checkbox(this.form,'CrmCustomerGroup',this,'#FFFFEC','white');" id="CrmCustomerGroup_checkbox" <?php if(URL::get('do')=='delete') echo 'checked';?> >
                            <!--/IF:cond-->
                        </td />
                        <td nowrap align="left">
                            [[|items.indent|]]
                            [[|items.indent_image|]]
                            <span class="page_indent">&nbsp;</span>
                            <!--IF:cond([[=items.group_id=]]!=1 or User::is_admin())-->
                            <a href="<?php echo URL::build_current();?>&do=edit&id=[[|items.id|]]">[[|items.name|]]</a>
                            <!--ELSE-->
                            [[|items.name|]]
                            <!--/IF:cond-->
                        </td>
                        <!--IF:cond([[=items.group_id=]]!=1 or User::is_admin())-->
                        <td width="24px" align="center">
                            [[|items.move_up|]]
                        </td>
                        <td width="24px" align="center">
                            [[|items.move_down|]]
                        </td>
                        <!--ELSE-->
                        <td width="24px" align="center">
                            x
                        </td>
                        <td width="24px" align="center">
                            x
                        </td>
                        <!--/IF:cond-->
                    </tr>
                    <!--/LIST:items-->
                </tbody>

        </table>
        <input type="hidden" name="do" value="delete"/>
	<!--IF:delete(URL::get('do')=='delete')-->
	<input type="hidden" name="confirm" value="1" />
	<!--/IF:delete-->
	</form>
	</div>
</div>

