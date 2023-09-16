<div class="row qlbh-news">
  <div class="col-md-12">
  <h2 class="header"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Thông tin từ ban quản trị</h2>
      <div class="row">
          <div class="col-md-12">
            <!--IF:cond(!empty([[=news_all=]]))-->
            <!--LIST:news_all-->
              <div class="similar">
                  <!--IF:img_cond(!empty([[=small_thumb_url=]]))-->
                  <div class="img-container">
                     <img src="[[|news_all.small_thumb_url|]]" />
                  </div>
                  <!--/IF:img_cond-->              
                  <h3>[[|news_all.name|]]</h3>
                  <span class="time">Thời gian: <?php echo date('d/m/Y',[[=news_all.time=]])?> </span>
                  <p><?php echo MiString::display_sort_title(strip_tags([[=news_all.description=]]),100);?></p>
              </div>
              <!--/LIST:news_all-->
              <div class="paging">
                  [[|paging|]]
              </div><!--End .pt-->
              <!--ELSE-->
              <div class="note"><h2>Không có tin bài trong chuyên mục này</h2></div>
              <!--/IF:cond-->
          </div>
      </div>
  </div>
</div><!--End .row-->