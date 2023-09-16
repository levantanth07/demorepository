<?php $j=1;?>
<!--LIST:news_categories-->
<div class="news-box">
    <div class="title">
       <h2><p>[[|news_categories.name|]]</p><span class="vart"></span></h2>
    </div><!--End .title-->
    <div class="main-news-box">
        <?php $i=1;?>
        <!--LIST:news_categories.items-->
        <?php if($i==1){?>
        <div class="news-first <?php echo ($j==1)?'first':'';?>">
            <a href="[[|news_categories.items.category_name_id|]]/[[|news_categories.items.name_id|]].html"><img src="[[|news_categories.items.small_thumb_url|]]"></a>
            <h3><a href="[[|news_categories.items.category_name_id|]]/[[|news_categories.items.name_id|]].html">[[|news_categories.items.name|]]</a></h3>
            <p>[[|news_categories.items.brief|]]</p>
        </div>
        <?php }?>
        <?php if($i==2){?>
        <div class="news-center">
            <a href="[[|news_categories.items.category_name_id|]]/[[|news_categories.items.name_id|]].html"><img src="[[|news_categories.items.small_thumb_url|]]"></a>
            <h3><a href="[[|news_categories.items.category_name_id|]]/[[|news_categories.items.name_id|]].html">[[|news_categories.items.name|]]</a></h3>
            <p>[[|news_categories.items.brief|]]</p>
        </div>
        <?php }?>
        <?php if($i>2){?>
        <?php if($i==3){?>
        <div class="news-last"><?php }?>
            <?php if($i%3==0){?><div class="col-xs-6 col-md-6">
                <ul><?php }?>
                    <li><a href="[[|news_categories.items.category_name_id|]]/[[|news_categories.items.name_id|]].html" title="[[|news_categories.items.name|]]">[[|news_categories.items.sort_name|]]</a></li>
                 <?php if($i+1==6 or $i+1==sizeof([[=news_categories.items=]])){?></ul>
           </div><?php }?>
        <?php if($i+1==sizeof([[=news_categories.items=]])){?></div><!--End .news-last--><?php }?>
        <?php }$i++;?>
        <!--/LIST:news_categories.items-->
    </div><!--End .main-news-box-->
</div><!--End .news-box-->
<?php $j++;?>
<!--/LIST:news_categories-->