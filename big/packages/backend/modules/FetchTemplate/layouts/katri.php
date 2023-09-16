<fieldset id="toolbar">
	<div id="toolbar-title">
		<?php echo Portal::language(Url::get('cmd'));?>
	</div>
	<div id="toolbar-content">
	<table align="right">
	  <tbody>
		<tr>
		  <?php if(User::can_view(false,ANY_CATEGORY)){?><td id="toolbar-back" align="center"><a href="<?php echo Url::build_current();?>#"> <span title="[[.List.]]"> </span> [[.List.]] </a> </td><?php }?>
		</tr>
	  </tbody>
	</table>
	</div>
</fieldset>
<br>
<fieldset id="toolbar">
<?php if(Form::$current->is_error()){echo Form::$current->error_messages();}?>
<form method="post" name="form1">
	<table class="table">
		<tr>
			<td>URL (<span class="require">*</span>)</td>
			<td><input name="url" type="text" class="form-control" id="url"/></td>
	  </tr>
	  <tr>
			<td>Danh mục</td>
			<td><select name="category_id" id="category_id" class="form-control"></select></td>
	  </tr>
	   <tr>
			<td>&nbsp;</td>
      <td>
      <!--IF:cond(Url::get('type') == 'PRODUCT')-->
      <input name="san_pham" type="submit" class="btn btn-primary" value=" Sản phẩm " />
      <!--ELSE-->
      <input name="tin_tuc" type="submit" class="btn btn-primary" value=" Tin tức " />
       <!--/IF:cond-->
      </td>
	  </tr>
	</table>
</form>
</fieldset>
