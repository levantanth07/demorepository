<style type="text/css">
.adress{ text-align:center;color:#686868;margin-bottom:20px;}
.adress span{ font-weight:bold;color:#686868;}
</style>
<div class="adress"><span><?php echo Portal::language('address')?>: </span><?php if(Portal::language()==1) {echo Portal::get_setting('company_address');} else {echo Portal::get_setting('company_address_en');} ?></div>
