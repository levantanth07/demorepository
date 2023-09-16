<?php
function get_dir($dir){
	if(@is_dir(ROOT_PATH.$dir)){
		return $dir;
	}else{
		$new_dir = @mkdir(ROOT_PATH.$dir);
		return $dir;
	}
}
function my_mkdir($dir){
	$temp = explode('/',$dir);
	$new_dir = '';
	$i=0;
	foreach($temp as $key=>$value){
		if($value){
			$new_dir = get_dir($new_dir.(($i>0)?'/':'').$value);
			$i++;
		}
	}
	return $new_dir;
}
function write_file($file,$content){	
	$handler = fopen($file,'w');
	fwrite($handler,$content);
	fclose($handler);
}
function get_content($file){
	return @file_get_contents($file);
}
function save_file($file,$table,$id){
	$max_upload_file_size =Portal::get_setting('max_upload_file_size',2*1024*1024);
	$type_file=Portal::get_setting('type_upload_file','png|jpg|jpeg|gif|txt|doc|docx|pdf|swf|xls|xlsx');		
	foreach($table as $key=>$value){
		if(isset($file[$key]) and is_array($file[$key]) and $file[$key]){
			foreach($file[$key]['name'] as $name=>$src){
				if($src and $src!="" and preg_match('/\.('.$type_file.')$/i',strtolower($src)) and filesize($file[$key]['tmp_name'][$name])< $max_upload_file_size){
					$path=ManageContentDB::make_folfer(str_replace('#','',PORTAL_ID));
					
					$new_name=$path.'/'.time().'_'.convert_utf8_to_latin($src);
					
					if(move_uploaded_file($file[$key]['tmp_name'][$name], $new_name)){	
						DB::update_id(strtolower($key),array($name=>$new_name),$id);		
						
					}							
				}			
			}
			
		}
	}		
}
function update_upload_file($field, $dir,$type='IMAGE',$old_file=false,$new_width=560,$new_height=560, $constraint=false){
	if(isset($_FILES[$field]) and $_FILES[$field]['name']){
        list($width, $height) = getimagesize($_FILES[$field]['tmp_name']);
		if($old_file and file_exists($old_file)){
			$_REQUEST[$field] = $old_file;
		}else{
			$dir = my_mkdir('upload/'.$dir);
			require_once 'packages/core/includes/utils/vn_code.php';
			$file_name = str_replace(array('"','\''),'',convert_utf8_to_latin($_FILES[$field]['name']));
			$thumb_name = '';
			if($type=='IMAGE'){
				$ext = 'jpg';
				switch($_FILES[$field]['type']){
					case 'image/jpeg';$ext = 'jpg';break;
					case 'image/png';$ext = 'png';break;
					case 'image/gif';$ext = 'gif';break;
					case 'image/bmp';$ext = 'bmp';break;
				}
				$rand = rand(000000,999999).''.time();
				$new_name = $dir.'/image_url_'.$rand.'.'.$ext;
				$thumb_name =$dir.'/thumb_url_'.$rand.'.'.$ext;
			}else{
				if(file_exists(''.$dir.'/'.$file_name)){				
					$new_name = ''.$dir.'/'.time().'_'.$file_name;
				}else{
					$new_name = ''.$dir.'/'.$file_name;
				}
			}
			$_REQUEST[$field] = $new_name;
		}
		$max_upload_file_size = 2*1024*1024;
		@eval('$max_upload_file_size ='. Portal::get_setting('size_upload').';');
		if($type=='IMAGE'){
			$type_file = 'jpg|gif|png|jpeg|swf|ico';//Portal::get_setting('type_image_upload');
		}elseif($type=='FILE'){
			$type_file = Portal::get_setting('type_file_upload');
		}
		if(preg_match('/\.('.$type_file.')$/i',strtolower($_FILES[$field]['name']),$matches) and filesize($_FILES[$field]['tmp_name'])< $max_upload_file_size){
			
			if(move_uploaded_file($_FILES[$field]['tmp_name'],$_REQUEST[$field])){
				if($new_width and $new_height){
					//create_thumb($_REQUEST[$field],$thumb_name,$new_width,$new_height, true);//create thumb//$constraint//$new_width,$new_height
					//echo $thumb_name;die;
                    if($width>$new_width){
                        create_thumb($_REQUEST[$field],$_REQUEST[$field],$new_width,$new_height, true);//resize photo
                    }
					//$watermarkImage = 'assets/standard/images/sealed_logo.png';
					//doResizeAndWatermark ($_REQUEST[$field],$ext,$watermarkImage);
				}
			}else{
				$_REQUEST[$field] = '';
			}
		}else{
			return '';
		}
        $_REQUEST[$field] = 'https://'.$_SERVER['HTTP_HOST'].'/'.$new_name;
		return 'https://'.$_SERVER['HTTP_HOST'].'/'.$new_name;
	}
}
function update_mi_upload_file($table, $field, $dir){
	if(isset($_REQUEST['mi_'.$table])){
		foreach($_REQUEST['mi_'.$table] as $key=>$record){
			if(isset($_FILES['mi_'.$table.'_'.$field.'_'.$key]) and $_FILES['mi_'.$table.'_'.$field.'_'.$key]['name']){
				$_REQUEST['mi_'.$table][$key][$field] = 'upload/'.$dir.'/'.time().$_FILES['mi_'.$table.'_'.$field.'_'.$key]['name'];
				move_uploaded_file($_FILES['mi_'.$table.'_'.$field.'_'.$key]['tmp_name'],$_REQUEST['mi_'.$table][$key][$field]);
			}
		}
	}
}
function image_open($image_url){
	if(!($image = @imagecreatefromgif($image_url))){
		if(!($image = @imagecreatefromjpeg($image_url))){
			if(!($image = @imagecreatefrompng($image_url))){
				if(!($image = @imagecreatefromwbmp($image_url))){
					return false;
				}
			}
		}
	}
	return $image;
}
function create_thumb($image,$new_image,$new_width,$new_height, $constraint=false){
	$new_image;
	$source = image_open($image);
	$width=imagesx($source);
	$height=imagesy($source);
	// Load
	if($constraint){
		$y1 = 0;
		$y2 = $height;
		$x1 = 0;
		$x2 = $width;
		$new_height = ($height*$new_width)/$width;
	}
	$thumb = imagecreatetruecolor($new_width, $new_height);
	imagefill($thumb,1,1,ImageColorAllocate( $thumb, 0, 0, 0 ) );
	if(!$constraint){
		if($width/$new_width>$height/$new_height){
			$y1 = 0;
			$y2 = $height;
			$x1 = ($width-($new_width*$height/$new_height))/2;
			$x2 = $width-2*$x1;
		}else{
			$x1 = 0;
			$x2 = $height;
			$y1 = ($height-($new_height*$width/$new_width))/2;
			$y2 = $height-2*$y1;
		}
	}
	// Resize
	imagecopyresized($thumb, $source, 0, 0, $x1, $y1, $new_width, $new_height, $x2-$x1, $y2-$y1);
	// Output
	if(file_exists($new_image)){
		@unlink($new_image);
	}
	imagejpeg($thumb,$new_image,100);
}

