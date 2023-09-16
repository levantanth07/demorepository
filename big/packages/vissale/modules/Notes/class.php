<?php

class Notes extends Module
{

    function __construct($row)
    {
        Module::Module($row);
        require_once 'packages/vissale/lib/php/vissale.php';
        require_once 'db.php';

        switch (Url::get('cmd')) {
            case "pin_note":
                $this->pin_note();
            break;
            case "show_modal_note":
                $this->show_modal_note();
            break;
            case "update_note":
                $this->update_note();
            break;
            case "delete_note":
                $this->delete_note();
            break;
            case 'ajax_load_data':
                $this->ajax_load_data();
            break;
            default:
                require_once "forms/note.php";
                $this->add_form(new NoteForm());
                break;
        }
    }

    function ajax_load_data()
    {
        $data = ['success' => false];
        try {
            if (Url::get('page')) {
                $html = '';
                $notes = NotesDB::getNotesByUserId(Url::iget('page'));
                if (!empty($notes)) {
                    foreach ($notes as $note) {
                        $html .= '
                            <div class="grid-item masonry-brick">
                                <div class="box-google-keep masonry-content" data-id="'. $note['id'] .'" title="Click để sửa">
                                    <div class="text-ellipsis">'. $note['content'] .'</div>
                                    <div class="masonry-footer text-right">Đã chỉnh sửa '. date('d-m-Y H:i:s', strtotime($note['updated_at'])) .'</div>
                                    <a href="javascript:void(0)" title="Ghim ghi chú" class="pins" data-toggle="tooltip" data-placement="bottom" data-id="'. $note['id'] .'" data-pin="1"><i class="fa fa-bookmark-o"></i></a>
                                    <a href="javascript:void(0)" title="Xóa ghi chú" class="btn-delete-note" data-toggle="tooltip" data-placement="top" data-id="'. $note['id'] .'"><i class="fa fa-trash-o"></i></a>
                                </div>
                            </div>
                        ';
                    }
                }

                $data = [
                    'success' => true,
                    'html' => $html
                ];
            }
        } catch (Exception $e) {
            
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE); die();
    }

    function show_modal_note()
    {
        $data['success'] = false;
        if (Url::iget('id')) {
            $id = Url::iget("id");
            $record = DB::fetch("SELECT id, title, content, is_pin FROM notes WHERE id = $id");
            $checked = ($record['is_pin'] == 1) ? "checked" : "";
            $html = '
                <div class="modal fade" tabindex="-1" role="dialog" id="notes-modal">
                    <div class="modal-dialog" role="document">
                        <form action="#" method="POST" accept-charset="utf-8" id="frm-notes-modal">
                            <input type="hidden" id="note_id" value="'. $record['id'] .'">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <label for="">Tiêu đề</label>
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Tiêu đề" value="'. $record['title'] .'">
                                </div>
                                <div class="modal-body">
                                    <label for="">Nội dung ghi chú</label>
                                    <textarea name="content" class="form-control" required id="ckeditor">'. $record['content'] .'</textarea>
                                    <label><input type="checkbox" class="pin-note" value="1" '. $checked .'> Ghim ghi chú</label>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng lại</button>
                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            ';
            $data = [
                'success' => true,
                'html' => $html
            ];
        }

        echo json_encode($data); exit();
    }

    function pin_note()
    {
        $data['success'] = false;
        if (Url::iget('id') && Url::iget('is_pin')) {
            DB::update_id('notes', [
                'is_pin' => Url::iget('is_pin'),
                'updated_at' => date("Y-m-d H:i:s")
            ], Url::iget('id'));
            $data['success'] = true;
        }

        echo json_encode($data); exit();
    }

    function update_note()
    {
        $data['success'] = false;
        if (Url::get('id')) {
            $rows = [];
            $rows['updated_at'] = date("Y-m-d H:i:s");
            if (Url::get("title")) {
                $rows['title'] = Url::get("title");
            }

            if (Url::get("content")) {
                $rows['content'] = Url::get("content");
            }

            if (Url::get("is_pin")) {
                $rows['is_pin'] = Url::get("is_pin");
            }

            DB::update_id('notes', $rows, Url::iget('id'));
            $data['success'] = true;
        }

        echo json_encode($data); exit();
    }

    function delete_note()
    {
        $data['success'] = false;
        if (Url::iget('id')) {
            DB::delete_id('notes',  Url::iget('id'));
            $data['success'] = true;
        }

        echo json_encode($data); exit();
    }
}