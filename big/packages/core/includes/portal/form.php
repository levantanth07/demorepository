<?php
class Form
{
	static $current = false;
	var $name = false;
	var $inputs = array();
	var $errors = false;
	var $error_messages = false;
	var $is_submit = false;
	var $count = 1;
	static $form_count = 1;
	function Form($name=false)
	{
		$this->name=$name;
	}
	function on_submit()
	{
	}
	function is_submit()
	{
		if(!$this->is_submit)
		{
			$this->is_submit = 1;
			if(isset(Module::$current))
			{
				if(isset($_REQUEST['form_block_id']))
				{
					if($_REQUEST['form_block_id']==Module::block_id())
					{
						if($this->inputs)
						{
							$this->is_submit = 2;
							foreach($this->inputs as $name=>$types)
							{
								if(!strpos($name,'.') and !isset($_REQUEST[$name]))
								{
									$this->is_submit = 1;
									break;
								}
							}
						}
					}
				}
			}
		}
		return $this->is_submit == 2;
	}
	function is_error()
	{
		return $this->errors<>false or $this->error_messages<>false;
	}
	function add($name, $type)
	{
		$this->inputs[$name][] = $type;
	}
	function link_css($file_name,$media=false)
	{
		if(strpos(Portal::$extra_header,'<link rel="stylesheet" href="'.$file_name.'" '.($media?'media="'.$media.'"':'').' type="text/css" />')===false)
		{
			Portal::$extra_header .= '<link rel="stylesheet" href="'.$file_name.'" '.($media?'media="'.$media.'"':'').' type="text/css" />';
		}
	}
	function link_js($file_name)
	{
		if(strpos(Portal::$extra_header,'<script type="text/javascript" src="'.$file_name.'"></script>')===false)
		{
			Portal::$extra_header .= '
<script type="text/javascript" src="'.$file_name.'"></script>';
		}
	}
	function auto_refresh($time, $url)
	{
		Portal::$extra_header .= '<meta http-equiv="Refresh" content="'.$time.'; URL='.$url.'">';
	}
	function get_messages()
	{
		$this->error_messages=false;
		if($this->errors)
		{
			foreach($this->errors as $name=>$types)
			{
				foreach($types as $type)
				{
					$this->error_messages[$name][]=$type->get_message();
				}
			}
		}
		return $this->error_messages;
	}
	function check($exclude=array())
	{
		if($this->is_submit())
		{
			$this->errors = false;
			if($this->inputs)
			{
				foreach ($this->inputs as $name=>$types)
				{
					foreach($types as $type)
					{
						if(!in_array($name,$exclude))
						{
							if(!strpos($name,'.'))
							{
								if(!$type->check($_REQUEST[$name]))
								{
									$this->errors[$name][] = $type;
								}
							}
							else
							{
								$names = explode('.',$name);
								$table = 'mi_'.$names[0];
								$field = $names[1];
								if(isset($_REQUEST[$table]))
								{
									if(is_array($_REQUEST[$table]))
									{
										foreach($_REQUEST[$table] as $key=>$record)
										{
											if(isset($record[$field]))
											{
												if(!$type->check($record[$field]))
												{
													$this->errors[$table.'['.$key.']['.$field.']'][] = $type;
												}
											}
											else
											{
												if(!$type->check(''))
												{
													$this->errors[$table.'['.$key.']['.$field.']'][] = $type;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			$this->get_messages();
			if(!$this->errors)
			{
				foreach ($this->inputs as $name=>$types)
				{
					foreach($types as $type)
					{
						if(get_class($type)=='floattype' or get_class($type)=='inttype')
						{
							if(!strpos($name,'.'))
							{
								$_REQUEST[$name] = str_replace(',','',$_REQUEST[$name]);
							}
							else
							{
								$names = explode('.',$name);
								$table = $names[0];
								$field = $names[1];
								if(isset($_REQUEST['mi_'.$table]))
								{
									if(is_array($_REQUEST['mi_'.$table]))
									{
										foreach($_REQUEST['mi_'.$table] as $key=>$record)
										{
											if(isset($record[$field]))
											{
												$_REQUEST['mi_'.$table][$key][$field] = str_replace(',','',$record[$field]);
											}
										}
									}
								}
							}
						}
					}
				}
			}
			return !$this->errors;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Validate mimetype image upload
	 * Chú ý hàm này phải được gọi sau check() để tránh việc check() xóa $this->errors
	 *
	 * @param      <type>  $fieldName  The field name
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function validateUploadImage(string $fieldName, $required = false)
	{
		if(!$required && (empty($_FILES[$fieldName]) || empty($_FILES[$fieldName]['size']))){
			return true;
		}

		require_once ROOT_PATH . 'packages/core/includes/common/ImageType.php';
		if($required && empty($_FILES[$fieldName])){
			$this->error($fieldName, 'Vui lòng upload kèm ảnh.');
			return false;
		}

        if(!ImageType::canUploadImageWithMimeType($_FILES[$fieldName]['type'], $_FILES[$fieldName]['tmp_name'])){
            $this->error($fieldName, 'Vui lòng upload ảnh có định dạng jpg,jpeg,png,gif.');
            return false;
        }

        return true;
	}

	function error($name, $message,$lang=true)
	{
		$this->error_messages[$name][]=$lang?Portal::language($message):$message;
	}
	function parse_layout($name, $params=array())
	{
		$dir = ROOT_PATH.'cache/modules/'.Module::$current->data[(Module::$current->data['module']['type']!='WRAPPER')?'module':'wrapper']['name'];
		$cache_file_name = $dir.'/'.$name.'.php';
		$file_name = Module::$current->data[(Module::$current->data['module']['type']!='WRAPPER')?'module':'wrapper']['path'].'layouts/'.$name.'.php';
		if(!file_exists($cache_file_name) or (($cache_time=@filemtime($cache_file_name)) and (@filemtime($cache_file_name)<@filemtime($file_name))))
		{
			require_once 'packages/core/includes/portal/generate_layout.php';
			$generate_layout = new GenerateLayout(file_get_contents($file_name));
			$text = $generate_layout->generate_text($generate_layout->synchronize());
			if(!is_dir($dir))
			{
				@mkdir($dir);
			}
			if($file = @fopen($cache_file_name,'w+'))
			{
				fwrite($file,$text);
				fclose($file);
			}
			$this->map = $params;
			$this->map['parse_layout'] = $text;
		}
		else
		{
			$this->map = $params;
			$this->map['parse_layout'] = file_get_contents($cache_file_name);
		}
		Module::invoke_event('ONPARSELAYOUT',Module::$current,$this->map);
		eval('?>'.$this->map['parse_layout'].'<?php ');
	}
	//In ra cac thong bao loi neu co
	function error_messages()
	{
		$this->count = Form::$form_count;
		Form::$form_count++;
		if(!$this->error_messages)
		{
			$show = ' display:none;"';
		}
		else
		{
			$show = '';
		}
		$txt = '<div class="row" style="'.$show.'"><div class="col-md-12"  id="error_messages_'.$this->count.'"><div class="alert alert-danger" id="error_messages_content'.$this->count.'">';
		if($this->error_messages)
		{
			foreach ($this->error_messages as $name=>$error_messages)
			{
				foreach($error_messages as $error_message)
				{
					if(trim($this->name))
					{
						$txt .= '<div onclick="var pos=jQuery(\'#'.$name.'\').offset(); window.scrollTo(pos.left,pos.top);jQuery(\'#'.$name.'\').focus().css(\'border\',\'2px inset #ccc\') ;return false;" title="Vị trí lỗi"><i class="fa fa-exclamation-triangle"></i> '.$error_message.'</div>';
					}
					else
					{
						$txt .= '<div><i class="fa fa-exclamation-triangle"></i> '.$error_message.'</div>';
					}
				}
			}
		}
		$txt .= '</div></div></div><br>';
		return $txt;
	}
	//In ra cac thong bao loi neu co
	function ext_error_messages($form_name)
	{
		$this->count = Form::$form_count;
		Form::$form_count++;
		if($this->error_messages)
		{
			foreach ($this->error_messages as $name=>$error_messages)
			{
				foreach($error_messages as $error_message)
				{
					echo $form_name.'.findById(\''.$name.'\').markInvalid(\''.addslashes($error_message).'\');
';
				}
			}
		}
		return $txt;
	}
	function draw()
	{
	}
	//Gan lai $current
	//Goi ham draw()
	function on_draw()
	{
		$last_form = &Form::$current;
		Form::$current = &$this;
		$this->draw();
		Form::$current=&$last_form;
	}

	/**
     * Determines if ajax.
     *
     * @return     bool  True if ajax, False otherwise.
     */
    public static function isAjax()
    {
    	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

	/**
	 * Build flash message key
	 *
	 * @param      <type>  $id     The identifier
	 *
	 * @return     string  The flash message key.
	 */
	private static function get_flash_message_key($id)
	{
		return 'flash_message_' . $id;
	}

	/**
	 * Sets the flash message.
	 *
	 * @param      <type>  $id       The new value
	 * @param      <type>  $message  The message
	 */
	public static function set_flash_message($id, $message)
    {
        $_SESSION[self::get_flash_message_key($id)] = $message;
    }


	/**
	 * Determines if flash message.
	 *
	 * @param      <type>  $id     The identifier
	 *
	 * @return     bool    True if flash message, False otherwise.
	 */
	public static function has_flash_message($id)
	{
        return isset($_SESSION[self::get_flash_message_key($id)]);
	}


    /**
     * Xóa flash mesage
     *
     * @param      <type>  $id     The identifier
     */
    protected static function del_flash_message($id)
    {
        unset($_SESSION[self::get_flash_message_key($id)]);
    }

    /**
     * Gets the flash message.
     *
     * @param      <type>  $id     The identifier
     *
     * @return     <type>  The flash message.
     */
    public static function get_flash_message($id)
    {	
    	$key = self::get_flash_message_key($id);
        if(isset($_SESSION[$key])){
        	$message = $_SESSION[$key];
        	self::del_flash_message($id);
        	
        	return $message;
        }
    }

    /**
     * Hiển thị flash mesage và xóa nó khỏi session
     *
     * @param      <type>  $id     The identifier
     */
    public static function draw_flash_message_success($id)
    {
        self::draw_flash_message($id, ['bgColor' => '#0bab0b', 'textColor' => '#fff']);
    }

    /**
     * Hiển thị flash mesage và xóa nó khỏi session
     *
     * @param      <type>  $id     The identifier
     */
    public static function draw_flash_message_error($id)
    {
        self::draw_flash_message($id, ['bgColor' => '#c11f1f', 'textColor' => '#fff']);
    }

    /**
     * Hiển thị flash mesage và xóa nó khỏi session
     *
     * @param      <type>  $id       The identifier
     * @param      array   $options  The options
     */
    public static function draw_flash_message($id, $options = [])
    {
        if($message = self::get_flash_message($id)){
        	echo self::draw_message($message, $options);
        }
    }

    /**
     * Draws a message.
     *
     * @param      <type>  $message  The message
     * @param      array   $options  The options
     */
    public static function draw_message($message, array $options = [])
    {	
    	$defaultOptions = ['bgColor' => 'grey', 'textColor' => '#000', 'padding' => '10px', 'margin' => '0 0 15px 0'];
    	extract(array_merge($defaultOptions, $options));

    	echo sprintf(
    		'<div style="padding: %s; background: %s; color: %s; border-radius: 3px; margin: %s;">%s</div>', 
    		$padding,
    		$bgColor, 
    		$textColor, 
    		$margin,
    		$message
    	);
    }

    /**
     * { function_description }
     *
     * @param      <type>  $message  The message
     *
     * @return     string  ( description_of_the_return_value )
     */
    public static function render_error($message)
    {
    	die('<div class="col-xs-12" style="padding: 15px"><div class="alert alert-danger" style="display: flex;">
    	    <svg width="15px" style="margin-right: 6px" aria-hidden="true" focusable="false" data-prefix="fad" data-icon="exclamation-triangle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="svg-inline--fa fa-exclamation-triangle fa-w-18 fa-2x"><g class="fa-group"><path fill="#fff" d="M569.52 440L329.58 24c-18.44-32-64.69-32-83.16 0L6.48 440c-18.42 31.94 4.64 72 41.57 72h479.89c36.87 0 60.06-40 41.58-72zM288 448a32 32 0 1 1 32-32 32 32 0 0 1-32 32zm38.24-238.41l-12.8 128A16 16 0 0 1 297.52 352h-19a16 16 0 0 1-15.92-14.41l-12.8-128A16 16 0 0 1 265.68 192h44.64a16 16 0 0 1 15.92 17.59z" class="fa-secondary"></path><path fill="transparent" d="M310.32 192h-44.64a16 16 0 0 0-15.92 17.59l12.8 128A16 16 0 0 0 278.48 352h19a16 16 0 0 0 15.92-14.41l12.8-128A16 16 0 0 0 310.32 192zM288 384a32 32 0 1 0 32 32 32 32 0 0 0-32-32z" class="fa-primary"></path></g></svg> 
    	    ' . $message . '</div></div>');
    }

}
Form::$current=&System::$false;