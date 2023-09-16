<?php
class UploadImage extends Module{
	function UploadImage($row){
		if(User::can_edit(MODULE_PRODUCTADMIN,ANY_CATEGORY)){
			Module::Module($row);
			require_once 'db.php';
			if(Url::get('do') == 'upload_image'){
				ini_set("html_errors", "0");
				// Check the upload
				if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
					echo "ERROR:invalid upload";
					exit(0);
				}
				// Get the image and create a thumbnail
				$img = imagecreatefromjpeg($_FILES["Filedata"]["tmp_name"]);
				if (!$img) {
					echo "ERROR:could not create image handle ".$_FILES["Filedata"]["tmp_name"];
					exit(0);
				}
				$width = imageSX($img);
				$height = imageSY($img);
			
				if (!$width || !$height) {
					echo "ERROR: Invalid width or height";
					exit(0);
				}
				// Build the thumbnail
				$target_width = 100;
				$target_height = 100;
				$target_ratio = $target_width / $target_height;
			
				$img_ratio = $width / $height;
			
				if ($target_ratio > $img_ratio) {
					$new_height = $target_height;
					$new_width = $img_ratio * $target_height;
				} else {
					$new_height = $target_width / $img_ratio;
					$new_width = $target_width;
				}
			
				if ($new_height > $target_height) {
					$new_height = $target_height;
				}
				if ($new_width > $target_width) {
					$new_height = $target_width;
				}
			
				$new_img = ImageCreateTrueColor(100, 100);
				if (!@imagefilledrectangle($new_img, 0, 0, $target_width-1, $target_height-1, 0)) {	// Fill the image black
					echo "ERROR:Could not fill new image";
					exit(0);
				}
				if (!@imagecopyresampled($new_img, $img, ($target_width-$new_width)/2, ($target_height-$new_height)/2, 0, 0, $new_width, $new_height, $width, $height)) {
					echo "ERROR:Could not resize image";
					exit(0);
				}
				if (!isset($_SESSION["file_info"])) {
					$_SESSION["file_info"] = array();
				}
				// Use a output buffering to load the image into a variable
				ob_start();
				imagejpeg($new_img);
				$imagevariable = ob_get_contents();
				ob_end_clean();
			
				$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
				$_SESSION["file_info"][$file_id] = $imagevariable;
				if(Url::get('product_id') and $product = DB::fetch('SELECT product.id,product.name_id FROM product WHERE product.id='.Url::get('product_id'))){
					if(isset($_FILES['Filedata'])){
						require_once 'packages/core/includes/utils/upload_file.php';
						$dir = 'default/product/'.$product['name_id'];
						update_upload_file('Filedata',$dir);
						$image = array();
						$image_url = $_REQUEST['Filedata'];
						$thumb_url = str_replace('image_url','thumb_url',$_REQUEST['Filedata']);
						$small_thumb_url = str_replace('image_url','small_thumb_url',$_REQUEST['Filedata']);
						create_thumb($_REQUEST['Filedata'],$thumb_url,377,197, true);
						create_thumb($_REQUEST['Filedata'],$small_thumb_url,120,120, true);
						$image['image_url'] = $image_url;
						$image['thumb_url'] = $thumb_url;
						$image['small_thumb_url'] = $small_thumb_url;
						if($position = DB::fetch('select id,position from product_image where product_id='.Url::iget('product_id').' order by position desc','position')){
							$pos = $position+1;
						}else{
							$pos = 1;
						}
						DB::insert('product_image',array('name'=>'','position'=>$pos,'image_url'=>$image['image_url'],'thumb_url'=>$image['thumb_url'],'small_thumb_url'=>$image['small_thumb_url'],'product_id'=>$product['id']));
					}
				}
				echo "FILEID:" . $file_id;	// Return the file id to the script
				exit();
			}
			if($cmd = Url::get('cmd') and $id = Url::get('position_id') and $product_id = Url::get('product_id')){
				switch($cmd){
					case 'up': $this->up_position($cmd,$id); break;
					case 'down': $this->down_position($cmd,$id); break;
					case 'delete': $this->Delete($id); break;
				}
			}
			require_once 'forms/list.php';
			$this->add_form(new UploadImageForm());
		}else{
			//Url::access_denied();
		}
	}
	function up_position($cmd,$id){
		$sql = 'select
					id,position
				from
					product_image
				where
					position =
					(select
						max(position) as position
					from
						product_image
					where
						product_id = '.Url::iget('product_id').'
						and position < '.Url::get('position',0).')
					and product_id = '.Url::iget('product_id')
				;
		if($before = DB::fetch($sql)){
			DB::update_id('product_image',array('position'=>$before['position']),$id);
			DB::update_id('product_image',array('position'=>Url::get('position')),$before['id']);
		}
	}
	function down_position($cmd,$id){
		$sql = 'select
					id,position
				from
					product_image
				where
					position =
					(select
						min(position) as position
					from
						product_image
					where
						product_id = '.Url::iget('product_id').'
						and position > '.Url::get('position',0).')
					and product_id = '.Url::iget('product_id')
				;
		if($next = DB::fetch($sql)){
			DB::update_id('product_image',array('position'=>$next['position']),$id);
			DB::update_id('product_image',array('position'=>Url::get('position')),$next['id']);
		}
	}
	function Delete($id){
		if($row = DB::select('product_image','id='.intval($id))){
			@unlink($row['image_url']);
			@unlink($row['thumb_url']);
			@unlink($row['small_thumb_url']);						
			DB::delete_id('product_image',$id);
		}
	}
}
?>