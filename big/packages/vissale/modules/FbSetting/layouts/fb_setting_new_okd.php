<script>
    function make_cmd(cmd) {
        jQuery('#cmd').val(cmd);
        document.AccountFbSettingForm.submit();
    }
    function registerPage(obj,page_id,app_type){
        window.location='index062019.php?page=fb_setting&cmd=register_page&page_id='+page_id+'&app_type='+app_type;
    }
    function unRegisterPage(obj,page_id,app_type){
      if(confirm('Bạn có chắc chắn bỏ đăng ký page không?')){
        window.location='index062019.php?page=fb_setting&cmd=unregister_page&page_id='+page_id+'&app_type='+app_type;
      }
    }
</script>
<style>
    .table tr th,.table tr td{border:1px solid #00A8FF !important;}
</style>
<fieldset id="toolbar">
	<div id="toolbar-info">Facebook</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<td id="toolbar-save"  align="center"><a onclick="window.open('https://admin.tuha.vn/fb_module/api/cc.php','update_cache','width=100px,height=100px');make_cmd('save');"><span title="Ghi lại"> </span> Ghi </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<div style="height:8px;"></div>
<fieldset id="toolbar">
<form name="AccountFbSettingForm" method="post" class="form-inline" id="AccountFbSettingForm" enctype="multipart/form-data">
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#tab1" aria-controls="messages" role="tab" data-toggle="tab">Đăng ký Fanpage</a></li>
    <li role="presentation"><a href="#tab2" aria-controls="home" role="tab" data-toggle="tab">Cấu hình Facebook</a></li>
  </ul>
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="tab1">
      <div style="background: #FFF;padding-top:5px;">
          <div class="pull-right" style="margin:5px 5px 5px 0px">
            <input name="keyword" type="text" name="keyword" class="form-control" style="width:300px;" placeholder="Tìm page" onchange="AccountFbSettingForm.cmd.value='';AccountFbSettingForm.submit();">
          </div>
          <div class="pull-right" style="margin:5px 5px 5px 0px">
              <a href="#" onclick="window.open('https://login.tuha.vn/login.php?group_id=<?php echo Session::get('group_id');?>','','width=1px,height=1px');setTimeout('location.reload()',3000);return false;" class="btn btn-default"><img src="assets/vissale/images/icon/fb.ico" alt=""> Đồng bộ danh sách Fanpage</a>
              <a href="#" onclick="window.open('https://admin.tuha.vn/fb_module/api/create_webhook.php?group_id=<?php echo Session::get('group_id');?>','','width=1px,height=1px');setTimeout('location.reload()',3000);return false;" class="btn btn-default"><img src="assets/vissale/images/icon/fb.ico" alt=""> Đồng bộ Messenger</a>
          </div>
          <table class="table table-bordered">
              <tr>
                  <th width="1%">#</th>
                  <th>ID của trang</th>
                  <th>Tên trang (Tổng: <?php echo sizeof([[=pages=]])?> trang)</th>
                  <th width="10%">Mẫu câu trả lời</th>
                  <th width="10%">Đăng ký Chat </th>
                  <th width="10%">Đăng ký Auto Comment</th>
              </tr>
              <?php $i=1?>
              <!--LIST:pages-->
              <tr <?php echo ($i%2==0)?'style="background: #efefef"':'';?>>
                  <td><?php echo $i;?></td>
                  <td>
                    <a href="https://facebook.com/[[|pages.page_id|]]/" target="_blank">[[|pages.page_id|]]</a>
                    <div class="small" style="color:#999;height:20px;width:250px;overflow-x: hidden;white-space: nowrap; ">Token: [[|pages.token|]]</div>
                    <div class="small" style="color:#999;height:20px;width:250px;overflow-x: hidden;white-space: nowrap;">Messenger Token: [[|pages.messenger_token|]]</div></td>
                  </td>
                  <td>[[|pages.page_name|]]
                  <div class="small" style="color:#339900">SHOP: [[|pages.group_name|]]</div></td>
                  <td align="center"><a href="<?php echo Url::build_current(array('cmd'=>'reply_bank','page_id'=>[[=pages.id=]]))?>" class="btn btn-default">Khai báo ([[|pages.had_reply|]])</a></td>
                  <td align="center"><?php echo ([[=pages.subscribed_messenger=]])?'<a href="#" onclick="unRegisterPage(this,\''.([[=pages.page_id=]]).'\',\'vissale_app\');return false;" class="btn btn-sm btn-danger">Hủy đăng ký</a>':'<a href="#" onclick="registerPage(this,\''.([[=pages.page_id=]]).'\',\'vissale_app\');return false;" class="btn btn-sm btn-success">Đăng ký</a>';?></td>
                  <td align="center"><?php echo [[=pages.subscribed_customer_app=]]?'<a href="#" onclick="unRegisterPage(this,\''.([[=pages.page_id=]]).'\',\'custom_app\');return false;" class="btn btn-sm btn-danger">Hủy đăng ký</a>':'<a href="#" onclick="registerPage(this,\''.([[=pages.page_id=]]).'\',\'custom_app\');return false;" class="btn btn-sm btn-success">Đăng ký</a>';?></td>
              </tr>
              <?php $i++?>
              <!--/LIST:pages-->
          </table>
      </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="tab2">
      <div>
        <br>
        <table class="table">
            <tr style="background-color:#F0F0F0">
                <th width="20%" align="left">Tên cấu hình</th>
                <th width="80%" align="left">Giá trị</th>
            </tr>
            <tr>
              <td align="left" valign="top" title="fb_app_id">Facebook app_id</td>
              <td align="left"><input name="config_fb_app_id" type="text" id="fb_app_id" class="input-big-huge" /></td>
            </tr>
            <tr>
              <td align="left" valign="top" title="fb_app_secret_key">Facebook fb_app_secret_key</td>
              <td align="left"><input name="config_fb_app_secret_key" type="text" id="fb_app_secret_key" class="input-big-huge"/></td>
            </tr>
            <tr>
                <td width="20%" align="left" valign="top" title="fb_app_version">Facebook fb_app_version</td>
                <td width="80%" align="left"><input name="config_fb_app_version" type="text" id="fb_app_version" class="input-big-huge"></td>
            </tr>
            <tr>
              <td colspan=2>
                <div class="alert alert-success">
                  Copy vào khai báo miền tên và đăng nhập facebook trong app: <a href="#">https://app.tuha.vn</a> | <a href="#">https://login.tuha.vn/callback.php</a> | <a href="#">https://admin.tuha.vn</a>
                </div>
              </td>
            </tr>
            </tr>
                <tr bgcolor="#C8FFBB">
                <td colspan="2" align="left" valign="top"><strong>Trả lời</strong></td>
            </tr>
            <tr bgcolor="#FFF">
                <td align="left" valign="top" title="reply_comment_has_phone">Comment khi có SĐT</td>
                <td align="left"><textarea name="config_reply_comment_has_phone" id="reply_comment_has_phone" class="input-big-huge" rows="5"></textarea></td>
            </tr>
            <tr bgcolor="#FFF">
                <td align="left" valign="top" title="reply_comment_nophone">Comment khi không có SĐT</td>
                <td align="left"><textarea name="config_reply_comment_nophone" id="reply_comment_nophone" class="input-big-huge" rows="5"></textarea>
                </td>
            </tr>
            <tr bgcolor="#FFF">
                <td align="left" valign="top" title="reply_conversation_has_phone">Inbox khi có SĐT</td>
                <td align="left"><textarea name="config_reply_conversation_has_phone" id="reply_conversation_has_phone" class="input-big-huge" rows="5"></textarea>
                </td>
            </tr>
            <tr bgcolor="#FFF">
                <td align="left" valign="top" title="reply_conversation_nophone">Inbox khi không có SĐT</td>
                <td align="left"><textarea name="config_reply_conversation_nophone" id="reply_conversation_nophone" class="input-big-huge" rows="5"></textarea>
                </td>
            </tr>
            <tr bgcolor="#FFFFCC">
                <td width="20%" align="left" valign="top" title="words_blacklist">Bộ lọc từ</td>
                <td width="80%" align="left"><input name="config_words_blacklist" type="text" id="words_blacklist" class="input-big-huge"></td>
            </tr>
            <tr bgcolor="#FFFFCC">
                <td width="20%" align="left" valign="top" title="phone_filter">Bộ lọc SĐT</td>
                <td width="80%" align="left"><input name="config_phone_filter" type="text" id="phone_filter" class="input-big-huge"></td>
            </tr>
            <tr bgcolor="#FFFFCC">
                <td width="20%" align="left" valign="top" title="user_coment_filter">Bộ lọc Facebook ID</td>
                <td width="80%" align="left"><textarea name="config_user_coment_filter" id="user_coment_filter" class="input-big-huge" rows="5"></textarea></td>
            </tr>
            <tr>
                <td>Like</td>
                <td><select name="config_like_comment" id="config_like_comment" class="form-control"></select></td>
            </tr>
            <tr>
                <td>Ẩn khi có số điện thoại</td>
                <td><select name="config_hide_phone_comment" id="config_hide_phone_comment" class="form-control"></select></td>
            </tr>
            <tr>
                <td>Ẩn khi không có số điện thoại</td>
                <td><select name="config_hide_nophone_comment" id="config_hide_nophone_comment" class="form-control"></select>
                </td>
            </tr>
            <tr>
                <td>Trả lời inbox</td>
                <td><select name="config_reply_conversation" id="config_reply_conversation" class="form-control"></select></td>
            </tr>
            <!--Dia chi phia bac-->
        </table>
    </div>
    </div>
  </div>
<input name="cmd" type="hidden" id="cmd" value="save">
</form>
</fieldset>
