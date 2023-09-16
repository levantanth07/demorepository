<?php
$hot_news = DB::fetch('select news.id,news.name_id,news.name_'.Portal::language().' as name from news inner join category on category.id=news.category_id where '.IDStructure::child_cond(DB::structure_id('category','468')).' order by news.time desc');
?>
<table cellpadding="5" cellspacing="0" width="100%" style="margin-bottom:5px;">
    <tr>
        <td style="padding-left:0px;" width="1%" nowrap="nowrap"><strong><?php echo Portal::language('Hot_news');?> : </strong></td>
        <td><marquee scrolldelay=10 scrollamount=4 onmouseover="this.stop();" onmouseout="this.start();"><a style="color:red;" href="<?php echo Url::build('xem-tin-tuc',array('name_id'=>$hot_news['name_id']),REWRITE)?>"><?php echo $hot_news['name'];?></a></marquee></td>
    </tr>
</table>