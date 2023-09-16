<script src="assets/admin/scripts/tinymce/tinymce.min.js"></script>
<script>
tinymce.init({
  selector: '#description_1,#description_2',
	language:'vi',
  height: 500,
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
    filemanager_crossdomain: true,
    external_filemanager_path:"https://media.tuha.vn/filemanager/filemanager/",
    filemanager_title:"Quản lý FILE pro" ,
    filemanager_access_key:"5998805fbc81d7335a602f65ade654fd",
   external_plugins: { "filemanager" : "https://media.tuha.vn/filemanager/filemanager/plugin.min.js"}
 });
</script>
<script type="text/javascript" src="assets/standard/js/multiple.select.js"></script>
<script>
$(document).ready(function(){
	$('#time').datetimepicker({format: 'DD/MM/YYYY'});
	$('#category_id').multiselect();
	<!--IF:cond(Url::get('cmd')=='edit')-->
	$("#categoryWrapper").css({'display':'none'});
	<!--ELSE-->
	$("#categoryWrapper").css({'display':'block'});
	<!--/IF:cond-->
	$('#edit_category').click(function(){
			if ($(this).is(':checked')) {
					$("#categoryWrapper").css({'display':'block'});
			} else {
					$("#categoryWrapper").css({'display':'none'});
			}
	 });
});
</script>
<script type="text/javascript">
    // File Picker modification for FCK Editor v2.0 - www.fckeditor.net
    // by: Pete Forde <pete@unspace.ca> @ Unspace Interactive
    var urlobj;

    function BrowseServer(obj)
    {
        urlobj = obj;
        OpenServerBrowser(
            'https://media.tuha.vn/filemanager/filemanager/dialog.php?type=Images&field_id='+obj+'&fldr=&descending=0&crossdomain=1&popup=1&akey=5998805fbc81d7335a602f65ade654fd',
            screen.width * 0.7,
            screen.height * 0.7 ) ;
    }

    function OpenServerBrowser( url, width, height )
    {
        var iLeft = (screen.width - width) / 2 ;
        var iTop = (screen.height - height) / 2 ;
        var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes" ;
        sOptions += ",width=" + width ;
        sOptions += ",height=" + height ;
        sOptions += ",left=" + iLeft ;
        sOptions += ",top=" + iTop ;
        var oWindow = window.open( url, "BrowseWindow", sOptions ) ;
    }

    function SetUrl( url, width, height, alt )
    {
        document.getElementById(urlobj).value = $('#img-container').find('img').attr('src');
        oWindow = null;
    }
