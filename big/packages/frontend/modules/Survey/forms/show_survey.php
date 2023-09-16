<?php
class ShowSurveyForm extends Form
{
	function ShowSurveyForm()
	{
		Form::Form('ShowSurvey');
		if(Module::get_setting('skins_template'))
		{
			$this->link_css(Module::get_setting('skins_template').'/style.css');
		}
	}
	function draw()
	{
		if(DEVELOPING or !($list_layout = Module::get_setting('show_survey_cache')))
		{
			if($layout_template = Module::get_setting('layout_template')){
				$frame_code = file_get_contents($layout_template.'/show_survey.php');
				require_once('packages/core/includes/portal/generate_layout.php');
				$generate_layout = new GenerateLayout($frame_code);
				$list_layout = $generate_layout->generate_text($generate_layout->synchronize());
				if(Module::get_setting('skin_template') and preg_match('/packages\/(\w+)\/templates\/SignIn\/skins\/(\w+)/',Module::get_setting('skin_template'),$patterns)){
					$list_layout = '<div class="'.$patterns[1].'-survey-'.$patterns[2].'">'.$list_layout.'</div>';
				}
				Module::set_setting('show_survey_cache',$list_layout);
			}
			else
			{
				$list_layout = '';
			}
		}
		$check = 1;
		$type = 0;
		$title = '';
		$survey=DB::fetch('select value from block_setting where block_id = '.Module::block_id().' and setting_id = "survey_id"');
		$survey_id = $survey['value'];
		if ($survey_id!=0){
			DB::query('select name_'.Portal::language().' as name,question_'.Portal::language().' as question_name,id,multi from survey where id="'.$survey_id.'"' );
			$row=DB::fetch();
			if($row['multi']==1)
			{
				$type=1;
			}
			$title = $row['name'];
		}else{
			$check = 0;
		}
		$arr = array('check'=>$check);
		if($check==1){
			DB::query('select `survey_options`.name_'.Portal::language().' as name,id from `survey_options` where `survey_id`="'.$row['id'].'" order by position');
			$items = DB::fetch_all();
			$question_name = $row['question_name'];
			$arr = $arr+array('items'=>$items,'type'=>$type,'question_name'=>$question_name);
		}
		$button='<a href="'.URL::build('survey_admin',array('href'=>'?'.$_SERVER['QUERY_STRING'],'block_id'=>Module::block_id())).'">'.Portal::language('select_other_survey').'</a>';
		$this->map = $arr+array(
				'survey_id'=>$survey_id,
				'title'=>$title,
				'button'=>$button);
		eval('?>'.$list_layout.'<?php ');
	}
}
?>