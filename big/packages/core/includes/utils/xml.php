<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY thedeath
******************************/
class XML
{
	public static $dom;
	function read($file)
	{
		XML::$dom = new DOMDocument();
		return XML::$dom->load($file);
	}
	function create_xml($file,$items)
	{
		$content  = '<?xml version="1.0" encoding="utf-8"?>';
		$content .= '<items>';
		foreach($items as $key=>$item)
		{
			$content .= '<item id="'.$key.'">';
			foreach($item as $key_ => $value)
			{
				$content .= '<'.$key_.'>'. $value.'</'.$key_.'>';
			}
			$content .= '</item>';
		}
		$content .= '</items>';
		$fp = @fopen($file.'.xml','w');
		if(!$fp)
		{
			die('Error cannot create XML file');
		}
		fwrite($fp,$content);
		fclose($fp);
	}
	function fetch_all($file)
	{
		if(XML::read($file) and $doc=XML::$dom->documentElement and $doc = $doc->childNodes)
		{
			$items = array();
			if($doc->length>0)
			{
				foreach($doc as $value)
				{
					if($value->hasAttribute('id'))
					{
						$items[$value->getAttribute('id')] = array();
					}
					if($value->nodeType == XML_ELEMENT_NODE and $value->childNodes->length>1)
					{
						foreach($value->childNodes as $node)
						{
							if($node->nodeName!='#text')
							{
								$items[$value->getAttribute('id')] += array($node->nodeName => $node->nodeValue);
							}
						}
					}
					else if($value->nodeName!='#text')
					{
						$items[$value->nodeName] = $value->nodeValue;
					}
				}
			}
			return $items;
		}
		return false;
	}
}
?>