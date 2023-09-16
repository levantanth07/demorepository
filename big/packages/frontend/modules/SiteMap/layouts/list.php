<div class="gallery-bound">
	<div class="frame-home-title" style="width: 676px;">
        <div class="fr-ht-bound">
            <div class="fr-ht-b-left">
            	[[.Site_map.]]
            </div>
            <div class="fr-ht-b-right"></div>
            <div class="fr-ht-b-end-left"></div>
        </div><!--End .fr-ht-bound-->
        <!--Gach ngang cuoi title-->
        <div style="width: 675px;height: 1px;background:#e5e5e5;"></div>
        <div style="clear: both;"></div>
    </div>
    <div class="site-map-bound" style="border:solid 1px gainsboro;height:554px;width:636px;">
        <!--LIST:pages-->
        <div class="site-map-content">
            <!--IF:cond1(isset([[=pages.childs=]]))-->
                <!--LIST:pages.childs-->
                    <div class="site-map-child-<?php echo IDStructure::level([[=pages.childs.structure_id=]]);?>">
                        <a href="<?php if([[=pages.childs.url=]]){ echo [[=pages.childs.url=]]; }else{ echo Url::build([[=pages.name=]],array('name_id'=>[[=pages.childs.name_id=]]),REWRITE); }?>">[[|pages.childs.name|]]</a>
                    </div>
                <!--/LIST:pages.childs-->
            <!--ELSE-->
                <!--IF:cond2(isset([[=pages.url=]]) and [[=pages.url=]])-->
                    <a href="[[|pages.url|]]" class="site-map-content-link"><span>[[|pages.title|]]</span></a>
                <!--ELSE-->
                    <a href="<?php echo Url::build('trang-tin',array('name_id'=>[[=pages.name=]]),REWRITE);?>" class="site-map-content-link"><span>[[|pages.title|]]</span></a>
                <!--/IF:cond2-->
            <!--/IF:cond1-->
        </div>
        <!--/LIST:pages-->
    </div>
</div>