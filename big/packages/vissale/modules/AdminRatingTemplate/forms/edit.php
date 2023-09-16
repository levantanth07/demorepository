<?php
class EditAdminRatingTemplateForm extends Form{
	function __construct(){
		Form::Form('EditAdminRatingTemplateForm');
		$this->add('rating_template.content',new TextType(true,'Bạn vui lòng nhập nội dung câu phản hồi (Tối đa 225 ký tự)',0,225));
		$this->link_js('packages/core/includes/js/multi_items.js');
	}
	function on_submit(){
		if($this->check() and Url::post('save') and !Url::get('keyword')){
			if(isset($_REQUEST['mi_rating_template'])){
				foreach($_REQUEST['mi_rating_template'] as $key=>$record){
				    if(isset($record) and !$record['content']){
				        $this->error('rating_template.content','Bạn vui lòng nhập tên');
				        return;
                    }
					$record['group_id'] = Session::get('group_id');
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					if($record['id'] and DB::exists_id('rating_template',$record['id'])){
						DB::update('rating_template',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
						$record['id'] = DB::insert('rating_template',$record);
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
					DB::delete_id('rating_template',$id);
				}
			}

			Url::js_redirect(true);
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		$cond = '
			rating_template.group_id = '.Session::get('group_id').'
				'.(Url::get('keyword')?' AND rating_template.content LIKE "%'.Url::get('keyword').'%"':'').'
			';
        $item_per_page = 1000;
        DB::query('
				select 
					rating_template.id as total
				from 
					rating_template
				where 
					'.$cond.'
			');
        $count = DB::fetch();
        $this->map['total'] = $count['total'];
        require_once 'packages/core/includes/utils/paging.php';
        $paging = paging($count['total'],$item_per_page);
        $sql = '
				select 
					rating_template.*
				from 
					rating_template
				WHERE
					'.$cond.'
				order by 
					rating_template.point,rating_template.id  DESC
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
        $mi_rating_template = DB::fetch_all($sql);
        if(!isset($_REQUEST['mi_rating_template'])){
            $_REQUEST['mi_rating_template'] = $mi_rating_template;
        }
		$this->map['paging'] = $paging;
		$this->parse_layout('edit',$this->map);
	}
}
?>