<?php
class Survey extends Module
{
	function Survey($row)
	{
		Module::Module($row);
		define('DEVELOPING',true);
		require_once 'packages/frontend/includes/estore_lib.php';
		require_once 'db.php';
		switch(URL::get('cmd'))
		{
		case 'view':
			require_once 'forms/view_survey.php';
			$this->add_form(new ViewSurveyForm());break;
		default:
			require_once 'forms/show_survey.php';
			$this->add_form(new ShowSurveyForm());break;
		}
	}
}
?>
