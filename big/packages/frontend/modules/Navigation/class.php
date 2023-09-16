<?php
/*
	create by : ngocnv
	date : 14/07/2009
	edit : 30/07/2009
	Function : Chi tiet san pham
*/
class Navigation extends Module
{
	function Navigation($row)
	{
		Module::Module($row);
		if(URL::get('cmd')=='delete_cache_'.Module::block_id() and User::is_admin())
		{
			Module::$current->set_setting('cache','');
			URL::redirect_url('?'.str_replace('&cmd=delete_cache_'.Module::block_id(),'',$_SERVER['QUERY_STRING']));
		}
		require_once 'db.php';
		require_once 'forms/list.php';
		$this->add_form(new NavigationForm());
	}
	static function show_childs($child_arr,$href, &$map)
	{
		$href = $href.'&amp;portal='.str_replace('#','',PORTAL_ID).'';
		$map['str_ul_structure'] .= '<ul>';
		foreach($child_arr as $value)
		{
			$map['str_ul_structure'] .= '<li><a '.(isset($value['target'])?'target="'.$value['target'].'"':'').' href="'.$value['href'].'">'.$value['name_'.Portal::language()].'</a></li>';
			if(isset($value['childs']))
			{
				Navigation::show_childs($value['childs'],$value['href'],$map);
			}
		}
		$map['str_ul_structure'] .= '</ul>';
	}
	static function make_tree(&$categories,&$map)
	{
		//echo $map['category_cascade'].'<br>';
		//System::debug($categories);
		if(Module::get_setting('category_cascade')
		//and isset($categories[URL::get(Module::get_setting('category_id_param','category_id'))]))//Dieu kien hien thi theo cach cascade
		and ($key = Navigation::check_name(URL::get(Module::get_setting('category_id_param','name_id')),$categories)))
		{
			$crumbs = DB::fetch_all('
				SELECT
					id, structure_id
				FROM
					category
				WHERE
					status <>"HIDE"
					and '.IDStructure::path_cond($categories[$key]['structure_id']).'
				ORDER BY
					structure_id
			');
			$categories = String::array2tree($categories,'childs');
			$new_categories = array();
			Navigation::cascade_tree($categories,$crumbs,$new_categories,1);
			$categories = $new_categories;
			//print_r($categories);
		}
		else
		{
			/*echo '<pre>';
			print_r($categories);
			echo '<pre>';*/
			$categories = String::array2tree($categories,'childs');
			//print_r($categories);
		}
	}
	static function check_name($name_id,$arr)
	{
		if((Module::get_setting('category_id_param','name_id')=='category_id')
			and isset($arr[$name_id])
		)
		{
			return $name_id;
		}
		if(is_array($arr))
		{
			foreach($arr as $key=>$value)
			{
				if($name_id==$value['name_id']) return $key;
			}
		}
		return false;
	}
	static function cascade_tree($categories, &$crumbs, &$new_categories, $level)
	{
		foreach($categories as $id=>$category)
		{
			$category['level'] = $level;
			if(isset($crumbs[$id]))
			{
				$childs = $category['childs'];
				$category['childs']=array();
			}
			$new_categories[$id] = $category;
			if(isset($crumbs[$id]))
			{
				Navigation::cascade_tree($childs, $crumbs, $new_categories,$level+1);
			}
		}
	}
}
?>