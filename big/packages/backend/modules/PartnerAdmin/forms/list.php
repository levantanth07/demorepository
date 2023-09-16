<?php
class ListPartnerAdminForm extends Form
{
	function __construct()
	{
		Form::Form('ListPartnerAdminForm');
	}
	function save_position()
	{
		foreach($_REQUEST as $key=>$value)
		{
			if(preg_match('/position_([0-9]+)/',$key,$match) and isset($match[1]))
			{
				DB::update_id('partner',array('position'=>Url::get('position_'.$match[1])),$match[1]);
			}
		}
		Url::redirect_current();
	}
	function delete()
	{
		if(isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0)
		{
			foreach($_REQUEST['selected_ids'] as $key)
			{
				if($item = DB::exists_id('partner',$key))
				{
					save_recycle_bin('partner',$item);
					DB::delete_id('partner',intval($key));
					save_log($key);
				}
			}
		}
		Url::redirect_current();
	}
	function on_submit()
	{
		switch(Url::get('cmd'))
		{
			case 'update_position':
				$this->save_position();
				break;
			case 'delete':
				$this->delete();
				break;
		}
	}
	function draw()
	{
        try {
            $cond = '1 = 1';
            $cond .= $this->get_condition();
            $this->get_just_edited_id();
            require_once 'packages/core/includes/utils/paging.php';
            $status = array(
                'SHOW'=>'Hiện thị',
                'HOME'=>'Show trang chủ',
                'HOT'=>'HOT',
                'MENU'=>'Menu',
                'FEATURE'=>'Nổi bật',
                'HIDE'=>'Ẩn'
            );
            $item_per_page = Url::get('item_per_page',20);
            $total = PartnerAdminDB::get_total_item($cond);
            $paging = paging($total,$item_per_page,10,false,'page_no',['cmd','type','category_id']);
            $items = PartnerAdminDB::get_items($cond,'partner.id DESC',$item_per_page);
            $item_per_page_list = [20=>20,30=>30,50=>50,100=>100];
            $this->parse_layout('list',$this->just_edited_id+array(
                'items'=>$items,
                'paging'=>$paging,
                'total'=>$total,
                'status_list'=>array(Portal::language('select_status')) + $status,
                'item_per_page_list'=>$item_per_page_list
            ));
        } catch (Exception $e) {
			echo 'Lỗi xử lý phân trang'."\n";
        }
	}
	function get_just_edited_id()
	{
		$this->just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids'))
		{
			if(is_string(UrL::get('selected_ids')))
			{
				if (strstr(UrL::get('selected_ids'),','))
				{
					$this->just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}
				else
				{
					$this->just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
	}
	function get_condition()
	{
		$cond = '';
		if(Url::get('author'))
		{
			$cond.= ' 1=1';
		}
		if(Url::get('status'))
		{
			$cond.= ' and partner.status="'.Url::get('status').'"';
		}
		if(Url::get('search'))
		{
			$cond .= URL::get('search')? ' AND ((partner.name) LIKE "%'.addslashes(URL::sget('search')).'%")':'';
		}
		return $cond;
	}
}
?>
