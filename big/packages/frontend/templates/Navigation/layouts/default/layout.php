<?php
if($this->map['categories'])
{
	Navigation::make_tree($this->map['categories'],$this->map);
	$this->map['str_ul_structure'] = set_ul_structure($this->map['categories']);
}
else
{
	$this->map['str_ul_structure'] = '';
}
?>
<!--IF:cond(isset([[=str_ul_structure=]]))-->
<UL id="navigation_<?php echo Module::block_id();?>" class="<?php
	if(preg_match('/packages\/(\w+)\/templates\/Navigation\/skins\/(\w+)/',Module::get_setting('list_skin_template'),$patterns)){
		echo $patterns[1].'-navigation-'.$patterns[2];
	}
?>">
	[[|str_ul_structure|]]
</UL><div style="clear:both"></div>
<!--/IF:cond-->
<script type="text/javascript">
function navHover() {
	var lis = document.getElementById("navigation_<?php echo Module::block_id();?>").getElementsByTagName("LI");
	for (var i=0; i<lis.length; i++) {
		lis[i].onmouseover=function() {
			this.className+=" iehover";
		}
		lis[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" iehover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", navHover);
</script>