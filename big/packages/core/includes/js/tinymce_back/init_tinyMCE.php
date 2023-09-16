<script type="text/javascript" src="packages/core/includes/js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
function simple_mce(id_list)
{
	tinyMCE.init({
		mode : "exact",
		elements:id_list,
		plugins : "insert_image,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
		theme_advanced_toolbar_location : "top",
		document_base_url:"upload/default/content/",
		theme_advanced_buttons1 :"save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,code,removeformat",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3:"",
		content_css : "assets/default/css/editor.css",
		file_browser_callback : "FileManager",
		theme_advanced_toolbar_align : "left",
		theme : "advanced"
	});
}
function advance_mce(id_list)
{
	tinyMCE.init({
		mode : "exact",
		theme : "advanced",
		document_base_url:"upload/default/content/",
		elements:id_list,
		plugins : "insert_image,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,code,removeformat",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_resizing : false,
		content_css : "assets/default/css/editor.css",
		file_browser_callback : "FileManager",
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
}
function FileManager(field_name, url, type, win) {
	var cmsURL = '?page=file_manager';
	tinyMCE.activeEditor.windowManager.open({
		file : cmsURL+'&type='+type,
		title : 'File_Manager',
		width : 800,
		height : 400,
		resizable : "yes",
		inline : "yes",
		close_previous : "no"
	}, {
		window : win,
		input : field_name
	});
	return false;
}
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if ( pressbutton == 'menulink' ) {
		if ( form.menuselect.value == "" ) {
			alert( "Please select a Menu" );
			return;
		} else if ( form.link_name.value == "" ) {
			alert( "Please enter a title for this Menu Item" );
			return;
		}
	}
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	var text = tinyMCE.getcontent();
	if (form.title.value == ""){
		alert( "Article must have a Title" );
	} else if (form.sectionid.value == "-1"){
		alert( "You must select a Section." );
	} else if (form.catid.value == "-1"){
		alert( "You must select a Category." );
	} else if (form.catid.value == ""){
		alert( "You must select a Category." );
	} else if (text == ""){
		alert( "Article must have some text" );
	} else {
		tinyMCE.triggerSave();
		submitform( pressbutton );
	}
}
</script>