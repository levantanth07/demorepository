<?php
class Advertisment extends Module
{
	function __construct($row)
	{
		Module::Module($row);
		if(URL::get('cmd')=='click' and $id=intval(URL::get('id')) and $advertisment = DB::select('advertisment',$id) and $item=DB::select('media','id="'.$advertisment['item_id'].'" and portal_id="'.PORTAL_ID.'"'))
		{
			if(!Session::is_set('advertisments'))
			{
				$ids = array_flip(explode(',',Session::get('advertisments')));
				if(!isset($ids[$item['id']]))
				{
					$ids[$item['id']] = 1;
					DB::update('advertisment',array('click_count'=>$advertisment['click_count']+1),'id="'.$advertisment['id'].'"');
					Session::set('advertisments', implode(',',array_keys($ids)));
				}
			}
			echo '<script>window.location="'.$item['url'].'"</script>';
		}
		else
		{
			Module::Module($row);
			require_once 'forms/list.php';
			$this->add_form(new AdvertismentForm());
		}
	}
}
?>