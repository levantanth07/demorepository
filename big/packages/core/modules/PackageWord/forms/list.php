<?php
class ListPackageWordForm extends Form
{
    var $languages;
	function __construct()
	{
        $this->languages = DB::select_all('language','language.active=1');
		Form::Form('ListPackageWordForm');
        if(Url::post('update') == 'update'){
            $this->update();
        }
        if(Url::get('delete') == 'delete'){
            $this->delete();
        }
	}
	function update(){
        if(URL::get('records')) {
            foreach(URL::get('records') as $id=>$record) {
                if($record['id'])
                {
                    $record['time'] = time();
                    //$record['package_id'] = DB::fetch('select id from package where name="'.addslashes($record['package_id']).'" ','id');
                    if(!DB::fetch('select id from word where BINARY id="'.$record['id'].'"'))
                    {
                        DB::insert('word',$record);
                    }
                    else
                    {
                        DB::update('word',$record,'(BINARY id="'.$record['id'].'")');
                    }
                }
            }
            if(URL::get('module_id') and $module = DB::select('module','id="'.URL::get('module_id').'"'))
            {
                require_once 'packages/core/includes/utils/dir.php';
                empty_all_dir(ROOT_PATH.'cache/modules/'.$module['name']);
            }
            Portal::make_word_cache();
            URL::js_redirect(true,'Bạn vừa câp nhật thành công.',array('module_id','block_id'));
        }else{
            //URL::js_redirect(true,'Bạn chưa chọn input nào để chỉnh sửa.');
        }
    }
    function delete(){
        if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0)
        {
            foreach(URL::get('selected_ids') as $id)
            {
                DB::delete('word','BINARY id="'.$id.'"');
            }
            Url::js_redirect(true,'Xoá thành công!');
        }
    }
	function draw()
	{
		$languages = DB::select_all('language');
		if(URL::get('order_by'))
		{
			$order_by = URL::get('order_by').' '.URL::get('dir');
		}
		else
		{
			$order_by = ' id asc';
		}
		$cond = '
				1 '
				.(URL::get('search_by_package_id')?'
					and '.IDStructure::child_cond(DB::fetch('select structure_id from `package` where id="'. URL::get('search_by_package_id',1).'"','structure_id'),false,'package.').'
				':'')
				.(URL::get('search_by_id')?' and (BINARY `word`.`id` LIKE "%'.URL::get('search_by_id').'%")':'')
				.(URL::get('search_by_time')?' and `word`.`time` LIKE "%'.URL::get('search_by_time').'%"':'')
            .(URL::get('keyword')?' and `word`.`id` LIKE "%'.URL::get('keyword').'%"':'')
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and (BINARY  `word`.id in ("'.join(URL::get('selected_ids'),'","').'"))':'')
		;
		if(URL::get('module_id') and $module=DB::select('module','id="'.URL::get('module_id').'"'))
		{
			$words = $this->get_undefined_word(URL::get('module_id'));
			$cond .= ' and (BINARY `word`.id in ("'.join(array_keys($words),'","').'"))';
		}
		foreach($languages as $id=>$language)
		{
			$cond = $cond
				.(URL::get('search_by_value_'.$id)?' and `word`.value_'.$id.' LIKE "%'.URL::get('search_by_value_'.$id).'%"':'') ;
		}
		$item_per_page = isset($module)?200:Module::$current->get_setting('item_per_page',100);
		DB::query('
			select count(*) as acount
			from
				`word`
				'.(URL::get('search_by_package_id')?'
				left outer join `package` on `package`.id=`word`.`package_id`
				':'').'
			where '.$cond.'
			order by
				'.$order_by.'
			limit 0,1
		');
		$count = DB::fetch();
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($count['acount'],$item_per_page);
		$addition = '';
		foreach($languages as $id=>$language)
		{
			$addition = $addition
			.',`word`.`value_'.$id.'`' ;
		}
		DB::query('
			select
				`word`.id
				,IF(`word`.`time`>0,FROM_UNIXTIME(`word`.`time`,"%d/%m/%Y"),"") as time
				'.$addition.'
				,`package`.name as package_id
			from
			 	`word`
				left outer join `package` on `package`.id=`word`.`package_id`
			where '.$cond.'
			order by '.$order_by.'
			limit '.((page_no()-1)*$item_per_page).','.$item_per_page.'
		');
		$items = DB::fetch_all();
		$packages = DB::fetch_all('
			select
				`package`.name as id,name	as name
			from
				package
			order by
				name
		');
		$i=1;
		foreach ($items as $key=>$value)
		{
			$items[$key]['i']=$i++;
		}
		$just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids'))
		{
			if(is_string(UrL::get('selected_ids')))
			{
				if (strstr(UrL::get('selected_ids'),','))
				{
					$just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else
				{
					$just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
		$new_items = array();
		if(isset($module) and isset($words) and !(URL::get('search_by_id') or URL::get('search_by_time')or URL::get('search_by_value_1')or URL::get('search_by_value_2'))and URL::get('cmd')!='delete')
		{
			$package = DB::select('package',$module['package_id']);
			foreach($words as $word_id=>$word)
			{
				if(!isset($items[$word_id]))
				{
					$new_items[$word_id] = array(
						'id'=>$word_id,
					);
					foreach($this->languages as $language)
					{
						$new_items[$word_id]['value_'.$language['id']] = ucfirst(str_replace('_',' ',$word_id));
					}
					$new_items[$word_id] += array(
						'package_id'=>$package['name'],
						'time'=>date('d/m/Y'),
					);
				}
			}
		}
		$this->parse_layout('list',
			$just_edited_id+
			array(
				'module_name'=>isset($module)?$module['name']:'',
				'items'=>$items,
				'new_items'=>$new_items,
				'paging'=>$paging,
				'packages'=>$packages,
				'order_by'=>$order_by,
				'languages'=>$languages
			)
		);
	}
	function get_undefined_word($module_id)
	{
		$words = array();
		if($module_id)
		{
			$module = DB::select('module',$module_id);
			$package = DB::select('package',$module['package_id']);
			if($module['name'] == 'HTML' and URL::get('block_id') and $html = Module::get_setting('html',false,URL::get('block_id')))
			{
				$words=$this->get_words(false,$words,$module['id'],$package['structure_id'],$html);
				$words=$this->get_form_words(false,$words,$module['id'],$package['structure_id'],$html);
			}
			if(URL::get('block_id') and $cache = Module::get_setting('cache',false,URL::get('block_id')))
			{
				$words=$this->get_words(false,$words,$module['id'],$package['structure_id'],$cache);
				$words=$this->get_form_words(false,$words,$module['id'],$package['structure_id'],$cache);
			}
			if(($module['name'] == 'Frame' or $module['name'] == 'content') and URL::get('block_id') and $title = Module::get_setting('title',false,URL::get('block_id')) and !strpos($title,'\''))
			{
				$text = ucfirst(str_replace('_',' ',$title));
				$word_text = array();
				foreach($this->languages as $language)
				{
					if($language['id']!=1)
					{
						$word_text+=array($language['id']=>$text);
					}
				}
				$this->add_word($words,$title,$word_text,$package['structure_id']);
			}
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
		if($file and is_file($file) and file_exists($file))
		{
			$f = fopen($file,'r');
			while(!feof($f))
			{
				$st .= fread($f,1000);
			}
			fclose($f);
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
		}
		return $words;
	}
	function add_word(&$words,$word,$word_text,$structure_id)
	{
		if(!isset($words[$word]))
		{
			if($package_word = DB::fetch('select word.*, package.name as package_name from word inner join package on (BINARY package.id=package_id) where (BINARY word.id="'.addslashes($word).'") and '.IDStructure::path_cond($structure_id).' order by structure_id desc'))
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
}
?>