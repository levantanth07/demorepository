<script>
function make_cmd(cmd)
{
	jQuery('#cmd').val(cmd);
	document.FrontEndForm.submit();
}
</script>
<div class="container">
    <fieldset id="toolbar">
        <div id="toolbar-info">Cấu hình giao diện</div>
        <div id="toolbar-content">
            <table align="right">
                <tbody>
                <tr>
                    <td id="toolbar-save"  align="center"><a onclick="make_cmd('save');"> <span title="Save"> </span> Lưu </a> </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <hr>
    <fieldset id="toolbar">
        <form name="FrontEndForm" method="post" enctype="multipart/form-data">
            <table class="table table-striped">
                <tr>
                    <th width="26%" align="left">Tên thuộc tính</th>
                    <th width="80%" align="left">Giá trị</th>
                </tr>
                <tr>
                    <td align="left" valign="top" title="order_columns">Cột hiển thị trong đơn hàng</td>
                    <td align="left"><textarea name="config_order_columns" id="order_columns" class="form-control" rows="10"></textarea></td>
                </tr>
                <tr>
                    <td align="left" valign="top" title="contact_notification_text_1">Thông báo liên hệ gửi thành công (Tiếng Việt)</td>
                    <td align="left"><input name="config_contact_notification_text_1" type="text" id="contact_notification_text_1" class="form-control"></td>
                </tr>
                <tr>
                    <td align="left" valign="top" title="contact_notification_text_2">Thông báo liên hệ gửi thành công (Tiếng Anh)</td>
                    <td align="left"><input name="config_contact_notification_text_2" type="text" id="contact_notification_text_2" class="form-control"></td>
                </tr>
                <tr>
                    <td rowspan="3" align="left" valign="top" title="">Logo</td>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left"></td>
                </tr>
                <tr>
                    <td align="left">
                        <div id="delete_logo">
                            <?php if(Url::get('config_logo') and file_exists(Url::get('config_logo'))){?>
                                <img src="<?php echo Url::get('config_logo');?>" alt="LOGO">
                                [<a href="<?php echo Url::get('config_logo');?>" target="_blank" style="color:#FF0000">view</a>]&nbsp;[<a href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('config_logo')));?>" onclick="jQuery('#config_logo').html('');" target="_blank" style="color:#FF0000">delete</a>]
                            <?php }?>
                        </div>
                        <input name="config_logo" type="file" id="logo" class="file">
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="top" title="facebook">Link facebook</td>
                    <td align="left"><textarea name="config_facebook" class="textarea-small" id="facebook" style="height:50px"></textarea></td>
                </tr>
                <tr>
                    <td align="left" valign="top" title="facebook">Link Youtube</td>
                    <td align="left"><textarea name="config_youtube" class="textarea-small" id="youtube" style="height:50px"></textarea></td>
                </tr>
                <tr>
                    <td align="left" valign="top" title="support_online">Bản đồ</td>
                    <td align="left"><textarea name="config_google_map" class="textarea-small" id="config_google_map" style="height:50px"></textarea></td>
                </tr>
                <tr>
                    <td width="26%" align="left" valign="top" title="support_online">HTML footer</td>
                    <td width="80%" align="left"><textarea name="config_footer_html" class="textarea-small" id="footer_html" style="height:300px"></textarea></td>
                </tr>
            </table>
            <input name="cmd" type="hidden" id="cmd" value="front_end">
        </form>
    </fieldset>
</div>