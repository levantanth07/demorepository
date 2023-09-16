<style>
    .select2{width: 100% !important;}
    #columnOptionModal .ui-sortable-helper{border:1px dotted #0b97c4;background-color: #EFEFEF;cursor: move}
    #columnOptionModal .ui-sortable-handle:hover{border:1px dotted #0b97c4;background-color: #EFEFEF;cursor: move}
    #columnOptionModal  .list-group .list-group-item{padding:5px 10px 5px 10px !important;min-height: 30px;}
    span.user {font-weight: bold; }
    span.at_time,span.at_day {font-style: italic; }
</style>
<script src="assets/vissale/js/jquery-sortable-min.js"></script>
<link rel="stylesheet" type="text/css" href="/assets/vissale/css/AdminUserInfo/style.css">
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 style="margin: 0">Tùy chọn cột xuất excel đơn hàng</h4>
                </div>
                <div class="panel-body">
                    <ul class="list-group" id="sortable" style="max-height: 600px; overflow-y: scroll;">
                        <?php foreach($this->map['columns'] as $position => $column): ?>
                           
                        <li class="list-group-item<?=!empty($column['selected']) ? ' ui-state-default' : ''?>" 
                            id="column_<?=$position;?>" 
                            lang="<?=$column['name']?>:<?=$column['id']?>">
                            <i class="fa fa-arrows-v"></i> 
                            <i class="fa fa-align-justify"></i> 
                            <input type="checkbox" id="column_<?=$column['id']?>" onclick="onClickCheckBox(this)" data-order="<?=$column['order']?>" data-id="<?=$column['id']?>" data-name="<?=$column['name']?>" data-selected="<?=$column['selected'] ? 'true' : 'false'?>"  <?=$column['selected'] ? 'checked' : '';?>> <?=$column['name']?>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="notify">
                    <?php if($this->map['option']): ?>
                        <span class="status">
                            <?=$this->map['option']['updated_at'] ? 'Cập nhật' : 'Thêm mới' ?>
                        </span>
                        bởi 
                        <span class="user">
                            <?=$this->map['option']['value']['username']?>
                        </span>
                        lúc 
                        <span class="at_time"><?=date('H:i', $this->map['time'])?></span>
                        ngày
                        <span class="at_day"><?=date('d/m/Y', $this->map['time'])?></span>
                    <?php endif;?>

                    <div class="pull-right text-warning">
                        Kéo thả để đổi vị trí cột
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $( "#sortable" ).sortable({
            axis: 'y',
            update: update
        });
    })
    function onClickCheckBox(el)
    {   
        el.dataset.selected = el.checked
        update();
    }

    function update()
    {   
        let postData = $('#sortable')
            .find('input')
            .filter(function(index, el){
                return el.dataset.selected == 'true'
            })
            .map(function(index, el) {
                return el.dataset.id;
            })
            .toArray()
            .join(',');

        $.ajax({
            url: '/index062019.php?page=admin_group_info&do=manager_columns_export_excel',
            data: {
                form_block_id: <?=Module::block_id()?>,
                data: postData
            },
            type: 'POST',
        })
        .done(function(data){
            if(data.message === 'MIN_COLUMN_1'){
                return alert('Số cột tối thiểu phải là 1');
            }
            if(data.status !== 'success'){
                return alert('Lỗi sắp xếp cột. Bạn vui lòng kiểm tra lại kết nối!');
            }
        })
        .error(function(error){
            alert('Lỗi sắp xếp cột. Bạn vui lòng kiểm tra lại kết nối!');
                location.reload();
        });
    }

</script>