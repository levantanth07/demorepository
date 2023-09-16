<?php
class ModuleAdminForm extends Form
{
	function __construct()
	{
		Form::Form("ModuleAdminForm");
		$this->add('id',new IDType(true,'object_not_exists','module'));
		$this->link_css(Portal::template('core').'/css/category.css');
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm'))
		{
			$module = DB::select('module',$_REQUEST['id']);
			if(URL::get('delete_database'))
			{
				$tables = DB::fetch_all('
					select
						id,table
					from
						module_table
					where
						module_id="'.$_REQUEST['id'].'"');
				foreach($tables as $id=>$table)
				{
					if(!DB::select('module_table','table="'.$table['table'].'" and module_id<>"'.$_REQUEST['id'].'"'))
					{
						DB::query('drop table `'.$table.'`;');
					}
				}
			}
			if(URL::get('delete_code'))
			{
				require_once 'packages/core/includes/portal/package.php';
				$path = get_package_path($module['package_id']).'modules/'.$module['name'];
				require_once 'packages/core/includes/utils/dir.php';
				empty_all_dir($path,true);
			}
			$this->delete($this,$_REQUEST['id']);
			Url::redirect_current(array('package_id','name'));
		}
	}
	function draw()
	{
		$languages = DB::select_all('language');
		DB::query('
			select
				`module`.id
				,`module`.`name` ,`module`.`use_dblclick`
				,`module`.title_'.Portal::language().' as title
				,`module`.description_'.Portal::language().' as description
				,`package`.`name` as package_id, module.type
			from
			 	`module`
				left outer join `package` on `package`.id=`module`.package_id
			where
				`module`.id = "'.URL::sget('id').'"');
		if($row = DB::fetch())
		{
		}
		$languages = DB::select_all('language');
		DB::query('
			select
				`module_table`.id
				,`module_table`.`table`
			from
				`module_table`
			where
				`module_table`.module_id="'.$_REQUEST['id'].'"'
		);
		$row['module_table_items'] = DB::fetch_all();
		$this->parse_layout('detail',$row+array('languages'=>$languages));
	}
	function delete(&$form,$id)
	{
		$row = DB::select('module',$id);
		DB::delete('module_table', 'module_id='.$id);
		//DB::delete('module_word', 'module_id='.$id);
		/*DB::query('
			DELETE FROM
				container_block
			USING
				container_block, block
			WHERE
				container_block.container_id = block.id
				AND module_id="'.$id.'"
		');*/
		DB::query('
			DELETE FROM
				block_setting
			USING
				block, block_setting
			WHERE
				block_id=block.id
				AND module_id="'.$id.'"
		');
		DB::query('
			DELETE FROM
				menu_item
			WHERE
				`condition` LIKE "%(MODULE_'.$row['name'].')%"
		');
		DB::query('
			DELETE FROM
				privilege_module
			WHERE
				module_id="'.$id.'"
		');
		DB::delete('block', 'module_id='.$id);
		DB::delete_id('module', $id);
	}
}
?>