<?php
class ExportPackageForm extends Form
{
	function ExportPackageForm()
	{
		Form::Form('ExportPackageForm');
		$this->add('package_id',new IDType(true,'invalid_package','package'));
	}
	function draw()
	{
		$this->parse_layout('export_package',array(
			'package_id_list'=>String::get_list(DB::select_all('package',false,' structure_id')),
			'package_id'=>URL::get('package_id',15),
			'code'=>$this->export(URL::get('package_id'))
		));
	}
	function export($package_id)
	{
		if(!($package = DB::exists_id('package',$package_id)))
		{
			return;
		}
		//Tao package
		$code = '';
		$code .= $this->export_package($package);
		$code .= '
$package_change = array('.$package['id'].'=>$package_id);
';
		$code .= $this->export_child_package($package);
		DB::query('
			select
				module.*
			from
				module
				inner join package on package_id = package.id
			where
				'.IDStructure::child_cond($package['structure_id']));
		$modules = DB::fetch_all();
		foreach($modules as $module)
		{
			$code .= $this->export_module($module);
		}
		DB::query('
			select
				page.*
			from
				page
				inner join package on package_id = package.id
			where
				'.IDStructure::child_cond($package['structure_id']));
		$pages = DB::fetch_all();
		foreach($pages as $page)
		{
			$code .= $this->export_page($page);
		}
		$code.= '
@unlink(\'cache/tables/module.cache.php\');
@unlink(\'cache/tables/package.cache.php\');
require_once \'packages/core/includes/utils/dir.php\';
empty_all_dir(\'cache/page_layouts\');
';
		require_once 'packages/core/includes/portal/package.php';
		$path = get_package_path($package['id']);
		$code .= $this->copy_dir(substr($path,0,strlen($path)-1));
		return $code;
	}
	function export_package($package)
	{
		$code = 'require_once \'packages/core/includes/system/si_database.php\';
';
		$packages = DB::select_all('package',IDStructure::path_cond($package['structure_id']),'structure_id');
		foreach($packages as $package)
		{
			if($package['structure_id']!=ID_ROOT)
			{
				$code .= '
if($package = DB::select(\'package\',\'name="'.$package['name'].'" and \'.IDStructure::child_cond($structure_id)))
{
	$package_id = $package[\'id\'];
	$structure_id = $package[\'structure_id\'];
}
else
{';
			require_once 'packages/core/includes/portal/package.php';
			$path = get_package_path($package['id']);
			unset($package['id']);
			unset($package['structure_id']);
			$code .= '
	$package = '.var_export($package,true).';
	$structure_id = si_next_child(\'package\',$structure_id);
	$package[\'structure_id\'] = $structure_id;
	$package_id = DB::insert(\'package\',$package);
	if(!is_dir(\''.$path.'\'))
	{
		mkdir(\''.$path.'\');
	}
}';
			}
			else
			{
				$code .= '
	$package_id = '.$package['id'].';
	$structure_id = '.$package['structure_id'].';';
			}
		}
		return $code;
	}
	function export_child_package($parent)
	{
		$code = '';
		if($packages = DB::select_all('package',IDStructure::direct_child_cond($parent['structure_id'])))
		{
			$code = '
$package_id = $package_change['.$parent['id'].'];
$parent_package = DB::select(\'package\',$package_id);';
			foreach($packages as $package)
			{
				$code .='
if(!($package=DB::select(\'package\',\'name="'.$package['name'].'" and \'.IDStructure::direct_child_cond($parent_package[\'structure_id\']))))
{';
				$inserting_package = $package;
				unset($inserting_package['id']);
				unset($inserting_package['structure_id']);
				$code .= '
	$package = '.var_export($inserting_package,true).';
	$package[\'structure_id\'] = si_next_child(\'package\',$parent_package[\'structure_id\']);
	$package_change['.$package['id'].'] = DB::insert(\'package\',$package);
}
else
{
	$package_change['.$package['id'].'] = $package[\'id\'];
}';
			}
			foreach($packages as $package)
			{
				$code .= $this->export_child_package($package);
			}
		}
		return $code;
	}
	function export_module($module)
	{
		$inserting_module = $module;
		$code = '
if(!DB::select(\'module\',\'name="'.$module['name'].'"\'))
{';
		unset($inserting_module['id']);
		unset($inserting_module['package_id']);
		$code .= '
	$module = '.var_export($inserting_module,true).';
	$module[\'package_id\'] = $package_change['.$module['package_id'].'];
	$module_id = DB::insert(\'module\',$module);
';
		if($words = DB::select_all('module_word', 'module_id='.$module['id']))
		{
			$code .= '
	DB::query(\'insert into module_word(module_id, name, value_1, value_2) values ';
			$first = true;
			foreach($words as $word)
			{
				if(!$first)
				{
					$code .= ',';
				}
				else
				{
					$first = false;
				}
				$code .= '
		(\'.$module_id.\',"'.$word['name'].'","'.strtr($word['value_1'],array('&'=>'&amp;','\\'=>'\\\\','\''=>'\\\'','"'=>'\\"','</textarea'=>'&lt;/textarea','</TEXTAREA'=>'&lt;/TEXTAREA')).'","'.strtr($word['value_2'],array('\\'=>'\\\\','\''=>'\\\'','"'=>'\\"')).'")';
			}
			$code .= '\');';
		}
		$code .= '
}';
		return $code;
	}
	function export_page($page)
	{
		$code = '
if(!($page = DB::select(\'page\',\'name="'.$page['name'].'" and params="'.$page['params'].'"\')))
{';
		$layout = DB::select('layout',$page['layout_id']);
		$code .= '
	if($layout = DB::select(\'layout\',\'name="'.$layout['name'].'"\'))
	{
		$layout_id = $layout[\'id\'];
	}
	else
	{
		$layout_id = 1;
	}';
		$inserting_page = $page;
		unset($inserting_page['id']);
		unset($inserting_page['package_id']);
		unset($inserting_page['layout_id']);
		$code .= '
	$page = '.var_export($inserting_page,true).';
	$page[\'package_id\'] = $package_change['.$page['package_id'].'];
	$page[\'layout_id\'] = $layout_id;
	$page_id = DB::insert(\'page\',$page);';
		$blocks = DB::select_all('block','page_id='.$page['id']);
		foreach($blocks as $block)
		{
			if($module = DB::select('module',$block['module_id']))
			{
				$settings = DB::select_all('block_setting','block_id='.$block['id']);
				$code .= '
	if($module = DB::select(\'module\',\'name="'.$module['name'].'"\'))
	{';
				unset($block['id']);
				unset($block['module_id']);
				unset($block['page_id']);
				$code.='
		$block = '.var_export($block,true).';
		$block[\'module_id\'] = $module[\'id\'];
		$block[\'page_id\'] = $page_id;
		$block_id = DB::insert(\'block\',$block);';
				foreach($settings as $setting)
				{
					unset($setting['id']);
					unset($setting['block_id']);
					$code .= '
		$setting = '.var_export($setting,true).';
		$setting[\'block_id\'] = $block_id;
		DB::insert(\'block_setting\',$setting);';
				}
				$code .= '
	}';
			}
		}
		$code .= '
}';
		return $code;
	}
	function copy_dir($path)
	{
		$code = '';
		if(is_dir($path))
		{
			$code .= '
if(!is_dir(\''.$path.'\'))
{
	@mkdir(\''.$path.'\');
}
if(is_dir(\''.$path.'\'))
{';
			$dir = opendir($path);
			while($file = readdir($dir))
			{
				if($file!='.' and $file != '..')
				{
					$code .= $this->copy_dir($path.'/'.$file);
				}
			}
			$code .= '
}';
		}
		else
		{
			if(file_exists($path))
			{
				$f = fopen($path,'r');
				$st = '';
				while(!feof($f))
				{
					$st .= fread($f,2000);
				}
				$code .= '
	if($f = fopen(\''.$path.'\',\'w+\'))
	{
		fwrite($f,\''.strtr($st,array('&'=>'&amp;','\\'=>'\\\\','\''=>'\\\'','</textarea'=>'&lt;/textarea','</TEXTAREA'=>'&lt;/TEXTAREA')).'\');
	}';
			}
		}
		return $code;
	}
}//end class
?>