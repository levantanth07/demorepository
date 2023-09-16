if(typeof(TCV)=='undefined')
{
	TCV = {};
}
if(!TCV.Ajax)
{
	TCV.Ajax =	{
		processResponse: function(){
			if (TCV.Ajax.httpRequest.readyState == 4) {
				if (TCV.Ajax.httpRequest.status >= 200 && TCV.Ajax.httpRequest.status < 300) {
					TCV.Ajax.options.success(TCV.Ajax.httpRequest);
					jQuery("#loading-layer").fadeOut(500);
				} else {
					TCV.Ajax.options.failure();
				}
		   }else{
				TCV.Ajax.options.onProgress();
		   }
		},
		failure:function()
		{
		},
		onProgress:function()
		{
			jQuery("#loading-layer").fadeIn(100);
		},
		request:function(options)
		{
			if(!TCV.Ajax.httpRequest)
			{
				if(window.XMLHttpRequest){ //Mozilla, Safari,...
					TCV.Ajax.httpRequest = new XMLHttpRequest();
					if (TCV.Ajax.httpRequest.overrideMimeType) {
						TCV.Ajax.httpRequest.overrideMimeType('text/xml');
					}
				} if (window.ActiveXObject) { // IE
					try {
						TCV.Ajax.httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
					}
					catch (e) {
					   try {
							TCV.Ajax.httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
						   }
						catch (e) {}
					 }
				}
			}
			TCV.Ajax.options = options;
			if(!TCV.Ajax.options.failure)
			{
				TCV.Ajax.options.failure = TCV.Ajax.failure;
			}
			if(!TCV.Ajax.options.onProgress)
			{
				TCV.Ajax.options.onProgress = TCV.Ajax.onProgress;
			}
			if(!TCV.Ajax.options.method)
			{
				TCV.Ajax.options.method = 'GET';
			}
			if(!TCV.Ajax.options.params)
			{
				TCV.Ajax.options.params = {};
			}
			var param_str = '';
			for(var i in TCV.Ajax.options.params)
			{
				param_str += (param_str?'&':'')+encodeURI(i)+'='+encodeURI(TCV.Ajax.options.params[i]);
			}
			TCV.Ajax.options.method = 'GET';
			if(TCV.Ajax.options.method == 'GET')
			{
				TCV.Ajax.options.url += '&'+param_str;
				param_str = null;
			}
			TCV.Ajax.httpRequest.abort();
			TCV.Ajax.httpRequest.onreadystatechange = TCV.Ajax.processResponse;
			TCV.Ajax.httpRequest.open(TCV.Ajax.options.method,TCV.Ajax.options.url,true);
			TCV.Ajax.httpRequest.setRequestHeader("label", "value");
			if(TCV.Ajax.options.method == 'POST')
			{
				TCV.Ajax.httpRequest.setRequestHeader("content-type", "application/x-www-form-urlencoded");
				TCV.Ajax.httpRequest.setRequestHeader("content-length", param_str.length);
				TCV.Ajax.httpRequest.setRequestHeader("Connection", "close");
			}
			TCV.Ajax.httpRequest.send(param_str);
		}
	}
}