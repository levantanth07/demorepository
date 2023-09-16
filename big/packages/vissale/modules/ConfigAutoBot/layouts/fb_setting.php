<?php
$user_id = DB::fetch('select id from users where username="'.Session::get('user_id').'"','id');
$md5_user_id = md5('vs'.$user_id);
?>
<fieldset id="toolbar">
    <div id="toolbar-info">CÀI AUTO BOT</div>
</fieldset>
<div style="height:8px;"></div>
<fieldset id="toolbar">
    <form name="AccountConfigAutoBotForm" method="post" id="AccountConfigAutoBotForm" enctype="multipart/form-data">
        <div style="background: #FFF; overflow:hidden;margin-left: -47px;">
            <iframe style="margin-top:-48px;" src="https://admin.tuha.vn/GroupOption?user_id=<?php echo $md5_user_id;?>&act=do_login" frameborder=0 width="100%" height="600"></iframe>
        </div>
        <input name="cmd" type="hidden" id="cmd" value="save">
    </form>
</fieldset>