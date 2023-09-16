<div class="container full">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="<?=Url::build('admin_orders')?>">Đơn hàng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Đồng bộ Fanpage</li>
            <li class="pull-right">
                <div class="pull-right">
                    
                </div>
            </li>
        </ol>
    </nav>
    <div class="box" id="vichatPageListWrapper">
        <div class="box-header">
            <h3 class="box-title">Đồng bộ Fanpage</h3>
        </div>
        <div class="box-body">
            <div class="alert alert-info">Bạn đã đồng bộ vẫn có thể đồng bộ lại để cập nhật thống tin mới nhất từ Fanpage.</div>
            <ul id="vichatPageList" class="list-group">

            </ul>
        </div>
    </div>
</div>
<!--IF:admin_cond(Session::get('admin_group') or User::is_admin())-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.10.1/bootstrap-table.min.js"></script>
<script type="text/javascript">
    let pages = [[|pages|]];
    console.log(pages);
    jQuery(document).ready(function(){
        getvichatPageList();
    });
    function getvichatPageList(){
        let importBtn;
        $.ajax({
            type: "GET",
            url: 'https://api-vichat.tuha.vn/api/pages/get-by-group?shop_id=<?php echo Session::get('group_id');?>',
            data: '',
            cache: false,
            success: function (JSON) {
                let count = 0;
                for(let i in JSON){
                    count++;
                    let pageId = JSON[i].id;
                    let pageName = JSON[i].name;
                    importBtn = ' <a href="#" onclick="importVichatPage(this,\''+pageId+'\',\''+pageName+'\');return false;" class="btn btn-success pull-right">Đồng bộ</a>';
                    if(pages[pageId]){
                        importBtn += ' <span class="btn btn-default pull-right">(Đã đồng bộ)</span>';
                    }
                    jQuery('#vichatPageList').append('<li class="list-group-item">'+count+'. '+pageId+' - <a target="_blank" href="https://facebook.com/'+pageId+'">'+ pageName + '</a>' +importBtn+'</li>');
                }
            }
        });
    }
    function importVichatPage(obj,pageId,pageName){
        $.ajax({
            method: "POST",
            url: 'form.php?block_id=<?php echo Module::block_id(); ?>',
            data : {
                'cmd':'import_vichat_page',
                'page_id': pageId,
                'name':pageName
            },
            beforeSend: function(){
                jQuery(obj).html('Đang tải ...');
            },
            success: function(content){
                console.log(content);
                eval("var r="+content);
                if(r.RESULT==0){
                    alert('Có lỗi xảy ra. Vui lòng kiểm tra lại mạng internet hoặc F5 để tao tác lại');
                }else{
                    $.notify({message: 'Đồng bộ thành công...!' },{type: 'warning'});
                    jQuery(obj).html('Đã đồng bộ');
                    jQuery(obj).removeClass('btn btn-success');
                    jQuery(obj).addClass('btn btn-default');
                }
            }
        });
    }
</script>
<!--/IF:admin_cond-->
</div>