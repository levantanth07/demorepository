<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li><a href="" title="Trang chủ"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a></li>
                <li><a href="bai-viet/">Bài viết</a></li>
                <!--IF:cond([[=category_name_id=]] != 'bai-viet')-->
                <li><a href="bai-viet/[[|category_name_id|]]/">[[|category_name|]]</a></li>
                <!--/IF:cond-->
            </ul>
        </div>
        <div class="col-md-12">
            <div class="box box-info news-detail">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-<?=([[=category_name_id=]] != 'chinh-sach' and [[=category_name_id=]] != 'gioi-thieu')?'8':'12'?>">
                                    <div class="box-header">
                                        <h1 class="box-title"><span>[[|name|]]</span></h1>
                                    </div>
                                    <div class="desc">
                                        [[|description|]]
                                    </div>
                                    <!--IF:cond([[=tuha_content_admin=]])-->
                                    <div class="pull-right">
                                        <a href="<?=Url::build('news_admin')?>&cmd=edit&id=[[|id|]]" class="btn btn-warning btn-sm">Sửa bài</a>
                                        <br>
                                        <?=date('H:i d/m/Y',[[=time=]])?>
                                    </div>
                                    <!--/IF:cond-->
                                    <!--IF:cond([[=tags=]])-->
                                    <div class="tag">Tags: [[|tags|]]</div>
                                    <!--/IF:cond-->
                                </div>
                                <div class="col-md-<?=([[=category_name_id=]] != 'chinh-sach' and [[=category_name_id=]] != 'gioi-thieu')?'4':'12'?>">
                                    <div <?=([[=category_name_id=]] != 'chinh-sach' and [[=category_name_id=]] != 'gioi-thieu')?'':'class="hidden"'?>><a href="https://tuha.vn/bao-gia-tuha" title="Xem bảng báo giá QLBH"><img src="assets/standard/images/gift/GIF-6-640-x-360.gif" alt="QLBH" class="img-responsive"></a></div>
                                    <!--IF:cond(!empty([[=r_news=]]))-->
                                    <div class="related-news">
                                        <h2 class="title">Bài viết liên quan</h2>
                                        <ul>
                                            <!--LIST:r_news-->
                                            <li>
                                                <a href="bai-viet/[[|r_news.category_name_id|]]/[[|r_news.name_id|]]/">
                                                    <i class="fa fa-angle-right"></i> [[|r_news.name|]]
                                                </a>
                                            </li>
                                            <!--/LIST:r_news-->
                                        </ul>
                                    </div>
                                    <!--/IF:cond-->
                                    <div class="related-news more">
                                        <h2 class="title">Tham khảo thêm</h2>
                                        <ul>
                                            <li>
                                                <a href="https://tuha.vn/phan-mem-ban-hang.html/" target="_blank">
                                                    <i class="fa fa-check-square-o"></i> Phần mềm bán hàng
                                                </a>
                                            </li>
                                            <a href="https://tuha.vn/phan-mem-quan-ly-ban-hang.html/" target="_blank">
                                                <i class="fa fa-check-square-o"></i> Phần mềm quản lý bán hàng
                                            </a>
                                            </li>
                                            <li>
                                                <a href="https://tuha.vn" target="_blank">
                                                    <i class="fa fa-check-square-o"></i> Phần mềm quản lý bán hàng online tốt nhất
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://pages.tuha.vn" target="_blank">
                                                    <i class="fa fa-check-square-o"></i> Phần mềm quản lý bán hàng Facebook tốt nhất
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://beauty.shopal.vn/" target="_blank">
                                                    <i class="fa fa-check-square-o"></i> Phần mềm quản lý SPA tốt nhất
                                                </a>
                                            </li>
                                            <li>
                                                <a href="https://work.tuha.vn" target="_blank">
                                                    <i class="fa fa-check-square-o"></i> Phần mềm chấm công tốt nhất
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="box-two blog-facebook text-center">
                                        <div style="font-size:20px;font-weight: bold;padding: 20px 0px 20px;">[QLBH ] Phần mềm quản lý bán hàng Online tốt nhất hiện nay</div>
                                        <a href="https://app.tuha.vn/dang-nhap?do=dang-ky&ref=[[|account_id|]]" class="btn btn-success btn-lg"> <i class="fa fa-hand-o-right"></i> DÙNG THỬ MIỄN PHÍ</a>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>