<?php
class ManageAdvertismentForm extends Form{
	function ManageAdvertismentForm(){
		Form::Form('ManageAdvertismentForm');
		$this->add('region',new TextType(true,'invalid_region',0,100));
		$this->add('position',new TextType(false,'invalid_position',0,100));
		$this->add('end_time',new DateType(true,'invalid_end_time',0,32));
		$this->add('start_time',new DateType(true,'invalid_start_time',0,32));
		$this->link_js(Portal::template_js('core').'jquery/datepicker.js');
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/jquery/datepicker.css');
	}
	function insert_advertisment($row){
		DB::insert('advertisment',$row);
	}
	function update_advertisment($row,$id){
		DB::update_id('advertisment',$row,$id);
	}
	function on_submit(){
		if($this->check()){
			$items_list = array();
			foreach($_REQUEST as $key=>$value){
				if(preg_match('/item_list_([0-9]+)/',$key,$match)){
					$items_list[$match[1]] = $match[1];
				}
			}
			if(count($items_list)>0){
				foreach($items_list as $key){
					$new_row = array();
					if($advertisment = DB::select('media','id="'.$key.'" and type="ADVERTISMENT" and portal_id="'.PORTAL_ID.'"')){
						$new_row = array('region','position');
						$new_row['start_time']=Date_Time::to_time(Url::get('start_time'));
						$new_row['end_time']=Date_Time::to_time(Url::get('end_time'));
						$new_row['item_id']=$key;
						if(Url::get('id') and $item=DB::exists_id('advertisment',Url::get('id'))){
							$new_row['category_id']=$_REQUEST['categories'][0];
							$this->update_advertisment($new_row,Url::get('id'));
						}else{
							if($categories=Url::get('categories') and is_array(Url::get('categories')) and count(Url::get('categories'))>0){
								$size=count(Url::get('categories'));
								for($i=0;$i<$size;$i++){
									$new_row['category_id']=$categories[$i];
									$this->insert_advertisment($new_row);
								}
							}else{
								$this->insert_advertisment($new_row);
							}
						}
					}
				}
			}else{
				$this->error('item_id','invalid_advertisment');
				return;
			}
			Url::redirect_current();
		}
	}
	function draw(){
		if(Url::get('id') and $item=DB::fetch('select *,FROM_UNIXTIME(end_time,"%d/%m/%Y") as end_time,FROM_UNIXTIME(start_time,"%d/%m/%Y") as start_time from  advertisment where id='.intval(Url::get('id')))){
			foreach($item as $key =>$value){
				if(is_string($value) and !isset($_REQUEST[$key])){
					$_REQUEST[$key] = $value;
				}
			}
		}
		$cond = '1';
		if(Url::get('page_name')){
			$cond.=' and page.name="'.Url::get('page_name').'"';
		}
		$page_name = array();
		$regions = ManageAdvertismentDB::get_region($cond);
		$page_name = ManageAdvertismentDB::get_page_name();
		$this->parse_layout('advertisment',array(
			'region_list'=>String::get_list($regions),
			'page_name_list'=>String::get_list($page_name),
			'items'=>ManageAdvertismentDB::get_item_id_list(),
			'categories[]_list'=>array('0'=>'-----')+ManageAdvertismentDB::get_categories()
		));
        //System::debug(ManageAdvertismentDB::get_categories());
	}
}
?>