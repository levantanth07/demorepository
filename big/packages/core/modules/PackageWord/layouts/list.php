<script type="text/javascript" src="packages/core/includes/js/table_multi_items.js"></script>
<script src="packages/core/includes/js/ajax.js" type="text/javascript"></script>
<script>
    let table_fields =
        {'':''
            ,'id':''<!--LIST:languages-->
            ,'value_[[|languages.id|]]':'text'
            <!--/LIST:languages-->,'package_id':'suggest','time':'time'
        };
    field_error_messages = {};
    define_select_fields = {
        '':''
    }
    define_suggest_fields = {
        '':''
        ,'package_id':{
            '':''
            <!--LIST:packages-->
            ,'<?php echo addslashes([[=packages.id=]]);?>':'<?php echo addslashes([[=packages.name=]]);?>'
            <!--/LIST:packages-->
        }
    }
    define_field_actions = {
        '':''
    }
</script>
<?php
$title = (URL::get('cmd')=='delete')?'Xóa khai báo ngôn ngữ':'Khai báo ngôn ngữ';
$action = (URL::get('cmd')=='delete')?'delete':'list';
?>
<div class="container">
    <form name="ListPackageWordForm" method="post" class="form-inline">
    <table class="table">
        <tr><td width="90%">
                <h2><?php echo $title;?> - <?php echo $action;?></h2>
            </td>
            <?php
            if(User::can_edit()and User::can_add()){?>
                <td class="form_title_button" nowrap="nowrap">
                    <button name="update" type="submit" class="btn btn-primary" tabindex="-1" value="update"> Cập nhật</button>
                </td>
            <?php }
            if(User::can_delete()){?>
                <td class="form_title_button" nowrap="nowrap">
                    <button name="delete" type="submit" class="btn btn-danger" value="delete"> Xoá</button>
                </td>
            <?php }
            ?>
        </tr>
    </table>
    <?php if(URL::get('module_id')){
	?>
	<p>
	<a javascript:void(0)><font size="+1"><b>module_words_of [[|module_name|]]</b></font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo URL::build_current();?>"><font size="+1">all_words</font></a>
	</p>
	<?php
	}?>
    <div class="box" id="center_region">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-addon">Tìm kiếm</span>
                <input name="keyword" type="text" id="keyword" class="form-control" tabindex="100" onchange="ListPackageWordForm.submit();">
            </div>
        </div>
        <table class="table table-striped table-bordered" id="main_table">
            <thead>
            <tr valign="middle">
                <th width="1%" title="check_all">
                    <input type="checkbox" value="1" id="PackageWord_all_checkbox" onclick="select_all_checkbox(this.form,'PackageWord',this.checked,'#FFFFEC','white');"<?php if(URL::get('cmd')=='delete') echo ' checked';?>></th>
                <th nowrap width="200">
                    <?php Draw::title_label('id',Portal::language('id'));?></th>
                <!--LIST:languages-->
                <th nowrap>
                    <?php Draw::title_label('value_'.[[=languages.id=]],Portal::language('value').'('.[[=languages.code=]].')');?>
                </th>
                <!--/LIST:languages-->
                <th nowrap>
                    Package
                </th>
                <th nowrap width="100">
                    Thời gian
                </th>
            </tr>
            </thead>
            <tbody>
            <!--LIST:items-->
            <tr valign="middle" id="PackageWord_tr_[[|items.id|]]" onclick="edit_row(this,'[[|items.id|]]');">
                <td><input name="selected_ids[]" type="checkbox" value="[[|items.id|]]" title="[[|items.i|]]" onclick="select_checkbox(this.form,'PackageWord',this,'#FFFFEC','white');" <?php if(URL::get('cmd')=='delete') echo 'checked';?>></td>
                <td>
                    <div class="normal_input_text" id="id_[[|items.id|]]">[[|items.id|]]</div></td>
                <!--LIST:languages-->
                <td>
                    <div class="normal_input_text" id="value_[[|languages.id|]]_[[|items.id|]]"><?php echo [[=items=]]['current']['value_'.[[=languages.id=]]];?></div>
                </td>
                <!--/LIST:languages-->
                <td nowrap align="left">
                    <div class="label label-default">
                        [[|items.package_id|]]
                    </div>
                </td>
                <td>
                    <div class="label label-default">
                        <?php echo date('d/m/Y',intval([[=items.time=]]));?>
                    </div>
                </td>
            </tr>
            <!--/LIST:items-->
            </tbody>
        </table>
        <div class="paging">[[|paging|]]</div>
            <input type="hidden" name="edit_ids" value="0<?php foreach([[=items=]] as $id=>$item)echo ','.$id;?>"/>
            <script language="javascript">
                init_search_row();
                $('search_by_id').value = '<?php echo String::string2js(URL::get('search_by_id'));?>';<!--LIST:languages-->
                $('search_by_value_[[|languages.id|]]').value = '<?php echo String::string2js(URL::get('search_by_value_'.[[=languages.id=]]));?>';
                <!--/LIST:languages-->
                $('search_by_package_id').value = '<?php echo String::string2js(URL::get('search_by_package_id'));?>';
                $('search_by_time').value = '<?php echo String::string2js(URL::get('search_by_time'));?>';
            </script>
            <input type="button" value="  Add  " onclick="add_row();">
            <hr>
            <table width="100%"><tr>
                    <td width="100%">
                        select:&nbsp;
                        <a  onclick="select_all_checkbox(document.ListPackageWordForm,'PackageWord',true,'#FFFFEC','white');">select_all</a>&nbsp;
                        <a  onclick="select_all_checkbox(document.ListPackageWordForm,'PackageWord',false,'#FFFFEC','white');">select_none</a>
                        <a  onclick="select_all_checkbox(document.ListPackageWordForm,'PackageWord',-1,'#FFFFEC','white');">select_invert</a>
                    </td>
                </tr></table>
            <input type="hidden" name="cmd" id="cmd" value=""/>
            <input type="hidden" name="page_no" value="<?php echo URL::get('page_no');?>"/>
            <!--IF:delete(URL::get('cmd')=='delete')-->
            <input type="hidden" name="confirm" value="1" />
            <!--/IF:delete-->
            <input type="hidden" name="page_no" value="1"/>
    </div>
    </form>
</div>
<div id="suggest_box" style="position:absolute; border:1px solid black;background-color:white;display:none;"></div>
<br clear="all">
<script type="text/javascript">
document.body.onkeydown = function(evt){
	if(!evt)evt=event;
	if(default_onkeydown(evt))
	{
		if(document.all)evt.returnValue=false;
		else return false;
	}
};
function check_error()
{
	var tr = $('main_table').firstChild.nextSibling.firstChild;
	while(tr)
	{
		var div = tr.childNodes[1];
		if(div.firstChild.firstChild&&div.firstChild.firstChild.tagname)
		{
			for(var i in table_fields)
			{
				if(i)
				{
					var value = div.firstChild.firstChild.value;
					if(value!='')
					{
						if(!field_check_error(i,value,table_fields[i]))
						{
							div.firstChild.firstChild.focus();
							if(field_error_messages[i])
							{
								alert(field_error_messages[i]);
							}
							else
							{
								alert('Invalid '+i);
							}
							return false;
						}
					}
					div = div.nextSibling;
				}
			}
		}
		tr = tr.nextSibling;
	}
	return true;
}
<?php
foreach([[=new_items=]] as $item)
{
echo 'add_row('.String::array2js(array_values($item)).');
';
}
?>
</script>