<?php
class CrmCustomerForm extends Form {
	function __construct() {
		Form::Form("");
		$this->add('id', new IDType(true, 'object_not_exists', 'crm_customer'));
        //$this->link_js('assets/vissale/js/lodash.min.js');
	}
	function on_submit() {
		if ($this->check() and URL::get('confirm')) {
			CrmCustomerDB::delete(URL::iget('id'));
			Url::redirect_current(array('org'));
		}
	}
	function draw() {
		$group_id = Session::get('group_id');
		$id = Url::iget('cid');
		if ($id and $row = CrmCustomerDB::get_customer($id,$group_id)) {
            require_once 'packages/core/includes/utils/paging.php';
            require_once('packages/vissale/modules/CrmCustomerSchedule/db.php');
            require_once('packages/vissale/modules/CrmCustomerNote/db.php');
            require_once('packages/vissale/modules/CrmCustomerCallHistory/db.php');
            require_once('packages/vissale/modules/CrmCustomerPathology/db.php');
            require_once('packages/vissale/modules/AdminOrders/db.php');

			$row['contact_name'] = $row['contact_id'] ? DB::fetch('select id,name from crm_customer where id=' . $row['contact_id'], 'name') : '';
			$row['orders'] = CrmCustomerDB::get_orders($row['id']);

	        require_once 'packages/core/includes/utils/paging.php';
			$row['total_amount'] = System::display_number(CrmCustomerDB::get_total_amount($row['id']));
			$row['cards'] = CrmCustomerDB::get_card($row['id']);

			//CSKH
			$row['notes'] = CrmCustomerNoteDB::get_items(false,false, 200);
			$row['calls'] = CrmCustomerCallHistoryDB::get_items(false, false, 200);
            $row['schedule_status'] = CrmCustomerScheduleDB::$status;
            $row['can_edit_schedule'] = CrmCustomerScheduleDB::can_edit();
            $searchQuery = CrmCustomerScheduleDB::conditions();
            $row['schedules'] = CrmCustomerScheduleDB::get_items($searchQuery,false, 200);
            $row['pathology'] = CrmCustomerPathologyDB::get_items(false,false, 200);
            $customer_age = Date_Time::get_age( substr($row['birth_date'],-4,4) );
            $row['customer_age'] = !empty($customer_age) ? "<br>$customer_age tuổi" : '';
			$this->parse_layout('detail', $row);
            //
            $this->parse_layout('script',$row);
		} else {
            Url::js_redirect('customer', 'Khách hàng không tồn tại.');
        }
	}
}