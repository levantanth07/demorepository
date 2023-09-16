<?php
class CustomInput
{
	static $Bs_count = 0;
	function __construct($definition, $name, $id, $value)
	{
		$this->name = $name;
		$this->id = $id;
		$this->definition = $definition;
		$this->value = $value;
		if($definition['value_list'])
		{
			eval($definition['value_list']);
		}
	}
	function Bs_init($name, $path)
	{
		if(!isset($GLOBALS[$name]))
		{
			if($name == 'Bs_ToolbarCss_init')
			{
				echo '
<script>
if (moz) {
  document.writeln("<link rel=\'stylesheet\' href=\'packages/core/includes/js/blueshoes/javascript/components/toolbar/win2k_mz.css\'>");
} else {
  document.writeln("<link rel=\'stylesheet\' href=\'packages/core/includes/js/blueshoes/javascript/components/toolbar/win2k_ie.css\'>");
}
</script>';
			}
			else
			{
				echo '
		<script type="text/javascript" src="packages/core/includes/js/blueshoes/javascript/'.$path.'"></script>';
			}
			$GLOBALS[$name] = true;
		}
	}
	function html_code()
	{
		CustomInput::$Bs_count++;
		$code = '';
		$name_code = 'id="'.$this->id.'" name="'.$this->name.'"';
		$style_code = (isset($this->definition['extend']) and $this->definition['extend'])?' '.$this->definition['extend']:(($this->definition['type']!='SELECT' and $this->definition['type']!='FONT_FAMILY' and $this->definition['type']!='FONT_SIZE' and $this->definition['type']!='YESNO' and $this->definition['type']!='FONT_WEIGHT' and $this->definition['type']!='CHECKBOX')?' style="width:300px;"':'');
		switch($this->definition['type'])
		{
		case 'SELECT':
			if($this->definition['meta'])
			{
				eval('$meta='.$this->definition['meta'].';');
				$submit_on_change = isset($meta['submit_on_change']) and $meta['submit_on_change'];
			}
			else
			{
				$submit_on_change = false;
			}
			$code .= '<select class="form-control" '.$name_code.$style_code.($submit_on_change?' onchange="this.form.submit();"':'').'>
';
			if(isset($this->options))
			{
				foreach($this->options as $key=>$value)
				{
					$code .= '<option value="'.addslashes($key).'"'.(($this->value==$key)?'  selected':'').'>'.$value.'</option>';
				}
			}
			$code .= '
</select> - '.$this->value;
			break;
		case 'IMAGELIST':
			$code .= '<input type="hidden" '.$name_code.' value="'.str_replace('"','&quot;',$this->value).'">
			<div id="div_'.$this->id.'" style="width:98%;height:150px;overflow-y:auto;border:1px solid black;padding:10px;">
';
			if(isset($this->options))
			{
				foreach($this->options as $key=>$value)
				{
					$code .= '<div class="image_list_item'.(($key==$this->value)?'_selected':'').'" onclick="select_image_list(this,\''.$this->id.'\',\''.addslashes($key).'\')"><img width="60" height="50" src="'.$value['icon'].'"><br>'.$value['name'].'</div>';
				}
			}
			$code .= '
</div>';
			break;
		case 'COLOR':
			$this->Bs_init('Bs_init', 'core/lang/Bs_Misc.lib.js');
			$this->Bs_init('Bs_ColorUtil_init', 'core/gfx/Bs_ColorUtil.lib.js');
			$this->Bs_init('Bs_Button_init', 'components/toolbar/Bs_Button.class.js');
			$this->Bs_init('Bs_ColorPicker_init', 'components/colorpicker/Bs_ColorPicker.class.js');
			$this->Bs_init('Bs_ToolbarCss_init', '');
			$code .= '
<div style="display:inline"><input type="text" name="'.$this->name.'" id="'.$this->id.'" maxlength="7" size="7"></div>
<script>
	myColorObj_'.CustomInput::$Bs_count.' = new Bs_ColorPicker("'.$this->id.'");
	myColorObj_'.CustomInput::$Bs_count.'.colorizeBackground = true;
	myColorObj_'.CustomInput::$Bs_count.'.setColorByHex("'.$this->value.'");
	myColorObj_'.CustomInput::$Bs_count.'.draw();
</script>
';
			break;
		case 'FONT_FAMILY':
			$code .= '<select '.$name_code.$style_code.'>
				<option value="Arial, Helvetica, sans-serif">Arial</option>
				<option value="\'Times New Roman\', Times, serif">Time New Roman</option>
				<option value="\'Courier New\', Courier, monospace">Courier New</option>
				<option value="Georgia, \'Times New Roman\', Times, serif">Georgia</option>
				<option value="Verdana, Arial, Helvetica, sans-serif">Verdana</option>
				<option value="Geneva, Arial, Helvetica, sans-serif">Geneva</option>
				<option value="inherit">inherit</option>
			</select>';
			$code .= '<script>$(\''.$this->id.'\').value=\''.addslashes($this->value).'\';</script>';
			break;
		case 'FONT_SIZE':
			$code .= '<select '.$name_code.$style_code.'>';
			for($i=7; $i<=36; $i++)
			{
				$code .= '<option value="'.$i.'px">'.$i.'px</option>';
			}
			$code .= '
				<option value="inherit">inherit</option>
				<option value="large">large</option>
				<option value="medium">medium</option>
				<option value="small">small</option>
				<option value="smaller">smaller</option>
				<option value="x-large">x-large</option>
				<option value="x-small">x-small</option>
				<option value="xx-large">xx-large</option>
				<option value="xx-small">xx-small</option>
			</select>';
			$code .= '<script>$(\''.$this->id.'\').value=\''.addslashes($this->value).'\';</script>';
			break;
		case 'FONT_WEIGHT':
			$code .= '<select '.$name_code.$style_code.'>';
			for($i=1; $i<=9; $i++)
			{
				$code .= '<option value="'.($i*100).'px">'.($i*100).'px</option>';
			}
			$code .= '
				<option value="inherit">inherit</option>
				<option value="bold">bold</option>
				<option value="bolder">bolder</option>
				<option value="lighter">lighter</option>
				<option value="normal">normal</option>
			</select>';
			$code .= '<script>$(\''.$this->id.'\').value=\''.addslashes($this->value).'\';</script>';
			break;
		case 'TABLE':
			break;
		case 'DATE':
			$this->Bs_init('Bs_init', 'core/lang/Bs_Misc.lib.js');
			$this->Bs_init('Bs_Button_init', 'components/toolbar/Bs_Button.class.js');
			$this->Bs_init('Bs_NumberField_init', 'components/numberfield/Bs_NumberField.class.js');
			$this->Bs_init('Bs_DatePicker_init', 'components/datepicker/Bs_DatePicker.class.js');
			$this->Bs_init('Bs_FormFieldSelect_init', 'core/form/Bs_FormFieldSelect.class.js');
			$this->Bs_init('Bs_ToolbarCss_init', '');
			if($this->value<>'')
			{
				$code .= '
<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.addslashes($this->value).'" style="width:80px;" maxlength="11">
<script>
	myDatePicker_'.CustomInput::$Bs_count.' = new Bs_DatePicker();
	myDatePicker_'.CustomInput::$Bs_count.'.dateFormat = \'fr\';
	myDatePicker_'.CustomInput::$Bs_count.'.internalDateFormat = \'eu\';
	myDatePicker_'.CustomInput::$Bs_count.'.displayDateFormat = \'eu\';
    myDatePicker_'.CustomInput::$Bs_count.'.convertField(\''.$this->id.'\');
</script>
';
			}
			else
			{
				$code .= '
<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="" style="width:80px;" maxlength="11">
<script>
	myDatePicker_'.CustomInput::$Bs_count.' = new Bs_DatePicker();
	myDatePicker_'.CustomInput::$Bs_count.'.dateFormat = \'fr\';
	myDatePicker_'.CustomInput::$Bs_count.'.internalDateFormat = \'eu\';
	myDatePicker_'.CustomInput::$Bs_count.'.displayDateFormat = \'eu\';
    myDatePicker_'.CustomInput::$Bs_count.'.convertField(\''.$this->id.'\');
</script>
';
			}
			break;
		case 'DATETIME':
			$this->Bs_init('Bs_init', 'core/lang/Bs_Misc.lib.js');
			$this->Bs_init('Bs_Button_init', 'components/toolbar/Bs_Button.class.js');
			$this->Bs_init('Bs_NumberField_init', 'components/numberfield/Bs_NumberField.class.js');
			$this->Bs_init('Bs_DatePicker_init', 'components/datepicker/Bs_DatePicker.class.js');
			$this->Bs_init('Bs_FormFieldSelect_init', 'core/form/Bs_FormFieldSelect.class.js');
			$this->Bs_init('Bs_ToolbarCss_init', '');
			$code .= '
<div><input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.addslashes(date('d/m/Y',$this->value)).'" style="width:126px;" maxlength="11"></div>
<script>
	myDatePicker_'.CustomInput::$Bs_count.' = new Bs_DatePicker();
	myDatePicker_'.CustomInput::$Bs_count.'.dateFormat = \'eu\';
	myDatePicker_'.CustomInput::$Bs_count.'.internalDateFormat = \'eu\';
	myDatePicker_'.CustomInput::$Bs_count.'.displayDateFormat = \'eu\';
    myDatePicker_'.CustomInput::$Bs_count.'.convertField(\''.$this->id.'\');
</script>
';
			$code .= '<input type="text" name="'.$this->name.'_time" style="width:126px;" maxlength="11" value="'.date('H:m:i',$this->value).'">';
			break;
		case 'YESNO':
			$code .= '<select '.$name_code.$style_code.'>
				<option value="1"'.(($this->value)?'  selected':'').'>Yes</option>
				<option value="0"'.((!$this->value)?'  selected':'').'>No</option>
			</select>';
			break;
		case 'RICH_EDITOR':
			//require_once 'packages/core/includes/utils/mce_editor.php';
			$code .= '<script language="javascript" type="text/javascript" src="packages/core/includes/js/init_tinyMCE.js"></script>';
			$style_code = (isset($this->definition['extend']) and $this->definition['extend'])?' '.$this->definition['extend']:' class="form-control html-edit"';
			$code .= '<textarea '.$name_code.$style_code.' onfocus="init_rich_editor(\''.$this->id.'\');" class="form-control html-edit">'.$this->value.'</textarea>';
			$code .= '';
			break;
		case 'SIMPLE':
			//require_once 'packages/core/includes/utils/mce_editor.php';
			$code .= '
<script language="javascript" type="text/javascript" src="packages/core/includes/js/init_tinyMCE.js"></script>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		theme : "simple",
		mode : "exact",
		elements : "'.$this->id.'",
		display_tab_class : "showTab",
		content_css : "'.Portal::template('core').'/editor.css"
	});
</script>';
			$style_code = (isset($this->definition['extend']) and $this->definition['extend'])?' '.$this->definition['extend']:' class="form-control html-edit"';
			$code .= '<textarea '.$name_code.$style_code.'>'.$this->value.'</textarea>';
			break;
		case 'TEXTAREA':
			$style_code = (isset($this->definition['extend']) and $this->definition['extend'])?' '.$this->definition['extend']:' class="form-control html-edit"';
			$code .= '<textarea '.$name_code.$style_code.' rows="10">'.str_replace('<','&lt;',$this->value).'</textarea><button type="button" onclick="">Preview</button>';
			break;
		case 'RADIO':
			foreach($this->options as $key=>$option)
			{
				$code .= '<input type="radio" name="'.$this->name.'" value="'.$key.'"'.(($this->value==$key)?' checked':'').'> '.$option.'&nbsp;&nbsp;&nbsp;';
			}
			break;
		default:
			$type = 'text';
			switch($this->definition['type'])
			{
				case 'CHECKBOX':
					$type='checkbox';
					break;
				case 'RADIO':
					$type='radio';
					break;
				case 'PASSWORD':
					$type='password';
					break;
				case 'FILE':
				case 'IMAGE':
					$type='file';
					$preview = true;
					$width = 150;
					$height = 150;
					$code .= '<table width="100%"><tr><td valign="top">';
					if($this->value and $size = @getimagesize($this->value))
					{
						if($size[0]>200)
						{
							$width = 200;
							$height = round(($width/$size[0])*$size[1]);
						}
						else
						if($size[1]>200)
						{
							$height = 200;
							$width = round(($height/$size[1])*$size[0]);
						}
						else
						if($size[0]<50 and $size[0]>0)
						{
							$width = $size[0] * 2;
							$height = $size[1] * 2;
						}
						else
						{
							$width=$size[0];
							$height=$size[1];
						}
					}
					if($this->definition['meta'])
					{
						eval('$meta='.$this->definition['meta'].';');
						$preview = !isset($meta['preview']) or $meta['preview'];
						$width = (isset($meta['width']))?$meta['width']:$width.'px';
						$height = (isset($meta['height']))?$meta['height']:$height.'px';
					}
					if($preview and $this->value)
					{
						if($this->definition['type']=='IMAGE')
						{
							$code .= '<a target="_blank" href="'.$this->value.'"><img src="'.$this->value.'"'.($width?' width="'.$width.'"':'').($height?' height="'.$height.'"':'').'></a><br>';
						}
						else
						{
							$code .= '<a target="_blank" href="'.$this->value.'">'.$this->value.'</a><br>';
						}
					}
					$code .= '</td><td>';
					break;
			}
			$code .= '<input type="'.$type.'" '.$name_code.$style_code.($this->value?(($this->definition['type']=='CHECKBOX')?' checked':' value="'.str_replace('"','&quot;',$this->value).'"'):'');
			if(($this->definition['type'] == 'IMAGE' or $this->definition['type'] == 'FILE'))
			{
				$code .= ' onchange = "this.parentNode.previousSibling.firstChild.firstChild'.(($this->definition['type'] == 'IMAGE')?'.src=\'file:///\'+this.value':'.innerHTML=\'\'').';">';
				$code .= '<input type="hidden" id="'.$this->id.'_deleted" name="delete_images_'.$this->name.'" value="0"> <a javascript:void(0) onclick="$(\''.$this->id.'_deleted\').value=1;this.parentNode.previousSibling.firstChild.firstChild'.(($this->definition['type'] == 'IMAGE')?'.src=\''.Portal::template('core').'/images/spacer.gif\'':'.innerHTML=\'\'').';if(document.all){event.returnValue=false;}else {return false;}">X&#243;a</a><br>Copy t&#7915; &#273;&#432;&#7901;ng link kh&#225;c:<br><input type="text" id="'.$this->id.'_copy_from_link" name="copy_from_link_'.$this->name.'" value="" style="width:300px" onchange="this.parentNode.previousSibling.firstChild.firstChild'.(($this->definition['type'] == 'IMAGE')?'.src=this.value':'.innerHTML=\'\'').';"></td></tr></table>';
			}
			else
			{
				$code .= '>';
			}
			break;
		}
		return $code;
	}
}
?>