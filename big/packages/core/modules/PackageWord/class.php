<?php
class PackageWord extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_view())
		{
			switch(URL::get('cmd'))
			{
			case 'delete':
				if(is_array(URL::get('selected_ids')) and sizeof(URL::get('selected_ids'))>0 and User::can_delete())
				{
					foreach(URL::get('selected_ids') as $id)
					{
						DB::delete('word','BINARY id="'.$id.'"');
					}
					Url::redirect_current(array('search_by_package_id', 'search_by_id', 'search_by_time', 'search_by_value', 'page_no'));
				}
				break;
			default:
				require_once 'forms/list.php';
				$this->add_form(new ListPackageWordForm());
				break;
			}
		}
		else
		{
			Url::js_redirect(true,'Bạn không có quyền truy cập!');
		}
	}
}
?>