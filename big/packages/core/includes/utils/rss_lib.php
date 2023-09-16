<?php
/*
	RSS Extractor and Displayer
	(c) 2007  Scriptol.com - Licence Mozilla 1.1.
	rsslib.php
	Requirements:
	- PHP 5.
	- A RSS feed.
	Using the library:
	Insert this code into the page that displays the RSS feed:
	<?php
	require_once("rsslib.php");
	echo RSS_Display("http://www.xul.fr/rss.xml", 25);
	?>
*/
$RSS_content = array();
function RSS_Tags($item, $type)
{
		$y = array();
		$tnl = $item->getElementsByTagName("title");
		$tnl = $tnl->item(0);
		$title = $tnl->firstChild->data;
		$tnl = $item->getElementsByTagName("link");
		$tnl = $tnl->item(0);
		$link = $tnl->firstChild->data;
		$tnl = $item->getElementsByTagName("description");
		$tnl = $tnl->item(0);
		$description = $tnl->firstChild->data;
		$tnl = $item->getElementsByTagName("pubDate");
		$tnl = $tnl->item(0);
		$pubDate = $tnl->firstChild->data;
		$y["title"] = $title;
		$y["link"] = $link;
		$y["description"] = $description;
		$y["type"] = $type;
		$y["pubDate"] = $pubDate;
		return $y;
}
function RSS_Channel($channel)
{
	global $RSS_content;
	$items = $channel->getElementsByTagName("item");
	// Processing channel
	$y = RSS_Tags($channel, 0);
	// get description of channel, type 0
	array_push($RSS_content, $y);
	// Processing articles
	foreach($items as $item)
	{
		$y = RSS_Tags($item, 1);	// get description of article, type 1
		array_push($RSS_content, $y);
	}
}
function RSS_Retrieve($url)
{
	global $RSS_content;
	$doc  = new DOMDocument();
	$doc->load($url);
	$channels = $doc->getElementsByTagName("channel");
	$RSS_content = array();
	foreach($channels as $channel)
	{
		 RSS_Channel($channel);
	}
}
function RSS_Display($url, $size)
{
	global $RSS_content;
	$opened = false;
	$page = "";
	RSS_Retrieve($url);
	if($size > 0)
	{
		$recents = array_slice($RSS_content, 0, $size);
	}
	foreach($recents as $article)
	{
		$type = $article["type"];
		if($type == 0)
		{
			if($opened == true)
			{
				$page .="</ul>\n";
				$opened = false;
			}
			$page .="";
		}
		else
		{
			if($opened == false)
			{
				$page .= "<ul style=\"list-style:decimal;margin:10px;\">\n";
				$opened = true;
			}
		}
		$title = $article["title"];
		$link = $article["link"];
		$pubDate = $article["pubDate"];
		$description = $article["description"];
		if($type == 0)
		{
			if(Portal::get_setting('estore_use_frame'))
			{
				$page .= CmsLib::get_frame('begin',Portal::get_setting('estore_use_frame'),$title);
				$page .= '<br><div>'.$description.'</div>';
			}
			else
			{
				$page .= '<a target="_blank" href="'.$link.'"><b>'.$title.'</b></a></div>
				<br><div>'.$description.'</div>';
			}
		}
		else
		{
			$page .= "<li style=\"text-align:justify;\"><a target=\"_blank\" href=\"$link\" style=\"font-size:13px;\"><b>$title</b></a>&nbsp;($pubDate)";
			if($description != false)
			{
				$page .= "<br>$description";
			}
			$page .= "</li>\n";
		}
		if($type==0)
		{
			$page .="<br />";
		}
	}
	if($opened == true)
	{
		if(Portal::get_setting('estore_use_frame'))
		{
			$page .= CmsLib::get_frame('end',Portal::get_setting('estore_use_frame'));
		}
		else
		{
			$page .="</ul>\n";
		}
	}
	return $page."\n";
}
?>