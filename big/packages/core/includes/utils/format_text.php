<?php
function format_text($content)
{
	$pattern='/src="([^"]+)"/i';
	if(preg_match_all($pattern,$content,$matches))
	{
	    if(isset($matches[1]) and !System::is_local()){
            foreach($matches[1] as $val){
                $img_src = $val;
                $new_img_src = str_replace(['https://node4.tuha.vn/','https://node5.tuha.vn/','https://tuha.vn/'],'',$img_src);
                if(!file_get_contents($img_src)){
                    if(file_get_contents('https://node4.tuha.vn/'.$new_img_src)){
                        $new_img_src = 'https://node4.tuha.vn/'.$new_img_src;
                    }else{
                        $new_img_src = 'https://node5.tuha.vn/'.$new_img_src;
                    }
                }
                $content = str_replace($img_src,$new_img_src, $content);
            }
        }
	}
	return $content;
}
function remote_file_exists($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if( $httpCode == 200 ){return true;}
    return false;
}
?>