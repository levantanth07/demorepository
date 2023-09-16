<script type="text/javascript" src="packages/core/includes/js/jquery/jquery-1.2.6.js"></script>
<script type="text/javascript" src="packages/core/includes/js/jquery/jquery.galleryview-1.1.js"></script>
<script type="text/javascript" src="packages/core/includes/js/jquery/jquery.timers-1.1.2.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#photos').galleryView({
			panel_width: 800,
			panel_height: 300,
			transition_speed: 1500,
			transition_interval: 5000,
			nav_theme: 'dark',
			border: '1px solid white',
			pause_on_hover: true,
			path:'assets/default/images/slide/LightboxPanel/'
		});
	});
</script>
<div id="photos" class="galleryview">
	<!--LIST:items-->
	<div class="panel">
	     <img src="[[|items.image_url|]]"/>
	  </div>
 	<!--/LIST:items-->
</div>
