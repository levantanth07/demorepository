<script language="javascript">
packages = {
'':''
<!--LIST:packages-->
,[[|packages.id|]]:{
	'':''
	<!--LIST:packages.modules-->
	,[[|packages.modules.id|]]:'[[|packages.modules.name|]]'
	<!--/LIST:packages.modules-->
}
<!--/LIST:packages-->
};
block_moved = false;
</script>
<br>
<div class="container">
    <h3 class="title">Chỉnh sửa page</h3>
    <table class="table" style="background: #fff;">
        <tr>
            <td>
                <table class="table">
                    <tr>
                        <td><a href="<?php echo URL::build([[=name=]]);?>&[[|params|]]">[[|name|]] - [[|title|]]</a></td>
                        <td><a target="_blank" href="<?php echo URL::build('layout');?>&cmd=edit&id=[[|layout|]]">[[.edit_layout.]]</a></td>
                        <td>
                            <a href="<?php echo Url::build('page',array('id','cmd'=>'refresh','href'=>'?'.$_SERVER['QUERY_STRING']));?>">Xo&#225; cache</a>
                            &nbsp;|&nbsp;<a href="<?php echo Url::build('page',array('cmd'=>'edit','id'));?>">S&#7917;a</a>
                            &nbsp;|&nbsp;<a href="<?php echo Url::build('page');?>&package_id=[[|package_id|]]">Danh s&#225;ch trang</a>
                            &nbsp;|&nbsp;<a href="<?php echo Url::build('module');?>">Modules</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <div class="row">
                    <div class="col-md-4">
                        Layout:
                        <select name="layout" id="layout" onchange="changeLayout(this.value);" class="form-control"></select>
                        <script type="text/javascript">
                            function changeLayout(id){
                                location='<?php echo URL::build('edit_page');?>&id=[[|id|]]&cmd=change_layout&new_layout='+id;
                            }
                            function changePackage(id){
                                while (getId('module_id').length> 0) {
                                    getId('module_id').remove(0);
                                }
                                if(packages[id]){
                                    for(var i in packages[id]){
                                        getId('module_id').add(new Option(packages[id][i],i));
                                    }
                                }
                            }
                            current_select_module = '';
                        </script>
                    </div>
                    <div class="col-md-4">
                        Package:
                        <select name="package_id" id="package_id" onchange="changePackage(this.value);" class="form-control"></select>
                    </div>
                    <div class="col-md-4">
                        <a href = "#" ondragstart="event.dataTransfer.setData('Text', '-'+getId('module_id').value);event.dataTransfer.effectAllowed = 'copy';">Modules</a>
                        :
                        <select name="module_id" id="module_id" class="form-control"></select>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div style="float:left;width:100%; height: 300px;overflow: auto;">
        [[|regions|]]
    </div>
    <br clear="all">
    <table>
        <tr>
            <td>
                <div class="box">
                    <div class="box-header">Modules:</div>
                    <div class="box-body">
                        <!--LIST:new_modules-->
                        &nbsp;&nbsp;<a href = "#" ondragstart="event.dataTransfer.setData('Text', '-[[|new_modules.id|]]');event.dataTransfer.effectAllowed = 'copy';" class="btn btn-default btn-sm" style="margin-bottom:2px;">[[|new_modules.name|]]</a>
                        <!--/LIST:new_modules-->
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <br clear="all">
</div>
<script language="javascript">
    changePackage([[|package_id|]]);
    <!--IF:cond(Url::get('layout'))-->
    getId('layout').value = <?= Url::get('layout');?>
    <!--/IF:cond-->
</script>
