<?php
class PackageAdminForm extends Form
{
	function PackageAdminForm()
	{
		Form::Form("PackageAdminForm");
		$this->add('id',new IDType(true,'object_not_exists','package'));
		$this->link_css(Portal::template('core').'/css/category.css');
	}
	function on_submit()
	{
		if($this->check() and URL::get('confirm'))
		{
			$this->delete($this,$_REQUEST['id']);
			Url::redirect_current(array(
	'name'=>isset($_GET['name'])?$_GET['name']:'',
	  ));
		}
	}
	function draw()
	{
		$languages = DB::select_all('language');
		DB::query('
			select
				`package`.id
				,`package`.structure_id
				,`package`.`name`
				,`package`.title_'.Portal::language().' as title
				,`package`.description_'.Portal::language().' as description
			from
			 	`package`
			where
				`package`.id = "'.URL::sget('id').'"');
		if($row = DB::fetch())
		{
		}
		$languages = DB::select_all('language');
		DB::query('
			select module.* from module where 1 and package_id = '.$row['id'].'
		');
		$row['module_related_fields'] = DB::fetch_all(); DB::query('
			select page.* from page where 1 and package_id = '.$row['id'].'
		');
		$row['edit_page_related_fields'] = DB::fetch_all();
		$this->parse_layout('detail',$row+array('languages'=>$languages));
	}
	function delete(&$form,$id)
	{
		$package = DB::select('package',$id);
		$packages = DB::fetch_all('
			SELECT
				id
			FROM
				package
			WHERE
				'.IDStructure::child_cond($package['structure_id']).'
			ORDER BY
				structure_id DESC
		');
		foreach($packages as $package)
		{
			PackageAdminForm::delete_package($package['id']);
		}
	}
	function delete_package($id)
	{
		$row = DB::select('package',$id);
		DB::query('
			DELETE FROM
				module_table
			USING
				module_table, module
			WHERE
				module_id=module.id
				AND module.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				block_setting
			USING
				block, block_setting, module
			WHERE
				module_id=module.id
				AND block_id=block.id
				AND module.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				block
			USING
				block, module
			WHERE
				module_id=module.id
				AND module.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				privilege_module
			USING
				privilege_module, module
			WHERE
				module_id = module.id
				AND module.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				module
			WHERE
				package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				page_action
			USING
				page_action, page
			WHERE
				page_id=page.id
				AND page.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				block_setting
			USING
				block_setting, block, page
			WHERE
				block_id=block.id
				AND page_id = page.id
				AND page.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				block
			USING
				block, page
			WHERE
				page_id=page.id
				AND page.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				page
			WHERE
				page.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				privilege_module
			USING
				privilege_module, privilege
			WHERE
				privilege_id = privilege.id
				AND privilege.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				privilege
			WHERE
				privilege.package_id="'.$id.'"'
		);
		DB::query('
			DELETE FROM
				privilege
			WHERE
				privilege.package_id="'.$id.'"'
		);
		DB::delete('package_word', 'package_id='.$id);
		DB::delete_id('package', $id);
	}
}
?>