//ham xu ly upload anh? - 7A
function update_upload_image($field, $dir, $iname, $old_file=false,$new_width=false,$new_height=false, $constraint=false){
	if(isset($_FILES['file_'.$field]) and $_FILES['file_'.$field]['name']){
		//kiem tra dung luong
		if ($_FILES['file_'.$field]['size'] > 1024*1024){
			//thong bao loi
			return 'K&#237;ch th&#432;&#7899;c file upload kh&#244;ng h&#7907;p l&#7879;!';
		}
		//kiem tra dinh dang anh
		$temp = preg_split('/[\/\\\\]+/', $_FILES['file_'.$field]['name']);
		$filename = $temp[count($temp)-1];
		if (!preg_match('/\.(gif|jpg)$/i',$filename)){
			//thong bao loi
			return '&#272;&#7883;nh d&#7841;ng file upload kh&#244;ng ph&#7843;i l&#224; GIF ho&#7863;c JPG!';
		}

		if($old_file and file_exists($old_file)){
			$_REQUEST[$field] = $old_file;
		}else{
			$new_name = $dir.'/'.$iname;
			$_REQUEST[$field] = $new_name;
		}
		if(move_uploaded_file($_FILES['file_'.$field]['tmp_name'],$_REQUEST[$field])){
			return '0';
			if($new_width and $new_height){
				create_thumb($_REQUEST[$field],$_REQUEST[$field],$new_width,$new_height, $constraint);
			}
		}else{
			return 'Kh&#244;ng upload &#273;&#432;&#7907;c file';
			$_REQUEST[$field] = '';
		}
	}
	return '0';
}
// ------------ edit by ngocnv : 01/08/2009 ---------------
function check_dir($folder){
	if(is_dir($folder)){
		if(is_writable($folder)){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
/*----------edit 27/01/2016----------*/
function multi_upload_file($field, $dir,$type='IMAGE'){
	if(isset($_FILES[$field])){	
		$new_name = '';
		$dir = my_mkdir('upload/'.$dir);
		foreach($_FILES[$field]['name'] as $k=>$value){
			if($value){
				if(file_exists(''.$dir.'/'.$value)){				
					$new_name[$k+1]['value'] = ''.$dir.'/'.time().'_'.$value;					
				}else{
					require_once 'packages/core/includes/utils/vn_code.php';	
					$new_name[$k+1]['value'] = ''.$dir.'/'.convert_utf8_to_latin($value);
				}
				$new_name[$k+1]['name'] = $value;
				$max_upload_file_size = 2*1024*1024;
				@eval('$max_upload_file_size ='. Portal::get_setting('size_upload').';');
				if($type=='IMAGE'){
					$type_file = Portal::get_setting('type_image_upload');
				}elseif($type=='FILE'){
					$type_file = Portal::get_setting('type_file_upload');
				}
				if(preg_match('/\.('.$type_file.')$/i',strtolower($value),$matches) and filesize($_FILES[$field]['tmp_name'][$k])< $max_upload_file_size){
					if(!move_uploaded_file($_FILES[$field]['tmp_name'][$k],$new_name[$k+1]['value'])){
						$new_name[$k+1]['value'] = '';
					}
				}else{
					$new_name[$k+1]['value'] = '';
				}
			}
		}
		$_REQUEST[$field] = $new_name;
		return $new_name;
	}
}
function doResizeAndWatermark ($imageUrl,$ext='png',$watermarkImageUrl){
	switch ($ext){
			case 'jpg':
					$background = imagecreatefromjpeg($imageUrl);
					break;
			case 'jpeg':
					$background = imagecreatefromjpeg($imageUrl);
					break;
			case 'png':
					$background = imagecreatefrompng($imageUrl);
					break;
			case 'gif':
					$background = imagecreatefromgif($imageUrl);
					break;
			default:
					die("Image is of unsupported type.");
	}
	$watermarkImage = imagecreatefrompng($watermarkImageUrl);
	
	// Set the margins for the stamp and get the height/width of the stamp image
	$marge_right = 10;
	$marge_bottom = 10;
	$sx = imagesx($watermarkImage);
	$sy = imagesy($watermarkImage);
	
	// Copy the stamp image onto our photo using the margin offsets and the photo 
	// width to calculate positioning of the stamp. 
	imagecopy($background,$watermarkImage, imagesx($background) - $sx - $marge_right, $marge_bottom, 0, 0, imagesx($watermarkImage), imagesy($watermarkImage));
	switch ($ext){
			case 'jpg':
					imagejpeg($background,$imageUrl);
					break;
			case 'jpeg':
					imagejpeg($background,$imageUrl);
					break;
			case 'png':
					imagepng($background,$imageUrl);
					break;
			case 'gif':
					imagegif($background,$imageUrl);
					break;
			default:
					die("Image is of unsupported type.");
	}
}?>