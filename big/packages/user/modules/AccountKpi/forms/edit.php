<?php
class EditAccountKpiForm extends Form{
	function __construct(){
		Form::Form('EditAccountKpiForm');
		$this->add('revenue_target.total',new TextType(true,'Bạn vui lòng nhập chỉ tiêu',0,50));
		$this->link_js('packages/core/includes/js/multi_items.js');
	}
	function on_submit(){
		if($this->check() and Url::post('save') and !Url::get('keyword')){
			if(isset($_REQUEST['mi_bundle'])){
				foreach($_REQUEST['mi_bundle'] as $key=>$record){
				    if(isset($record) and !$record['total']){
				        $this->error('revenue_target.total','Bạn vui lòng nhập chỉ tiêu doanh thu');
				        return;
                    }
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					$record['total'] = System::calculate_number($record['total']);
					if($record['id'] and DB::exists_id('revenue_target',$record['id'])){
						DB::update('revenue_target',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
                        $record['group_id'] = AccountKpi::$group_id;
                        $record['user_id'] = AccountKpi::$user_id;
                        $record['account_id'] = AccountKpi::$account_id;
                        $record['year'] = date('Y');
                        $record['created_time'] = time();
                        $record['date'] = date('Y-m-d');
                        $record['current_total'] = '0';
                        if(DB::exists('select id from revenue_target where user_id='.$record['user_id'].' and year='.$record['year'].' and month='.$record['month'])){
                            $this->error('revenue_target.total','Chỉ tiêu tháng '.$record['month'].'/'.$record['year'].' đã khai báo rồi.');
                            return;
                        }else{
                            $record['id'] = DB::insert('revenue_target',$record);
                        }
					}
					/////
				}
				if (isset($ids) and sizeof($ids)){
					$_REQUEST['selected_ids'].=','.join(',',$ids);
				}
			}
			if($deleted_ids = URL::get('deleted_ids')){
				$ids = explode(',',$deleted_ids);
				foreach($ids as $id){
					DB::delete_id('revenue_target',$id);
				}
			}
die;
			Url::js_redirect(true);
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		$cond = '
			revenue_target.group_id = '.Session::get('group_id').'
				'.(Url::get('keyword')?' AND revenue_target.name LIKE "%'.Url::get('keyword').'%"':'').'
			';		
		//if(!isset($_REQUEST['mi_bundle']))
		{
			$item_per_page = 200;
			DB::query('
				select 
					count(distinct revenue_target.id) as acount
				from 
					revenue_target
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];			
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			$sql = '
				select 
					revenue_target.*
				from 
					revenue_target
				WHERE
					'.$cond.'
				GROUP BY
					revenue_target.id
				order by 
					revenue_target.id  DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$mi_bundle = DB::fetch_all($sql);
			foreach($mi_bundle as $key=>$value){
			    $mi_bundle[$key]['total'] = System::display_number($value['total']);
            }
			$_REQUEST['mi_bundle'] = $mi_bundle;
		}
		$this->map['paging'] = $paging;
		$this->parse_layout('edit',$this->map);
	}
}
?>