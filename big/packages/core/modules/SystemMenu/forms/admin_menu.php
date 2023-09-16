<?php
class SystemMenuForm extends Form
{
	function __construct()
	{
		Form::Form('SystemMenuForm');
		/*$this->link_js('assets/admin/scripts/jquery-1.7.1.js');
		$this->link_js('packages/core/includes/js/jquery/jquery.dimensions.min.js');
		$this->link_js('packages/core/includes/js/jquery/jquery.menu.js');
		//$this->link_js('packages/core/includes/js/jquery/ui.core.js');		
		//$this->link_js('packages/core/includes/js/jquery/ui.tabs.js');
		$this->link_css('assets/default/css/global.css');
		$this->link_css('assets/admin/css/menu.css');
		$this->link_css('assets/default/css/jquery/tabs.css');*/
		$this->link_js('assets/standard/js/jquery.js');
		$this->link_js('assets/standard/js/bootstrap.min.js');
		$this->link_js('assets/standard/js/prettyPhoto/jquery.prettyPhoto.js');
		$this->link_js('assets/standard/js/jquery.isotope.min.js');
		$this->link_js('assets/standard/js/main.js');
		$this->link_js('assets/standard/js/wow.min.js');
        
		$this->link_css('assets/stand	ard/css/bootstrap.min.css');
		$this->link_css('assets/standard/css/font-awesome.min.css');
		$this->link_css('assets/standard/css/animate.min.css');
		$this->link_css('assets/standard/js/prettyPhoto/css/prettyPhoto.css');
		$this->link_css('assets/admin/css/style.css');
		//$this->link_css('assets/standard/css/responsive.css');	
		$this->link_css('assets/default/css/cms.css');
		//$this->link_css('http://blackrockdigital.github.io/startbootstrap-simple-sidebar/css/simple-sidebar.css');
	}
	function draw()
	{
		$this->map = array();
		$user = Session::get('data_user');
		$this->map['full_name'] = $user['full_name'];
		require 'packages/core/includes/utils/category.php';
		$layout = 'admin_menu';
		if(User::can_admin(false,ANY_CATEGORY)){
			require_once 'cache/tables/function.cache.php';
		}else{
			$categories = array();
			$layout = 'admin_menu';
			$privilege_categories = DB::fetch_all('SELECT account_privilege.id, account_id,function.structure_id FROM account_privilege INNER JOIN function ON function.id = category_id WHERE account_id = \''.Session::get('user_id').'\'');
			foreach($privilege_categories as $value){
				$categories += DB::fetch_all('
					select
						*
						,name_'.Portal::language().' as name
						,group_name_'.Portal::language().'
					from
						function
					where
					function.status <> "HIDE"
					and '.IDstructure::child_cond($value['structure_id'],false,'function.')
					.' order by structure_id'
				);
			}
		}
		$this->map['categories'] = MiString::array2tree($categories,'child');
		$this->parse_layout($layout,$this->map);
	}
}
?>