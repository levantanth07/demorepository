<!--IF:cond(isset([[=str_ul_structure=]]))-->
<UL id="[[|css_template|]]">
	[[|str_ul_structure|]]
</UL>
<!--/IF:cond-->
<script type="text/javascript">
function navHover() {
	var lis = document.getElementById("[[|css_template|]]").getElementsByTagName("LI");
	for (var i=0; i<lis.length; i++) {
		lis[i].onmouseover=function() {
			this.className+="iehover";
		}
		lis[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" iehover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", navHover);
</script>
