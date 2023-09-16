<?php
class Session
{
	static $name;
	static $vars;
	static $init_vars;
	static function getIP()
	{
		// Find the user's IP address. (but don't let it give you 'unknown'!)
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_CLIENT_IP']) && (preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $_SERVER['HTTP_CLIENT_IP']) == 0 || preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $_SERVER['REMOTE_ADDR']) != 0))
		{
			// We have both forwarded for AND client IP... check the first forwarded for as the block - only switch if it's better that way.
			if (strtok($_SERVER['HTTP_X_FORWARDED_FOR'], '.') != strtok($_SERVER['HTTP_CLIENT_IP'], '.') && '.' . strtok($_SERVER['HTTP_X_FORWARDED_FOR'], '.') == strrchr($_SERVER['HTTP_CLIENT_IP'], '.') && (preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $_SERVER['HTTP_X_FORWARDED_FOR']) == 0 || preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $_SERVER['REMOTE_ADDR']) != 0))
				$_SERVER['REMOTE_ADDR'] = implode('.', array_reverse(explode('.', $_SERVER['HTTP_CLIENT_IP'])));
			else
				$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CLIENT_IP'];
		}
		if (!empty($_SERVER['HTTP_CLIENT_IP']) && (preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $_SERVER['HTTP_CLIENT_IP']) == 0 || preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $_SERVER['REMOTE_ADDR']) != 0))
		{
			// Since they are in different blocks, it's probably reversed.
			if (strtok($_SERVER['REMOTE_ADDR'], '.') != strtok($_SERVER['HTTP_CLIENT_IP'], '.'))
				$_SERVER['REMOTE_ADDR'] = implode('.', array_reverse(explode('.', $_SERVER['HTTP_CLIENT_IP'])));
			else
				$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			// If there are commas, get the last one.. probably.
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false)
			{
				$ips = array_reverse(explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']));

				// Go through each IP...
				foreach ($ips as $i => $ip)
				{
					// Make sure it's in a valid range...
					if (preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $ip) != 0 && preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $_SERVER['REMOTE_ADDR']) == 0)
						continue;

					// Otherwise, we've got an IP!
					$_SERVER['REMOTE_ADDR'] = trim($ip);
					break;
				}
			}
			// Otherwise just use the only one.
			elseif (preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $_SERVER['HTTP_X_FORWARDED_FOR']) == 0 || preg_match('~^((0|10|172\.16|192\.168|255|127\.0)\.|unknown)~', $_SERVER['REMOTE_ADDR']) != 0)
				$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (!isset($_SERVER['REMOTE_ADDR']))
			$_SERVER['REMOTE_ADDR'] = '';
	}
	static function start()
	{
		Session::$vars = array();
		Session::$init_vars = var_export(Session::$vars,true);
		if(!Session::$name)
		{
			Session::$name = md5($_SERVER['REMOTE_ADDR'].'&'.Session::getIP());
		}
		if($vars = DB::fetch('
			SELECT
				vars
			FROM
				session
			WHERE
				id="'.addslashes(Session::$name).'"
		','vars'))
		{
			Session::$init_vars = $vars;
			eval('Session::$vars = '.$vars.';');
		}
		
	}
	static function end()
	{
		if(Portal::get_setting('session_last_gc_time')<time()-8*3600)
		{
			Portal::set_setting('session_last_gc_time',time());
			DB::update_query('
				DELETE FROM
					session
				WHERE
					last_active_time<'.(time()-8*3600).'
			');
		}
		$vars = var_export(Session::$vars,1);
		if(Session::$init_vars != $vars)
		{
			if($session=DB::select('session','id="'.addslashes(Session::$name).'"'))
			{
				DB::update('session',
					array(
						'vars' => $vars,
						'last_active_time'=>time()
					)
					,'id="'.addslashes(Session::$name).'"'
				);
			}
			else
			{
				DB::insert('session',
					array(
						'id'=>Session::$name,
						'vars'=>$vars,
						'time'=>time(),
						'last_active_time'=>time(),
						'ip'=>Session::getIP()
					)
				);
			}
		}
	}
	static function destroy()
	{
		DB::delete('session','id="'.addslashes(Session::$name).'"');
	}
	static function name($name = false)
	{
		if($name)
		{
			Session::$name = $name;
		}
		return Session::$name;
	}
	static function delete($name, $field=false)
	{
		if($field)
		{
			if(isset(Session::$vars[$name][$field]))
			{
				unset(Session::$vars[$name][$field]);
			}
		}
		else
		{
			if(isset(Session::$vars[$name]))
			{
				unset(Session::$vars[$name]);
			}
		}
	}
	static function get($name, $field=false)
	{
		if(isset(Session::$vars[$name]))
		{
			if($field)
			{
				if(isset(Session::$vars[$name][$field]))
				{
					return Session::$vars[$name][$field];
				}
				return false;
			}
			return Session::$vars[$name];
		}
	}
	static function set($name,$value)
	{
		Session::$vars[$name] = $value;
	}
	static function is_set($name, $field=false)
	{
		if($field)
		{
			return isset(Session::$vars[$name]) and isset(Session::$vars[$name][$field]);
		}
		return isset(Session::$vars[$name]);
	}
}
Session::start();
