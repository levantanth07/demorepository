require_once 'packages/core/includes/utils/category.php';
$this->cond = 'status<>"HIDE" and portal_id="'.PORTAL_ID.'" and structure_id<>'.ID_ROOT.'';
if(Module::get_setting('category_id'))
{
	if(Module::get_setting('category_id')=='current')
	{
		if(URL::get('category_id') and $category = DB::select('category','id="'.URL::get('category_id').'" and portal_id="'.PORTAL_ID.'"'))
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
if(Module::get_setting('data_type'))
{
	$this->cond .= ' and type="'.Module::get_setting('data_type').'"';
}
if(Module::get_setting('extra_condition'))
{
	$this->cond .= Module::get_setting('extra_condition');
}
$query = 'select *,name_'.Portal::language().' as name from category where '.$this->cond.' order by structure_id ';
$this->map['categories'] = DB::fetch_all($query);
