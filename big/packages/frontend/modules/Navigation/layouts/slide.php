<script type="text/javascript" src="packages/core/includes/js/category/jquery.js"></script>
<script type="text/javascript" src="packages/core/includes/js/category/chili-1.7.pack.js"></script>
<script type="text/javascript" src="packages/core/includes/js/category/jquery.easing.js"></script>
<script type="text/javascript" src="packages/core/includes/js/category/jquery.accordion.js"></script>
<script type="text/javascript">
	jQuery().ready(
		function(){
		jQuery('#navigation').Accordion({
				active: true,
				header: '.head',
				navigation: false,
				event: 'mouseover',
				autoheight: true,
				animated: 'bounceslide'//or "easeslid"
			});
		}
	);
	</script>
<UL id="navigation">
	[[|str_ul_structure|]]
</UL>