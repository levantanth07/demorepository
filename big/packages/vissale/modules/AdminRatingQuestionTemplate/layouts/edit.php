<div style="display:none">
	<div id="mi_rating_question_template_sample">
        <div id="mi_rating_question_template_#xxxx#">
            <div id="input_group_#xxxx#" class="row" style="padding-bottom:5px;margin-bottom:5px;">
                <div class="col-xs-1 hidden">
                   <input  type="checkbox" id="_checked_#xxxx#" tabindex="-1">
                </div>
                <div class="col-xs-2">
                    <input  name="mi_rating_question_template[#xxxx#][position]" class="form-control" type="text" id="position_#xxxx#" placeholder="Tự động">
                    <input  name="mi_rating_question_template[#xxxx#][id]" type="hidden" id="id_#xxxx#" class="form-control"  value="(auto)" tabindex="-1" readonly>
                </div>
                <div class="col-xs-6">
                    <input  name="mi_rating_question_template[#xxxx#][content]" class="form-control" type="text" id="content_#xxxx#">
                </div>
                <div class="col-xs-3">
                    <a class="btn btn-default btn-sm" href="#" onClick="if(confirm('Nhấn xóa xong bạn phải lưu lại. Bạn có chắc muốn xóa?')){mi_delete_row(getId('input_group_#xxxx#'),'mi_rating_question_template','#xxxx#','');}return false;" title="Xóa"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                </div>
            </div>
        </div>
	</div>
</div>
<?php $title = (URL::get('cmd')=='delete')?'Xóa Mẫu câu hỏi mẫu / CKSH':' Danh sách câu hỏi mẫu';?>
<br>
<div class="container">
    <form name="EditAdminRatingQuestionTemplateForm" id="EditAdminRatingQuestionTemplateForm" method="post" enctype="multipart/form-data" class="form-inline">
        <div class="box box-default">
            <div class="box-header with-border">
                <div class="box-title">
                    <i class="fa fa-question-circle"></i><?php echo $title;?>
                </div>
                <div class="box-tools pull-right">
                    <div class="form-group">
                        <input name="keyword" type="text" id="keyword" class="form-control" placeholder="Tìm kiếm" onchange="EditAdminRatingQuestionTemplateForm.submit();">
                    </div>
                    <div class="form-group">
                        <input  name="save" type="submit" class="btn btn-primary" value="Lưu">
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php if(Form::$current->is_error())
                        {
                            ?>
                            <div><?php echo Form::$current->error_messages();?></div>
                            <?php
                        }
                        ?>
                        <input  name="selected_ids" type="hidden" value="<?php echo URL::get('selected_ids');?>">
                        <input  name="deleted_ids" id="deleted_ids" type="hidden" value="<?php echo URL::get('deleted_ids');?>">
                        <div class="multi-item-wrapper">
                            <div id="mi_rating_question_template_all_elems">
                                <div class="row">
                                    <div class="col-xs-1 hidden"><input type="checkbox" value="1" onclick="mi_select_all_row('mi_rating_question_template',this.checked);"></div>
                                    <div class="col-xs-2"><label>Vị trí xuất hiện</label></div>
                                    <div class="col-xs-6"><label>Nội dung câu</label></div>
                                    <div class="col-xs-3"><label>Xóa</label></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right small">Nhấn <strong>Enter</strong> hoặc <input type="button" value="+ Thêm" class="btn btn-warning btn-sm" onclick="mi_add_new_row('mi_rating_question_template');"></div>
                        <div>[[|paging|]]</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
mi_init_rows('mi_rating_question_template',<?php if(isset($_REQUEST['mi_rating_question_template'])){echo MiString::array2js($_REQUEST['mi_rating_question_template']);}else{echo '[]';}?>);
$(document).keypress(function(e) {
    if(e.which == 13) {
        mi_add_new_row('mi_rating_question_template');
        $('#mi_rating_question_template_'+input_count).css({'background-color':'#c8fcff'});
        return false;
    }
});
</script>
