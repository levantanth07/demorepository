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
	<div id="toolbar-info">Khai báo mẫu trả lời Facebook</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		<td id="toolbar-save"  align="center"><a onclick="make_cmd('save');//window.open('https://admin.tuha.vn/fb_module/api/cc.php','update_cache','width=100px,height=100px');"><span title="Ghi lại"> </span> Ghi </a> </td>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<div style="height:8px;"></div>
<fieldset id="toolbar">
<form name="AccountFbSettingForm" method="post" class="form-inline" id="AccountFbSettingForm" enctype="multipart/form-data">
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#tab2" aria-controls="home" role="tab" data-toggle="tab">Khai báo câu trả lời của page</a></li>
  </ul>
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="tab2">
      <div>
        <br>
        <table class="table">
            <tr style="background-color:#F0F0F0">
                <th width="20%" align="left">Tên cấu hình</th>
                <th width="80%" align="left">Giá trị</th>
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
            <!--Dia chi phia bac-->
        </table>
    </div>
    </div>
  </div>
<input name="cmd" type="hidden" id="cmd" value="save">
<input name="page_id" type="hidden" id="page_id">

</form>
</fieldset>
