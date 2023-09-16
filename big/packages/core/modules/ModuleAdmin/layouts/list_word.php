<?php
System::set_page_title(Portal::get_setting('website_title','').' [[.language.]]'.[[=name=]]);
?>
<div class="form_bound">
	<script type="text/javascript">
		function switch_display(obj)
		{
			obj = $(obj);
			if(obj.style.display=='none')
			{
				obj.style.display='';
			}
			else
			{
				obj.style.display='none';
			}
		}
	</script>
<table cellpadding="0" width="100%"><tr><td  class="form_title"><img src="assets/default/images/buttons/language_button.gif" align="absmiddle"/>[[.language.]] [[|name|]]</td>
			<td class="form_title_button"><a javascript:void(0) onclick="ListModuleWordForm.submit();"><img src="assets/default/images/buttons/save_button.gif" style="text-align:center"/><br />[[.save.]]</a></td>
			<td class="form_title_button"><a href="<?php echo URL::build_current(array('cmd'=>'view','id','package_id'=>isset($_GET['package_id'])?$_GET['package_id']:'','name'=>isset($_GET['name'])?$_GET['name']:''));?>"><img src="assets/default/images/buttons/go_back_button.gif" style="text-align:center" alt=""/><br />[[.back.]]</a></td>
			<td class="form_title_button"><a target="_blank" href="<?php echo URL::build_current(array('cmd'=>'export','module_id'=>URL::get('id')));?>"><img src="assets/default/images/buttons/export_button.gif" style="text-align:center"/><br />[[.export.]]</a></td>
			<td class="form_title_button"><a  href="<?php echo URL::build('default');?>"><img src="assets/default/images/buttons/frontpage.gif" alt=""/><br />Trang ch&#7911;</a></td></tr></table>
<div class="form_content">
	<?php if(Form::$current->is_error())
	{
	echo Form::$current->error_messages();
	}
	?>  <form name="ListModuleWordForm" method="post">
  <table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="white">
        <tr valign="middle">
		  <th width="5%" height="19" align="left" valign="top" nowrap="nowrap">Own</th>
          <th width="10%" height="19" align="left" valign="top">T&#7915;</th>
		  <!--LIST:languages-->
          <th align="left" valign="top" bordercolor="#FFFFFF">[[|languages.name|]]</th>
		  <!--/LIST:languages-->
        </tr>
  <?php
	foreach(MAP['words'] as $word=>$word_text)
	{
		echo '<tr><td width="1%"><input type="checkbox" name="private_word_'.$word.'" value=1 '.($word_text['private']?'checked':'').(isset($word_text['package_name'])?' title="'.$word_text['package_name'].'"':'').'></td><td with="150" nowrap>'.$word.'</td>';
		static $i=1;
		foreach(MAP['languages'] as $language)
		{
			if(isset($word_text[$language['id']]) and $word_text[$language['id']] and isset($word_text[1]) and $word_text[1])
			{
				echo '<td><span onclick="switch_display(this);switch_display(document.getElementById(\'word_'.$i.'\'));">'.$word_text[$language['id']].'</span><input style="display:none" id="word_'.$i.'" type="text" name="word'.$language['id'].'_'.$word.'" value="'.strtr($word_text[$language['id']],array('"'=>'\\"')).'" '.(($language['id']!=1)?' english="1"':'').'></td>';
			}
			else
			{
				echo '<td><input type="text" name="word'.$language['id'].'_'.$word.'" value="'.(isset($word_text[$language['id']])?strtr($word_text[$language['id']],array('"'=>'\\"')):'').'"></td>';
			}
			$i++;
		}
		if(isset($word_text['id']))
		{
			echo '<td valign="top"><a href="'.URL::build_current(array('cmd'=>'delete_word')).'&id='.$word_text['id'].'"><img src="assets/default/images/buttons/delete.gif" alt="X&oacute;a" width="12" height="12" border="0"></a></td>';
		}
		echo '</tr>';
	}
  ?>  		</tr>
	  </table>
	  <input type="hidden" name="add_word_list" value="1" />
	  <input type="submit" value="  [[.update.]]  " />
  	</form>
	</div>
</div>