<?php

require_once ROOT_PATH . 'packages/core/includes/common/SystemsTree.php';

class ListGroupsSystemForm extends Form{
	function __construct(){
		Form::Form('ListGroupsSystemForm');
	}
	function on_submit(){
		if(URL::get('confirm')){
			$this->deleted_selected_ids();
		}
	}
	function draw(){
		if(!can_tuha_administrator()){
			Url::access_denied();
		}
		SystemsTree::setPreBuildItemCallback(function(array &$system) {
			$system['admin_users'] = implode(', ',MiString::get_list(get_admin_group_system($system['id'])));
			$system['checked'] = URL::get('cmd') == 'delete' ? ' checked' : '';
		});

		$portal_id = PORTAL_ID;
		$this->get_just_edited_id();
		$status = array(''=>'Thao tác','MENU'=>'Đặt là Menu','SHOW'=>'Không đặt là Menu');
		$this->parse_layout('list',$this->just_edited_id+
			array(
				'category'=> SystemsTree::buildHtml(),
				'status_list'=>$status,
				'title'=>(Url::get('page') == 'menu')?'Menu hiển thị':'HỆ THỐNG CỘNG ĐỒNG'
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

	function get_items($portal_id){
		$this->get_select_condition($portal_id);
		$this->items = DB::fetch_all('
			select
				groups_system.id
				,groups_system.name_id
				,groups_system.structure_id
				,groups_system.`status`
				,groups_system.`icon_url`
				,groups_system.name
				,groups_system.description
			from
			 	groups_system
			where
				 '.$this->cond.'
				 AND '.((Url::get('cmd')=='delete')?IDStructure::child_cond(ID_ROOT):IDStructure::direct_child_cond(ID_ROOT)).'
			order by
				groups_system.structure_id
		');
		$i=0;
	}
	function get_select_condition($portal_id){
		$this->cond = '
			1=1'
			.((URL::get('cmd')=='delete' and is_array(URL::get('selected_ids')))?' and groups_system.id in ("'.join(URL::get('selected_ids'),'","').'")':'')
		;
	}
	function covert_to_ul_li($category){
		$lang = 1;
		$st = '';
		require_once('packages/core/includes/utils/category.php');
		category_indent($category);
		foreach($category as $key=>$value){
			$admin_users = implode(', ',MiString::get_list(get_admin_group_system($key)));
			$value['name'] = '<strong>'.$value['name'].'</strong>'.(User::is_admin()?'':'').'';// - <em>'.$value['structure_id'].'</em>
			$level = IDStructure::level($value['structure_id']);
			$url = Url::build_current(array('cmd'=>'edit','id'=>$key));
			if($level==1) {
				$st .= '
				<li>
					'.$value['move_up'].' '.$value['move_down'].' <input  name="selected_ids[]" type="checkbox" id="Category_checkbox" value="'.$key.'" onclick="select_checkbox(document.ListCategoryForm,\'Category\',this.checked,\'#FFFFEC\',\'#FFF\');" '.((URL::get('cmd')=='delete')?' checked':'').'> <span  class="node"><i class="icon-folder-open"></i></span>';
					$st.=' <a href="'.$url.'">'.$value['name'].'</a> - '.$admin_users.'';
					if ($childs = DB::fetch_all('select id,structure_id,groups_system.name,groups_system.name_id from `groups_system` where' . IDStructure::direct_child_cond($value['structure_id']) . ' order by structure_id')) {
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
					<a href="'.$url.'">'.$value['name'].'</a> - '.$admin_users.'';
					$sub_sql = '
						select 
							groups_system.id,groups_system.structure_id,groups_system.name,groups_system.name_id 
						from 
							groups_system 
						where 
							' . IDStructure::direct_child_cond($value['structure_id']) . ' 
						order by 
							structure_id';
					if ($childs = DB::fetch_all($sub_sql)) {
						$st .= '<ul>';
						$st .= $this->covert_to_ul_li($childs);
						$st .= '</ul>';
					}
					$st.=' 
				</li>';
			}elseif($level>=3){
                $st.='
				<li>
					'.$value['move_up'].' '.$value['move_down'].' <input  name="selected_ids[]" type="checkbox" value="'.$key.'" id="Category_checkbox" onclick="select_checkbox(document.ListCategoryForm,\'Category\',this.checked,\'#FFFFEC\',\'#FFF\');" '.((URL::get('cmd')=='delete')?' checked':'').'> <span class="node"> <i class="icon-minus-sign"></i></span>
					<a href="'.$url.'">'.$value['name'].'</a> - '.$admin_users.'';
                $sub_sql = '
						select 
							groups_system.id,groups_system.structure_id,groups_system.name,groups_system.name_id 
						from 
							groups_system 
						where 
							' . IDStructure::direct_child_cond($value['structure_id']) . ' 
						order by 
							structure_id';
                if ($childs = DB::fetch_all($sub_sql)) {
                    $st .= '<ul>';
                    $st .= $this->covert_to_ul_li($childs);
                    $st .= '</ul>';
                }
                $st.=' 
				</li>';
			}elseif($level>=4){
                $st.='<li>'.$value['move_up'].' '.$value['move_down'].' <input  name="selected_ids[]" type="checkbox" value="'.$key.'" id="Category_checkbox" onclick="select_checkbox(document.ListCategoryForm,\'Category\',this.checked,\'#FFFFEC\',\'#FFF\');" '.((URL::get('cmd')=='delete')?' checked':'').'> <span class="node"> <i class="icon-leaf"></i></span><a href="'.$url.'">'.$value['name'].' - '.$admin_users.'</a></li>';
            }
			//=============================================================
		}
		return $st;
	}
}
?>