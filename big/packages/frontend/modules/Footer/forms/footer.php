<?php
class FooterForm extends Form{
	function __construct(){
		Form::Form('FooterForm');
	}
	function on_submit(){
		if(!DB::exists('select * from newsletter where email = "'.Url::get('newsletter_email').'"')){
			$array = array(
				'email'=>Url::get('newsletter_email'),
				'ip'=>$_SERVER['REMOTE_ADDR'],
				'time'=>time()
			);
			DB::insert('newsletter',$array);
			echo '<script>
				alert("'.Portal::language('your_email_has_just_sent').'");
				window.location="/";
			</script>';
			exit();
		}else{
			echo '<script>
				alert("'.Portal::language('email_existed').'");
				window.location="'.$_SERVER['REQUEST_URI'].'#newsletter";
			</script>';
		}
	}
	function draw(){
		$content = Portal::get_setting('estore_footer_'.Portal::language(),false);
		$button_name='hidden_'.Module::block_id();
		$button='<a href="'.URL::build('account_setting',array('category_id'=>44,'portal'=>substr(PORTAL_ID,1),'account_id'=>PORTAL_ID)).'#enchor_estore_footer_content_1"><img src="'.Portal::template('core').'/images/buttons/select.jpg" alt="Thay &#273;&#7893;i n&#7897;i dung"></a>';
		if($content){
			$languages = DB::select_all('language');
		}
		$number_format = number_format(Portal::$page_gen_time->get_timer(),4);
		$link_admin_html = URL::build('admin_html',array('href'=>'?'.$_SERVER['QUERY_STRING'],'block_id'=>Module::block_id()));
		$information_query_in_page='';
		$total_query=0;
		$requests = '';
		if(DEBUG==1){
			$str='';
			$total_query = DB::num_queries();
			//$requests = var_export($_REQUEST);
			if(isset($GLOBALS['information_query']) and count($GLOBALS['information_query'])>0){
				foreach($GLOBALS['information_query'] as $key=>$value){
					$str.='<span style="font-weight:bold">Module '.$value['name'].' have got '.$value['number_queries'].' query (in time '.$value['timer'].'s).</span><br><span style="text-decoration:underline">Query excuted :</span><br>';
					$query='';
					if(is_array($value['query']) and count($value['query'])>0){
						foreach($value['query'] as $key_query=>$value_query){
							$query.='<span style="color:#ff0000;padding-left:50px">'.$value_query.'</span><br>';
						}
					}
					$total_query+=$value['number_queries'];
					$str.=$query;
				}
			}
			$information_query_in_page=$str;
		}
		$this->map = array(
			'content'=>$content,
			'button_name'=>$button_name,
			'button'=>$button,
			'timer'=>number_format(Portal::$page_gen_time->get_timer(),4),		
			'information_query_in_page'=>$information_query_in_page,
			'total_query'=>$total_query,
			'number_format'=>$number_format,
			'number_query'=>DB::num_queries(),
			'requests'=>$requests,
			'link_structure_page'=>Url::build('edit_page',array('id'=>Portal::$page['id'])),
			'link_edit_page'=>Url::build('page',array('id'=>Portal::$page['id'],'cmd'=>'edit')),
			'delete_cache'=>Url::build('page',array('id'=>Portal::$page['id'],'cmd'=>'refresh','href'=>'?'.$_SERVER['QUERY_STRING'])),
			'link_admin_html'=>$link_admin_html
		);
		$this->parse_layout('footer', $this->map);
	}
}
?>
