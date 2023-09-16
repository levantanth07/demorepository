(function(){var c=tinymce.DOM,a=tinymce.dom.Event,d=tinymce.each,b=tinymce.is;tinymce.create("tinymce.plugins.Compat2x",{getInfo:function(){return{longname:"Compat2x",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/compat2x",version:tinyMCE.majorVersion+"."+tinyMCE.minorVersion}}});(function(){tinymce.extend(tinyMCE,{addToLang:function(f,e){d(e,function(h,g){tinyMCE.i18n[(tinyMCE.settings.language||"en")+"."+(f?f+"_":"")+g]=h})},getInstanceById:function(e){return this.get(e)}})})();(function(){var e=tinymce.EditorManager;tinyMCE.instances={};tinyMCE.plugins={};tinymce.PluginManager.onAdd.add(function(f,h,g){tinyMCE.plugins[h]=g});tinyMCE.majorVersion=tinymce.majorVersion;tinyMCE.minorVersion=tinymce.minorVersion;tinyMCE.releaseDate=tinymce.releaseDate;tinyMCE.baseURL=tinymce.baseURL;tinyMCE.isIE=tinyMCE.isMSIE=tinymce.isIE||tinymce.isOpera;tinyMCE.isMSIE5=tinymce.isIE;tinyMCE.isMSIE5_0=tinymce.isIE;tinyMCE.isMSIE7=tinymce.isIE;tinyMCE.isGecko=tinymce.isGecko;tinyMCE.isSafari=tinymce.isWebKit;tinyMCE.isOpera=tinymce.isOpera;tinyMCE.isMac=false;tinyMCE.isNS7=false;tinyMCE.isNS71=false;tinyMCE.compat=true;TinyMCE_Engine=tinyMCE;tinymce.extend(tinyMCE,{getParam:function(g,f){return this.activeEditor.getParam(g,f)},addEvent:function(i,g,h,j){tinymce.dom.Event.add(i,g,h,j||this)},getControlHTML:function(f){return e.activeEditor.controlManager.createControl(f)},loadCSS:function(f){tinymce.DOM.loadCSS(f)},importCSS:function(g,f){if(g==document){this.loadCSS(f)}else{new tinymce.dom.DOMUtils(g).loadCSS(f)}},log:function(){console.debug.apply(console,arguments)},getLang:function(h,g){var f=e.activeEditor.getLang(h.replace(/^lang_/g,""),g);if(/^[0-9\-.]+$/g.test(f)){return parseInt(f)}return f},isInstance:function(f){return f!=null&&typeof(f)=="object"&&f.execCommand},triggerNodeChange:function(){e.activeEditor.nodeChanged()},regexpReplace:function(j,f,h,i){var g;if(j==null){return j}if(typeof(i)=="undefined"){i="g"}g=new RegExp(f,i);return j.replace(g,h)},trim:function(f){return tinymce.trim(f)},xmlEncode:function(f){return tinymce.DOM.encode(f)},explode:function(f,h){var g=[];tinymce.each(f.split(h),function(i){if(i!=""){g.push(i)}});return g},switchClass:function(h,g){var f;if(/^mceButton/.test(g)){f=e.activeEditor.controlManager.get(h);if(!f){return}switch(g){case"mceButtonNormal":f.setDisabled(false);f.setActive(false);return;case"mceButtonDisabled":f.setDisabled(true);return;case"mceButtonSelected":f.setActive(true);f.setDisabled(false);return}}},addCSSClass:function(g,h,f){return tinymce.DOM.addClass(g,h,f)},hasCSSClass:function(f,g){return tinymce.DOM.hasClass(f,g)},removeCSSClass:function(f,g){return tinymce.DOM.removeClass(f,g)},getCSSClasses:function(){var f=e.activeEditor.dom.getClasses(),g=[];d(f,function(h){g.push(h["class"])});return g},setWindowArg:function(g,f){e.activeEditor.windowManager.params[g]=f},getWindowArg:function(i,g){var h=e.activeEditor.windowManager,f;f=h.getParam(i);if(f===""){return""}return f||h.getFeature(i)||g},getParentNode:function(h,g){return this._getDOM().getParent(h,g)},selectElements:function(o,k,m){var l,j=[],h,g;for(g=0,k=k.split(",");g<k.length;g++){for(l=0,h=o.getElementsByTagName(k[g]);l<h.length;l++){(!m||m(h[l]))&&j.push(h[l])}}return j},getNodeTree:function(i,f,g,h){return this.selectNodes(i,function(j){return(!g||j.nodeType==g)&&(!h||j.nodeName==h)},f?f:[])},getAttrib:function(g,h,f){return this._getDOM().getAttrib(g,h,f)},setAttrib:function(g,h,f){return this._getDOM().setAttrib(g,h,f)},getElementsByAttributeValue:function(m,k,g,h){var j,f=m.getElementsByTagName(k),l=[];for(j=0;j<f.length;j++){if(tinyMCE.getAttrib(f[j],g).indexOf(h)!=-1){l[l.length]=f[j]}}return l},selectNodes:function(k,j,g){var h;if(!g){g=[]}if(j(k)){g[g.length]=k}if(k.hasChildNodes()){for(h=0;h<k.childNodes.length;h++){tinyMCE.selectNodes(k.childNodes[h],j,g)}}return g},getcontent:function(){return e.activeEditor.getcontent()},getParentElement:function(i,g,h){if(g){g=new RegExp("^("+g.toUpperCase().replace(/,/g,"|")+")$","g")}return this._getDOM().getParent(i,function(f){return f.nodeType==1&&(!g||g.test(f.nodeName))&&(!h||h(f))},this.activeEditor.getBody())},importPluginLanguagePack:function(f){tinymce.PluginManager.requireLangPack(f)},getButtonHTML:function(l,j,h,k,i,g){var f=e.activeEditor;h=h.replace(/\{\$pluginurl\}/g,tinyMCE.pluginURL);h=h.replace(/\{\$themeurl\}/g,tinyMCE.themeURL);j=j.replace(/^lang_/g,"");return f.controlManager.createButton(l,{title:j,command:k,ui:i,value:g,scope:this,"class":"compat",image:h})},addSelectAccessibility:function(h,g,f){if(!g._isAccessible){g.onkeydown=tinyMCE.accessibleEventHandler;g.onblur=tinyMCE.accessibleEventHandler;g._isAccessible=true;g._win=f}return false},accessibleEventHandler:function(g){var h,f=this._win;g=tinymce.isIE?f.event:g;h=tinymce.isIE?g.srcElement:g.target;if(g.type=="blur"){if(h.oldonchange){h.onchange=h.oldonchange;h.oldonchange=null}return true}if(h.nodeName=="SELECT"&&!h.oldonchange){h.oldonchange=h.onchange;h.onchange=null}if(g.keyCode==13||g.keyCode==32){h.onchange=h.oldonchange;h.onchange();h.oldonchange=null;tinyMCE.cancelEvent(g);return false}return true},cancelEvent:function(f){return tinymce.dom.Event.cancel(f)},handleVisualAid:function(f){e.activeEditor.addVisual(f)},getAbsPosition:function(g,f){return tinymce.DOM.getPos(g,f)},cleanupEventStr:function(f){f=""+f;f=f.replace("function anonymous()\n{\n","");f=f.replace("\n}","");f=f.replace(/^return true;/gi,"");return f},getVisualAidClass:function(f){return f},parseStyle:function(f){return this._getDOM().parseStyle(f)},serializeStyle:function(f){return this._getDOM().serializeStyle(f)},openWindow:function(h,g){var f=e.activeEditor,i={},j;for(j in h){i[j]=h[j]}h=i;g=g||{};h.url=new tinymce.util.URI(tinymce.ThemeManager.themeURLs[f.settings.theme]).toAbsolute(h.file);h.inline=h.inline||g.inline;f.windowManager.open(h,g)},closeWindow:function(f){e.activeEditor.windowManager.close(f)},getOuterHTML:function(f){return tinymce.DOM.getOuterHTML(f)},setOuterHTML:function(g,f,i){return tinymce.DOM.setOuterHTML(g,f,i)},hasPlugin:function(f){return tinymce.PluginManager.get(f)!=null},_setEventsEnabled:function(){},addPlugin:function(g,i){var h=this;function j(f){tinyMCE.selectedInstance=f;f.onInit.add(function(){h.settings=f.settings;h.settings.base_href=tinyMCE.documentBasePath;tinyMCE.settings=h.settings;tinyMCE.documentBasePath=f.documentBasePath;if(i.initInstance){i.initInstance(f)}f.contentDocument=f.getDoc();f.contentWindow=f.getWin();f.undoRedo=f.undoManager;f.startcontent=f.getcontent({format:"raw"});tinyMCE.instances[f.id]=f;tinyMCE.loadedFiles=[]});f.onActivate.add(function(){tinyMCE.settings=f.settings;tinyMCE.selectedInstance=f});if(i.handleNodeChange){f.onNodeChange.add(function(l,k,m){i.handleNodeChange(l.id,m,0,0,false,!l.selection.isCollapsed())})}if(i.onChange){f.onChange.add(function(k,l){return i.onChange(k)})}if(i.cleanup){f.onGetcontent.add(function(){})}this.getInfo=function(){return i.getInfo()};this.createControl=function(k){tinyMCE.pluginURL=tinymce.baseURL+"/plugins/"+g;tinyMCE.themeURL=tinymce.baseURL+"/themes/"+tinyMCE.activeEditor.settings.theme;if(i.getControlHTML){return i.getControlHTML(k)}return null};this.execCommand=function(l,k,m){if(i.execCommand){return i.execCommand(f.id,f.getBody(),l,k,m)}return false}}tinymce.PluginManager.add(g,j)},_getDOM:function(){return tinyMCE.activeEditor?tinyMCE.activeEditor.dom:tinymce.DOM},convertRelativeToAbsoluteURL:function(f,g){return new tinymce.util.URI(f).toAbsolute(g)},convertAbsoluteURLToRelativeURL:function(f,g){return new tinymce.util.URI(f).toRelative(g)}});tinymce.extend(tinymce.Editor.prototype,{getFocusElement:function(){return this.selection.getNode()},getData:function(f){if(!this.data){this.data=[]}if(!this.data[f]){this.data[f]=[]}return this.data[f]},hasPlugin:function(f){return this.plugins[f]!=null},getContainerWin:function(){return window},getHTML:function(f){return this.getcontent({format:f?"raw":"html"})},setHTML:function(f){this.setcontent(f)},getSel:function(){return this.selection.getSel()},getRng:function(){return this.selection.getRng()},isHidden:function(){var f;if(!tinymce.isGecko){return false}f=this.getSel();return(!f||!f.rangeCount||f.rangeCount==0)},translate:function(f){var h=this.settings.language,g;if(!f){return f}g=tinymce.EditorManager.i18n[h+"."+f]||f.replace(/{\#([^}]+)\}/g,function(j,i){return tinymce.EditorManager.i18n[h+"."+i]||"{#"+i+"}"});g=g.replace(/{\$lang_([^}]+)\}/g,function(j,i){return tinymce.EditorManager.i18n[h+"."+i]||"{$lang_"+i+"}"});return g},repaint:function(){this.execCommand("mceRepaint")}});tinymce.extend(tinymce.dom.Selection.prototype,{getSelectedText:function(){return this.getcontent({format:"text"})},getSelectedHTML:function(){return this.getcontent({format:"html"})},getFocusElement:function(){return this.getNode()},selectNode:function(i,j,g,f){var h=this;h.select(i,g||0);if(!b(j)){j=true}if(j){if(!b(f)){f=true}h.collapse(f)}}})}).call(this);tinymce.PluginManager.add("compat2x",tinymce.plugins.Compat2x)})();