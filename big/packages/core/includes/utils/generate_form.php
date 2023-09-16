<?php
/******************************
COPY RIGHT BY NYN PORTAL - TCV
WRITTEN BY THEDEATH
******************************/
class GenerateForm
{
	function GenerateForm($type_field,$name='',$id='',$value='')
	{
		$this->name=$name;
		$this->id=$id;
		$this->value=$value;
		$this->type_field = $type_field;
		if(isset($type_field['value_list']))
		{
			eval($type_field['value_list']);
		}
	}
	function link_js($path)
	{
		echo '<script type="text/javascript" src="'.$path.'"></script>';
	}
	function link_css($path)
	{
		echo '<link rel="stylesheet" href="'.$path.'">';
	}
	function html_code()
	{
		$code='<DIV class="cms-bound-title">';
		if($this->type_field['style']==2)
		{
			$code.='<DIV style="float:left;" class="cms-title" title="'.$this->type_field['help'].'">'.$this->type_field['label'];
		}
		else
		{
			$code.='<DIV class="cms-title" title="'.$this->type_field['help'].'">'.$this->type_field['label'];
		}
		if($this->type_field['require']==1)
		{
			$code.=' (<span class="cms-require">*</span>)';
		}
		$code.='</DIV>';
		switch($this->type_field['type'])
		{
			case 'SELECT':
				$code.='<SELECT name="'.$this->name.'" id="'.$this->id.'" ';
				$code.=$this->type_field['extend'];
				if(isset($this->type_field['meta']) and $this->type_field['meta']!='')
				{
					eval('$meta='.$this->type_field['meta'].';');
					if(isset($meta['change_onsubmit']))
					{
						$code.=' onchange="this.form.submit();" ';
					}
				}
				$code.=' class="cms-change-background">';
				if(isset($this->options))
				{
					foreach($this->options as $key=>$value)
					{
						if($this->value==$key)
						{
							$code.='<option value="'.$key.'" selected="selected">'.$value.'</option>';
						}
						else
						{
							$code.='<option value="'.$key.'">'.$value.'</option>';
						}
					}
				}
				$code.='</SELECT>';
				break;
			case 'FONT_FAMILY':
				$code .= '<SELECT name="'.$this->name.'" id="'.$this->id.'" ';
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				$code .='<option value="Arial, Helvetica, sans-serif">Arial</option>';
				$code .='<option value="\'Times New Roman\', Times, serif">Time New Roman</option>';
				$code .='<option value="\'Courier New\', Courier, monospace">Courier New</option>';
				$code .='<option value="Georgia, \'Times New Roman\', Times, serif">Georgia</option>';
				$code .='<option value="Verdana, Arial, Helvetica, sans-serif">Verdana</option>';
				$code .='<option value="Geneva, Arial, Helvetica, sans-serif">Geneva</option>';
				$code .='<option value="inherit">inherit</option>';
				$code .='</SELECT>';
				$code .= '<script>jQuery(\''.$this->id.'\').value=\''.addslashes($this->value).'\';</script>';
				break;
			case 'FONT_SIZE':
				$code .= '<SELECT name="'.$this->name.'" id="'.$this->id.'" ';
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				for($i=7; $i<=36; $i++)
				{
					$code .= '<option value="'.$i.'px">'.$i.'px</option>';
				}
				$code .= '<option value="inherit">inherit</option>';
				$code .= '<option value="large">large</option>';
				$code .= '<option value="medium">medium</option>';
				$code .= '<option value="small">small</option>';
				$code .= '<option value="smaller">smaller</option>';
				$code .= '<option value="x-large">x-large</option>';
				$code .= '<option value="x-small">x-small</option>';
				$code .= '<option value="xx-large">xx-large</option>';
				$code .= '<option value="xx-small">xx-small</option>';
				$code .= '</SELECT>';
				$code .= '<script>jQuery(\''.$this->id.'\').value=\''.addslashes($this->value).'\';</script>';
				break;
			case 'FONT_WEIGHT':
				$code .= '<SELECT name="'.$this->name.'" id="'.$this->id.'" ';
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				for($i=1; $i<=9; $i++)
				{
					$code .= '<option value="'.($i*100).'px">'.($i*100).'px</option>';
				}
				$code .= '<option value="inherit">inherit</option>';
				$code .= '<option value="bold">bold</option>';
				$code .= '<option value="bolder">bolder</option>';
				$code .= '<option value="lighter">lighter</option>';
				$code .= '<option value="normal">normal</option>';
				$code .= '</select>';
				$code .= '<script>jQuery(\''.$this->id.'\').value=\''.addslashes($this->value).'\';</script>';
				break;
			case 'DATE':
				$this->link_js('packages/core/includes/js/jquery/datepicker.js');
				$this->link_css('packages/core/assets/default/css/jquery/datepicker.css');
				$code.='<INPUT name="'.$this->name.'" type="TEXT" id="'.$this->id.'" ';
				$code.=' value="'.$this->value.'" ';
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				$code .= '<script>jQuery(\'#'.$this->id.'\').datepicker();</script>';
				break;
			case 'DATETIME':
				$this->link_js('packages/core/includes/js/jquery/datepicker.js');
				$this->link_css('packages/core/assets/default/css/jquery/datepicker.css');
				$code.='<INPUT name="'.$this->name.'" type="TEXT" id="'.$this->id.'" ';
				$code.=' value="'.$this->value.'" ';
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				$code .= '<script>jQuery(\'#'.$this->id.'\').datepicker({dateFormat: jQuery.datepicker.TIME});</script>';
				break;
			case 'YESNO':
				$code .='<SELECT name="'.$this->name.'" id="'.$this->id.'" ';
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				$code .='<option value="1"'.(($this->value)?'  selected':'').'>Yes</option>';
				$code .='<option value="0"'.((!$this->value)?'  selected':'').'>No</option>';
				$code .='</SELECT>';
				break;
			case 'CHECKBOX':
				$code.='<INPUT name="'.$this->name.'" type="checkbox" id="'.$this->id.'" ';
				if($this->value==1)
				{
					$code.=' checked="checked"';
				}
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				break;
			case 'RADIO':
				$code.='<INPUT name="'.$this->name.'" type="radio" id="'.$this->id.'" ';
				if($this->value==1)
				{
					$code.=' checked="checked"';
				}
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				break;
			case 'INT':
			case 'FLOAT':
				$this->link_js('packages/core/includes/js/jquery/numbersonly.js');
				$code.='<INPUT name="'.$this->name.'" type="TEXT" id="'.$this->id.'" ';
				$code.=' value="'.$this->value.'" ';
				$code.=$this->type_field['extend'].' onkeypress="return numbersonly(event);" class="cms-change-background">';
				break;
			case 'COLOR':
				$this->link_css("packages/core/includes/js/jgrid/colorpicker/css/colorpicker.css");
				$this->link_js("packages/core/includes/js/jgrid/colorpicker/js/colorpicker.js");
				$this->link_js("packages/core/includes/js/jgrid/colorpicker/js/eye.js");
				$code.='<script>';
				$code.='(function($){';
				$code.='var initLayout = function() {';
				$code.='$("#colorSelector").ColorPicker({';
				$code.='color: "#0000ff",';
				$code.='onShow: function (colpkr) {';
				$code.='$(colpkr).fadeIn(500);';
				$code.='return false;';
				$code.='},';
				$code.='onHide: function (colpkr) {';
				$code.='$(colpkr).fadeOut(500);';
				$code.='return false;';
				$code.='},';
				$code.='onChange: function (hsb, hex, rgb) {';
				$code.='$("#'.$this->id.'").val("#"+hex);';
				$code.='$("#colorSelector div").css("backgroundColor", "#" + hex);';
				$code.='}';
				$code.='});';
				$code.='};';
				$code.='EYE.register(initLayout, "init");';
				$code.='})(jQuery)';
				$code.='</script>';
				$code.='<DIV><DIV style="float:left"><INPUT name="'.$this->name.'" type="TEXT" id="'.$this->id.'" ';
				$code.=' value="'.$this->value.'" ';
				$code.=$this->type_field['extend'].' style="float:left;" class="cms-change-background"></DIV>';
				$code.='<div id="colorSelector" style="float:left;margin-left:5px;"><div style="background-color: #0000ff"></div></div></DIV>';
				break;
			case 'TEXTAREA':
				$code.= '<TEXTAREA name="'.$this->name.'" type="TEXT" id="'.$this->id.'"';
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				$code.=$this->value;
				$code.= '</TEXTAREA>';
				break;
			case 'FILE':
				$code.='<INPUT name="'.$this->name.'" type="file" id="'.$this->id.'" ';
				$code.=$this->type_field['extend'].' class="cms-change-background" size="16">';
				if($this->value and file_exists($this->value))
				{
					$code.='<A href="'.$this->value.'" style="padding-left:5px" class="cms-priview" target="_blank">'.Portal::language('priview').'</A>&nbsp;|&nbsp;<A href="'.Url::build_current(array('cmd'=>'delete_file','src'=>$this->value)).'" class="cms-priview" target="_blank">'.Portal::language('delete').'</A>';
				}
				break;
			case 'IMAGE':
				$this->link_css("packages/core/includes/js/jgrid/gallery/gallery.css");
				$this->link_js("packages/core/includes/js/jgrid/gallery/gallery.js");
				$code.='<INPUT name="'.$this->name.'" type="file" id="'.$this->id.'" ';
				$code.=' onchange ="this.nextSibling.href=\'file:///\'+this.value "';
				$code.=$this->type_field['extend'].' class="cms-change-background" size="16">';
				if($this->value and file_exists($this->value))
				{
					$code.='<div><a href="'.$this->value.'" style="padding-left:5px" class="thickbox" rel="gallery-plants">'.Portal::language('priview').'</a>';
					$code.='&nbsp;|&nbsp;<A href="'.Url::build_current(array('cmd'=>'delete_file','src'=>$this->value)).'" target="_blank">'.Portal::language('delete').'</A></div>';
				}
				break;
			case 'MULTI_ITEM':
				$code.='<DIV id="MULTI_ITEM_'.$this->id.'">';
				@eval('$this->value='.$this->value.';');
				if(is_array($this->value) and count($this->value)>0)
				{
					foreach($this->value as $name=>$value)
					{
						$code.='<DIV>';
						$code.='<span> '.Portal::language('name').' </span>'.'<INPUT name="'.$this->name.'[]" type="TEXT" id="'.$this->id.'" ';
						$code.=' value="'.$name.'" ';
						$code.=$this->type_field['extend'].' class="cms-change-background">';
						$code.='<span> '.Portal::language('link').' </span>';
						$code.='<INPUT name="'.$this->name.'[]" type="TEXT" id="'.$this->id.'" ';
						$code.=' value="'.$value.'" ';
						$code.=$this->type_field['extend'].'>';
						$code.='</DIV>';
						$code.='</DIV>';
					}
				}
				else
				{
					$code.='<DIV>';
					$code.='<span> '.Portal::language('name').' </span>'.'<INPUT name="'.$this->name.'[]" type="TEXT" id="'.$this->id.'" ';
					$code.=' value="'.$this->value.'" ';
					$code.=$this->type_field['extend'].' class="cms-change-background">';
					$code.='<span> '.Portal::language('link').' </span>';
					$code.='<INPUT name="'.$this->name.'[]" type="TEXT" id="'.$this->id.'" ';
					$code.=' value="'.$this->value.'" ';
					$code.=$this->type_field['extend'].'>';
					$code.='</DIV>';
					$code.='</DIV>';
				}
				$code.='<input style="margin-left:22px;" onclick="add_multi(\'MULTI_ITEM_'.$this->id.'\')" type="button" value="'.Portal::language('add').'">';
				break;
			case 'SIMPLE':
			 	$this->link_js("packages/core/includes/js/editor/editor/scripts/innovaeditor.js");
				$code.= '<TEXTAREA name="'.$this->name.'" type="TEXT" id="'.$this->id.'"';
				$code.=$this->type_field['extend'].'>';
				$code.=$this->value;
				$code.= '</TEXTAREA>';
				$code.= '<script>';
				$code.= 'var oEdit2 = new InnovaEditor("oEdit2");';
				$code.= 'oEdit2.width=\'100%\';';
				$code.= 'oEdit2.height=240;';
				$code.= 'oEdit2.btnPrint=true;';
				$code.= 'oEdit2.btnPasteText=true;';
				$code.= 'oEdit2.btnFlash=true;';
				$code.= 'oEdit2.btnMedia=true;';
				$code.= 'oEdit2.btnLTR=true;';
				$code.= 'oEdit2.btnRTL=true;';
				$code.= 'oEdit2.btnSpellCheck=true;';
				$code.= 'oEdit2.btnStrikethrough=true;';
				$code.= 'oEdit2.btnSuperscript=true;';
				$code.= 'oEdit2.btnSubscript=true;';
				$code.= 'oEdit2.btnClearAll=true;';
				$code.= 'oEdit2.btnSave=true;';
				$code.= 'oEdit2.btnStyles=true;';
				$code.= 'oEdit2.css="style/test.css";';
				$code.= 'oEdit2.cmdAssetManager = "modalDialogShow(\'http://'.$_SERVER['SERVER_NAME'].'/packages/core/includes/js/editor/editor/assetmanager/assetmanager.php\',750,550)"; ';
				$code.= 'oEdit2.cmdInternallink = "modelessDialogShow(\'links.htm\',365,270)"; ';
				$code.= 'oEdit2.cmdCustomObject = "modelessDialogShow(\'objects.htm\',365,270)"; ';
				$code.= 'oEdit2.arrCustomTag=[["First Name","{%first_name%}"],';
				$code.= '["Last Name","{%last_name%}"],';
				$code.= '["Email","{%email%}"]];';
				$code.= 'oEdit2.customColors=["#ff4500","#ffa500","#808000","#4682b4","#1e90ff","#9400d3","#ff1493","#a9a9a9"];';
				$code.= 'oEdit2.mode="XHTMLBody";';
				$code.= 'oEdit2.REPLACE("'.$this->id.'");';
				$code.= '</script>';
				break;
			case 'RICH_EDITOR':
				$code.='<div>';
				$code.= '<TEXTAREA name="'.$this->name.'" type="TEXT" id="'.$this->id.'"';
				$code.=' style="display:none">';
				$code.=$this->value;
				$code.= '</TEXTAREA>';
				$code.='<input type="hidden" id="'.$this->id.'___Config" value="" style="display:none" />';
				$code.='<iframe id="'.$this->id.'___Frame" src="http://'.$_SERVER['SERVER_NAME'].'/packages/core/includes/js/editor/FCKeditor/editor/fckeditor.html?InstanceName='.$this->name.'&amp;Toolbar=Default" width="720" height="400" frameborder="0" scrolling="no" '.$this->type_field['extend'].'></iframe>';
				$code.='</div>';
				break;
			default:
				$code.='<INPUT name="'.$this->name.'" type="TEXT" id="'.$this->id.'" ';
				$code.=' value="'.$this->value.'" ';
				$code.=$this->type_field['extend'].' class="cms-change-background">';
				break;
		}
		if($this->type_field['require']==1)
		{
			$code.='<label class="cms-error" for="'.$this->type_field['name'].'" id="'.$this->type_field['name'].'_error">'.$this->type_field['error_message'].'</label>';
		}
		return $code.'</DIV>';
	}
}
?>