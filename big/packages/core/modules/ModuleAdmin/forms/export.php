<?php
	class ExportModuleForm extends Form
	{
		function __construct()
		{
			Form::Form('ExportModuleForm');
			$this->add('module_id',new IDType(true,'modules/invalid','module'));
			$languages = DB::select_all('language');
			$this->link_css(Portal::template('core').'/css/system.css');
		}
		function draw()
		{
			$code = $this->export(URL::get('module_id'));
			if($module=DB::select('module',URL::get('module_id')))
			{
				$file=fopen('cache/exports/'.$module['name'].'.php','w+');
				fwrite($file,$code);
				fclose($file);
			}
			$this->parse_layout('export',
				array(
					'module_id_list'=>String::get_list(DB::select_all('module',false,'name')),
					'module_id'=>0,
					'code'=>$code
				)
			);
		}
		function export($id)
		{
			if(!($module = DB::exists_id('module',$id)))
			{
				return '';
			}
			require_once 'packages/core/includes/portal/package.php';
			$code = '
if(!DB::select(\'module\',\'name="'.$module['name'].'"\'))
{
$page_change = array();';
			$code .= $this->export_package(DB::select('package',$module['package_id']));
			$package_id = $module['package_id'];
			unset($module['id']);
			unset($module['package_id']);
			$code .= '
$module = '.var_export($module,true).';
$module[\'package_id\'] = $package_id;
$id = DB::insert(\'module\',$module);';
			$words = DB::select_all('module_word', 'module_id='.$id);
			foreach($words as $word)
			{
				unset($word['id']);
				unset($word['module_id']);
				$code .= '
$word = '.var_export($word,true).';
$word[\'module_id\'] = $id;
DB::insert(\'module_word\',$word);';
			}
			DB::query('
				select
					page.*
				from
					page
					inner join block on page_id=page.id
				where
					module_id='.$id);
			$pages = DB::fetch_all();
			foreach($pages as $page)
			{
				$code .= $this->export_page($page,$id);
			}
			$code.= '
				@unlink(\'cache/tables/module.cache.php\');
				@unlink(\'cache/tables/package.cache.php\');
				require_once \'packages/core/includes/utils/dir.php\';
empty_all_dir(\'cache/page_layouts\');
}';
			$code .= $this->copy_dir(get_package_path($package_id).'modules/'.$module['name']);
			return $code;
		}
		function export_package($package)
		{
			if($package['structure_id']!=ID_ROOT)
			{
				require_once 'packages/core/includes/system/si_database.php';
				if($parent = si_parent('package',$package['structure_id']))
				{
					$code = '
require_once \'packages/core/includes/system/si_database.php\';
if($package = DB::select(\'package\',\'name="'.$package['name'].'"\') and $parent=si_parent(\'package\',$package[\'structure_id\']) and $parent[\'name\']==\''.$parent['name'].'\')
{
	$package_id = $package[\'id\'];
}
else
{';
					$code .= $this->export_package($parent);
					require_once 'packages/core/includes/portal/package.php';
					$path = get_package_path($package['id']);
					unset($package['id']);
					unset($package['structure_id']);
					$code .= '
		$package = '.var_export($package,true).';
		$package[\'structure_id\'] = si_next_child(\'package\',DB::structure_id(\'package\',$package_id));
		$package_id = DB::insert(\'package\',$package);
		if(!is_dir(\''.$path.'\'))
		{
			mkdir(\''.$path.'\');
		}
';
					$code .= '
}';
				}
				return $code;
			}
			else
			{
				return '
		$package_id='.$package['id'].';';
			}
		}
		function export_page($page,$module_id)
		{
			$code = '
if($page = DB::select(\'page\',\'name="'.$page['name'].'" and params="'.$page['params'].'"\'))
{
	$page_id = $page[\'id\'];';
			if($block = DB::select('block','module_id='.$module_id))
			{
				$settings = DB::select_all('block_setting','block_id='.$block['id']);
				unset($block['id']);
				unset($block['module_id']);
				unset($block['page_id']);
				$code.='
		$block = '.var_export($block,true).';
		$block[\'module_id\'] = $id;
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
		}
		$code .='
}
else
{';
			$package = DB::select('package',$page['package_id']);
			$this->export_package($package);
			$layout = DB::select('layout',$page['layout_id']);
			unset($layout['id']);
			$code .= '
	if($layout = DB::select(\'layout\',\'name="'.$layout['name'].'"\'))
	{
		$layout_id = $layout[\'id\'];
	}
	else
	{
		$layout_id = 1;
	}';
			$page_id = $page['id'];
			unset($page['id']);
			unset($page['package_id']);
			unset($page['layout_id']);
			$code .= '
	$page = '.var_export($page,true).';
	$page[\'package_id\'] = $package_id;
	$page[\'layout_id\'] = $layout_id;
	$page_id = DB::insert(\'page\',$page);';
			$blocks = DB::select_all('block','page_id='.$page_id);
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
			$code .='
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
	}
?>