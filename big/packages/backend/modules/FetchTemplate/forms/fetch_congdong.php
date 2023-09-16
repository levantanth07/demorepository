<?php
class FetchCongDongForm extends Form{
	function FetchCongDongForm(){
		Form::Form('FetchCongDongForm');
		$this->add('url',new TextType(true,'invalid_url',0,2000));
		$this->link_css('assets/default/css/cms.css');
	}
	function parse_content($url,$category_id){
		if(preg_match('/http\:\/\//',$url) and $content=file_get_contents($url)){
			//$pattern_name='/\<P class=pTitle\>([^\<]+)\<\/P\>/';
			$pattern_name = '/\<a href="([^\"]+)" rel="bookmark" title="[^\"]+">([^\<]+)\<\/a\>/';
			//pattern_brief='/\<P class=pHead\>(.*)\<\/P\>/';
			//$pattern_image='/<IMG class=lImage onclick="return showImage(this.src)" height=150 hspace=0 src="ImageView.aspx?ThumbnailID=264692" width=200 border=1 Hyperlink>/';
			//$pattern_description='<P class=pBody>';
			if(preg_match_all($pattern_name,$content,$match_name)){
				//$name=$match_name[2][0];
				$links = $match_name[1];
				//$names = $match_name[2]; 
				foreach($links as $value){
					$this->fetch_detail($value,$category_id);
				}
			}
			echo '<script>alert("Lấy dữ liệu thành công!");window.location="'.Url::build_current(array('cmd')).'";</script>';
		}else{
			echo '<script>alert("'.Portal::language('link_no_exists').'")</script>';
		}
	}
	function on_submit(){
		if($this->check()){
			$this->parse_content(Url::get('url'),Url::get('category_id'));
		}
	}
	function draw(){
		$categorys_id_list =String::get_list(FetchTemplateDB::get_category());
		$this->parse_layout('fetch_congdong',array(
			'category_id_list'=>$categorys_id_list
		));
	}
	function fetch_detail($url,$category_id){
		$content=file_get_contents($url);
		$pattern_name='<h1>';
		$pattern_desc='<div class="post-content2">';
		$name = FetchTemplateDB::cut_string($content,$pattern_name,'</h1>');
		$description = FetchTemplateDB::cut_string($content,$pattern_desc,'</div><!-- POST ENTRY END -->');
		if(isset($name)){
			$languages = DB::select_all('language');
			foreach($languages as $language){
				$item['name_'.$language['id']]=$name;
				$item['brief_'.$language['id']]=String::display_sort_title($description,50);
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
				//$item['image_url']=isset($image_url)?$image_url:'';
				DB::insert('news',$item);
			}
		}
	}
}
?>