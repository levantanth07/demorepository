<?php
// define('ENVIRONMENT', 'develop'); // staging, production, develop
class Session{
	static $name;
	static $init_vars;
	static function start(){
		$cookies = session_get_cookie_params();
		$options = [
			'cookie_path' => '/',
			'cookie_domain' => $cookies['domain'],
			
		];
		if(ENVIRONMENT == 'production'){
			array_push($options, [
				'cookie_httponly' => true,
				'cookie_samesite' => 'Lax',
				'cookie_lifetime' => 14400
			]);
		}
        session_start($options);
	}

	/**
	 * { function_description }
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function id() 
	{
		return session_id();
	}

	/**
	 * { function_description }
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function commit()
	{
		return session_commit();
	}

	/**
	 * { function_description }
	 *
	 * @param      <type>  $bool   The bool
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function regenerate_id($bool)
	{
		return session_regenerate_id($bool);
	}

	static function end(){

	}
	static function destroy(){
		session_destroy();
	}
	static function name($name = false){
		if($name){
			Session::$name = $name;
		}
		return Session::$name;
	}
	static function delete($name, $field=false){
		if($field){
			unset($_SESSION[$name][$field]);
		}else{
			unset($_SESSION[$name]);
		}
	}
	static function get($name, $field=false){

		if(isset($_SESSION[$name])){
			if($field){
				if(isset($_SESSION[$name][$field])){
					return $_SESSION[$name][$field];
				}
				return false;
			}
			return $_SESSION[$name];
		}
	}
	static function set($name,$value){
		$_SESSION[$name] = $value;
	}
	static function is_set($name, $field=false){
		if($field){
			return isset($_SESSION[$name]) and isset($_SESSION[$name][$field]);
		}
		return isset($_SESSION[$name]);
	}
}
Session::start();
