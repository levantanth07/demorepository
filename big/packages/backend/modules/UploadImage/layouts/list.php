<div id="module_<?php echo Module::block_id(); ?>">
	<script type="text/javascript">
      var swfu;
      window.onload = function () {
        swfu = new SWFUpload({
          // Backend Settings
          upload_url: "form.php?block_id=<?php echo Module::block_id(); ?>&do=upload_image",
          post_params: {"PHPSESSID": "<?php echo session_id(); ?>","product_id":"<?php echo Url::get('product_id');?>"},
  
          // File Upload Settings
          file_size_limit : "2 MB",	// 2MB
          file_types : "*.jpg;*.png;*.gif",
          file_types_description : "Images",
          file_upload_limit : "<?php echo [[=limit=]];?>",
  
          // Event Handler Settings - these functions as defined in Handlers.js
          //  The handlers are not part of SWFUpload but are part of my website and control how
          //  my website reacts to the SWFUpload events.
          file_queue_error_handler : fileQueueError,
          file_dialog_complete_handler : fileDialogComplete,
          upload_progress_handler : uploadProgress,
          upload_error_handler : uploadError,
          upload_success_handler : uploadSuccess,
          upload_complete_handler : uploadComplete,
  
          // Button Settings
          button_image_url : "assets/admin/scripts/swfupload/images/button_upload.png",
          button_placeholder_id : "spanButtonPlaceholder",
          button_width: 203,
          button_height: 36,
          button_text : '<span class="button"><?php if([[=limit=]]<=0){?>Bạn đã tải hết giới hạn<?php }else{?>Bạn có thể chọn <?php echo [[=limit=]];?> ảnh<br><span class="buttonSmall">(Dung lượng nhỏ hơn 5 MB)</span></span><?php }?>',
          button_text_top_padding: 3,
          button_text_left_padding: 65,
          <?php if([[=limit=]]<=0){?>
          button_disabled: true,
          <?php }?>
          button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
          button_cursor: SWFUpload.CURSOR.HAND,
          // Flash Settings
          flash_url : "assets/admin/scripts/swfupload/swfupload.swf",
          custom_settings : {
            upload_target : "divFileProgressContainer",
            progressTarget : "divFileProgressContainer"
          },
          
          // Debug Settings
          debug: false
        });
      };
    </script>
  <fieldset id="toolbar">
    <div id="toolbar-title">
        Upload ảnh sản phẩm
    </div>
    <div id="toolbar-content">
    <table align="right">
      <tbody>
      <tr>
        <td id="toolbar-save"  align="center"><a onclick="form_manage_image.submit();"> <span title="Edit"> </span> Ghi lại </a> </td>
        <td id="toolbar-cancel"  align="center"><a href="#" onClick="window.close();return false;"> <span title="Back"> </span> Đóng </a> </td>
      </tr>
      </tbody>
    </table>
    </div>
  </fieldset>
  <br clear="all">
  <div class="product-image-main">
      <div class="notice">[[|notice|]]</div>
      <div style="padding-left:10px;margin-bottom:10px;">Nếu kích thước hình ảnh quá lớn bạn phải <a target="_blank" href="http://www.diendandulich.biz/thuthuat/timagebatchresize3-0-thay-doi-kich-thuoc-hinh-anh-hang-loat-t9042.html" style="font-weight:bold;color:#0066CC;text-decoration:underline;">resize (thu nhỏ)</a> lại trước khi tải lên!</div>
      <div><?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?></div>
      <div style="padding-left:10px; margin-bottom:20px;">
      <form>
          <div style="padding:5px;border:1px solid #CCC;float:left;">
              <span id="spanButtonPlaceholder"></span>
          </div>
      </form>
      <div id="divFileProgressContainer" style="height: 75px;"></div>
      <div id="thumbnails"></div>
      <div id="image_list"></div>
      <form  name="form_manage_image" id="form_manage_image" method="POST" enctype="multipart/form-data">
      <input  type="hidden"  name="product_id"  id="product_id" value="[[|product_id|]]" />
      <!--IF:images([[=images=]])-->
      <fieldset id="toolbar">
          <table width="100%">
              <tr>
                  <td width="1%" valign="top" nowrap="nowrap">
                  <img src="[[|image|]]" style="width:400px; " id="view_image" />
                  </td>
                  <td valign="top">
                      <div style="height:500px;overflow:scroll">
                      <table width="99%" cellpadding="2" cellspacing="0" border="1" bordercolor="#CCC">
                      <?php $i = 1; ?>
                      <!--LIST:images-->
                          <tr>
                              <td width="1%" nowrap="nowrap"><img class="img_thumb" value="[[|images.image_url|]]" style="width:70px;  padding:3px 4px;" src="[[|images.small_thumb_url|]]" /></td>
                              <td valign="top" style="padding-top:3px;"><label style="text-align:left;">Tiêu đề: </label><input type="text" name="title[[[|images.id|]]]" id="title_[[[|images.id|]]]" style="width:70%" value="[[|images.name|]]" /><span style="margin-left:5px;"><?php echo $i++; ?></span></td>
                              <td valign="top" width="1%" align="center" style="padding:3px 5px;"><img title="down" style="cursor:pointer" onclick="ChangePosition('down',[[|images.id|]],[[|images.position|]]);" src="assets/admin/images/buttons/up.png"  /></td>
                              <td valign="top" width="1%" align="center" style="padding:3px 5px;"><img title="up" style="cursor:pointer" onclick="ChangePosition('up',[[|images.id|]],[[|images.position|]]);" src="assets/admin/images/buttons/down.png"  /></td>
                              <td valign="top" width="1%" align="center" style="padding:3px 5px;"><img onclick="DeleteImage([[|images.id|]]);return false;" src="assets/admin/images/buttons/delete.png"></td>
                          </tr>
                      <!--/LIST:images-->
                      </table>
                      </div>
                  </td>
              </tr>
          </table>
      </fieldset>
      <script>
          jQuery('.img_thumb').click(function(){
              jQuery('#view_image').attr('src',jQuery(this).attr('value'));
          })
          function ChangePosition(cmd,id,pos){
              jQuery.ajax({
                  method: "POST",
                  url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                  data :{
                      'cmd':cmd,
                      'position_id':id,
                      'position':pos,
                      'product_id':[[|product_id|]]
                  },
                  beforeSend: function(){
                  },
                  success: function(content){
                      document.getElementById('module_<?php echo Module::block_id(); ?>').innerHTML=content;
                  }
              });
          }
          function DeleteImage(id){
              if(confirm('Bạn có thực sự muốn xóa ảnh?')){
                  jQuery.ajax({
                      method: "POST",
                      url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
                      data :{
                          'cmd':'delete',
                          'position_id':id,
                          'product_id':[[|product_id|]]
                      },
                      beforeSend: function(){
                      },
                      success: function(content){
                          document.getElementById('module_<?php echo Module::block_id(); ?>').innerHTML=content;
                      }
                  });
              }
          }
      </script>
      <!--ELSE-->
      <fieldset id="toolbar">
          <div class="note-unavailable">[[.no_image_available.]]</div>
      </fieldset>
      <!--/IF:images-->
      </form>
  </div>
</div>