<?php
class NewsDetailForm extends Form
{
	function __construct()
	{
		Form::Form('NewsDetailForm');
		//$this->add('full_name',new TextType(true,'full_name',0,255));
		//$this->add('email',new EmailType(true,'email'));
		//$this->add('content',new TextType(true,'content',0,2000));
		if(Url::get('name_id') == 'bao-gia-tuha'){
			$this->link_css('assets/standard/css/pricing.css');
		}
	}
	function on_submit(){
		
		$security_code = Session::get('security_code');
		if($this->check()){
			if(($_REQUEST["verify_comfirm_code"] == $security_code) && (!empty($_REQUEST["verify_comfirm_code"]) && !empty($security_code))){
				if(Url::get('id')){
					NewsDetailDB::update_news_comment(Url::iget('id'));
					if(Url::iget('id') and $news = DB::select('news','id='.Url::iget('id').'')){
						echo '<script>
										alert("Bạn đã gửi phản hồi bài viết thành công. Phản hồi sẽ được hiển thị sau khi Tòa soạn kiểm duyệt nội dung!");
										window.location = "'.Url::get('name_id').'.html";
								</script>';
						exit();
					}
				}
			}else{
			 	$this->error('verify_comfirm_code','invalid_confirm_code');
			}
		}
	}
	function draw()
	{
		$this->map = array();
        $this->map['tuha_administrator'] = can_tuha_administrator();
        $this->map['tuha_content_admin'] = can_tuha_content_admin();
		//////
		//$cond='type="NEWS" and portal_id="'.PORTAL_ID.'" and '.IDStructure::direct_child_cond( DB::structure_id('category',213));
		//$this->map['category'] = NewsDetailDB::get_category_parent($cond);
		//System::debug($this->map['category']);die;
		$this->map['category_id'] = 213;//blog
		require_once 'packages/core/includes/utils/format_text.php';
		if($item = NewsDetail::$item)
		{
            $account = DB::fetch('select id,is_active from account where id="'.$item['user_id'].'"');
            if($account['is_active']){
                $this->map['account_id'] =  $account['id'];
            }else{
                $this->map['account_id'] =  'pal.AnhTuan';
            }
			////
			//$item['description'] = format_text($item['description']);

			/*\
			if(Session::get('device')=='MOBILE'){
				$pos = MiString::strposX($this->map['description'],'</p>',4);
				if($pos){
					$adv_str = '<div id="bs_mobileinpage"><p></p><p></p><p></p><p></p></div><div id="AdAsia"></div>';
					$this->map['desc'] = MiString::stringInsert($this->map['description'],$adv_str,$pos);
				}
				$this->map['desc'] .= '<div class="inner-adv text-center"><!-- m.hay_middle_300x600 -->
							<ins class="adsbygoogle"
							style="display:inline-block;width:300px;height:600px"
							data-ad-client="ca-pub-6946473156367935"
							data-ad-slot="7546002780"></ins>
							<script>
							(adsbygoogle = window.adsbygoogle || []).push({});
							</script></div>';
			}*/

			$category = DB::fetch('select id, name_'.Portal::language().' as category_name,name_id_'.Portal::language().' as name_id from category where id = '.$item['category_id']);
			$this->map['category_name'] = $category['category_name'];
			$this->map['category_name_id'] = $category['name_id'];
			$this->map['category_id'] = $category['id'];
			$mode = true;
			DB::update_hit_count('news',$item['id']);
			$cond = 'news.publish and news.portal_id="'.PORTAL_ID.'" and '.IDStructure::child_cond(DB::structure_id('category',$item['category_id'])).' and  news.status<>"HIDE" and news.id<>'.$item['id'];
			$this->map['r_news'] = NewsDetailDB::get_items($cond,6);

			//$this->map['r_news'] = NewsDetailDB::get_items('news.portal_id="'.PORTAL_ID.'" and '.IDStructure::child_cond(DB::structure_id('category',$this->map['category_id'])).' and news.status!="HIDE" and news.id<>'.$item['id']);
			//Ten loai danh muc cua tin
			$item['tags'] = MiString::create_tags($item['tags'],'bai-viet');
            $layout = 'list';
			$this->parse_layout($layout,$mode?$item+$this->map:$this->map);
		}
	}
}
?>
