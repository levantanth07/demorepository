echo ('<style>.image_list_item,.image_list_item_selected\
{\
	display:block;\
	width:80px;\
	height:80px;\
	float:left;\
	border:2px solid white;\
	font-size:10px;\
	text-align:center;\
	line-height:10px;\
}\
.image_list_item_selected\
{\
	border:2px solid black;\
}</style>');
function select_image_list(obj,id,key)
{
	if($(id).value!=key)
	{
		for(var i in obj.parentNode.childNodes)
		{
			obj.parentNode.childNodes[i].className = 'image_list_item';
		}
		obj.className = 'image_list_item_selected';
		$(id).value = key;
	}
}