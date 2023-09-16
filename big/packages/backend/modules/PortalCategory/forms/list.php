<?php
class ListPortalCategoryForm extends Form{
	function __construct(){
		Form::Form('ListPortalCategoryForm');
	}
	function on_submit(){
		if(URL::get('confirm')){
			$this->deleted_selected_ids();
		}
	}
	function draw(){
		$portal_id = PORTAL_ID;
		$this->get_just_edited_id();
		$this->get_items($portal_id);
		$category=$this->items;
		$st = '<ul>';
		$st .= $this->covert_to_ul_li($category);
		$st .= '</ul>';
		$status = array(''=>'Thao tác','MENU'=>'Đặt là Menu','SHOW'=>'Không đặt là Menu');
		$this->parse_layout('list',$this->just_edited_id+
			array(
				'category'=>$st,
				'status_list'=>$status,
				'title'=>(Url::get('page') == 'menu')?'Menu hiển thị':'Quản lý danh mục'
			)
		);
	}
	function get_just_edited_id(){
		$this->just_edited_id['just_edited_ids'] = array();
		if (UrL::get('selected_ids')){
			if(is_string(UrL::get('selected_ids'))){
				if (strstr(UrL::get('selected_ids'),',')){
					$this->just_edited_id['just_edited_ids']=explode(',',UrL::get('selected_ids'));
				}else{
					$this->just_edited_id['just_edited_ids']=array('0'=>UrL::get('selected_ids'));
				}
			}
		}
	}
	function deleted_selected_ids(){
		require_once 'detail.php';
		foreach(URL::get('selected_ids') as $id){
			if($id and $category=DB::exists_id('category',$id)){// and User::can_edit(false,$category['structure_id']){
				save_recycle_bin('category',$category);
				DB::delete_id('category',$id);
				@unlink($category['icon_url']);
				save_log($id);
			}
			if($this->is_error()){
				return;
			}
		}
		Url::redirect_current(Module::$current->redirect_parameters);
	}
	function get_items($portal_id){
		$this->get_select_condition($portal_id);
		if(Url::get('page') == 'menu'){
			$extra_cond = ' and category.status="MENU"';
		}else{
			if(Url::get('type') == 'ALL'){
				$extra_cond = ' and (category.type!="")';
			}else{
				$extra_cond = ' and (category.type="'.Url::get('type','NEWS').'")';
			}
		}
		$this->items = DB::fetch_all('
			select
				`category`.id
				,`category`.name_id_'.Portal::language().' as name_id
				,`category`.structure_id
				,`category`.`status`
				,`category`.`icon_url`
				,`category`.name_'.Portal::language().' as name
				,`category`.description_'.Portal::language().' as description
				,`category`.`type`
			from
			 	`category`
			where
				 '.$this->cond.$extra_cond.'
				 AND '.IDStructure::direct_child_cond(ID_ROOT).'
			order by
				`category`.structure_id
		');
		$i=0;
	}
	function get_select_condition($portal_id){
		$this->cond = '
			1=1'
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and `category`.id in ("'.join(URL::get('selected_ids'),'","').'")':'')
		;
	}
	function covert_to_ul_li($category){
		$lang = 1;
		$st = '';
		require_once('packages/core/includes/utils/category.php');
		category_indent($category);
		foreach($category as $key=>$value){
			$value['name'] = '<strong>'.$value['name'].'</strong> - <em>'.$value['status'].'</em> '.(User::is_admin()?' - <em>'.$value['structure_id'].'</em>':'').'';
			$level = IDStructure::level($value['structure_id']);
			$url = Url::build_current(array('cmd'=>'edit','id'=>$key));
			if($level==1) {
				$st .= '
				<li>
					'.$value['move_up'].' '.$value['move_down'].' <input  name="selected_ids[]" type="checkbox" id="Category_checkbox" value="'.$key.'" onclick="select_checkbox(document.ListCategoryForm,\'Category\',this.checked,\'#FFFFEC\',\'#FFF\');" '.((URL::get('cmd')=='delete')?' checked':'').'> <span  class="node"><i class="icon-folder-open"></i></span>';
					$st.=' <a href="'.$url.'">'.$value['name'].'</a>';
					if ($childs = DB::fetch_all('select id,type,status,structure_id,name_' . $lang . ' as name,`category`.name_id_'.$lang.' as name_id,url from category where category.type="'.$value['type'].'" AND ' . IDStructure::direct_child_cond($value['structure_id']) . ' order by structure_id')) {
						$st .= '<ul>';
						$st .= $this->covert_to_ul_li($childs);
						$st .= '</ul>';
					}
					$st.='
				</li>';
			}elseif($level==2) {
				$st.='
				<li>
					'.$value['move_up'].' '.$value['move_down'].' <input  name="selected_ids[]" type="checkbox" value="'.$key.'" id="Category_checkbox" onclick="select_checkbox(document.ListCategoryForm,\'Category\',this.checked,\'#FFFFEC\',\'#FFF\');" '.((URL::get('cmd')=='delete')?' checked':'').'> <span class="node"> <i class="icon-minus-sign"></i></span>
					<a href="'.$url.'">'.$value['name'].'</a>';
					if ($childs = DB::fetch_all('select id,type,status,structure_id,name_' . $lang . ' as name,`category`.name_id_'.$lang.' as name_id,url from category where category.type="'.$value['type'].'" AND  ' . IDStructure::direct_child_cond($value['structure_id']) . ' order by structure_id')) {
						$st .= '<ul>';
						$st .= $this->covert_to_ul_li($childs);
						$st .= '</ul>';
					}
					$st.=' 
				</li>';
			}elseif($level>=3){
				$st.='<li>'.$value['move_up'].' '.$value['move_down'].' <input  name="selected_ids[]" type="checkbox" value="'.$key.'" id="Category_checkbox" onclick="select_checkbox(document.ListCategoryForm,\'Category\',this.checked,\'#FFFFEC\',\'#FFF\');" '.((URL::get('cmd')=='delete')?' checked':'').'> <span class="node"> <i class="icon-leaf"></i></span><a href="'.$url.'">'.$value['name'].'</a></li>';
			}
			//=============================================================
		}
		return $st;
	}
}
?>