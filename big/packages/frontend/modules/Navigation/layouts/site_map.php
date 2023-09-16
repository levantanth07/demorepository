<?php $title = Module::get_setting('title_'.Portal::language());
if([[=have_category_title=]])
{
	echo '<div class="'.[[=category_title_class=]].'" style="'.Module::get_setting('category_title_css').'">'.$title.'</div>';
}
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
    	<td>
        	<table cellpadding="0" cellspacing="0" width="100%">
				<!--LIST:categories-->
            	<tr>
                	<td>[[|categories.name|]]</td>
                </tr>
				<!--/LIST:categories-->
            </table>
        </td>
    </tr>
</table>