</script>
<div class="container">
    <br>
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title">
                Quản lý nội dung <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
            </h3>
            <!--IF:cond(Url::get('cmd')=='edit')-->
            <a href="bai-viet/[[|category_name_id|]]/[[|name_id|]]/" class="btn btn-default"> <i class="fa fa-search"></i> Xem bài viết</a>
            <!--/IF:cond-->
            <div class="box-tools pull-right">
                <table align="right">
                    <tbody>
                    <tr>
                        <td id="toolbar-save"  align="center"><a onclick="EditNewsAdmin.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
                        <td id="toolbar-back"  align="center"><a href="<?php echo Url::build_current(array());?>"> <span title="New"> </span> Quay lại </a> </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="box-body">
            <?php if(Form::$current->is_error()){echo '<hr>'.Form::$current->error_messages();}?>
            <form name="EditNewsAdmin" id="EditNewsAdmin" method="post" enctype="multipart/form-data" onsubmit="return checkInput();">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab1" aria-controls="home" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Thông tin chung</a></li>
                    <li role="presentation"><a href="#tab2" aria-controls="messages" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span> Hình ảnh / video <?= Url::get('small_thumb_url')?' - Có ảnh':'<span id="hasImage" style="color:#ff0000"> - chưa có ảnh</span>';?></a></li>
                    <li role="presentation"><a href="#tab3" aria-controls="messages" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> SEO</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tab1">
                        <table class="table">
                            <tr>
                                <td width="16%" align="left">Danh mục (<span class="require">*</span>)
                                    <br><strong><?php echo (Url::get('cmd')=='edit')?'Sửa danh mục <input  name="edit_category" type="checkbox" id="edit_category" value="1">':'Thêm danh mục';?></strong><br><br>
                                </td>
                                <td width="28%" align="left" bgcolor="#f5f5dc">
                                    <div style="color:#FF851A;">[[|categories|]]</div>
                                    <div id="categoryWrapper" style="position:absolute;display:none;">
                                        <select  name="category_id[]" multiple id="category_id" class="form-control" style="max-width:100%;display:none;">
                                            [[|category_options|]]
                                        </select>
                                    </div>
                                </td>
                                <td width="12%" align="left"><?php if(User::can_admin(false,ANY_CATEGORY)){?>Duyệt bài<?php }?></td>
                                <td width="44%" align="left"><?php if(User::can_admin(false,ANY_CATEGORY)){?><input  name="publish" type="checkbox" value="1" id="publish" <?php if(Url::get('publish')==1){echo 'checked="checked"';}?>> <!--IF:cond([[=publish=]] and [[=publisher=]])-->Người duyệt: <strong>[[|publisher|]]</strong>/[[|published_time|]]<!--/IF:cond--><?php }?></td>
                            </tr>
                            <tr>
                                <td align="left">Trạng thái</td>
                                <td align="left"><select name="status" id="status" class="form-control"></select></td>
                                <td align="left">Vị trí</td>
                                <td align="left"><input name="position" type="text" id="position" class="form-control"/></td>
                            </tr>
                            <tr>
                                <td align="left"><!--Nhãn--></td>
                                <td align="left"><!--<select name="label" id="label" class="form-control">
				      </select>--></td>
                                <td align="left">Ngày đăng</td>
                                <td align="left">
                                    <div class="input-group date">
                                        <input name="time" type="text" id="time" class="form-control"/>
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                                    </div>
                                    <div class="small"> (Chọn ngày để lên lịch đăng bài)</div>
                                </td>
                            </tr>
                        </table><br>
                        <ul class="nav nav-tabs" role="tablist">
                            <?php $i=0;?>
                            <!--LIST:languages-->
                            <li role="presentation" <?php echo ($i==0)?'class="active"':'';?>><a href="#info_tab_[[|languages.id|]]" aria-controls="home" role="tab" data-toggle="tab"><img src="[[|languages.icon_url|]]" alt="[[|languages.name|]]" /> [[|languages.name|]]</a></li>
                            <?php $i++;?>
                            <!--/LIST:languages-->
                        </ul>
                        <?php $i=0;?>
                        <div class="tab-content">
                            <!--LIST:languages-->
                            <div role="tabpanel" class="tab-pane  <?php echo ($i==0)?'active':'';?>" id="info_tab_[[|languages.id|]]">
                                <h3></h3>
                                <div class="form-group">
                                    <label>Tiêu đề tin [[|languages.name|]](<span class="require">*</span>)</label>
                                    <input name="name_[[|languages.id|]]" type="text" id="name_[[|languages.id|]]" class="form-control"  />
                                </div>
                                <!--IF:cond(Url::get('cmd')=='edit')-->
                                <div class="form-group">
                                    <label>Slug [[|languages.name|]](<span class="require">*</span> Hiển thị trên đường dẫn)</label>
                                    <input name="name_id_[[|languages.id|]]" type="text" id="name_id_[[|languages.id|]]" class="form-control"  />
                                </div>
                                <!--/IF:cond-->
                                <div class="form-group">
                                    <label>Mô tả vắn (Hiển thị ở danh sách tin không quá 100 từ)</label>
                                    <textarea id="brief_[[|languages.id|]]" name="brief_[[|languages.id|]]" rows="5" class="form-control"><?php echo Url::get('brief_'.[[=languages.id=]],'');?></textarea><br />
                                </div>
                                <div class="form-group">
                                    <label>Thông tin đầy đủ của bài viết</label>
                                    <textarea id="description_[[|languages.id|]]" name="description_[[|languages.id|]]" cols="75" rows="20" style="width:99%; height:350px;overflow:hidden"><?php echo Url::get('description_'.[[=languages.id=]],'');?></textarea><br />
                                </div>
                            </div>
                            <?php $i++;?>
                            <!--/LIST:languages-->
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab2">
                        <div class="text-center">
                            <img src="<?php echo Url::get('small_thumb_url');?>" onerror="this.src='assets/standard/images/no_product_image.png'" alt="QLBH" style="max-width: 500px;"><br>
                            <table class="table">
                                <tr>
                                    <td width="30%" align="right">Ảnh đại diện <span class="badge">560x400</span></td>
                                    <td width="70%" align="left">
                                        <div class="form-group">
                                            <input name="small_thumb_url" type="text" id="small_thumb_url" class="form-control" placeholder="Chọn ảnh từ thư viện ảnh" onchange="$('#hasImage').html('');">
                                        </div>
                                        <div class="form-group">
                                            Chọn ảnh từ thư viện ảnh:
                                            <button class="btn btn-warning" type="button" onclick="BrowseServer('small_thumb_url');"><i class="fa fa-camera"></i> Thư viện ảnh</button>
                                        </div>
                                        <div class="alert alert-warning-custom">
                                            Khi thêm ảnh lên thư viện ảnh nên quy hoạch đúng vào thư mục ngày tháng hiện tại.
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hidden">
                                    <td width="30%" align="right">Ảnh lớn</td>
                                    <td width="70%" align="left"><input name="image_url" type="file" id="image_url" class="file" size="18"><div id="delete_image_url"><?php if(Url::get('image_url') and file_exists(Url::get('image_url'))){?>[<a href="<?php echo Url::get('image_url');?>" target="_blank" class="label label-default">Xem</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('image_url')));?>" onclick="jQuery('#delete_image_url').html('');" target="_blank" class="label label-danger">Xoá</a>]<?php }?></div></td>
                                </tr>
                                <tr class="hidden">
                                    <td width="30%" align="right">File đính kèm</td>
                                    <td width="70%" align="left"><input name="file" type="file" id="file" class="file" size="18"><div id="delete_file"><?php if(Url::get('file') and file_exists(Url::get('file'))){?>[<a href="<?php echo Url::get('file');?>" target="_blank" class="label label-default">Xem</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('file')));?>" onclick="jQuery('#delete_file').html('');" target="_blank" class="label label-danger">Xoá</a>]<?php }?></div></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tab3">
                        <h4></h4>
                        <div>
                            <ul class="nav nav-tabs" role="tablist">
                                <?php $i=0;?>
                                <!--LIST:languages-->
                                <li role="presentation" <?php echo ($i==0)?'class="active"':'';?>><a href="#seo_tab_content_[[|languages.id|]]" aria-controls="home" role="tab" data-toggle="tab"><img src="[[|languages.icon_url|]]" alt="[[|languages.name|]]" /> [[|languages.name|]]</a></li>
                                <?php $i++;?>
                                <!--/LIST:languages-->
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content"><br>
                                <?php $i=0;?>
                                <!--LIST:languages-->
                                <div role="tabpanel" class="tab-pane  <?php echo ($i==0)?'active':'';?>" id="seo_tab_content_[[|languages.id|]]">
                                    <div class="input-group" style="width:100%;">
                                        <span class="input-group-addon" style="width:30%;">Meta title [[|languages.name|]](60 ký tự)</span>
                                        <input name="seo_title_[[|languages.id|]]" type="text" id="seo_title_[[|languages.id|]]" class="form-control">
                                    </div>
                                    <div class="input-group" style="width:100%;">
                                        <span class="input-group-addon" style="width:30%;">Meta keywords [[|languages.name|]]</span>
                                        <input name="seo_keywords_[[|languages.id|]]" type="text" id="seo_keywords_[[|languages.id|]]" class="form-control">
                                    </div>
                                    <div class="input-group" style="width:100%;">
                                        <span class="input-group-addon textarea-addon" style="width:30%;">Meta description [[|languages.name|]]<br>(150 ký tự)</span>
                                        <textarea name="seo_description_[[|languages.id|]]" id="seo_description_[[|languages.id|]]" class="form-control" rows="5" cols="22"></textarea>
                                    </div>
                                </div>
                                <?php $i++;?>
                                <!--/LIST:languages-->
                            </div>
                            <div style="margin-top:8px;">
                                <div class="input-group" style="width:100%;">
                                    <span class="input-group-addon textarea-addon" style="width:30%;">Tags</span>
                                    <textarea name="tags" id="tags" class="form-control" rows="5" cols="22"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function checkInput(){
        alert(1);
        return false;
        if($('category[]').val()){

        }
    }
</script>