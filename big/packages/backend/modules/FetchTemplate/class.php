<?php
class FetchTemplate extends Module
{
	function FetchTemplate($row)
	{
		Module::Module($row);
		require_once 'db.php';
		if(User::can_admin(false,ANY_CATEGORY))
		{
			switch(Url::get('cmd'))
			{
				case 'fetch_congdong'	:
					require_once 'forms/fetch_congdong.php';
					$this->add_form(new FetchCongDongForm());
					break;
				case 'fetch_katri'	:
					require_once 'forms/katri.php';
					$this->add_form(new FetchKatriForm());
					break;	
				case 'fetch_dantri'	:
					require_once 'forms/fetch_dantri.php';
					$this->add_form(new FetchDanTriForm());
					break;
				case 'fetch_vnexpress'	:
					require_once 'forms/fetch_vnexpress.php';
					$this->add_form(new FetchVnExpressForm());
					break;
				case 'fetch_tuoitre'	:
					require_once 'forms/fetch_tuoitre.php';
					$this->add_form(new FetchTuoiTreForm());
					break;
				case 'fetch_vnnet'	:
					require_once 'forms/fetch_vnnet.php';
					$this->add_form(new FetchVnNetForm());
					break;
				default:
					require_once 'forms/default.php';
					$this->add_form(new DefaultForm());
					break;
			}
		}
		else
		{
			URL::access_denied();
		}
	}
}
?>