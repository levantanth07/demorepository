<?php
class RestoreForm extends Form
{
	function RestoreForm()
	{
		Form::Form('RestoreForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function read_file($file)
	{
		return @file_get_contents($file);
	}
	function restore($file)
	{
		$table_system = array_flip (array('page','module','block','block_setting','module_setting','account'));
		if($file)
		{
			if(@preg_match_all('/backup\/sql\/(.*)\/(.*).sql/',$file,$matches) and isset($matches[2][0]))
			{
				$table = $matches[2][0];
				if(!isset($table_system[$table]))
				{
					$sql = explode('\n',$this->read_file($file));
					if(DB::fetch('SHOW TABLES LIKE "'.$table.'"'))
					{
						DB::query('drop table '.$table);
					}
					foreach($sql as $key=>$value)
					{
						if($value!='')
						{
							DB::query($value);
						}
					}
				}
			}
		}
	}
	function on_submit()
	{
		if(Url::get('cmd')=='restore' and isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0)
		{
			set_time_limit(0);
			foreach($_REQUEST['selected_ids'] as $key=>$value)
			{
				if($value!='')
				{
					$this->restore($value);
				}
			}
			Url::redirect_current();
		}
	}
	function draw()
	{
		require_once 'packages/core/includes/utils/dir.php';
		$path = Url::get('folders','').'/';
		$tables = get_files_in_dir($path,'file','','');
		$folders = get_files_in_dir('backup/sql/','dir','','');
		$this->parse_layout('restore',
			array(
				'tables'=>$tables,
				'folders_list'=>array(Portal::language('select_folder'))+$folders,
				'path'=>$path,
				'total'=>count($tables)
			));
	}
}
?>
