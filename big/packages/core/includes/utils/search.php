<?php
/**
* @return string
* @param string
* @desc Strip forbidden tags and delegate tag-source check to removeEvilAttributes()
*/
function removeEvilTags($source)
{
   $source = strip_tags($source, '<h1><b><i><a><ul><li><pre><hr><blockquote><img><table><tr><td><div><span><center><p><br>');
   return preg_replace('/<(.*?)>/ie', "'<'.MiString::removeEvilAttributes('\\1').'>'", $source);
}
/**
* @return string
* @param string
* @desc Strip forbidden attributes from a tag
*/
function removeEvilAttributes($tagSource)
{
   return stripslashes(preg_replace('/'.'javascript:|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup'.'/i', 'forbidden', $tagSource));
}
function str_to_search_keyword($keywords,&$search_hightlight)
{
	//Dau vao la mot xau da duoc chuan hoa
	$st=$keywords;
	$st=strtr($st,"ร ","รร");
	$st=strtr($st," ","+");
	$st=strtr($st,"|"," ");
	$count=0;
	$result="";
	for($i=0;$i<strlen($st);$i++)
	{
		if($st[$i]=='"')
		{
			$count++;
			if($count%2)$result.='+';
		}
		$result.=$st[$i];
	}
	$st=$result;
	while(preg_match('/"([^\"]*)"/',$st,$a))
	{
		$search_hightlight[]=strtr($a[1],array("+"=>"\s+","รร"=>"ร "));
		$st=strtr($st,array($a[0]=>" "));
		$result=strtr($result,array($a[0]=>strtr($a[0],array("+"=>" "))));
	}
	//tach tu
	while(preg_match('/[^+\s-]+/',$st,$a))
	{
		$search_hightlight[]=strtr($a[0],array("รร"=>"ร "));
		$st=strtr($st,array($a[0]=>" "));
	}
	$result=preg_replace("/([+|])/"," \\1",$result);
	if($result[0]!='-')$result="+".$result;
	return strtr($result,array("รร"=>"ร "));
}
function keyword_extract($text){
    $punc =". , : ; ' ? ! ( ) \" \\";
    $punc = explode(" ",$punc);
    foreach($punc as $value){
		$text = str_replace($value," ",$text);
    }
	$words = explode(" ",$text);
	$text = "";
	$i = 0;
	foreach($words as $value)
	{
		if($value!="") $text.=$value.' ';
	};
    $nums = strlen($text);
	$text = substr($text,0,$nums-1);
	return $text;
}
function hightlight_keyword($source, $keywords)
{
	if($keywords)
	{
		foreach ($keywords as $key=>$value)
		{
			$source=str_replace($value,'<span style=\'background-color:yellow;color:black\'>'.$value.'</span>',$source);
			//$source=preg_replace(array("/^($value)([^\w])/i","/([^\w])($value)([^\w])/i"), array("<strong><span style='background-color:yellow;color:black'>\\1</span></strong>\\2","\\1<strong><span style='background-color:yellow;color:black'>\\2</span></strong>\\3"), $source);
			//$source=preg_replace(array("/^($value)([^\w])/i","/([^\w])($value)([^\w])/i"), array("<strong><span style='background-color:yellow;color:black'>\\1</span></strong>\\2","\\1<strong><span style='background-color:yellow;color:black'>\\2</span></strong>\\3"), $source);
		}
	}
	return $source;
}
function extend_search_keywords($keywords)
{
	$keywords = strtolower($keywords);
	if($words = preg_split('/([^\w]+)/',$keywords,-1,PREG_SPLIT_DELIM_CAPTURE))
	{
		$keywords = '';
		foreach($words as $i => $word)
		{
			$keywords .= $word;
			if($i%2==0 and $word)
			{
				$end_char = $word[strlen($word)-1];
				if(strlen($word) < 4 and (is_numeric($end_char) or
					(ord($end_char)>=ord('a') and ord($end_char)<=ord('z')) or $end_char=='_'))
				{
					$keywords .= 'kjfs';
				}
			}
		}
	}
	return $keywords;
}
?>