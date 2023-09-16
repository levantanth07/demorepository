<style>
    .article-search {
        display: flex;
        padding: 10px 0 0;
        align-items: center;
    }

    .mt-0 {
        margin-top: 0;
    }

    .empty-article {
        padding: 2rem 0;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <ul class="breadcrumb">
                <li><a href="" title="Trang chủ"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a></li>
                <li><a href="blog/">Bài viết</a></li>
                <!--IF:cond([[=category_name_id=]] != 'bai-viet')-->
                <li><a href="bai-viet/[[|category_name_id|]]/">[[|category_name|]]</a></li>
                <!--/IF:cond-->
                <!--IF:cond([[=tags=]])-->
                <li>Tags: [[|tags|]]</li>
                <!--/IF:cond-->
            </ul>
        </div>

        <!--IF:cond([[=show_searchbox=]])-->
        <div class="col-md-4">
            <form action="" class="article-search" method="POST">
                <input type="text" class="form-control jsInputKeyword" name="keyword" minlength="4" maxlength="60" aria-describedby="helpId" value="[[|keyword|]]" placeholder="Nhập từ khóa" />
                <button type="submit" class="btn btn-primary mt-0">
                    <i class="glyphicon glyphicon-search"></i> Tìm kiếm
                </button>
            </form>
        </div>
        <!--/IF:cond-->
        <div class="col-md-12">
            <div class="title">
                <h1>[[|category_name|]]</h1>
            </div>
            <!--End .df-title-h3-->
        </div>
        <!--IF:cond(![[=news=]])-->
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class='empty-article'>
                        <p>Không tìm thấy kết quả nào phù hợp cho từ khóa <b>[[|keyword|]]</b></p>
                    </div>
                </div>
            </div>
        </div>
        <!--/IF:cond-->
        <!--LIST:news-->
        <div class="col-md-6 col-lg-4 news-row">
            <div class="row">
                <div class="col-md-12">
                    <a href="bai-viet/[[|news.category_name_id|]]/[[|news.name_id|]]/" class="img-store">
                        <img src="[[|news.small_thumb_url|]]" alt="[[|news.name|]]" width="100%">
                    </a>
                </div>
                <div class="col-md-12">
                    <h4>
                        <a href="bai-viet/[[|news.category_name_id|]]/[[|news.name_id|]]/">[[|news.name|]]</a>
                    </h4>
                    <p class="news-brief">
                        [[|news.brief|]].
                        <a href="bai-viet/[[|news.category_name_id|]]/[[|news.name_id|]]/" class="label label-primary"> <i class="fa fa-angle-right"></i> Chi tiết</a>
                    </p>
                </div>
            </div>
        </div>
        <!--/LIST:news-->
        <br clear="all">
        <div class="paging">[[|paging|]]</div>
    </div>
</div>