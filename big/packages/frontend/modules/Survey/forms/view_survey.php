<?php
if ((Url::get('id',0,1)==0) or !DB::exists_id('survey',$_REQUEST['id']))
{
	URl::redirect('trang-chu');
}
class ViewSurveyForm extends Form
{
	function ViewSurveyForm()
	{
		Form::Form('ViewSurvey');
		$ids=Url::get('ids','');
		if ($ids!='')
		{
			$ids='('.$ids.')';
			DB::query('update survey_options set count=count+1 where id in '.$ids.' and survey_id='.Url::get('id'));
			Url::redirect_current(array('cmd','id'));
		}
		if(Module::get_setting('skins_template'))
		{
			$this->link_css(Module::get_setting('skins_template').'/style.css');
		}
	}
	function random_color_elem()
	{
		$st = dechex(rand(10,30)*7);
		if(strlen($st)==1)
		{
			$st = '0'.$st;
		}
		return $st;
	}
	function random_color()
	{
		return '#'.$this->random_color_elem().$this->random_color_elem().$this->random_color_elem();
	}
	function draw()
	{
		srand(time());
		$survey_id=Url::get('id',0);
		$row=DB::select('survey',$survey_id);
		DB::query('select sum(count) as total from `survey_options` where survey_id='.$row['id']);
		$num=DB::fetch();
		$num=$num['total'];
		$check = 1;
		if($num==0)
		{
			$check = 0;
		}
		else
		{
			DB::query('select id,name_'.Portal::language().' as name,count from `survey_options` where survey_id='.$row['id']);
			$items = DB::fetch_all();
			foreach($items as $key=>$item)
			{
				$items[$key]['percent']=($items[$key]['count']/$num)*100;
				$items[$key]['width']=1+$items[$key]['percent'];
				$items[$key]['percent']=System::display_number($items[$key]['percent'],2);
				$items[$key]['color'] = $this->random_color();
			}
		}
		$arr = array(
			'question'=>$row['question_'.Portal::language()],
			'check'=>$check,
			'num'=>$num,
			'survey_id'=>$survey_id);
		if($check==1)
		{
			$arr = $arr + array('items'=>$items);
		}
		$this->map = $arr;
		if(DEVELOPING or !($list_layout = Module::get_setting('cache')))
		{
			if($layout_template = Module::get_setting('layout_template'))
			{
				$frame_code = file_get_contents($layout_template.'/view_survey.php');
				require_once('packages/core/includes/portal/generate_layout.php');
				$generate_layout = new GenerateLayout($frame_code);
				$list_layout = $generate_layout->generate_text($generate_layout->synchronize());
				if(Module::get_setting('skin_template') and preg_match('/packages\/(\w+)\/templates\/SignIn\/skins\/(\w+)/',Module::get_setting('skin_template'),$patterns)){
					$list_layout = '<div class="'.$patterns[1].'-survey-'.$patterns[2].'">'.$list_layout.'</div>';
				}
			}
			else
			{
				$list_layout = '';
			}
		}
		eval('?>'.$list_layout.'<?php ');
	}
}
?>