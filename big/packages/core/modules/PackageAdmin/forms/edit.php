<?php
class EditPackageAdminForm extends Form
{
	function EditPackageAdminForm()
	{
		Form::Form('EditPackageAdminForm');
		if(URL::get('cmd')=='edit')
		{
			$this->add('id',new IDType(true,'object_not_exists','package'));
		}
		$this->add('name',new TextType(true,'invalid_name',0,255));
		$languages = DB::select_all('language');
		foreach($languages as $language)
		{
			$this->add('title_'.$language['id'],new TextType(false,'invalid_title',0,2000));
			$this->add('description_'.$language['id'],new TextType(false,'invalid_description',0,200000));
		}
		$this->link_css('assets/default/css/tabs/tabpane.css');
	}
	function on_submit()
	{
		if(URL::get('cmd')=='edit')
		{
			$row = DB::select('package',$_REQUEST['id']);
		}
		if($this->check() and URL::get('confirm_edit'))
		{
			$extra = array();
			$languages = DB::select_all('language');
			foreach($languages as $language)
			{
				$extra=$extra+array('title_'.$language['id']=>Url::get('title_'.$language['id'],1)); $extra=$extra+array('description_'.$language['id']=>Url::get('description_'.$language['id'],1));
			}
			$new_row = $extra+
				array(
					'name', 'type'
				);
			if(URL::get('cmd')=='edit')
			{
				$id = $_REQUEST['id'];
				DB::update_id('package', $new_row,$id);
						if($row['structure_id']!=ID_ROOT)
		{
			if (Url::check(array('parent_id')))
			{
				$parent = DB::select('package',$_REQUEST['parent_id']);
				if($parent['structure_id']==$row['structure_id'])
				{
					$this->error('id','invalid_parent');
				}
				else
				{
					require_once 'packages/core/includes/system/si_database.php';
						if(!si_move('package',$row['structure_id'],$parent['structure_id']))
					{
						$this->error('id','invalid_parent');
					}
				}
			}
		}
			}
			else
			{
				require_once 'packages/core/includes/system/si_database.php';
				$id = DB::insert('package', $new_row+array('structure_id'=>si_child('package',structure_id('package',$_REQUEST['parent_id']))));
			}
			Url::redirect_current(array(
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  )+array('just_edited_id'=>$id));
		}
	}
	function draw()
	{
		$languages = DB::select_all('language');
		if(URL::get('cmd')=='edit' and $row=DB::select('package',URL::sget('id')))
		{
			foreach($row as $key=>$value)
			{
				if(is_string($value) and !isset($_POST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
			$edit_mode = true;
		}
		else
		{
			$edit_mode = false;
		}
				DB::query('
			select
				id,
				structure_id
				,name
				,title_'.Portal::language().' as title ,description_'.Portal::language().' as description
			from
				`package`
			order by structure_id
		');
		$parents = DB::fetch_all();
		require_once 'packages/core/includes/system/si_database.php';
		DB::query('
			select
				id,
				structure_id
				,name
				,title_'.Portal::language().' as title ,description_'.Portal::language().' as description
			from
			 	`package`
			order by structure_id
		');
		$parents = DB::fetch_all();
		$this->parse_layout('edit',
			($edit_mode?$row:array())+
			array(
				'languages'=>$languages,
				'parent_id_list'=>String::get_list($parents),
				'type_list'=>array('NORMAL'=>'NORMAL','SERVICE'=>'SERVICE','SYSTEM'=>'SYSTEM'),
				'parent_id'=>($edit_mode?si_parent_id('package',$row['structure_id']):1),
			)
		);
	}
}
?>