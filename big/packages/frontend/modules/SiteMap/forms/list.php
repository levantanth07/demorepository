<?php
class SiteMapForm extends Form
{
	function SiteMapForm()
	{
		Form::Form('SiteMapForm');
		$this->link_css(Portal::template('hotel').'css/sitemap.css');
	}
	function on_submit()
	{
	}
	function draw()
	{
		$site_maps = $site_map = SiteMapDB::site_map();
		$this->map['pages'] = SiteMapDB::get_category('and portal_id = "'.PORTAL_ID.'" and category.status != "HIDE" and '.IDStructure::direct_child_cond(DB::structure_id('category',1)));
		//System::debug($this->map['pages']);
		/*$type = array(
			'trang-tin'=>'NEWS'
			,'san-pham'=>'PRODUCT'
			,'hoi-dap'=>'FAQ'
		);
		foreach($site_maps as $id=>$page)
		{
			if(isset($type[$page['name']]))
			{
				$site_map +=  SiteMapDB::get_category('and category.status != "HIDE" and category.type="'.$type[$page['name']].'"');
			}
			//$site_map[$id] = $page;
		}*/
		//System::debug($site_map);
		$this->parse_layout('list',$this->map);
	}
}
?>
