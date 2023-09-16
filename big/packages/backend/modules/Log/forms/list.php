<?php
class ListLogForm extends Form
{
    private  $arrURI;
    private  $isExportExcel;
    private  $isSendMailToCarrier;
    private  $isSendMailToWarehouse;
    private  $isUpdateShopSetting;
    private  $isRemoveOrders;
    private  $isPrint;
	function __construct()
	{
		Form::Form('ListLogForm');
		//$this->link_css('assets/default/css/cms.css');
        $this->arrURI = ['keyword', 'group_name', 'group_id'];
        $this->isExportExcel = url::get('type') == 'EXPORT_EXCEL';
        $this->isSendMailToCarrier = url::get('type') == 'SEND_EMAIL_TO_CARRIER';
        $this->isSendMailToWarehouse = url::get('type') == 'SEND_EMAIL_TO_WAREHOUSE';
        $this->isUpdateShopSetting = url::get('type') == 'UPDATE_SHOP_SETTING';
        $this->isRemoveOrders = url::get('type') == 'DELETE';
        $this->isPrint = url::get('type') == 'PRINT';
        if ($this->isExportExcel) {
            $this->arrURI = ['keyword', 'from_date', 'to_date', 'user_export', 'order_id', 'export_type', 'group_name', 'group_id'];
        } else if ($this->isSendMailToCarrier) {
            $this->arrURI = ['keyword', 'from_date', 'to_date', 'to_carrier', 'user_export', 'order_id', 'export_type', 'group_name', 'group_id'];
        } else if ($this->isUpdateShopSetting) {
            $this->arrURI = ['keyword', 'from_date', 'to_date', 'group_name', 'group_id'];
        } else if ($this->isPrint) {
            $this->arrURI = ['keyword', 'from_date', 'to_date', 'user_export', 'order_id', 'export_type', 'group_name', 'group_id'];
        } else if ($this->isSendMailToWarehouse) {
            $this->arrURI = ['keyword', 'from_date', 'to_date', 'to_warehouse', 'user_export', 'order_id', 'export_type', 'group_name', 'group_id', 'log_type_extra'];
        } else if ($this->isRemoveOrders) {
            $this->arrURI = ['keyword', 'from_date', 'to_date', 'group_name'];
        }
	}
	function on_submit()
	{
		/*if(Url::get('cmd') == 'delete' and Url::get('selected_ids') and count(Url::get('selected_ids'))>0)
		{
			foreach(Url::get('selected_ids') as $key=>$value)
			{
				DB::delete_id('log',$value);
			}
		}*/
		Url::redirect_current($this->arrURI,false,'',url::get('type'));
	}
	function draw()
	{
        $this->map['warehouse'] = '';
        $this->map['from_date'] = '';
        $this->map['to_date'] = '';
        $group_id = Url::get('group_id')? DB::escape(Url::get('group_id')):Session::get('group_id');
        $cond = ' 1=1 ';
        if($type = DB::escape(Url::get('type'))){
            $cond .= ' AND log.type= "'. $type .'"';
        }
	    if(!User::is_admin()){
            $cond .= ' AND groups.id='.$group_id;
        }else{
            $cond .= Url::get('group_id')?' AND groups.id='.DB::escape(Url::get('group_id')):'';
        }
        if($keyword = DB::escape(Url::get('keyword'))){
            $cond .= ' AND (log.title LIKE "%'.$keyword.'%" OR log.description LIKE "%'.$keyword.'%" OR log.ip LIKE "%'.$keyword.'%")';
        }

        if($warehouse = Url::get('to_warehouse')){
            if ($warehouse !='Chọn kho') {
                $cond .= ' AND log.carrier= "'. DB::escape($warehouse) .'"';
            }
        }
        $date = new DateTime(date("Y-m-d"));
        $tomorrowDATE = '';
        if(User::is_admin()){
            if($this->isExportExcel){
                $date->modify('-9 day');
                $tomorrowDATE = $date->format('Y-m-d');
            } elseif($this->isRemoveOrders || $this->isPrint) {
                $date->modify('-29 day');
                $tomorrowDATE = $date->format('Y-m-d');
            }
        }
        
        $fromDateFormat = '';
        $toDateFormat = '';
        if($fromDate = Url::get('from_date') and $toDate = Url::get('to_date')){
            $fromDateFormat = str_replace('/','-',$fromDate);
            $toDateFormat = str_replace('/','-',$toDate);
            $fromDateFormat = strtotime($fromDateFormat . ' 00:00:00');
            $toDateFormat = strtotime($toDateFormat . ' 23:59:59');
            $cond .= ' AND log.time BETWEEN '. $fromDateFormat .' AND '. $toDateFormat. ' ';
            if(User::is_admin()){
                if($this->isExportExcel && ($toDateFormat - $fromDateFormat > 10*24*3600 )){
                    die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 10 ngày!</div>');
                } elseif($toDateFormat - $fromDateFormat > 31*24*3600 && ($this->isRemoveOrders || $this->isPrint)) {
                    die('<div class="alert alert-warning">Bạn vui lòng chọn tối đa 1 tháng!</div>');
                }
                
            }
        } elseif (User::is_admin() && ($this->isExportExcel || $this->isRemoveOrders || $this->isPrint)){
            $toDate = date('Y-m-d').' 23:59:59';
            $fromDate = $tomorrowDATE.' 00:00:00';
            $_REQUEST['from_date'] = date('d/m/Y',strtotime($tomorrowDATE));
            $_REQUEST['to_date'] = date('d/m/Y');
            $fromDateFormat = strtotime($fromDate);
            $toDateFormat = strtotime($toDate);
            $cond .= ' AND log.time BETWEEN '. $fromDateFormat .' AND '. $toDateFormat. ' ';
        }
        if($userExport = trim(Url::get('user_export'))){
            $cond .= ' AND user_id = "'.DB::escape($userExport).'"';
        }
        if($orderID = trim(Url::get('order_id'))){
            $cond .= ' AND FIND_IN_SET('.DB::escape($orderID).', list_export_order_id) > 0';
        }
        if(isset($_GET['export_type'])){
            if($_GET['export_type'] != ''){
                $cond .= ' AND (censored_phone_number = '.DB::escape($_GET['export_type']).' OR censored_phone_number IS NULL)';
            }
        }

        if(isset($_GET['log_type_extra'])){
            $log_type_extra = intval($_GET['log_type_extra']);
            $cond .= ' AND (log_type_extra = '.$log_type_extra.')';
        }

        if($group_name = DB::escape(Url::get('group_name'))){
            $cond .= ' AND (groups.name LIKE "%'.$group_name.'%")';
        }
        
        $sql = 'SELECT log.id,log.portal_id,log.parameter,log.user_id,log.time,log.type,log.description,
                    log.module_id,log.title,log.note,log.group_id,log.ip,log.censored_phone_number,
                    log.carrier,log.carrier_email,log.log_type_extra,groups.name as group_name,
                    IF(CHAR_LENGTH(log.list_export_order_id) < 1000, log.list_export_order_id, "...") as list_export_order_id 
                FROM log LEFT JOIN groups ON groups.id=log.group_id 
                WHERE ' .$cond. ' ORDER BY log.id DESC';
        $allItems = DB::fetch_all($sql);
        if ($orderID = trim(Url::get('order_id'))) {
            $arrOrderId = explode(',', $orderID);
            $arrOrderId = array_map('trim', $arrOrderId);
            foreach ($allItems as $key => $row) {
                $arrExportOrderId = explode(',',$row['list_export_order_id']);
                $arrExportOrderId = array_map('trim', $arrExportOrderId);
                if (count($arrExportOrderId) > 0) {
                    $arrIntersect = array_intersect($arrExportOrderId,$arrOrderId);
                    if(count($arrIntersect) == 0){
                        unset($allItems[$key]);
                    }
                }
            }
        }
        $total = count($allItems);
        $arrItemLimit = array();
        $item_per_page = 20;
        if(count($allItems) > 0){
            if(count($allItems) < $item_per_page){
                $arrItemLimit = $allItems;
            } else {
                $pageNo = Url::get('page_no');
                if(empty($pageNo)){
                    $pageNo = 1;
                }
                $solan = 0;
                foreach($allItems as $key => $row){
                    if(($solan >= ($pageNo - 1) * $item_per_page) && ($solan < $pageNo * $item_per_page)){
                        $arrItemLimit[$key] = $row;
                    }
                    $solan++;
                    if($solan == ($pageNo * $item_per_page)){
                        break;
                    }
                }
            }
        }
		require_once 'packages/core/includes/utils/paging.php';
		$paging = paging($total,$item_per_page,10,false,'page_no',$this->arrURI);
		$items = $arrItemLimit;
        $textUser = '';
        //tieu de log
        $logTitle = '';
        $privilegeCode = 'XUAT_EXCEL';
        if($this->isExportExcel){
            $textUser = 'Nhân viên xuất excel';
            $logTitle = 'Lịch sử xuất excel';
        } else if($this->isSendMailToCarrier || $this->isSendMailToWarehouse){
            $textUser = 'Nhân viên gửi email';
            $arrCarrier = array('' => 'Nhà vận chuyển');
            $unsetKey = '';
            $keyWarehouse = '';
            $emailKey = '';
            $arrWarehouse[] = [
                'id' => '',
                'name' => 'Chọn kho'
            ];
            $warehouse = DB::select_all('qlbh_warehouse','structure_id='.ID_ROOT.' OR group_id = '.$group_id.'','structure_id');
            foreach ($warehouse as $key => $value) {
                $arrWarehouse[$key]['id'] = $value['name'];
                $arrWarehouse[$key]['name'] = $value['name'];
                if ($value['kho_tong_shop'] && $value['kho_tong_shop'] == 1) {
                    $unsetKey = $key;
                }
            }
            if ($unsetKey) {
                unset($arrWarehouse[$unsetKey]);
            }
            $data_request = array('shop_id' => $group_id);
            $dataRes = EleFunc::cUrlPost(HOST_API . '/api/transport/shop-setting/get', $data_request);
            $arrCarrierData = $dataRes['data']['brand'];
            $arrCarrierName = array();
            foreach($arrCarrierData as $keyCarrierData => $rowCarrierData){
                $arrCarrier[$rowCarrierData['alias']] = $rowCarrierData['name'];
            }
            foreach($items as $keyItems => $rowItems){
                if(isset($arrCarrier[$rowItems['carrier']])) {
                    $items[$keyItems]['carrier'] = $arrCarrier[$rowItems['carrier']];
                }
                $items[$keyItems]['list_export_order_id'] = str_replace(',',' , ',$rowItems['list_export_order_id']);
            }
            if ($this->isSendMailToCarrier) {
                $this->map['to_carrier_list'] = $arrCarrier;
                 $logTitle = 'Lịch sử gửi mail NVC';
            } else if ($this->isSendMailToWarehouse){
                $this->map['warehouse'] = $arrWarehouse;
                $logTitle = 'Lịch sử gửi mail Kho';
                $privilegeCode = "IN_DON";
            }
        } else if($this->isUpdateShopSetting) {
            $logTitle = 'Lịch sử cập nhật tùy chỉnh shop';
        } else if($this->isRemoveOrders) {
            $logTitle = 'Lịch sử xóa đơn hàng';
        }else if($this->isPrint){
            $logTitle = 'Lịch sử in đơn hàng';
            $textUser = 'Nhân viên in';
            $privilegeCode = 'IN_DON';
        }
        $this->map['logTitle'] = $logTitle;
        if($this->isExportExcel || $this->isSendMailToCarrier || $this->isPrint) {
            $this->map['user_export_list'] = array('' => $textUser) + LogDB::get_list_user($privilegeCode);
        }
        if ($this->isSendMailToWarehouse) {
            $this->map['user_export_list'] = array('' => $textUser) + LogDB::get_list_user($privilegeCode);
        }
		$this->parse_layout('list',$this->map + array(
			'paging'=>$paging
			,'items'=>$items
			,'total'=>$total
		));
	}
}
?>
