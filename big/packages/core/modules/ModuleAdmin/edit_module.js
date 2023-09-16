function edit_code_keypress(obj)
{
	if(typeof(event)=='undefined')
	{
		event = obj.event;
	}
	switch(event.keyCode)
	{
	case 9:
		var oSelection = obj.ownerDocument.selection;
		var oRange = oSelection.createRange();
		oRange.text='	';
		return true;
	case 13:
		var oSelection = obj.ownerDocument.selection;
		var oRange = oSelection.createRange();
		var oRange2 = oRange.duplicate();
		var i=0;
		var anchor_text = '<TEXTAREA id=';
		var st = '';
		do{
			st = oRange2.htmlText;
			oRange2.moveStart('character','-1');
		}
		while(oRange2.htmlText.indexOf(anchor_text)==-1 && oRange2.htmlText.substring(0,1)!=String.fromCharCode(13));
		i=0;
		while(i<st.length && (st.charCodeAt(i)==9 || st.charCodeAt(i)==32))
		{
			i++;
		}
		st = st.substring(0,i);
		oRange.text=String.fromCharCode(10)+String.fromCharCode(13)+st;
		return true;
	case 83:
	case 115:
		if(event.ctrlKey)
		{
			obj.form.submit();
			return true;
		}
	}
}