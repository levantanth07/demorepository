<?php
class SignIn extends Module{
	function __construct($row){
        require_once 'db.php';
		Module::Module($row);
		if(Url::get('do') == 'dang-ky'){
			Url::redirect('dang-nhap');
        }else{
            Portal::$document_title = 'đăng nhập';
        }
		//mysqli_query("SET charset 'utf8';");
		//mysqli_query("SET names 'utf8';");
		if($code = Url::get('code')){
			$app_id = "488803164824762";
			$app_secret = "979d8b4b59f4817722876e17d3919c9e";
			$redirect_uri = urlencode("https://app.tuha.vn/?page=dang-nhap");
			///
			// Get access token info
			//$facebook_access_token_uri = 'https://graph.facebook.com/v2.8/oauth/access_token?client_id='.$app_id.'&redirect_uri='.$redirect_uri.'&client_secret='.$app_secret.'&code='.$code;
			$facebook_access_token_uri = "https://graph.facebook.com/v2.8/oauth/access_token?client_id=$app_id&redirect_uri=$redirect_uri&client_secret=$app_secret&code=$code";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $facebook_access_token_uri);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			$aResponse = json_decode($response);
			$access_token = $aResponse->access_token;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/me?fields=name,email&access_token=$access_token");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);

			$user = json_decode($response);
			//System::debug($user);die;
			if($user->id){
				$full_name = isset($user->name)?$user->name:'FB User';
				$user_id = $user->id;
				$email = isset($user->email)?$user->email:false;
				SignInDB::register($user_id,$email,$full_name);
			}
		}
        
		require_once 'forms/sign_in_other_account.php';
		require_once 'forms/sign_in.php';
		$this->add_form(new SignInForm);
	}
}
?>