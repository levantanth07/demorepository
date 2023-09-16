<?php
class GroupsSystemForm extends Form{
	function __construct(){
		Form::Form("GroupsSystemForm");
		$this->add('id',new IDType(true,'object_not_exists','groups_system'));
		$this->link_css(Portal::template('core').'/css/category.css');
	}
	function on_submit(){
		if(Url::get('id') and $category=DB::fetch('select id,structure_id from groups_system where id='.intval(Url::get('id'))) and User::can_edit(false,$category['structure_id'])){
			$this->delete($this,$_REQUEST['id']);
			Url::redirect_current(Module::$current->redirect_parameters);
		}else{
			Url::redirect_current(Module::$current->redirect_parameters);
		}
	}
	function draw(){
		$this->load_data();
		$languages = DB::select_all('language');
		$this->parse_layout('detail',$this->item_data+array('languages'=>$languages));
	}
	function load_data(){
		DB::query('
			select
				`category`.id
				,`category`.structure_id
				,`category`.`is_visible` ,`category`.`icon_url`
				,`category`.name_'.Portal::language().' as name
				,`category`.description_'.Portal::language().' as description
				,`type`.`id` as type
			from
			 	`category`
				left outer join `type` on `type`.id=`category`.type
			where
				`category`.id = "'.URL::sget('id').'"');
		if($this->item_data = DB::fetch()){
		}
	}
	function load_multiple_items(){
	}
}
?>