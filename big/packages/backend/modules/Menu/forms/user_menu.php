<?php
class UserMenuForm extends Form
{
	function UserMenuForm()
	{
		Form::Form('UserMenuForm');
		$this->link_js('packages/core/includes/js/jquery/jquery-1.7.1.js');
		$this->link_js('packages/core/includes/js/jquery/jquery.dimensions.min.js');
		$this->link_js('packages/core/includes/js/jquery/jquery.menu.js');
		$this->link_js('packages/core/includes/js/jquery/ui.core.js');		
		$this->link_js('packages/core/includes/js/jquery/ui.tabs.js');
		$this->link_css('assets/default/css/global.css');
		$this->link_css('assets/admin/css/menu.css');
		$this->link_css('assets/admin/css/style.css');
		$this->link_css('assets/default/css/jquery/tabs.css');
	}
	function draw()
	{
		echo 1;
		$this->map = array(); $this->map['check_member'] = false;
		require_once 'cache/tables/function.cache.php';
		$this->map['categories'] = $categories;
		$parent_structure_id = '';
		$sub_menu = DB::fetch('select id,structure_id,name_1 from function where status!="HIDE" and url="?page='. DB::escape(DataFilter::removeXSSinHtml(Url::sget('page'))).'"');
		if(IDStructure::level($sub_menu['structure_id'])==3){
			$parent_structure_id = IDStructure::parent($sub_menu['structure_id']);
		}
		else
		{
			$items = array();
			$this->map['check_member'] = true;
			$items[84] = array('id'=>84,'name'=>'Trang chủ','structure_id'=>'101000000000000000','url'=>'','icon_url' => 'upload/default/icon//icon_report.png','check'=>0);
			//$items[85] = array('id'=>85,'name'=>'Đăng tin','structure_id'=>'102000000000000000','url'=>'dang-tin.html','icon_url' => 'upload/default/icon//icon_report.png','check'=>0);
			//$items[86] = array('id'=>86,'name'=>'Tin đã đăng','structure_id'=>'103000000000000000','url'=>'manage_item.html','icon_url' => 'upload/default/icon//icon_report.png','check'=>0);
			$items[87] = array('id'=>87,'name'=>'Đơn hàng','structure_id'=>'104000000000000000','url'=>'manage_order.html','icon_url' => 'upload/default/icon//icon_report.png','check'=>0);
			$this->map['categories'] = $items;
		}		
		foreach($this->map['categories'] as $key=>$value){
			if(!$this->map['check_member']){
				if(IDStructure::have_child('function',$value['structure_id'])){
					$this->map['categories'][$key]['check'] = true;
				}else{
					$this->map['categories'][$key]['check'] = false;
				}			
			}			
			if($value['structure_id']==$parent_structure_id){
				$this->map['categories'][$key]['check_selected'] = true;
			}elseif(Url::sget('page')==substr($value['url'],6)){
				$this->map['categories'][$key]['check_selected'] = true;
			}else{
				$this->map['categories'][$key]['check_selected'] = false;
			}
		}
		$this->parse_layout('user_menu',$this->map);
	}
}
?>