function load_ajax(pram,block)
{
//	jQuery('#module_'+block).append()
	jQuery.ajax({
		method: "POST",url: 'form.php?block_id='+block,
		data : pram,
		beforeSend: function(){
			//jQuery('#load').fadeIn(10).animate({opacity: 1.0}, 10);
		},
		success: function(content){
			//jQuery('#load').fadeOut(1000);
			//jQuery('#loading').hide()
			document.getElementById('module_'+block).innerHTML=content;
		}
	});
}