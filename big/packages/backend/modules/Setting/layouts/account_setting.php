<script>
function make_cmd(cmd)
{
	jQuery('#cmd').val(cmd);
	document.AccountSettingForm.submit();
}
</script>
<div class="container">
    <fieldset id="toolbar">
        <div id="toolbar-info">Cấu hình chung</div>
        <div id="toolbar-content">
            <table align="right">
                <tbody>
                <tr>
                    <td id="toolbar-save"  align="center"><a onclick="make_cmd('save');"><span title="Save"> </span> Lưu </a> </td>
                </tr>
                </tbody>
            </table>

        </div>
    </fieldset><hr>
    <fieldset id="toolbar">
        <form name="AccountSettingForm" method="post" id="AccountSettingForm" enctype="multipart/form-data">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#tab1" aria-controls="home" role="tab" data-toggle="tab">Cấu hình thông tin chung</a></li>
                <li role="presentation"><a href="#tab2" aria-controls="messages" role="tab" data-toggle="tab">Cấu hình tùy chọn module</a></li>
                <li role="presentation"><a href="#tab3" aria-controls="messages" role="tab" data-toggle="tab">Hệ thống</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab1">
                    <div id="front_back_config" class="form_input">
                        <table class="table table-striped">
                            <tr>
                                <th width="20%" align="left">Tên cấu hình</th>
                                <th width="80%" align="left">Giá trị</th>
                            </tr>
                            <tr>
                                <td align="left" valign="top" title="email_support">Đơ vị tiền tệ mặc định</td>
                                <td align="left"><input name="config_default_currency" type="text" id="default_currency" class="form-control"/></td>
                            </tr>
                            <tr>
                                <td align="left" valign="top" title="email_support">Mã giảm giá</td>
                                <td align="left"><input name="config_discount_code" type="text" id="config_discount_code" class="form-control"/></td>
                            </tr>
                            <tr>
                                <td width="20%" align="left" valign="top" title="email_support_online_bac">Email hỗ trợ KH</td>
                                <td width="80%" align="left"><input name="config_email_support_online_bac" type="text" id="email_support"class="form-control"></td>
                            </tr>
                            <tr>
                                <td width="20%" align="left" valign="top" title="email_webmaster">email_webmaster</td>
                                <td width="80%" align="left"><input name="config_email_webmaster" type="text" id="email_webmaster"class="form-control"></td>
                            </tr>
                            <tr>
                                <td align="left" valign="top" title="phone_ban_buon">Số bán buôn</td>
                                <td align="left"><input name="config_phone_ban_buon" type="text" id="phone_ban_buon"class="form-control"></td>
                            </tr>
                            <tr>
                                <td width="20%" align="left" valign="top" title="hot_line">Hotline (name:Phone_number,..) - hot_line</td>
                                <td width="80%" align="left"><input name="config_hot_line" type="text" id="hot_line"class="form-control"></td>
                            </tr>
                            <tr>
                                <td width="20%" align="left" valign="top" title="fb_page_id">Facebook Fanpage Id</td>
                                <td width="80%" align="left"><input name="config_fb_page_id" type="text" id="fb_page_id"class="form-control"></td>
                            </tr>
                            <tr bgcolor="#C8FFBB">
                                <td colspan="2" align="left" valign="top"><strong>Mục thông tin liên hệ</strong></td>
                            </tr>
                            <tr bgcolor="#FFF">
                                <td align="left" valign="top" title="contact_text">Thông tin liên hệ dạng text</td>
                                <td align="left"><textarea name="config_contact_text" type="text" id="contact_text"class="form-control" rows="10"></textarea>
                                </td>
                            </tr>
                            <tr bgcolor="#FFFFCC">
                                <td width="20%" align="left" valign="top" title="company_name_1">Tên tổ chức (Tiếng Việt)</td>
                                <td width="80%" align="left"><input name="config_company_name_1" type="text" id="config_company_name"class="form-control"></td>
                            </tr>
                            <tr bgcolor="#FFFFCC">
                                <td width="20%" align="left" valign="top" title="company_name_3">Tên tổ chức (Tiếng Anh)</td>
                                <td width="80%" align="left"><input name="config_company_name_3" type="text" id="company_name_3"class="form-control"></td>
                            </tr>
                            <!--Dia chi phia bac-->
                            <tr bgcolor="#FFCC99">
                                <td align="left" valign="top" title="company_address_1">ĐC trụ sở chính (Tiếng Việt)</td>
                                <td align="left"><input name="config_company_address_1" type="text" id="company_address_1"class="form-control" /></td>
                            </tr>
                            <tr bgcolor="#FFCC99">
                                <td align="left" valign="top" title="config_company_address_3">ĐC trụ sở chính (Tiếng Anh)</td>
                                <td align="left"><input name="config_company_address_3" type="text" id="company_address_3"class="form-control" /></td>
                            </tr>
                            <tr bgcolor="#FFCC99">
                                <td width="20%" align="left" valign="top" title="company_phone_bac">Điện thoại</td>
                                <td width="80%" align="left"><input name="config_company_phone_bac" type="text" id="company_phone_bac"class="form-control"></td>
                            </tr>
                            <tr bgcolor="#FFCC99">
                                <td align="left" valign="top" title="company_fax">Skype</td>
                                <td align="left"><input name="config_skype" type="text" id="skype"class="form-control"></td>
                            </tr>
                            <tr bgcolor="#FFCC99">
                                <td width="20%" align="left" valign="top" title="company_fax">company_fax</td>
                                <td width="80%" align="left"><input name="config_company_fax" type="text" id="company_fax"class="form-control"></td>
                            </tr>
                            <tr bgcolor="#FFCC99">
                                <td width="20%" align="left" valign="top" title="company_email">Email</td>
                                <td width="80%" align="left"><input name="config_company_email" type="text" id="company_email"class="form-control"></td>
                            </tr>
                            <tr bgcolor="#EFEFEF">
                                <td align="left" valign="top" title="company_address_nam_1">ĐC văn phòng Miền Nam (Tiếng Việt)</td>
                                <td align="left"><input name="config_company_address_nam_1" type="text" id="company_address_nam_1"class="form-control" /></td>
                            </tr>
                            <tr bgcolor="#EFEFEF">
                                <td align="left" valign="top" title="company_address_nam_3">ĐC văn phòng Miền Nam (Tiếng Anh)</td>
                                <td align="left"><input name="config_company_address_nam_3" type="text" id="company_address_nam_3"class="form-control" /></td>
                            </tr>
                            <tr bgcolor="#EFEFEF">
                                <td width="20%" align="left" valign="top" title="company_phone_nam">Điện thoại VPMN</td>
                                <td width="80%" align="left"><input name="config_company_phone_nam" type="text" id="company_phone_nam"class="form-control"></td>
                            </tr>
                            <tr bgcolor="#EFEFEF">
                                <td align="left" valign="top" title="company_email_nam">Email VPMN</td>
                                <td align="left"><input name="config_company_email_nam" type="text" id="company_email_nam"class="form-control"></td>
                            </tr>
                            <tr bgcolor="#EFEFEF">
                                <td align="left" valign="top" bgcolor="#D7FFD9" title="company_address_nn1_1">ĐC văn phòng nước ngoài 1 (Tiếng Việt)</td>
                                <td align="left" bgcolor="#D7FFD9"><input name="config_company_address_nn1_1" type="text" id="company_address_nn1_1"class="form-control" /></td>
                            </tr>
                            <tr bgcolor="#EFEFEF">
                                <td align="left" valign="top" bgcolor="#D7FFD9" title="company_address_nn1_3">ĐC văn phòng nước ngoài 1 (Tiếng Anh)</td>
                                <td align="left" bgcolor="#D7FFD9"><input name="config_company_address_nn1_3" type="text" id="company_address_nn1_3"class="form-control" /></td>
                            </tr>
                            <tr bgcolor="#EFEFEF">
                                <td align="left" valign="top" bgcolor="#D7FFD9" title="company_phone_nn1">Điện thoại</td>
                                <td align="left" bgcolor="#D7FFD9"><input name="config_company_phone_nn1" type="text" id="company_phone_nn1"class="form-control"></td>
                            </tr>
                            <tr bgcolor="#EFEFEF">
                                <td align="left" valign="top" bgcolor="#D7FFD9" title="company_email_nn1">Email </td>
                                <td align="left" bgcolor="#D7FFD9"><input name="config_company_email_nn1" type="text" id="company_email_nn1"class="form-control"></td>
                            </tr>
                            <tr>
                                <td align="left" valign="top" bgcolor="#C5FCFF" title="company_address_nn2_1">ĐC văn phòng nước ngoài 2 (Tiếng Việt)</td>
                                <td align="left" bgcolor="#C5FCFF"><input name="config_company_address_nn2_1" type="text" id="company_address_nn2_1"class="form-control" /></td>
                            </tr>
                            <tr>
                                <td align="left" valign="top" bgcolor="#C5FCFF" title="company_address_nn2_3">ĐC văn phòng nước ngoài 2 (Tiếng Anh)</td>
                                <td align="left" bgcolor="#C5FCFF"><input name="config_company_address_nn2_3" type="text" id="company_address_nn2_3"class="form-control" /></td>
                            </tr>
                            <tr>
                                <td align="left" valign="top" bgcolor="#C5FCFF" title="company_phone_nn2">Điện thoại</td>
                                <td align="left" bgcolor="#C5FCFF"><input name="config_company_phone_nn2" type="text" id="company_phone_nn2"class="form-control"></td>
                            </tr>
                            <tr>
                                <td align="left" valign="top" bgcolor="#C5FCFF" title="company_email_nn2">Email </td>
                                <td align="left" bgcolor="#C5FCFF"><input name="config_company_email_nn2" type="text" id="company_email_nn2"class="form-control"></td>
                            </tr>
                            <tr>
                                <td width="20%" align="left" valign="top">&nbsp;</td>
                                <td width="80%" align="left">&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab2">
                    <table class="table table-striped">
                        <tr>
                            <td>Module sản phẩm</td>
                            <td><select name="config_product_module_enable" id="config_product_module_enable">
                                </select></td>
                        </tr>
                        <tr>
                            <td>Hiển thị giá sản phẩm</td>
                            <td><select name="config_product_module_show_price" id="config_product_module_show_price">
                                </select></td>
                        </tr>
                        <tr>
                            <td width="20%">Giỏ hàng</td>
                            <td><select name="config_product_module_cart" id="config_product_module_cart"></select>
                            </td>
                        </tr>
                        <tr>
                            <td width="20%">Dịch vụ</td>
                            <td><select name="config_service_enable_module" id="config_service_enable_module"></select></td>
                        </tr>
                    </table>
                </div>

                <div role="tabpanel" class="tab-pane" id="tab3">
                    <div id="system_config" class="form_input">
                        <table class="table table-striped">
                            <tr>
                                <th width="20%" align="left">Tên cấu hình</th>
                                <th width="80%" align="left">Giá trị</th>
                            </tr>
                            <tr>
                                <td align="left" title="use_cache">Kích hoạt site</td>
                                <td align="left"><select name="config_is_active" id="config_is_active"></select>
                                    <script>jQuery('#config_is_active').val(<?php echo Url::get('config_is_active',0)?>);</script></td>
                            </tr>
                            <tr>
                                <td align="left" title="use_cache">Bật thông báo lỗi</td>
                                <td align="left"><select name="config_display_errors" id="config_display_errors"></select>
                                    <script>jQuery('#config_display_errors').val(<?php echo Url::get('config_display_errors',0)?>);</script></td>
                            </tr>
                            <tr>
                                <td width="20%" align="left" title="portal_template">Thông báo dừng site</td>
                                <td width="80%" align="left"><input name="config_notification_when_interrption" type="text" id="notification_when_interrption"class="form-control"></td>
                            </tr>
                            <tr>
                                <td colspan="2" align="left" title="size_upload"><strong>Cấu hình email hệ thống</strong></td>
                            </tr>
                            <tr>
                                <td align="left" bgcolor="#FFFFFF" title="send_email_address">Email</td>
                                <td align="left" bgcolor="#FFFFFF"><input name="config_send_email_address" type="text" id="send_email_address"class="form-control"></td>
                            </tr>
                            <tr>
                                <td align="left" bgcolor="#FFFFFF" title="send_email_password">Mật khẩu email</td>
                                <td align="left" bgcolor="#FFFFFF"><input name="config_send_email_password" type="password" id="send_email_password"class="form-control"></td>
                            </tr>
                            <tr>
                                <td align="left" bgcolor="#FFFFFF" title="send_email_address">Host</td>
                                <td align="left" bgcolor="#FFFFFF"><input name="config_send_email_host" type="text" id="send_email_host"class="form-control"> Ví dụ: ssl://smtp.gmail.com</td>
                            </tr>
                            <tr>
                                <td align="left" bgcolor="#FFFFFF" title="send_email_password">Port</td>
                                <td align="left" bgcolor="#FFFFFF"><input name="config_send_email_port" type="text" id="send_email_port"class="form-control"> Ví dụ: 465</td>
                            </tr>
                            <tr>
                                <td colspan="2" align="left" title="size_upload">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="20%" align="left" title="size_upload">size_upload /(1times upload)</td>
                                <td width="80%" align="left"><input name="config_size_upload" type="text" id="size_upload" class="input-large"></td>
                            </tr>
                            <tr>
                                <td width="20%" align="left" title="type_image_upload">type_image_upload</td>
                                <td width="80%" align="left"><input name="config_type_image_upload" type="text" id="type_image_upload"class="form-control"></td>
                            </tr>
                            <tr>
                                <td width="20%" align="left" title="type_file_upload">type_file_upload</td>
                                <td width="80%" align="left"><input name="config_type_file_upload" type="text" id="type_file_upload"class="form-control"></td>
                            </tr>
                            <!--IF:cond(Url::get('a')=='dm')-->
                            <tr>
                                <td valign="top"  align="left" title="use_recycle_bin">Key</td>
                                <td  align="left"><input name="config_domain" type="text" id="domain" class="form-control">
                                    <script>jQuery('#use_recycle_bin').val(<?php echo Url::get('config_use_recycle_bin',0)?>);</script>
                                </td>
                            </tr>
                            <!--/IF:cond-->
                        </table>

                    </div>
                </div>
            </div>
            <input name="cmd" type="hidden" id="cmd" value="save">
        </form>
    </fieldset>
</div>