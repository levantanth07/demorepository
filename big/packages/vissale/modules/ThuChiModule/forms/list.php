<?php
class ListThuChiModuleForm extends Form
{
    protected $map;
    protected $team_ids;
    protected $today;

	function __construct()
	{
		Form::Form('ListThuChiModuleForm');

        $this->init();
	}

	function delete()
	{
		if(isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0)
		{
			foreach($_REQUEST['selected_ids'] as $key)
			{
				if($item = DB::exists_id('cash_flow', $key))
				{
                    ThuChiModuleDB::softDelete($key);
				}
			}
		}

		Url::redirect_current(['type']);
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
        $this->map = array();
        //
        $deletable = false;
        $editable = false;

        $accounting_privilege = check_user_privilege('ADMIN_KETOAN');
	    if ( check_user_privilege('ADMIN_KETOAN') && Session::get('account_type')==3 && Url::get('view_type')!='recycle_bin') {
            $deletable = true;
        }
        if ( check_user_privilege('ADMIN_KETOAN') && (Session::get('account_type')==3 || Session::get('admin_group')) && Url::get('view_type')!='recycle_bin') {
            $editable = true;
        }
        $this->map['team_id_list'] = $this->team_ids;
		$this->map['accounting_privilege'] = $accounting_privilege;
		$this->map['deletable'] = $deletable;
		$this->map['editable'] = $editable;
		$this->map['title'] = 'Quản lý thu chi';
        if (Url::get('view_type')=='recycle_bin') {
            $this->map['title'] .= ' -  Thùng rác';
        }
        $group_id = ThuChiModule::$group_id;
        $master_group_id = Session::get('master_group_id');
        if(Session::get('account_type')==3 and check_user_privilege('ADMIN_KETOAN')){//khoand edited in 14/11/2018
            $cond = ' (cash_flow.group_id='.$group_id.' or groups.master_group_id = '.$group_id.')';
            if($group_id_ = Url::get('group_id')){
                $cond = '(cash_flow.group_id = '.$group_id_.')';
            }
        } else {
            $cond = 'cash_flow.group_id = '.ThuChiModule::$group_id;
        }
        
		$this->map['turn_card_code'] = '';
        if($type = Url::get('type')=='pay'){
            $cond .= ' and cash_flow.bill_type = 0';
            $this->map['title'] = 'Quản lý phiếu chi';
        }elseif($type = Url::get('type')=='receive'){
            $cond .= ' and cash_flow.bill_type = 1';
            $this->map['title'] = 'Quản lý phiếu thu';
        }

		if($cid = Url::get('cid')){
          $card = DB::select('spa_turn_card','md5(concat(id,"'.CATBE.'"))="'.$cid.'"');
          $code = format_code($card['code'],$card['group_id']);
          $this->map['title'] = 'Quản lý phiếu thu';
          $this->map['turn_card_code'] = ' thẻ lần <a href="'.Url::build('turn_card',array('keyword'=>$card['id'])).'">'.$code.'</a>';
          $cond .= ' and md5(concat(cash_flow.turn_card_id,"'.CATBE.'")) = "'.$cid.'"';
        }

        if($oid = Url::get('oid')){
            $order = DB::select('spa_process','md5(concat(id,"'.CATBE.'"))="'.$oid.'"');
            $code = format_code($order['code'],$order['group_id']);
            $this->map['title'] = 'Quản lý phiếu thu';
            $this->map['turn_card_code'] = ' đơn lẻ <a href="'.Url::build('admin_process',array('keyword'=>$order['id'],'load_ajax'=>1)).'">'.$code.'</a>';
            $cond .= ' and md5(concat(cash_flow.order_id,"'.CATBE.'")) = "'.$oid.'"';
        }

		require_once 'packages/core/includes/utils/paging.php';
		$item_per_page = 100;
		if(!Url::get('item_per_page')){
			$_REQUEST['item_per_page'] = $item_per_page;
		}else{
			$item_per_page = Url::get('item_per_page');
		}
		$total = ThuChiModuleDB::get_total_item($cond);
        $this->map['total_cash_amount'] = ThuChiModuleDB::get_total_amount($cond,CASH);
        $this->map['total_card_amount'] = ThuChiModuleDB::get_total_amount($cond,CARD);
        $this->map['total_bank_amount'] = ThuChiModuleDB::get_total_amount($cond,BANK);
        $this->map['total_amount'] = $this->map['total_cash_amount'] + $this->map['total_card_amount'] + $this->map['total_bank_amount'];
		$items = ThuChiModuleDB::get_items($cond,'id',$item_per_page);
        $i=1;
		foreach ($items as $key => &$value) {
		    $items[$key]['index'] = (page_no() - 1)*$item_per_page + $i;
		    $i++;
            $value['amount'] = System::display_number($value['amount']);
            $value['bill_number'] = ThuChiModule::generatePrefixType($value['bill_type']) .'_'.ThuChiModule::generateCode($value['bill_number']);
        }
		//paging
		$paging = paging($total,$item_per_page,10,false,'page_no',
			array('cmd','item_per_page','search_bill_text' ,'from_bill_date', 'to_bill_date','cid','type', 'group_id', 'real_revenue', 'team_id','view_type')
		);
		$statistics = ThuChiModuleDB::get_statistics($cond);
		$leftAmount = $statistics['receive'] - $statistics['pay'];

		$this->map += array(
			'items'=>$items,
			'paging'=>$paging,
			'total'=>$total,
			'pay' => System::display_number($statistics['pay']),
			'receive' => System::display_number($statistics['receive']),
			'left_amount' => System::display_number($leftAmount)
		);
        $this->map['group_id_list'] = array(''=>'Chọn chi nhánh') + MiString::get_list(ThuChiModuleDB::get_groups($group_id));
		$this->map['real_revenue_list'] = [''=>'Tất cả thu/Chi', '1'=>'Doanh thu'];


        if (Url::get('view_type')=='recycle_bin') {
            $this->parse_layout('recycle_bin',$this->map);
        } else {
            $this->parse_layout('list',$this->map);
        }

	}

    protected function init(){
        $this->today = date("Y-m-d H:i:s");
        $this->team_ids  = ThuChiModuleDB::$teams;

        if ( empty(Url::get('team_id')) && Url::get('type')==='receive') {
            $_REQUEST['team_id']=1;
        }
        if ( empty(Url::get('team_id')) && Url::get('type')==='pay') {
            $_REQUEST['team_id']=-1;
        }
    }
}

