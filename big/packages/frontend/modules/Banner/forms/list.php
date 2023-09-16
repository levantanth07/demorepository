<?php
class BannerForm extends Form
{
    protected $map;
	function __construct()
	{
		Form::Form('BannerForm');
		/*$this->link_js('assets/standard/js/jquery.js');
		$this->link_js('assets/standard/js/bootstrap.min.js');
		$this->link_js('assets/standard/js/jquery.prettyPhoto.js');
		$this->link_js('assets/standard/js/jquery.isotope.min.js');
		$this->link_js('assets/standard/js/main.js');
		$this->link_js('assets/standard/js/wow.min.js');
		$this->link_css('assets/standard/css/bootstrap.min.css');
		$this->link_css('assets/standard/css/font-awesome.min.css');
		$this->link_css('assets/standard/css/animate.min.css');
		$this->link_css('assets/standard/css/prettyPhoto.css');
		$this->link_css('assets/standard/css/main.css');
		$this->link_css('assets/standard/css/responsive.css');
		*/
	}
	function on_submit(){
		if($phone = Url::post('phone') and $full_name=Url::post('full_name')){
            $pal_group_id = '1279';
            $account_id = 'pal.AnhTuan';
            $refer_url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $arr = [
                'phone'=>$phone,
                'contact'=>$full_name,
                'shop_id'=>$pal_group_id,
                'account_id'=>$account_id,
                'host'=>System::get_client_ip_env(),
                'time'=>time(),
                'message'=>'Liện hệ từ form',
                'refer_url'=>$refer_url,
                'contact_type'=>3,// from tuha.vn
                'checked'=>0
            ];
            DB::insert('contact_form',$arr);
            Url::js_redirect(true,'Quý khách đã gửi thành công. QLBH sẽ thông báo Quý khách thông tin khuyến mại hấp dẫn!');
        }
	}
	function draw()
	{
		$this->map = array();
		//require_once 'cache/tables/menu_'.Portal::language().'.cache.php';
		$categogies = BannerDB::get_categories();
		$this->map['item_ul_categories'] = $categogies;
		$this->map['category_name_id'] = Url::get('category_name_id')?Url::get('category_name_id'):(Url::get('category_name')?Url::get('category_name'):'');
		$this->map['page'] = (Url::get('page')=='xem-bai-viet')?'bai-viet':Datafilter::removeXSSinHtml(Url::get('page'));
		//$this->map['page'] = ($this->map['page']=='chi-tiet-san-pham')?'san-pham':$this->map['page'];
		$this->parse_layout('list',$this->map);
	}
}
?>