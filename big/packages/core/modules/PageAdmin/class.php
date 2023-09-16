<?php
class PageAdmin extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_view())
		{
			if(Url::check(array('cmd'=>'delete_all_cache')) and User::can_edit())
			{
				require_once 'packages/core/includes/utils/dir.php';
				empty_all_dir(ROOT_PATH.'cache/page_layouts');
				empty_all_dir(ROOT_PATH.'cache/modules');
				Url::redirect_current(array('package_id','portal_id'));
			}
			else
			if(Url::check(array('cmd'=>'generate_all_cache')) and User::can_edit())
			{
				$this->make_all_page_cache();
				Url::redirect_current(array('package_id','portal_id'));
			}
			if(Url::check(array('cmd'=>'refresh','id')) and User::can_edit())
			{
				require_once 'packages/core/includes/portal/update_page.php';
				update_page($_REQUEST['id']);
				if(Url::check('href'))
				{
					Url::redirect_url($_REQUEST['href']);
				}
				else
				{
					Url::redirect_current(array('package_id','portal_id'));
				}
			}
			else
			if(URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete())
			{
				if(sizeof(URL::get('selected_ids'))>1)
				{
					require_once 'forms/list.php';
					$this->add_form(new ListPageAdminForm());
				}
				else
				{
					$ids = URL::get('selected_ids');
					$_REQUEST['id'] = $ids[0];
					require_once 'forms/detail.php';
					$this->add_form(new PageAdminForm());
				}
			}
			else
			if(
				(((URL::check(array('cmd'=>'delete'))and User::can_delete())
					or (URL::check(array('cmd'=>'edit')) and User::can_edit())
					or (URL::check(array('cmd'=>'view')) and User::can_view_detail()))
					and Url::check('id') and DB::exists_id('page',$_REQUEST['id']))
				or
				(URL::check(array('cmd'=>'add')) and User::can_add())
				or
				(URL::check(array('cmd'=>'duplicate')) and User::can_add())
				or
				(URL::check(array('cmd'=>'add_blocks')) and User::can_add())
				or !URL::check('cmd')
				or URL::check(array('cmd'=>'list_sibling','name'))
			)
			{
				switch(URL::get('cmd'))
				{
				case 'delete':
					require_once 'forms/detail.php';
					$this->add_form(new PageAdminForm());break;
				case 'edit':
				case 'add':
					require_once 'forms/edit.php';
					$this->add_form(new EditPageAdminForm());break;
				case 'add_blocks':
					require_once 'forms/add_blocks.php';
					$this->add_form(new AddBlocksPageAdminForm());break;
				case 'view':
					require_once 'forms/detail.php';
					$this->add_form(new PageAdminForm());break;
				case 'duplicate':
					require_once 'forms/duplicate.php';
					$this->add_form(new DuplicatePageForm());break;
				case 'list_sibling':
					require_once 'forms/list_sibling.php';
					$this->add_form(new ListPageAdminSiblingForm());break;
				default:
					require_once 'forms/list.php';
					$this->add_form(new ListPageAdminForm());
					break;
				}
			}
			else
			{
				Url::redirect_current();
			}
		}
		else
		{
			URL::access_denied();
		}
	}
	function make_all_page_cache()
	{
		$pages= DB::fetch_all('select * from page');
		require_once 'packages/core/includes/portal/generate_page.php';
		$_REQUEST['cmd']='edit';
		$_REQUEST['id']=1;
		foreach($pages as $page)
		{
			if($page['name']!='sign_out' and $page['name']!='edit_page' and $page['name']!='restaurant_equipment' and $page['name']!='log' and $page['name']!='company_information'and $page['name']!='news'and $page['name']!='news_browse'and $page['name']!='cart')
			{
				echo $page['name'].'<br>';
				$generate_page = new GeneratePage($page);
				$code =  $generate_page->generate_text();
				$cache_file=ROOT_PATH.'cache/page_layouts/'.$generate_page->data['name'].($generate_page->data['params']?'.'.$generate_page->data['params']:'').'.cache.php';
				$fp = @fopen($cache_file, 'w+');
				fwrite ($fp, $code );
				fclose($fp);
				ob_start();
				require_once $cache_file;
				ob_end_clean();
			}
		}
	}
}
?>