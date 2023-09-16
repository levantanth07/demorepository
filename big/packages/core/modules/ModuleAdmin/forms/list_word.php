<?php
class ListModuleWordForm extends Form
{
	function __construct()
	{
		Form::Form('ListModuleWordForm');
		$this->languages = DB::select_all('language',false);
		$this->link_css(Portal::template('core').'/css/category.css');
	}
	function on_submit()
	{
		if(URL::check('add_word_list'))
		{
			if($module = DB::select('module',$_REQUEST['id']))
			{
				$package = DB::select('package',$module['package_id']);
				foreach($_REQUEST as $name=>$value)
				{
					if(preg_match('/^word([\d+])_(\w*)/',$name,$patterns))
					{
						if($word = DB::select('module_word','module_id='.$_REQUEST['id'].' and name="'.$patterns[2].'"'))
						{
							DB::update_id('module_word',array('value_'.$patterns[1]=>$value),$word['id']);
						}
						else
						{
							if(isset($_REQUEST['private_word_'.$patterns[2]]))
							{
								DB::insert('module_word',array('module_id'=>$_REQUEST['id'],'name'=>$patterns[2],'value_'.$patterns[1]=>$value));
							}
							else
							{
								if(!DB::fetch('select package_word.* from package_word inner join package on package.id = package_id where (BINARY package_word.id="'.addslashes($patterns[2]).'") and '.IDStructure::path_cond($package['structure_id']).' order by structure_id desc'))
								{
									DB::insert('module_word',array('module_id'=>$_REQUEST['id'],'name'=>$patterns[2],'value_'.$patterns[1]=>$value));
								}
								else
								{
									DB::insert('module_word',array('module_id'=>$_REQUEST['id'],'name'=>$patterns[2],'value_'.$patterns[1]=>$value));
								}
							}
						}
					}
				}
				require_once 'packages/core/includes/utils/dir.php';
				empty_all_dir(ROOT_PATH.'cache/modules/'.$module['name']);
			}
			URL::redirect_current(array('id','cmd'));
		}
	}
	function draw()
	{
		if(!($row = DB::select('module',$_REQUEST['id'])))
		{
			$row=array('name'=>'GLOBAL WORDS');
		}
		$this->parse_layout('list_word', $row+array(
			'languages'=>$this->languages,
			'words'=>$this->get_undefined_word()
		));
	}
	function get_undefined_word()
	{
		$module_id = URL::get('id',0);
		$words = array();
		$all_words = DB::select_all('module_word','module_id='.$module_id,' name');
		foreach($all_words as $key=>$word)
		{
			$word_text = array('id'=>$word['id'],'private'=>1);
			foreach($this->languages as $language)
			{
				$word_text+=array($language['id']=>$word['value_'.$language['id']]);
			}
			$words[$word['name']] = $word_text;
		}
		if(URL::check('id'))
		{
			$module = DB::select('module',$_REQUEST['id']);
			$package = DB::select('package',$module['package_id']);
			if($module['type'] == '' or $module['type'] == 'SERVICE')
			{
				require_once 'packages/core/includes/portal/package.php';
				$path = ROOT_PATH.get_package_path($module['package_id']).'modules/'.$module['name'].'/layouts';
				if(is_dir($path) and $dir = opendir($path))
				{
					while($file = readdir($dir))
					{
						if($file!='.' and $file!='..' and !is_dir($path.'/'.$file))
						{
							$words=$this->get_words($path.'/'.$file,$words,$module['id'],$package['structure_id']);
						}
					}
					closedir($dir);
				}
				$path = ROOT_PATH.get_package_path($module['package_id']).'modules/'.$module['name'].'/forms';
				if(is_dir($path) and $dir = opendir($path))
				{
					while($file = readdir($dir))
					{
						if($file!='.' and $file!='..')
						{
							$words=$this->get_form_words($path.'/'.$file,$words,$module['id'],$package['structure_id']);
						}
					}
					closedir($dir);
				}
			}
			else
			{
				$words=$this->get_words(false,$words,$module['id'],$package['structure_id'],$module['layout']);
				$words=$this->get_form_words(false,$words,$module['id'],$package['structure_id'],$module['code']);
			}
		}
		return $words;
	}
	function get_words($file, $words, $module_id, $structure_id, $st = '')
	{
		if($file)
		{
			$f = fopen($file,'r');
			while(!feof($f))
			{
				$st .= fread($f,1000);
			}
			fclose($f);
		}
		if(preg_match_all('/\[\[\.(\w+)\.\]\]/',$st,$found_words))
		{
			foreach($found_words[1] as $word)
			{
				if(!isset($words[$word]))
				{
					$text = ucfirst(str_replace('_',' ',$word));
					$word_text = array();
					foreach($this->languages as $language)
					{
						if($language['id']!=1)
						{
							$word_text+=array($language['id']=>$text);
						}
					}
					$this->add_word($words,$word,$word_text,$structure_id);
				}
			}
		}
		return $words;
	}
    function get_form_words($file, $words, $module_id, $structure_id, $st = '')
	{
		if($file)
		{
			$f = fopen($file,'r');
			while(!feof($f))
			{
				$st .= fread($f,1000);
			}
			fclose($f);
		}
		if(preg_match_all('/Type\(\w+,\'(\w+)\'/',$st,$found_words))
		{
			foreach($found_words[1] as $word)
			{
				if(!isset($last_words[$word]))
				{
					$text = ucfirst(str_replace('_',' ',$word));
					$word_text = array();
					foreach($this->languages as $language)
					{
						if($language['id']!=1)
						{
							$word_text+=array($language['id']=>$text);
						}
					}
					$this->add_word($words,$word,$word_text,$structure_id);
				}
			}
		}
        if(preg_match_all('/error\(\'\w+\',\s*\'(\w+)\'/',$st,$found_words))
		{
			foreach($found_words[1] as $word)
			{
				if(!isset($last_words[$word]))
				{
					$text = ucfirst(str_replace('_',' ',$word));
					$word_text = array();
					foreach($this->languages as $language)
					{
						if($language['id']!=1)
						{
							$word_text+=array($language['id']=>$text);
						}
					}
					$this->add_word($words,$word,$word_text,$structure_id);
				}
			}
		}
        if(preg_match_all('/Portal::language\(\'(\w+)\'/i',$st,$found_words))
		{
			foreach($found_words[1] as $word)
			{
				if(!isset($last_words[$word]))
				{
					$text = ucfirst(str_replace('_',' ',$word));
					$word_text = array();
					foreach($this->languages as $language)
					{
						if($language['id']!=1)
						{
							$word_text+=array($language['id']=>$text);
						}
					}
					$this->add_word($words,$word,$word_text,$structure_id);
				}
			}
		}
		return $words;
	}
	function add_word(&$words,$word,$word_text,$structure_id)
	{
		if(!isset($words[$word]))
		{
			if($package_word = DB::fetch('select package_word.*, package.name as package_name from package_word inner join package on package.id=package_id where (BINARY package_word.id="'.addslashes($word).'") and '.IDStructure::path_cond($structure_id).' order by structure_id desc'))
			{
				foreach($this->languages as $language)
				{
					$word_text[$language['id']] = $package_word['value_'.$language['id']];
				}
				$words[$word] = $word_text + array('private'=>0, 'package_name'=>$package_word['package_name']);
			}
			else
			{
				$words[$word] = $word_text + array('private'=>1);
			}
		}
	}
}//end class
?>