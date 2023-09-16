<div class="nav-search-bound">
	<form name="search" method="post" action="<?php echo Url::build('tim-kiem')?>">
    <div class="nav-search-field">
        <input name="keyword" type="text" id="keyword" />
    </div>
    <div class="nav-search-button">
    	<input name="submit" type="submit" id="submit" value="<?php echo Portal::language('search');?>" />
    </div>
    </form>
</div>