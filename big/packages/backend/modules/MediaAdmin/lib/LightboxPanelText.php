<script type="text/javascript" src="packages/core/includes/js/jquery/jquery.galleryview-1.1.js"></script>
<script type="text/javascript" src="packages/core/includes/js/jquery/jquery.timers-1.1.2.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#photos').galleryView({
			panel_width: 800,
			panel_height: 300,
			frame_width: 100,
			frame_height: 100,
			path:'assets/default/images/slide/LightboxPanelText/'
		});
	});
</script>
<div id="photos" class="galleryview">
	<!--LIST:items-->
	<div class="panel">
	     <img src="[[|items.image_url|]]"/>
	 </div>
 	<!--/LIST:items-->
	 <ul class="filmstrip">
	<!--LIST:items-->
		 <li><img src="[[|items.image_url|]]" alt="[[|items.name|]]" title="[[|items.name|]]" width="100" height="100" /></li>
	 	<!--/LIST:items-->
	 </ul>
</div>
