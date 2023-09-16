<script>
function make_cmd(cmd)
{
	jQuery('#cmd').val(cmd);
	document.SeoConfigForm.submit();
}
</script>
<div class="container">
    <fieldset id="toolbar">
        <div id="toolbar-info">SEO_config</div>
        <div id="toolbar-content">
            <table align="right">
                <tbody>
                <tr>
                    <td id="toolbar-save"  align="center"><a onclick="make_cmd('seo');"> <span title="Save"> </span> Lưu </a> </td>
                </tr>
                </tbody>
            </table>
        </div>
    </fieldset>
    <div style="height:8px;"></div>
    <fieldset id="toolbar">
        <form name="SeoConfigForm" method="post" enctype="multipart/form-data">
            <table class="table table-striped">
                <tr>
                    <th width="26%" align="left"><a>Setting_name</a></th>
                    <th width="74%" align="left"><a>Value</a></th>
                </tr>
                <tr>
                    <td align="left" valign="top" title="site_title">site_title</td>
                    <td align="left">
                        <!--LIST:languages-->
                        <input  name="site_title_[[|languages.id|]]" type="text" id="site_title_[[|languages.id|]]" value="<?php echo $_REQUEST['site_title_'.[[=languages.id=]]];?>" class="form-control" placeholder="[[|languages.name|]]">
                        <!--/LIST:languages-->
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="top" title="site_name">site_name</td>
                    <td align="left"><input  name="site_name" type="text" id="site_name" value="<?php echo $_REQUEST['site_name'];?>" class="form-control"></td>
                </tr>
                <tr>
                    <td align="left" valign="top" title="site_icon">Ảnh đại diện website (og:image, image_url)</td>
                    <td align="left"><input  name="image_url" type="file" id="image_url" class="form-control">
                        <div id="delete_image_url">
                            <?php if(Url::get('image_url') and file_exists(Url::get('image_url'))){?>
                                <a class="btn btn-default btn-sm" href="<?php echo Url::get('image_url');?>" target="_blank">Ảnh cỡ thật</a>
                                <a class="btn btn-danger btn-sm" href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('image_url')));?>" onclick="jQuery('#delete_image_url').html('');" target="_blank">Xoá</a>
                                <img src="<?php echo Url::get('image_url');?>" alt="image_url" width="100">
                            <?php }?>
                        </div></td>
                </tr>
                <tr>
                    <td align="left" valign="top" title="site_icon">Favicon</td>
                    <td align="left"><input  name="site_icon" type="file" id="site_icon" class="form-control">
                        <div id="delete_site_icon">
                            <?php if(Url::get('site_icon') and file_exists(Url::get('site_icon'))){?>
                                <a class="btn btn-default btn-sm" href="<?php echo Url::get('site_icon');?>" target="_blank">Ảnh cỡ thật</a>
                                <a class="btn btn-danger btn-sm" href="<?php echo Url::build_current(array('cmd'=>'unlink','link'=>Url::get('site_icon')));?>" onclick="jQuery('#delete_site_icon').html('');" target="_blank">Xoá</a>
                                <img src="<?php echo Url::get('site_icon');?>" alt="site_icon" width="20">
                            <?php }?>
                        </div></td>
                </tr>
                <!--LIST:languages-->
                <tr>
                    <td valign="top" title="website_keywords">website_keywords [[|languages.code|]]</td>
                    <td><textarea  name="website_keywords_[[|languages.id|]]"class="form-control" id="website_keywords_[[|languages.id|]]"><?php echo Url::get('website_keywords_'.[[=languages.id=]],'');?></textarea></td>
                </tr>
                <tr>
                    <td valign="top" title="website_description">website_description [[|languages.code|]]</td>
                    <td><textarea  name="website_description_[[|languages.id|]]"class="form-control" id="website_description_[[|languages.id|]]"><?php echo Url::get('website_description_'.[[=languages.id=]],'');?></textarea></td>
                </tr>
                <!--/LIST:languages-->
                <tr>
                    <td valign="top" title="google_analytics">google_analytics</td>
                    <td><textarea name="google_analytics" class="textarea-medium" id="google_analytics">[[|google_analytics|]]</textarea></td>
                </tr>
                <tr class="hidden">
                    <td valign="top" title="google_analytics">Auto link</td>
                    <td><textarea  name="auto_link" class="textarea-medium" id="auto_link">[[|auto_link|]]</textarea></td>
                </tr>
                <tr>
                    <td valign="top" title="google_analytics">Ads text link</td>
                    <td><textarea name="ads_text_link" class="textarea-medium" id="ads_text_link"></textarea></td>
                </tr>
                <tr>
                    <td valign="top" title="google_analytics">Hỏi đáp trang chủ</td>
                    <td><textarea name="home_faq" class="textarea-medium" id="home_faq"></textarea></td>
                </tr>
            </table>
        </form>
    </fieldset
</div>