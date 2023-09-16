<?php if(Url::get('category_id') and $path = DB::fetch_all('select id,name_'.Portal::language().' as name,name_id from category where '.IDStructure::path_cond(DB::structure_id('category',intval(Url::sget('category_id')))).' and category.type="PRODUCT" and portal_id="'.PORTAL_ID.'" order by structure_id')){}?>
<div class="frame-default-bound" <?php if(Module::get_setting('extra_css_bound')){ echo 'style="'.Module::get_setting('extra_css_bound').'"'; }?> >
    <div class="frame-default-title">
        <div class="frame-default-title-left">
            <div class="frame-default-title-right">
            	<div class="frame-default-title-center">
				<?php if(Module::get_setting('frame_icon_title') and file_exists(Module::get_setting('frame_icon_title'))){?><span class="frame-default-icon"><img src="<?php echo Module::get_setting('frame_icon_title');?>" /></span><?php }?>
                <?php if(Module::get_setting('frame_title_link')){?>
                    <a href="<?php echo Module::get_setting('frame_title_link');?>" class="frame-title-link">{{-title-}}</a>
                <?php }else if(isset($path) and $path){
				$total = sizeof($path);
				$i=1;
				foreach($path as $key=>$value){
					if(isset($_REQUEST['name']) and $_REQUEST['name']){
						echo '<a href="'.Url::build('san-pham',array('name_id'=>$value['name_id'])).'">'.$value['name'].'</a> '.(($i==$total)?''.($_REQUEST['name']?'&raquo; '.$_REQUEST['name']:''):'&raquo; ').'';
					}
					else
					{
						echo '<a href="'.Url::build('san-pham',array('name_id'=>$value['name_id'])).'">'.$value['name'].'</a> '.(($i!=$total)?'&raquo; ':'');
					}
					$i++;
				}
				}else{ ?>
                    {{-title-}}
                <?php }?>
                </div>
            </div>
        </div>
    </div>
    <div class="frame-default-content">
        <div class="frame-default-content-left">
            <div class="frame-default-content-right">{{-content-}}</div></div></div>
    <div class="frame-default-bottom">
        <div class="frame-default-bottom-left"><div class="frame-default-bottom-right"></div></div>
    </div>
</div>