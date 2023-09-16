require_once 'packages/core/includes/utils/category.php';
$this->cond = 'status<>"HIDE" and portal_id="'.PORTAL_ID.'" and structure_id<>'.ID_ROOT.'';
if(Module::get_setting('category_id'))
{
	if(Module::get_setting('category_id')=='current')
	{
		$cond = 'portal_id="'.PORTAL_ID.'"';
        $category_id = '';
    	if(Url::get('category_id'))
        {
        	$category_id = intval(Url::get('category_id'));
            $cond.= ' and id = '.$category_id;
        }
        else
        if(URL::get('name_id'))
        {
        	$category_id = addslashes(URL::get('name_id'));
            $cond.= ' and name_id="'.$category_id.'"';
        }
		if($category_id and $category = DB::select('category',$cond))
		{
			$root_structure_id = $category['structure_id'];
            $title = $category['name_'.Portal::language()];
			while(IDStructure::level($root_structure_id)>2)
			{
				$root_structure_id = IDStructure::parent($root_structure_id);
                $category_name = DB::select('category','structure_id="'.$root_structure_id.'" and portal_id="'.PORTAL_ID.'"');
                $title = $category_name['name_'.Portal::language()];
			}
		}
		else
		{
			$root_structure_id = ID_ROOT;
		}
	}
	else
	{
		$root_structure_id = DB::structure_id('category',Module::get_setting('category_id'));
		$this->cond .= ' and '.IDStructure::child_cond($root_structure_id).' and id <>'.Module::get_setting('category_id');
	}
}
else
{
	$root_structure_id = ID_ROOT;
}
if(Module::get_setting('level_limit'))
{
	$this->cond .= ' and '.IDStructure::direct_child_cond($root_structure_id,Module::get_setting('level_limit')).' ';
}
if(Module::get_setting('type'))
{
	$this->cond .= ' and type="'.Module::get_setting('type').'"';
}
if(Module::get_setting('extra_condition'))
{
	$this->cond .= Module::get_setting('extra_condition');
}
$query = 'select *,name_'.Portal::language().' as name from category where '.$this->cond.' order by structure_id ';
$this->map['categories'] = DB::fetch_all($query);
