<?php
class FetchVnExpressForm extends Form
{
	function FetchVnExpressForm()
	{
		Form::Form('FetchVnExpressForm');
		$this->add('url',new TextType(true,'invalid_url',0,2000));
		$this->link_css('assets/default/css/cms.css');
	}
	function parse_content($url,$category_id)
	{
		if(eregi('http://',$url) and $content=file_get_contents($url))
		{
			$pattern_name='/\<P class=Title\>([^\<]+)\<\/P\>/';
			$pattern_brief='<P class=Lead>';
			$pattern_image='/id=\"StoryAvatar\" src=\"([^"]+)\" /';
			$pattern_description='<P class=Normal>';
			if(preg_match_all($pattern_name,$content,$match_name))
			{
				$name=$match_name[1][0];
			}
			if($start=strpos($content,$pattern_brief)+strlen($pattern_brief) and $finish=strpos($content,'</P>',$start))
			{
				$brief=substr($content,$start,$finish-$start);
			}
			if($start=strpos($content,$pattern_description)+strlen($pattern_description) and $finish=strpos($content,'</STRONG></P>',$start))
			{
				$description=substr($content,$start,$finish-$start);
				$description=eregi_replace('/Files/Subject','http://vnexpress.net/Files/Subject',$description);
			}
			if(isset($name))
			{
				$languages = DB::select_all('language');
				foreach($languages as $language)
				{
					$item['name_'.$language['id']]=$name;
					$item['brief_'.$language['id']]=isset($brief)?$brief:'';
					$item['description_'.$language['id']]=isset($description)?$description:'';
				}
				$item['portal_id']=Url::get('portal_id')?Url::get('portal_id'):PORTAL_ID;
				$item['type']='NEWS';
				$item['category_id']=$category_id;
				$item['time']=time();
				$item['status']='SHOW';
				$item['user_id']=Session::get('user_id');
				require_once 'packages/core/includes/utils/vn_code.php';
				$name_id = convert_utf8_to_url_rewrite($item['name_1']);
				$item['name_id'] = $name_id;
				if(!DB::fetch('select name_id from news where name_id="'.$name_id.'"'))
				{
					if(Url::get('image_url'))
					{
						$image_url=Url::get('image_url');
						$name_image=substr($image_url,strrpos($image_url,'/')+1);
						@copy($image_url,'upload/'.substr(PORTAL_ID,1).'/content/'.$name_image);
						$image_url='upload/'.substr(PORTAL_ID,1).'/content/'.$name_image;
					}
					else
					{
						$image_url='';
					}
					$position = DB::fetch('select max(position)+1 as id from news where type="NEWS"');
					$item['position'] = $position['id'];
					$item['image_url']=isset($image_url)?$image_url:'';
					DB::insert('news',$item);
				}
				else
				{
					$this->error('duplicate_news','duplicate_news');
					return ;
				}
				Url::redirect_current(array('cmd'=>'fetch_vnexpress'));
			}
		}
		else
		{
			echo '<script>alert("'.Portal::language('link_no_exists').'")</script>';
		}
	}
	function on_submit()
	{
		if($this->check() and Url::check('url'))
		{
			$this->parse_content(Url::get('url'),Url::get('category_id'));
		}
	}
	function draw()
	{
		$categorys_id_list =String::get_list(FetchTemplateDB::get_category());
		$this->parse_layout('fetch_dantri',array(
			'category_id_list'=>$categorys_id_list,
		));
	}
}
?>