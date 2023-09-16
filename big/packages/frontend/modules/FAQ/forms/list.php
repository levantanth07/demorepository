<?php
class FAQForm extends Form{
	function FAQForm(){
		Form::Form('FAQForm');
		$this->link_css(Portal::template('longvu').'css/news.css');
		$this->link_js('packages/core/includes/js/jquery/jquery.validate.js');
		$this->add('question_name',new TextType(true,'invalid_question',0,2000));
	}
	function on_submit(){
		$security_code = $_SESSION["security_code"];
		if($this->check())
		{
			if ( ($_REQUEST["verify_confirm_code"] == $security_code) &&
				(!empty($_REQUEST["verify_confirm_code"]) && !empty($security_code)) )
			{
				require_once 'packages/core/includes/utils/vn_code.php';
				$array = array(
					'time'=>time(),
					'portal_id'=>PORTAL_ID,
					'user_id'=>Url::get('user',Session::get('user_id')),
					'type'=>'FAQ',
					'name_id'=>convert_utf8_to_url_rewrite(Url::get('question_name')),
					'status'=>'HIDE'
					//'category_id'=>intval(Url::get('category_id')),
					//'parent_id'=>Url::get('product_id')
				);
				$languages = DB::select_all('language');
				foreach($languages as $key=>$language)
				{
					$array['name_'.$key] = Url::get('question_name');
					$array['brief_'.$key] = Url::get('question_name');
				}
				FAQDB::insert_faq($array);
				Url::redirect_current(array('cmd'=>'success'));
				//Url::redirect_current(array('name_id'=>Url::get('name_id')));
			}
		}
	}
	function draw()
	{
		//$cond='news.status<>"HIDE" and news.type="FAQ" and news.portal_id="'.PORTAL_ID.'" and parent_id = '.intval(Url::get('product_id'));
		$cond='news.status<>"HIDE" and news.type="FAQ" and news.portal_id="'.PORTAL_ID.'"';
		$item_per_page = 10;
		require_once 'packages/core/includes/utils/paging.php';
		$total_item = FAQDB::get_total_item($cond);
		$paging = paging($total_item['acount'],$item_per_page);
		$item=FAQDB::get_item($cond,$item_per_page);
		$this->parse_layout('list',array(
				'item'=>$item,
				'paging'=>$paging
			));
	}
}
?>
