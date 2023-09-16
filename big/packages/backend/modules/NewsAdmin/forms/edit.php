<?php
class EditNewsAdminForm extends Form{
	protected $map;
	function __construct(){
		Form::Form('EditNewsAdminForm');
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		foreach($languages as $language){
			$this->add('name_'.$language['id'],new TextType(true,'Lỗi nhập tiêu đề '.$language['name'],0,2000));
		}
		//$this->link_css('assets/default/css/tabs/tabpane.css');
		//$this->link_js('assets/default/css/tabs/tabpane.js');
	}
	function on_submit(){
		if($this->check()){
			$rows = $this->save_item();
            $this->check_category();
			if(!$this->is_error()){
				if(Url::get('cmd')=='edit' and $item = DB::exists_id('news',Url::get('id'))){
					$id = intval(Url::get('id'));
					$rows += array('last_time_update'=>time());
					if(!$item['publisher'] and $rows['publish']){
							$rows['publisher'] = Session::get('user_id');
							$rows['published_time'] = time();
					}
					if($rows['publish']==0){
						$rows['publisher'] = '';
						$rows['published_time'] = 0;
					}
					DB::update_id('news',$rows+array('time'=>Date_Time::to_time(Url::get('time'))),$id);
					if(strip_tags($item['keywords'])){
						$rows['keywords'] = $item['keywords'];
					}
				}else{
					$rows += array('time'=>time(),'user_id'=>Session::get('user_id'),'hitcount'=>'0');
					$id = DB::insert('news',$rows);
				}
				if((Url::get('edit_category')
                    or Url::get('cmd')=='add')
                    and isset($_REQUEST['category_id'])
                    and !empty($_REQUEST['category_id'])
                ){
					NewsAdminDB::update_category($id,$_REQUEST['category_id']);
				}
				$this->save_image($id);
				//save_log($id);
				if($id){
					if($id){
						echo '<script>alert("'.Portal::language('update_successful').'");location="'.Url::build_current(array('cmd'=>'edit','id'=>$id)).'";</script>';
					}
				}
			}
		}
	}
	function draw(){
		$this->map = array();
		$this->map['categories'] = 'N/A';
		require_once 'cache/config/status.php';
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		$arr = array('1'=>'YES','0'=>'NO');
		$this->map['publish'] = 0;
		$this->map['publisher'] = '';
		$this->map['published_time'] = 0;
		$news_id = false;
		if(Url::get('cmd')=='edit' and $news_id = Url::iget('id')){
		    $news = NewsAdminDB::get_item('news.id='.Url::iget('id'));
		    $this->map['name_id'] = $news['name_id_'.Portal::language()];
            $this->map['category_name_id'] = $news['category_name_id'];
			$this->map['categories'] = NewsAdminDB::get_categories($news_id);
			$this->map['publish'] = $news['publish'];
			$this->map['publisher'] = $news['publisher'];
			$this->map['published_time'] = date('H:i\' d/m/y',$news['published_time']);
			$news['time'] = date('d/m/Y',$news['time']);
			foreach($news as $key=>$value){
				if(is_string($value) and !isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
		}else{
			$_REQUEST['time'] = date('d/m/Y');
		}
		$categories = NewsAdminDB::get_category($news_id);
		require_once 'packages/core/includes/utils/category.php';
		combobox_indent($categories);
		//System::Debug($categories);
		$category_options = '';
		foreach($categories as $value){
			$category_options .= '<option value="'.$value['id'].'" '.(($news_id and $value['news_id']==$news_id)?'selected':'').'>'.$value['name'].'</option>';
		}
		$this->map += array(
			'category_id_list'=>MiString::get_list($categories,false,false,true),
			'status_list'=>$status,
			'languages'=>$languages,
			'show_image_list'=>$arr,
			'show_email_list'=>$arr,
			'show_print_list'=>$arr,
			'show_time_list'=>$arr,
			'show_author_list'=>$arr,
			'show_comment_list'=>$arr,
			'front_page_list'=>$arr,
			'category_options'=>$category_options
		);
		$this->parse_layout('edit',$this->map);
	}
	function set_autolink($content){
		$str = Portal::get_setting('auto_link');
		$arr = explode("\n",$str);
		foreach($arr as $key=>$value){
			$tmp_arr = explode("=>",$value);
			if(preg_match("/\<a ([^\>]+)\>$tmp_arr[0]<\/a\>/i",$content)){
				continue;
			}else{
				if(isset($tmp_arr[1])){
					$content = preg_replace("/".$tmp_arr[0]."/","<a class=\"no-highlight\" target=\"_blank\" href=\"$tmp_arr[1]\">$tmp_arr[0]</a>",$content);	
				}
			}
		}
		return $content;
	}
	function save_item(){
		$rows = array();
		$languages = DB::fetch_all('select id,name,icon_url from language where active=1');
		foreach($languages as $language){
			$brief = Url::get('brief_'.$language['id'].'');//MiString::display_sort_title(strip_tags(Url::get('description_'.$language['id'].'')),50);
			$rows += array('name_'.$language['id'].''=>Url::get('name_'.$language['id'].''));
			$rows += array('brief_'.$language['id'].''=>$brief);//$this->set_autolink
			$rows += array('description_'.$language['id'].''=>Url::get('description_'.$language['id'].''));
			
			$rows += array('seo_title_'.$language['id'].''=>Url::get('seo_title_'.$language['id'].''));
			$rows += array('seo_keywords_'.$language['id'].''=>Url::get('seo_keywords_'.$language['id'].''));
			$rows += array('seo_description_'.$language['id'].''=>Url::get('seo_description_'.$language['id'].''));
		}
		require_once 'packages/core/includes/utils/vn_code.php';
		require_once 'packages/core/includes/utils/search.php';
		$rows['keywords']=str_replace(array(", ...",''),'',str_replace(array(' ','"','!','\'','"','"'),', ',strip_tags($rows['name_1'])));
		$rows += array(
		'publish'=>(Url::get('publish')==1)?1:'0'
		,'status'
		,'type'=>'NEWS'
		,'author'
		,'tags'
        ,'show_comment'=>Url::get('show_comment')==1?1:'0'
		,'portal_id'=>PORTAL_ID
		);
		if(Url::get('position')==''){
			$position = DB::fetch('select max(position)+1 as id from news where type="NEWS"');
			$rows['position'] = $position['id'];
		}else{
			$rows['position'] = Url::get('position');
		}
		foreach($languages as $language){
			if(Url::get('name_id_'.$language['id']) and Url::get('cmd')=='edit'){
                $name_id = trim(Url::get('name_id_'.$language['id']));
            }else{
                $name_id = convert_utf8_to_url_rewrite($rows['name_'.$language['id']]);
            }
            if(!DB::fetch('select name_id_'.$language['id'].' from news where name_id_1="'.$name_id.'" and portal_id="'.PORTAL_ID.'" and news.type="NEWS"')){
                $rows+=array('name_id_'.$language['id']=>$name_id?$name_id:'');
            }else{
                if(Url::get('id') and Url::get('cmd')=='edit'){
                    $rows+=array('name_id_'.$language['id']=>$name_id);
                }else{
                    $this->error('name','Tên bị trùng lặp');
                }
            }
		}
		return ($rows);
	}
	function save_image($id){
		require_once 'packages/core/includes/utils/upload_file.php';
		$dir = substr(PORTAL_ID,1).'/content/'.date('dmY');
		//update_upload_file('small_thumb_url',$dir);
		update_upload_file('image_url',$dir,false,false,false);
		update_upload_file('file',$dir,'FILE');
		$row = array();
		if(Url::get('small_thumb_url')!=''){
			$row = array_merge($row,array('small_thumb_url'));
		}
		if(Url::get('image_url')!=''){
			$row = array_merge($row,array('image_url'));
		}
		if(Url::get('file')!=''){
			$row = array_merge($row,array('file'));
		}
		DB::update_id('news',$row,$id);
	}
	function check_category(){
        if(Url::get('edit_category') or Url::get('cmd')=='add'){
            if(!Url::get('category_id')){
                $this->error('category_id','Bạn vui lòng chọn danh mục');
                return;
            }
        }
    }
}
?>
