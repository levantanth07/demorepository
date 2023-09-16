<script type="text/javascript" src="packages/core/includes/js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
function member_mce(id_list)
{
	tinyMCE.init({
		mode : "exact",
		elements:id_list,
		plugins : "tabfocus,insert_image,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
		theme_advanced_toolbar_location : "top",
		document_base_url:"upload/default/content/",
		theme_advanced_buttons1 :"bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,|,preview,|,forecolor,backcolor",
		theme_advanced_buttons3:"",
		content_css : "skins/default/css/editor.css",
		theme_advanced_toolbar_align : "left",
		theme : "advanced",
		tab_focus : ':prev,:next'
	});
}
</script>