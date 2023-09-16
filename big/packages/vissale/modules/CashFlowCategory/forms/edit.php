<?php
class EditCashFlowCategoryForm extends Form{
    protected $map;
	function __construct(){
        $this->map = array();
		Form::Form('EditCashFlowCategoryForm');
		$this->add('cash_flow_category.name',new TextType(true,'Chưa nhập tên',0,255));
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit') and !Url::get('search')){
			require_once 'packages/core/includes/utils/vn_code.php';
			if(isset($_REQUEST['mi_category'])){
				foreach($_REQUEST['mi_category'] as $key=>$record){
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					if($record['id'] and DB::exists_id('cash_flow_category',$record['id'])){
						DB::update('cash_flow_category',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
                        $record['group_id'] = CashFlowCategory::$group_id;
                        $record['created_at'] = date('Y-m-d H:i:s');
						$record['id'] = DB::insert('cash_flow_category',$record);
					}
					/////
				}
				if (isset($ids) and sizeof($ids)){
					$_REQUEST['selected_ids'].=','.join(',',$ids);
				}
			}
			if(URL::get('deleted_ids')){
				$ids = explode(',',URL::get('deleted_ids'));
				foreach($ids as $id){
					DB::delete('cash_flow_category','id='.$id.' and group_id='.CashFlowCategory::$group_id);
				}
			}
			//update_mi_upload_file();

			Url::js_redirect(true,'Dữ liệu cập nhật thành công!');
		}
	}	
	function draw(){
		$cond = '
			(cash_flow_category.group_id = '.CashFlowCategory::$group_id.' or cash_flow_category.group_id=0)
				'.(Url::get('keyword')?' AND cash_flow_category.name LIKE "%'.Url::get('keyword').'%"':'').'
			';
        $item_per_page = 200;
        $total = CashFlowCategoryDB::get_total_item($cond);
        $this->map['total'] = $total;
        require_once 'packages/core/includes/utils/paging.php';
        $paging = paging($total,$item_per_page);
        $mi_category = CashFlowCategoryDB::get_items($cond,$item_per_page);
        $_REQUEST['mi_category'] = $mi_category;
		$this->map['paging'] = $paging;

		$this->parse_layout('edit',$this->map);
	}
}
?>