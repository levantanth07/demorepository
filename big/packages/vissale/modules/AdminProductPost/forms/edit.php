<?php
class EditAdminProductPostForm extends Form{
	function __construct(){
		Form::Form('EditAdminProductPostForm');
		$this->add('fb_posts.post_id',new TextType(true,'Chưa nhập post id',0,255));
		$this->link_css('assets/default/css/cms.css');
	}
	function on_submit(){
		if($this->check() and URL::get('confirm_edit') and !Url::get('search')){
			require_once 'packages/core/includes/utils/vn_code.php';
			if(isset($_REQUEST['mi_product'])){
				foreach($_REQUEST['mi_product'] as $key=>$record){
					if($record['id']=='(auto)'){
						$record['id']=false;
					}
					$record['group_id'] = Session::get('group_id');
					if($record['fb_page_id']) {
						$page_id = DB::fetch('select page_id from fb_pages where id='.$record['fb_page_id'],'page_id');
						$record['post_id'] = $page_id. '_' . $record['post_id'];
						$record['page_id'] = $page_id;
					}
					if($record['product_id']){
						$record['bundle_id'] = DB::fetch('select bundle_id from products where id='.$record['product_id'],'bundle_id');
					}
					if($record['id'] and DB::exists_id('fb_posts',$record['id'])){
						$record['modified'] = date('Y-m-d H:i:s');
						DB::update('fb_posts',$record,'id='.$record['id']);
					}else{
						unset($record['id']);
						$record['created'] = date('Y-m-d H:i:s');
						$record['id'] = DB::insert('fb_posts',$record);
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
					DB::delete('fb_posts','id='.$id.' and group_id='.Session::get('group_id'));
				}
			}
			//update_mi_upload_file();
			//exit();
			Url::js_redirect(true);
		}else{
			Url::redirect_current(array('keyword'));
		}
	}	
	function draw(){
		$this->map = array();
		$paging = '';
		$cond = '
			fb_posts.group_id='.Session::get('group_id').'
				'.(Url::get('keyword')?' AND (fb_posts.page_id  LIKE  "%'.Url::get('keyword').'%" OR fb_posts.post_id  LIKE  "%'.Url::get('keyword').'%")':'').'
			';		
		//if(!isset($_REQUEST['mi_product']))
		{
			$item_per_page = 200;
			DB::query('
				select 
					count(distinct fb_posts.id) as acount
				from 
					fb_posts
					INNER JOIN fb_pages ON fb_pages.page_id AND fb_pages.status = 0
				where 
					'.$cond.'
			');
			$count = DB::fetch();
			$this->map['total'] = $count['acount'];			
			require_once 'packages/core/includes/utils/paging.php';
			$paging = paging($count['acount'],$item_per_page);
			$sql = '
				select 
					fb_posts.*
				from 
					fb_posts
					INNER JOIN fb_pages ON fb_pages.page_id AND fb_pages.status = 0
				WHERE
					'.$cond.'
				GROUP BY
					fb_posts.id
				order by 
					fb_posts.id desc
				LIMIT
					'.((page_no()-1)*$item_per_page).','.$item_per_page.'
			';
			$mi_product = DB::fetch_all($sql);
			foreach($mi_product as $key=>$val){
				if($pos=strpos($val['post_id'],'_')){
					$mi_product[$key]['post_id'] = substr($val['post_id'],$pos+1,strlen($val['post_id']));
				}
			}
			$_REQUEST['mi_product'] = $mi_product;
		}
		$this->map['paging'] = $paging;
		$this->map['total'] = $count['acount'];
		$type_options = '<option value="">Chọn loại</option>';
		$paging = paging($count['acount'],$item_per_page);
		//////////
		$sql = '
			select 
				products.id,products.name
			from 
				products
			WHERE
				products.group_id='.Session::get('group_id').'
			GROUP BY
				products.id
			order by 
				products.name
		';
		$products = DB::fetch_all($sql);
		$product_id_options = '<option value="">Chọn sp</option>';
		foreach($products as $key=>$val){
			$product_id_options .= '<option value="'.$key.'">'.$val['name'].'</option>';
		}
		$this->map['product_id_options'] = $product_id_options;
		///////////////////////////
		$sql = '
			select
				fb_pages.id,fb_pages.page_name AS name
			from
				fb_pages
			WHERE
				fb_pages.group_id='.Session::get('group_id').'
				AND fb_pages.status = 0
			order by
				fb_pages.status,fb_pages.page_name
		';
		$pages = DB::fetch_all($sql);
		$fb_page_id_options = '<option value="">Chọn Friendpage</option>';
		foreach($pages as $key=>$val){
			$fb_page_id_options .= '<option value="'.$key.'">'.$val['name'].'</option>';
		}
		$this->map['fb_page_id_options'] = $fb_page_id_options;
		////////////////////////////
		$this->map['status_options'] = '<option value="">Trạng thái</option><option value="SHOW">Kích hoạt</option><option value="HIDE">Ẩn</option>';
		$this->parse_layout('edit',$this->map);
	}
}
?>