<script src="assets/admin/scripts/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
  selector: '#content',
    language:'vi',
  height: 300,
  theme: 'modern',
  plugins: [
    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    'searchreplace wordcount visualblocks visualchars code fullscreen',
    'insertdatetime media nonbreaking save table contextmenu directionality',
    'emoticons template paste textcolor colorpicker textpattern imagetools'
  ],
  toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  toolbar2: 'print preview media | forecolor backcolor emoticons',
    theme_advanced_buttons1 : "openmanager",
  image_advtab: true,
  content_css: [
    'assets/admin/scripts/tinymce/skins/lightgray/skin.min.css'
  ],
    automatic_uploads: false,
    external_filemanager_path:"/filemanager/filemanager/",
   filemanager_title:"Quản lý FILE pro" ,
     filemanager_access_key:"5998805fbc81d7335a602f65ade654fd",
   external_plugins: { "filemanager" : "/filemanager/filemanager/plugin.min.js"}
 });
</script>
<div class="container">
    <style>
        .img-print-template img{
            max-width: 100%;
        }
    </style>
    <fieldset id="toolbar">
        <div id="toolbar-title">
            Quản lý cuộc gọi
            <span>[ <?php if(Url::get('cmd')=='add'){echo 'Thêm mới';} if(Url::get('cmd')=='edit')
                {echo 'Sửa';}?> ]</span>
        </div>
        <div id="toolbar-content" align="right">
            <table align="right" style="height: 58px;">
                <tbody>
                <tr>
                    <td align="center">
                        <a class="btn btn-primary" onclick="EditCrmCustomerCallHistory.submit();">
                            <i class="glyphicon glyphicon-floppy-disk"></i> Lưu </a>
                    </td>
                    <td align="center">
                        <!--IF:cond(Url::get('window')==1)-->
                        <a class="btn btn-danger" href="#" onclick="window.close();return false;"><i class="glyphicon glyphicon-log-out"></i> Đóng lại </a>
                        <!--ELSE-->
                        <a class="btn btn-danger" href="<?php echo Url::build_current(array('cmd'=>'list', 'cid' => [[=cid=]]));?>#"><i class="glyphicon glyphicon-log-out"></i> Quay lại </a>
                        <!--/IF:cond-->
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <br clear="all"/>
    <fieldset id="add_receive_form" class="row">
        <div class="col-md-8">
            <?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <strong>cuộc gọi:</strong>
                </div>
                <div class="panel-body">
                    <form name="EditCrmCustomerCallHistory" id="EditCrmCustomerCallHistory" method="post" enctype="multipart/form-data">
                        <input name="customer_id" type="hidden" id="customer_id" class="form-control">
                        <div class="form-group">
                            <label for="note_id">Mã cuộc gọi</label>
                            <input name="note_id" type="text" id="note_id" class="form-control" readonly placeholder="(auto)">
                        </div>
                        <div class="form-group">
                            <label for="status">Trạng thái (CSKH):</label>
                            <select name="status" id="status" class="form-control"></select>
                        </div>
                        <div class="form-group hidden">
                            <label for="customer_status">Level KH (Sales):</label>
                            <select name="customer_status_id" id="customer_status_id" class="form-control"></select>
                            <input name='old_customer_status_id' type='hidden' id='old_customer_status_id' value='<?=$this->map['old_customer_status_id'];?>'>
                        </div>
                        <div class="form-group">
                            <label for="bill_date">Nội dung:</label>
                            <textarea name="content" type="text" id="content" class="form-control" rows="5"
                                      placeholder="Nội dung cuộc gọi"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Cảm xúc khách hàng:</label>
                            <select name="customer_kinds" id="customer_kinds" class="form-control"></select>
                        </div>
                        <div class="form-group">
                            <label for="created_time">Thời Gian</label>
                            <input name="created_time" type="text" id="created_time" readonly class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="received_full_name">Tên khách hàng</label>
                            <input name="customer_name" type="text" id="customer_name" readonly class="form-control" placeholder="Tên khách hàng">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="glyphicon glyphicon-time"></i> Lịch sử</strong>
                </div>
                <div class="panel-body" style="max-height: 865px;overflow-y: auto">
                    <ul class="list-group">
                        <!--LIST:logs-->
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-12"><i class="fa fa-clock-o"></i>
                                    <?php echo date("d/m/Y H:i'", [[=logs.time=]]); ?>
                                </div>
                                <div class="col-md-12">
                                    <strong>[[|logs.user_id|]]</strong>
                                    <span class="small">[[|logs.description|]]</span>
                                </div>
                            </div>
                        </li>
                        <!--/LIST:logs-->
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </fieldset>
</div>
<script>
    jQuery(document).ready(function(){
        // jQuery('#created_time').datetimepicker({
        //     inline: true,
        //     sideBySide: true,
        //     format: 'YYYY-MM-DD HH:mm:ss',
        //     locale : 'vi'
        // });
    });
</script>