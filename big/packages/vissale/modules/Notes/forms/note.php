<?php

class NoteForm extends Form
{

    function __construct()
    {
        Form::Form('NoteForm');
        $this->link_js('assets/standard/ckeditor/ckeditor.js');
        if (Url::get("cmd") == "add_note" && Url::post("content")) {
            $data['success'] = false;
            try {
                $user_id = get_user_id();
                $rows = [
                    "content" =>  DB::escape(str_replace(["\r\n", "\n", "\r"], '', Url::post('content'))),
                    "user_id" => $user_id,
                    "updated_at" => date("Y-m-d H:i:s")
                ];
                if (Url::post('title')) {
                    $rows['title'] = DB::escape(Url::post('title'));
                }
                
                DB::insert("notes", $rows);
                $data['success'] = true;
            } catch (Exception $e) {
                $data['error'] = "Lỗi tạo ghi chú.";
            }

            echo json_encode($data); die();
        }
    }

    function on_submit()
    {
        try {
            $user_id = get_user_id();
            $rows = [
                "content" => DB::escape(str_replace(["\r\n", "\n", "\r"], '', Url::post('content'))),
                "user_id" => $user_id,
                "updated_at" => date("Y-m-d H:i:s")
            ];
            DB::insert("notes", $rows);
            Url::redirect_url();
        } catch (Exception $e) {
            Url::js_redirect(true, "Có lỗi xảy ra. Bạn vui lòng thử lại sau");
        }
    }

    function draw()
    {
        $data = [];
        $data['title'] = "Ghi chú";
        $user_id = get_user_id();
        $note_no_pins = NotesDB::getNotesByUserId();
        $note_pins = NotesDB::getNotesPins($user_id);
        $data['note_no_pins'] = $note_no_pins;
        $data['note_pins'] = $note_pins;

        $this->parse_layout("note", $data);
    }
}