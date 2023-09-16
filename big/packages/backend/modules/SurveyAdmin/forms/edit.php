<?php
class EditSurveyAdminForm extends Form
{
	function EditSurveyAdminForm()
	{
		Form::Form('EditSurveyAdminForm');
		if(URL::get('cmd')=='edit')
		{
			$this->add('id',new IDType(true,'object_not_exists','survey'));
		}
		$languages = DB::select_all('language');
		foreach($languages as $language)
		{
			$this->add('name_'.$language['id'],new TextType(true,'invalid_name',0,2000));
			$this->add('question_'.$language['id'],new TextType(true,'invalid_question',0,200000));
			$this->add('survey_options.name_'.$language['id'],new TextType(true,'invalid_name',0,200000));
		}
		$this->add('survey_options.position',new IntType(false,'invalid_position','0','100000000000'));
		$this->add('survey_options.count',new IntType(false,'invalid_count','0','100000000000'));
		$this->link_css('assets/default/css/cms.css');
		$this->link_css('assets/default/css/tabs/tabpane.css');
		$this->link_js('assets/default/css/tabs/tabpane.js');
	}
	function on_submit()
	{
		if(URL::get('cmd')=='edit')
		{
			$row = DB::select('survey',$_REQUEST['id']);
		}
		if($this->check() and URL::get('confirm_edit'))
		{
			$extra = array();
			$languages = DB::select_all('language');
			foreach($languages as $language)
			{
				$extra=$extra+array('name_'.$language['id']=>Url::get('name_'.$language['id'],1)); $extra=$extra+array('question_'.$language['id']=>Url::get('question_'.$language['id'],1));
			}
			$new_row = $extra+
				array(
					'multi', 'portal_id'=>PORTAL_ID
				);
			if(Url::get('is_parent'))
			{
				$new_row['is_parent'] = 1;
			}
			if(URL::get('cmd')=='edit')
			{
				$id = $_REQUEST['id'];
				DB::update_id('survey', $new_row,$id);
			}
			else
			{
				require_once 'packages/core/includes/system/si_database.php';
				$id = DB::insert('survey', $new_row);
			}
			save_log($id);
			if(URl::get('deleted_ids'))
			{
				foreach(URl::get('deleted_ids') as $delete_id)
				{
					DB::delete_id('survey_options',$delete_id);
				}
			}
			if(isset($_REQUEST['mi_survey_options']))
			{
				foreach($_REQUEST['mi_survey_options'] as $key=>$record)
				{
					$empty = true;
					foreach($record as $record_value)
					{
						if($record_value)
						{
							$empty = false;
						}
					}
					if(!$empty)
					{
						$record['survey_id'] = $id;
						if($record['id'])
						{
							DB::update('survey_options',$record,'id='.$record['id']);
						}
						else
						{
							unset($record['id']);
							DB::insert('survey_options',$record);
						}
					}
				}
			}
			Url::redirect_current(array(
	  )+array('just_edited_id'=>$id));
		}
	}
	function draw()
	{
		$languages = DB::select_all('language');
		if(URL::get('cmd')=='edit' and $row=DB::select('survey',URL::sget('id')))
		{
			foreach($row as $key=>$value)
			{
				if(is_string($value) and !isset($_POST[$key]))
				{
					$_REQUEST[$key] = $value;
				}
			}
			$edit_mode = true;
		}
		else
		{
			$edit_mode = false;
		}
		if(!isset($_REQUEST['mi_survey_options']) and $edit_mode)
		{
			$additions = '';
			foreach($languages as $language)
			{
				$additions .= '
			,`survey_options`.`name_'.$language['id'].'`
				';
			}
			DB::query('
				select
					`survey_options`.id
					,`survey_options`.`position` ,`survey_options`.`count`
					'.$additions.'
				from
					`survey_options`
				where
					`survey_options`.survey_id="'.URL::sget('id').'"'
			);
			$mi_survey_options = DB::fetch_all();
			foreach($mi_survey_options as $key=>$value)
			{
					$mi_survey_options[$key]['position'] = System::display_number($value['position']); $mi_survey_options[$key]['count'] = System::display_number($value['count']);
			}
			$_REQUEST['mi_survey_options'] = $mi_survey_options;
		}
		$list_parent = SurveyAdminDB::get_parent();
		$this->parse_layout('edit',
			($edit_mode?$row:array())+
			array(
			'languages'=>$languages,
			'parent'=>$list_parent
			)
		);
	}
}
?>
