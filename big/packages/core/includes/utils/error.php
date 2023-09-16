<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY vuonggialong
******************************/
function DBG_GetBacktrace()
{
	$vDebug = debug_backtrace();
	$vFiles = array();
	for ($i=0;$i<count($vDebug);$i++) {
		// skip the first one, since it's always this func
		$aFile = $vDebug[$i];
		if(isset($aFile['file']) and isset($aFile['function'])and isset($aFile['args']))
		{
			if ($i==0 or strpos(str_replace('\\','/',$aFile['file']),'packages/core/includes/')!==false or $aFile['function']=='usererrorhandler') { continue; }
			$vFiles[] = $aFile['function'].'('.($aFile['args']?'<pre>'.var_export($aFile['args'],true).'</pre>':'').') <b>at '.str_replace(ROOT_PATH,'',str_replace('\\','/',$aFile['file'])).':'.$aFile['line'].'</b>';
		}
	}
	return '<font face="Courier New,Courier" size=2>'.implode(',<br>',$vFiles).'</font>';
}
?>