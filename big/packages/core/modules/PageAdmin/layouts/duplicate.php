<?php System::set_page_title(Portal::get_setting('website_title','').' '.'duplicate_title');?>
<div class="container">
    <h3>Nhân bản trang</h3>
    <table class="table">
        <?php if(Form::$current->is_error())
        {
            ?>	<tr bgcolor="#EEEEEE" valign="top">
            <td align="right">&nbsp;</td>
            <td bgcolor="#EEEEEE"><div style="width:10px;">&nbsp;</div></td>
            <td bgcolor="#EEEEEE">B&#225;o l&#7895;i<br /><?php echo Form::$current->error_messages();?></td>
        </tr>
            <?php
        }
        ?>	<tr bgcolor="#EEEEEE" valign="top">
            <td bgcolor="#EEEEEE">
                <form name="addLayout" method="post">
                    <table class="table">
                        <tr>
                            <td width="30%">Tên trang</td>
                            <td><input name="name" type="text" id="name" value="[[|name|]]" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <td>Tham số (không bắt buộc)</td>
                            <td><input name="params" type="text" id="params" class="form-control"></td>
                        </tr>
                        <script src="<?php echo Portal::template('core');?>/css/tabs/tabpane.js" type="text/javascript"></script>
                        <!--LIST:languages-->
                        <tr id="enl_[[|languages.id|]]" <?php echo (($this->map['languages']['current']['id']==Portal::language())?'':'style="display:none"');?>>
                            <td><span class="style1">Tiêu đề</span></td>
                            <td><input name="title_[[|languages.id|]]" type="text" id="title_[[|languages.id|]]" value="[[|languages.title|]]" style="width:100%" /></td>
                        </tr>
                        <tr class="hidden">
                            <td valign="top"><span class="style1">description</span></td>
                            <td>
                                <textarea name="description_[[|languages.id|]]" style="width:100%" rows="7" id="description_[[|languages.id|]]">[[|languages.description|]]</textarea>
                            </td>
                        </tr>
                        <!--/LIST:languages-->
                        <tr>
                            <td><input type="submit" class="btn btn-primary" name="Submit" value="   Ghi lại   "></td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </form>
                <hr size="1" style="color:white">
                <p>
                    <a href="<?php echo URL::build_current(array('portal_id','package_id'));?>">Danh sách trang</a></p>
            </td>
        </tr>
    </table>
</div>
