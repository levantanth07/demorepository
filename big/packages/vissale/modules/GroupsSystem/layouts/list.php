<script>
	function check_selected(){
		var status = false;
		jQuery('form :checkbox').each(function(e){
			if(this.checked){
				status = true;
			}
		});
		return status;
	}
	function make_cmd(cmd){
		jQuery('#cmd').val(cmd);
		document.ListCategoryForm.submit();
	}
</script>
<link href="assets/admin/css/category.css" rel="stylesheet" type="text/css">
<form method="post" name="ListCategoryForm">
<fieldset id="toolbar">
	<div id="toolbar-title">
		[[|title|]] <span>[ <?php echo Portal::language(Url::get('cmd','list'));?> ]</span>
	</div>
  <div id="toolbar-content">
      <table align="right">
        <tbody>
        <tr>
        <?php
        if(URL::get('cmd')=='delete' and User::can_delete(false,ANY_CATEGORY)){?>
          <td id="toolbar-trash"  align="center"><a href="javascript:void(0)" onclick="$('cmd').cmd='delete';ListCategoryForm.submit();" > <span title="Xóa"> </span>Xóa</a> </td>
          <td id="toolbar-back"  align="center"><a href="<?php echo URL::build_current();?>"> <span title="Quay lại"> </span> Quay lại </a> </td>
        <?php
        }else{
        if(User::can_add(false,ANY_CATEGORY)){?>
            <td id="toolbar-new"  align="center"><a href="<?php echo Url::build_current(array('cmd'=>'add'));?>"> <span title="New"> </span> Thêm </a> </td>
          <?php }?>
        <?php if(User::can_delete(false,ANY_CATEGORY)){?>
         <td id="toolbar-trash"  align="center"><a  onclick="if(confirm('<?php echo Portal::language('are_you_sure_delete');?>')){if(check_selected()){make_cmd('delete')}else{alert('<?php echo Portal::language('You_must_select_at_least_item');?>');}}"> <span title="Trash"> </span> Xóa </a> </td>
        <?php }
        }?>
          <td id="toolbar-config" align="center"><a href="<?php echo Url::build_current(array('cmd'=>'export_cache'));?>"> <span title="Tạo cache"> </span> Tạo menu </a> </td>
        </tr>
        </tbody>
      </table>
    </div>
</fieldset>
<input type="hidden" name="cmd" value="" id="cmd"/>
<!--IF:delete(URL::get('cmd')=='delete')-->
<input type="hidden" name="confirm" value="1" />
<!--/IF:delete-->

<?php Form::draw_flash_message('DELETE_SYSTEM', ['color' => 'black', 'background' => '#fff3cd', 'margin' => '15px']);?>

<br>
<fieldset id="toolbar">
	<div id="toolbar-content">
    <div class="tree well">
        [[|category|]]
    </div>
  </div>
</fieldset>  
</form>
<script>
jQuery(function () {
    jQuery('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
    jQuery('.tree li.parent_li > span').on('click', function (e) {
        var children = jQuery(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            jQuery(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        }else{
            children.show('fast');
            jQuery(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
    });
});
</script>