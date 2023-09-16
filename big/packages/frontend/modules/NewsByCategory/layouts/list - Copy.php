<div class="container">
  <!--LIST:news_categories-->
  <div class="list news">
    <h2 class="title"><a href="trang-tin/[[|news_categories.name_id|]].html" title="[[|news_categories.name|]]">[[|news_categories.name|]]</a></h2>
    <div class="view-more"><a href="trang-tin/[[|news_categories.name_id|]].html" title="[[|news_categories.name|]]">Xem thêm &raquo;</a></div>
    <ul>
    	<?php $i=1;?>
        <!--LIST:news_categories.items-->
        <li>
            <a href="xem-tin/[[|news_categories.items.name_id|]].html" title="[[|news_categories.items.name|]]"><img src="[[|news_categories.items.small_thumb_url|]]"/><!--IF:cond([[=news_categories.items.status=]]=='HOT')--><img src="assets/current/images/hot.gif" style="width:auto !important;height:auto !important;" align="top"/><!--/IF:cond--><?php echo String::display_sort_title(strip_tags([[=news_categories.items.name=]]),15);?></a>
            <?php if($i==1){echo '<div class="brief">'.String::display_sort_title(strip_tags([[=news_categories.items.brief=]]),40).'</div>';} $i++;?>
        </li>
        <!--/LIST:news_categories.items-->
    </ul>
  </div><!--End #tabs-->
  <!--/LIST:news_categories-->
</div><!--End .container-->
<script type="text/javascript">
function update_hitcount_(id){
	jQuery.ajax({
		method: "POST",
		url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
		data : {
			'do':'update_hitcount',
			'id':id
		},
		success: function(){
		}
	});
}
</script>