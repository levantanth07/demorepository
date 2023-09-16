<?php
function update_module_list()
{
	if(URL::check(array('cmd'=>'update','package_id')) and DB::exists_id('package',URL::get('package_id')))
	{
		require_once 'packages/core/includes/portal/package.php';
		$path = get_package_path(URL::get('package_id')).'/modules';
		if($dir = opendir($path))
		{
			$languages = DB::select_all('language');
			while($file = readdir($dir))
			{
				if($file!='.' and $file!='..' and is_dir($path.$file))
				{
					$module_name = $file;
					if(!DB::select('module','name="'.$module_name.'"'))
					{
						$extra = array();
						foreach($languages as $language)
						{
							$extra += array('title_'.$language['id']=>$module_name,'description_'.$language['id']=>$module_name);
						}
						DB::insert('module',array('name'=>$module_name,'package_id'=>URL::get('package_id')));
					}
				}
			}
		}
	}
}
function generate_module_cache()
{
	require_once 'packages/core/includes/portal/layout.php';
	$modules = DB::select_all('module');
	foreach($modules as $module)
	{
		$path = get_package_path($module['package_id']).'modules/'.$module['name'].'/layouts/';
		echo $path.'<br>';
		$dir = opendir($path);
		while($file=readdir($dir))
		{
			if($file!='.' and $file!='..' and file_exists($path.$file))
			{
				$layout = new Layout(str_replace('.php','',$file),array(),false,$path);
				$layout->generate($path.$file,'cache/modules/'.$module['name'].'/default/vietnamese/'.$file);
			}
		}
	}
}
function update_module_help()
{
	if(URL::check(array('cmd'=>'update_help','id')) and $module=DB::exists_id('module',URL::get('id')))
	{
		$languages = DB::select_all('language');
		DB::query('
			select
				page.*
			from
				page
				inner join block on page_id=page.id
			where
				module_id = '.$module['id']
		);
		$pages = DB::fetch_all();
		$package = DB::select('package',$module['package_id']);
			DB::query('
				select
					name as id,
					value_1,value_2
				from
					module_word
				where
					module_id = '.$module['id']);
			$words = DB::fetch_all();
			DB::query('
				select
					module.*,
					page.name as page_name,
					page.title_1 as page_title_1,
					page.title_2 as page_title_2
				from
					module
					left outer join block on module_id=module.id
					left outer join page on page_id=page.id
				where
					module.package_id = '.$module['package_id']
			);
			$related_modules = DB::fetch_all();
			foreach($languages as $language)
			{
				$st = '
	 <table width="468" border="1" cellpadding=5>
		<caption align="left">
		<strong>'.(($language['id']==1)?'Th&ocirc;ng tin chung':'General information').'
		</strong>
		</caption>
	  <tr>
		<td width="139"><strong>'.(($language['id']==1)?'T&ecirc;n module':'Module name').'</strong></td>
		<td width="313">'.$module['title_'.$language['id']].'</td>
	  </tr>
	  <tr>
		<td><strong>'.(($language['id']==1)?'Thu&#7897;c g&oacute;i':'Package').'</strong></td>
		<td>'.$package['title_'.$language['id']].'</td>
	  </tr>
	  <tr>
		<td><strong>'.(($language['id']==1)?'D&ugrave;ng trong trang':'Used in page').'</strong></td>
		<td>';
				$first = true;
				foreach($pages as $page)
				{
					if($first)
					{
						$first = false;
					}
					else
					{
						$st.= ', ';
					}
					$st .= '<a href="?page='.$page['name'].'">'.$page['title_'.$language['id']].'</a>';
				}
				$st.=
		'</td>
	  </tr>
	</table>
	<p>&nbsp;</p>
	<table width="471" border="1" cellpadding=5>
		<caption align="left">
		<strong>'.(($language['id']==1)?'C&aacute;c form':'Forms').'
		</strong>
		</caption>
	  <tr>
		<td width="139"><strong>'.(($language['id']==1)?'T&ecirc;n form':'Form name').'</strong></td>
		<td width="316"><strong>'.(($language['id']==1)?'&Yacute; ngh&#297;a':'Description').'</strong></td>
	  </tr>
	  '.(isset($words['list_title'])?'
	  <tr>
		<td>'.$words['list_title']['value_'.$language['id']].'</td>
		<td>&nbsp;</td>
	  </tr>':'').
	  (isset($words['add_title'])?'
	  <tr>
		<td>'.$words['add_title']['value_'.$language['id']].'</td>
		<td>&nbsp;</td>
	  </tr>':'').
	  (isset($words['edit_title'])?'
	  <tr>
		<td>'.$words['edit_title']['value_'.$language['id']].'</td>
		<td>&nbsp;</td>
	  </tr>':'').
	  (isset($words['delete_title'])?'
	  <tr>
		<td>'.$words['delete_title']['value_'.$language['id']].'</td>
		<td>&nbsp;</td>
	  </tr>':'').
	  (isset($words['delete_selected'])?'
	  <tr>
		<td>'.$words['delete_selected']['value_'.$language['id']].'</td>
		<td>&nbsp;</td>
	  </tr>':'').'
	</table>
	<p>&nbsp;</p>
	<table width="471" border="1" cellpadding=5>
	  <caption align="left">
	  <strong>'.(($language['id']==1)?'C&aacute;c module li&ecirc;n quan':'Related modules').'</strong>
	  </caption>
	  <tr>
		<td width="139"><strong>'.(($language['id']==1)?'T&ecirc;n module':'Name').'</strong></td>
		<td width="316"><strong>'.(($language['id']==1)?'T&ecirc;n trang':'Page').'</strong></td>
	  </tr>';
			  foreach($related_modules as $related_module)
			  {
				$st.='
	  <tr>
		<td><a href="<?php echo URL::build(\'help\');?>&id='.$related_module['id'].'">'.$related_module['title_'.$language['id']].'</a></td>
		<td><a href="?page='.$related_module['page_name'].'">'.$related_module['page_title_'.$language['id']].'</a></td>
	  </tr>';
			  }
			  $st .= '
	</table>
	<p>&nbsp;</p>
	<table width="473" border="1" cellpadding=5>
		<caption align="left">
		<strong>'.(($language['id']==1)?'C&aacute;c tr&#432;&#7901;ng nh&#7853;p li&#7879;u':'Input fields').'</strong>
		</caption>
	  <tr>
		<td width="139"><strong>'.(($language['id']==1)?'T&ecirc;n tr&#432;&#7901;ng':'Name').'</strong></td>
		<td width="139"><strong>'.(($language['id']==1)?'&Yacute; ngh&#297;a':'Description').'</strong></td>
	  </tr>';
			  foreach($words as $key=>$word)
			  {
				if(strpos($key,'invalid')===false and $key!='object_not_exists'
				 and $key!='search' and strpos($key,'admin')===false and $key!='add' and $key!='edit' and $key!='delete'
				 and $key!='delete_selected' and $key!='add_item' and $key!='add_title' and $key!='edit_title' and $key!='list_title'
				 and $key!='list' and $key!='detail_title' and strpos($key,'confirm')===false)
				 {
					$st.='
	  <tr>
		<td>'.$word['value_'.$language['id']].'</td>
		<td>&nbsp;</td>
	  </tr>';
				}
				}
				$st.='
	</table>';
			DB::update('module', array(
				'description_'.$language['id']=>$st
				),'id='.$module['id']
			);
		}
	}
}
function update_module_table()
{
	$modules = DB::fetch_all('
		select
			module.*
		from
			module
			inner join package on package_id=package.id
		where
			'.IDStructure::child_cond(DB::fetch('select structure_id from package where id="'.URL::get('package_id',1).'"','structure_id'))
	);
	require_once 'packages/core/includes/portal/package.php';
	foreach($modules as $module)
	{
		$path = get_package_path($module['package_id']).'modules/'.$module['name'].'/forms';
		$is_service = false;
		if(@($dir = opendir($path)))
		{
			$tables = array();
			while($file=readdir($dir))
			{
				if($file!='.' and $file!='..' and file_exists($path.'/'.$file))
				{
					$content = file_get_contents($path.'/'.$file);
					if(preg_match_all('/select\(\'(\w+)\'/',$content,$found_tables))
					{
						foreach($found_tables[1] as $table)
						{
							$tables[$table] = 0;
						}
					}
					if(preg_match_all('/select\(PORTAL_PREFIX.\'(\w+)\'/',$content,$found_tables))
					{
						foreach($found_tables[1] as $table)
						{
							$tables[$table] = 1;
						}
						$is_service = true;
					}
				}
			}
			$old_tables = DB::fetch_all('select `table` as id from module_table where module_id="'.$module['id'].'"');
			foreach($tables as $table=>$table_is_service)
			{
				if(!isset($old_tables[$table]))
				{
					DB::insert('module_table',array('module_id'=>$module['id'],'table'=>$table,'multi_site'=>$table_is_service));
				}
				else
				if($table_is_service)
				{
					DB::update('module_table',array('multi_site'=>1),'module_id="'.$module['id'].'" and `table`="'.$table.'"');
				}
			}
			if($is_service)
			{
				DB::update_id('module',array('type'=>'SERVICE'),$module['id']);
			}
		}
	}
}
?>