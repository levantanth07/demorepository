<?php
class AddBlocksPageAdminForm extends Form
{
	function AddBlocksPageAdminForm()
	{
		Form::Form('addBlocksPageAdminForm');
		$this->add('module_id',new IDType(true,false,'module'));
		$this->add('page_name',new NameType(true,'missing_name'));
		$this->add('block_name',new TextType(true,'missing_name',0,255));
		$this->add('container_id',new IDType(false,false,'module'));
		$this->add('region',new NameType(true,'missing_name'));
		$this->add('position',new IntType(false,'missing_position'));
		$this->add('copy_setting_id',new IDType(false,false,'block'));
	}
	/// thuc hien cac hanh dong submit form
	function on_submit()
	{
		if($this->check())
		{
			if($pages = PageAdminDB::find_pages(URL::get('portals'),URL::get('page_name')))
			{
				foreach($pages as $page)
				{
					if(URL::get('confirm_delete'))
					{
						PageAdminDB::delete_block(URL::get('module_id'),$page['id'],URL::get('container_id'),URL::get('region'),URL::get('position'),URL::get('block_name'));
					}
					else
					{
						$block_id = PageAdminDB::add_block(URL::get('module_id'),$page['id'],URL::get('container_id'),URL::get('region'),URL::get('position'),URL::get('block_name'));
						if(URL::get('copy_setting_id'))
						{
							PageAdminDB::copy_block_setting($block_id, URL::get('copy_setting_id'));
						}
					}
					require_once 'packages/core/includes/portal/update_page.php';
					update_page($page['id']);
				}
			}
		}
	}
	// hien thi form sua doi thong tin cua Page
	function draw()
	{
		$module_id_list = String::get_list(PageAdminDB::get_module_list());
		$page_name_list = String::get_list(PageAdminDB::get_page_name_list());
		$container_id_list = array(''=>'')+String::get_list(PageAdminDB::get_container_list());
		$region_list = String::get_list(PageAdminDB::get_region_list());
		$portals_list = String::get_list(PageAdminDB::get_portal_list());
		$copy_setting_id_list = array(''=>'')+String::get_list(PageAdminDB::get_copy_setting_id_list(URL::get('module_id')));
		$position_list = array(''=>'');
		for($i=1;$i<=10;$i++)
		{
			$position_list[$i] = $i;
		}
		$this->parse_layout('add_blocks',array(
			'module_id_list'=>$module_id_list,
			'page_name_list'=>$page_name_list,
			'container_id_list'=>$container_id_list,
			'copy_setting_id_list'=>$copy_setting_id_list,
			'region_list'=>$region_list,
			'position_list'=>$position_list,
			'portals[]_list'=>$portals_list,
		));
	}
}//end class
?>