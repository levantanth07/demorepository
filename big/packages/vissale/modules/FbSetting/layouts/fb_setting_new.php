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
<fieldset id="toolbar">
  <div id="toolbar-info">Facebook</div>
  <div id="toolbar-content">
  <table align="right">
    <tbody>
    <tr>
    </tr>
    </tbody>
  </table>
  </div>
</fieldset>
<div style="height:8px;"></div>
<fieldset id="toolbar">
<form name="AccountFbSettingForm" method="post" class="form-inline" id="AccountFbSettingForm" enctype="multipart/form-data">
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#tab1" aria-controls="messages" role="tab" data-toggle="tab">Fanpage đã đồng bộ</a></li>
    <li role="presentation"><a href="#tab2" aria-controls="home" role="tab" data-toggle="tab">Đồng  Fanpage trên Vichat</a></li>
  </ul>
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="tab1">
      <div style="background: #FFF;padding-top:5px;">
          <div class="pull-right" style="margin:5px 5px 5px 0px">
            <input name="keyword" type="text" name="keyword" class="form-control" style="width:300px;" placeholder="Tìm page" onchange="AccountFbSettingForm.cmd.value='';AccountFbSettingForm.submit();">
          </div>
          <table class="table table-bordered">
              <tr>
                  <th width="1%">#</th>
                  <th>ID của trang</th>
                  <th>Tên trang (Tổng: <?php echo sizeof([[=pages=]])?> trang)</th>
                  <th width="10%">Mẫu câu trả lời</th>
                  <!--IF:cond(User::can_admin(false,ANY_CATEGORY))-->
                  <th width="10%" class="text-center">Xóa page</th>
                  <!--/IF:cond-->
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
                  <td align="center"><a href="<?php echo Url::build_current(array('cmd'=>'reply_bank','page_id'=>[[=pages.id=]]))?>" class="btn btn-success">Khai báo ([[|pages.had_reply|]])</a></td>
                  <!--IF:cond(User::can_admin(false,ANY_CATEGORY))-->
                  <td align="center"><a onclick="if(!confirm('Bạn có chắc chắn xóa page không?')){return false}" href="<?php echo Url::build_current(array('cmd'=>'delete','id'=>[[=pages.id=]]))?>" class="btn btn-danger">Xóa</a></td>
                  <!--/IF:cond-->
              </tr>
              <?php $i++?>
              <!--/LIST:pages-->
          </table>
      </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="tab2">
          <div>
              <table class="table table-bordered table-striped">
                  <tr>
                      <th width="1%">#</th>
                      <th>ID của trang</th>
                      <th>Tên trang (Tổng: <?php echo sizeof([[=vichat_pages=]])?> trang)</th>
                  </tr>
                  <?php $i=1?>
                  <!--LIST:vichat_pages-->
                  <tr>
                      <td><?php echo $i;?></td>
                      <td>
                          <a href="https://facebook.com/[[|vichat_pages.id|]]/" target="_blank">[[|vichat_pages.id|]]</a>
                      </td>
                      <td>[[|vichat_pages.name|]]</td>
                  </tr>
                  <?php $i++?>
                  <!--/LIST:vichat_pages-->
              </table>
          </div>
    </div>
  </div>
<input name="cmd" type="hidden" id="cmd" value="save">
</form>
</fieldset>
