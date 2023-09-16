<nav>
    <div class="sideBar">
        <div class="logo"><a href="#"><img src="assets/standard/images/logo-siderBar.png"/></a></div><!--End .logo-->
        <ul>
          <!--LIST:categories-->
          <li>
          	<a href="<?php echo (Portal::language()==1)?'san-pham':'product';?>/[[|categories.name_id|]]/">[[|categories.name|]]</a>
            <ul class="sub">
            	<!--LIST:categories.childs-->
            	<li><a href="<?php echo (Portal::language()==1)?'san-pham':'product';?>/[[|categories.childs.name_id|]]/">[[|categories.childs.name|]]</a></li>
              <!--/LIST:categories.childs-->
            </ul>
         	</li>
          <!--/LIST:categories-->
        </ul>
    </div><!--End .sideBar-->
</nav>