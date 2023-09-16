<?php
class FetchKatriForm extends Form{
	function FetchKatriForm(){
		Form::Form('FetchKatriForm');
		$this->add('url',new TextType(true,'invalid_url',0,2000));
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit(){
		if($this->check()){
			if(Url::get('tin_tuc') and Url::iget('category_id') and $category_name = DB::fetch('select name_1 from category where id='.Url::iget('category_id').'')){
				$this->crawl_list_tin_tuc(Url::get('url'),Url::iget('category_id'),$category_name);
			}
			if(Url::get('san_pham') and Url::iget('category_id') and $category_name = DB::fetch('select name_1 from category where id='.Url::iget('category_id').'')){
				$this->crawl_list_san_pham(Url::get('url'),Url::iget('category_id'),$category_name);
			}
			die;
			Url::redirect_current(array('cmd','type'));
		}
	}
	function draw(){
		$categories = FetchTemplateDB::get_category(Url::get('type')?Url::get('type'):'NEWS');
		require_once 'packages/core/includes/utils/category.php';
		combobox_indent($categories);
		$categorys_id_list =String::get_list($categories);
		$this->parse_layout('katri',array(
			'category_id_list'=>$categorys_id_list
		));
	}
	function crawl_list_tin_tuc($url,$category_id,$category_name){
		if(preg_match('/http\:\/\//',$url) and $content=file_get_contents($url)){
			//$pattern_name='/\<P class=pTitle\>([^\<]+)\<\/P\>/';
			$pattern_name = '/\<h1 class=\"item-title\"\>[\n|\s|\t]*\<a href=\'([^\']+)\'\>[\n|\s|\t]*([^\<]+)\<\/a\>\<\/h1\>/';
			//$pattern_brief='/\<P class=pHead\>(.*)\<\/P\>/';
			$pattern_image = '/\<img src=\'([^\']+)\' class=\"img\" width=\"188px\"
                            height=\"130px\" align=\"absmiddle\" alt=\'([^\']+)\' title=\'([^\']+)\' \/\>/';
			//$pattern_description='<P class=pBody>';
			if(preg_match_all($pattern_name,$content,$match_name) and preg_match_all($pattern_image,$content,$match_img)){
				//$name=$match_name[2][0];
				$links = $match_name[1];
				$images = $match_img[1];
				//$names = $match_name[2];
				$i=0; 
				foreach($links as $value){
					$this->crawl_chi_tiet_tin_tuc('http://katri.com.vn'.$value,$category_id,$category_name,'http://katri.com.vn'.$images[$i]);
					$i++;
				}
			}
			die;
			echo '<script>alert("Lấy dữ liệu thành công!");window.location="'.Url::build_current(array('cmd')).'";</script>';
		}else{
			echo '<script>alert("'.Portal::language('link_no_exists').'")</script>';
		}
	}
	function crawl_chi_tiet_tin_tuc($url,$category_id,$category_name,$image_url){
		$content=file_get_contents($url);
		$pattern_name='<h1 class="item-title item-fix">';
		$pattern_desc='<div class="item-content">';
		$name = strip_tags(FetchTemplateDB::cut_string($content,$pattern_name,'</h1>'));
		
		$description = FetchTemplateDB::cut_string($content,$pattern_desc,'</div>
        <div class="clear">
        </div>');
		if(isset($name)){
			$languages = DB::select_all('language');
			foreach($languages as $language){
				$item['name_'.$language['id']]=$name;
				$item['brief_'.$language['id']]=String::display_sort_title(strip_tags($description),50);
				$item['description_'.$language['id']]=isset($description)?$description:'';
			}
			$item['portal_id']=Url::get('portal_id')?Url::get('portal_id'):PORTAL_ID;
			$item['type']='NEWS';
			$item['category_id']=$category_id;
			$item['time']=time();
			$item['status']='SHOW';
			$item['publish']=1;
			$item['user_id']=Session::get('user_id');
			require_once 'packages/core/includes/utils/vn_code.php';
			$name_id = convert_utf8_to_url_rewrite($item['name_1']);
			$item['name_id'] = $name_id;
			if(!DB::fetch('select name_id from news where name_id="'.$name_id.'"')){
				/*if(Url::get('image_url')){
					$image_url=Url::get('image_url');
					$name_image=substr($image_url,strrpos($image_url,'/')+1);
					@copy($image_url,'upload/'.substr(PORTAL_ID,1).'/content/'.$name_image);
					$image_url='upload/'.substr(PORTAL_ID,1).'/content/'.$name_image;
				}else{
					$image_url='';
				}*/
				$position = DB::fetch('select max(position)+1 as id from news where type="NEWS"');
				$item['position'] = $position['id'];
				if($image_url){
					$name_image=substr($image_url,strrpos($image_url,'/')+1);
					$new_image = 'upload/'.substr(PORTAL_ID,1).'/content/convert/'.$name_image;
					@copy($image_url,$new_image);
				}
				$item['image_url']=$new_image;
				DB::insert('news',$item);
			}
		}
	}
	function crawl_list_san_pham($url,$category_id,$category_name){
		if(preg_match('/http\:\/\//',$url) and $content=file_get_contents($url)){
			//$pattern_name='/\<P class=pTitle\>([^\<]+)\<\/P\>/';
			$pattern_name = '/\<li\>\<a href=\'([^\']+)\'\>[\n\s\t]+\<img src=\'([^\']+)\'/';
			//$pattern_brief='/\<P class=pHead\>(.*)\<\/P\>/';
			//$pattern_description='<P class=pBody>';
			if(preg_match_all($pattern_name,$content,$match_name)){
				$links = $match_name[1];
				$images = $match_name[2];
				$i=0; 
				foreach($links as $value){
					$this->crawl_chi_tiet_san_pham('http://katri.com.vn'.$value,$category_id,$category_name,'http://katri.com.vn'.$images[$i]);
					$i++;
				}
			}
			die;
			echo '<script>alert("Lấy dữ liệu thành công!");window.location="'.Url::build_current(array('cmd')).'";</script>';
		}else{
			echo '<script>alert("'.Portal::language('link_no_exists').'")</script>';
		}
	}
	function crawl_chi_tiet_san_pham($url,$category_id,$category_name,$image_url){
		$content=file_get_contents($url);
		$pattern_name='<h1 class="item-title item-fix">';
		$pattern_desc='<div class="item-content">';
		$name = strip_tags(FetchTemplateDB::cut_string($content,$pattern_name,'</h1>'));
		$name = preg_replace('/[\n\t]*/','',trim($name));
		$description = FetchTemplateDB::cut_string($content,$pattern_desc,'</div>
        <div class="clear">
        </div>');
		$description = preg_replace('/style=\"[^\"]+\"/','',$description);
		$description = preg_replace('/face=\"[^\"]+\"/','',$description);
		if(isset($name)){
			$languages = DB::select_all('language');
			foreach($languages as $language){
				$item['name_'.$language['id']]=$name;
				$item['brief_'.$language['id']]=String::display_sort_title(strip_tags($description),50);
				$item['description_'.$language['id']]=isset($description)?$description:'';
			}
			$item['portal_id']=Url::get('portal_id')?Url::get('portal_id'):PORTAL_ID;
			$item['time']=time();
			$item['status']='SHOW';
			$item['user_id']=Session::get('user_id');
			require_once 'packages/core/includes/utils/vn_code.php';
			$name_id = convert_utf8_to_url_rewrite($item['name_1']);
			$item['name_id'] = $name_id;
			if(!DB::fetch('select name_id from product where name_id="'.$name_id.'"')){
				$position = DB::fetch('select max(position)+1 as id from product');
				$item['position'] = $position['id'];
				if($image_url){
					$name_image=substr($image_url,strrpos($image_url,'/')+1);
					$new_image = 'upload/'.substr(PORTAL_ID,1).'/content/convert/product/'.$name_image;
					@copy($image_url,$new_image);
				}
				$item['image_url']=$new_image;
				$item['small_thumb_url']=$new_image;
				$product_id = DB::insert('product',$item);
				DB::insert('product_category',array('category_id'=>$category_id,'product_id'=>$product_id));
			}
		}
	}
}
?>