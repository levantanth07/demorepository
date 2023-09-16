<?php
class GrantModeratorForm extends Form
{
	function __construct()
	{
		Form::Form('GrantModeratorForm');
		$this->add('account_id',new TextType(true,'invalid_user_id',0,50));
		$this->link_js('assets/standard/js/jquery-ui.min.js');
		$this->link_css('assets/standard/css/jquery-ui.min.css');		
	}
	function on_submit()
	{
		if($this->check())
		{
			if(Url::get('account_id') and !DB::exists_id('account',Url::sget('account_id')))
			{
				$this->error('account_id','account_id_not_exist');
				return;
			}
			if(!URL::get('portal_id') or !DB::select('account','id="#'.URL::sget('portal_id').'"'))
			{
				$this->error('portal_id','invalid_portal_id');
				return;
			}
			if(!Url::get('privilege_id'))
			{
				$this->error('privilege_id','you_must_select_privilege_id');
				return;
			}
			$privilege = array();
			foreach(Url::get('privilege_id') as $key)
			{
				if($item = DB::fetch('
					select
						function.structure_id as id
					from
						privilege
						left outer join function on `function`.id = privilege.category_id
					where
						privilege.id = '.$key
				))
				{
					if(isset($item['id']))
					{
						$privilege[$item['id']] = $item['id'];
					}
					else
					{
						$privilege[ID_ROOT] = ID_ROOT;
					}
				}
				/*if(Url::get('categories') and count(Url::get('categories'))>0)
				{
					foreach(Url::get('categories') as $category)
					{
						ModeratorDB::update_moderator(URL::get('id'),URL::get('account_id'),'#'.URL::get('portal_id'),$key,$category);
					}
				}
				else
				{
					ModeratorDB::update_moderator(URL::get('id'),URL::get('account_id'),'#'.URL::get('portal_id'),$key,false);
				}*/
				ModeratorDB::update_moderator(URL::get('id'),URL::get('account_id'),'#'.URL::get('portal_id'),$key,1);
			}
			Portal::set_setting('privilege',var_export($privilege,true),Url::sget('account_id'));
			die;
			Url::redirect_current();
		}
	}
	function draw()
	{
		if((Url::get('id') and $item=DB::select('account_privilege',intval(Url::get('id')))))
		{
			foreach($item as $key=>$value)
			{
				if(is_string($value) and !isset($_REQUEST[$key]))
				{
					$key=='portal_id'?$_REQUEST[$key] = substr($value,1):$_REQUEST[$key] = $value;
					if($key=='category_id')
					{
						$_REQUEST['categories[]'] = $value;
					}
				}
			}
		}
		if(!isset($_REQUEST['portal_id']))
		{
			$_REQUEST['portal_id'] = substr(PORTAL_ID,1);
		}
		$this->parse_layout('select_portal',array(
			'portals'=>ModeratorDB::get_portals(),
			'privilege'=>ModeratorDB::get_privileges(),
			'users'=>ModeratorDB::get_users(),
			'categories[]_list'=>MiString::get_list(ModeratorDB::get_categories())
		));
	}
}
?>