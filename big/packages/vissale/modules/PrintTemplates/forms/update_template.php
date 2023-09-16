<?php

class UpdateTemplateForm extends Form
{

    function __construct()
    {
        Form::Form('UpdateTemplateForm');
        if (!User::is_admin()) {
            URL::access_denied();
        }
    }

    function on_submit()
    {
        $groups = Url::post('group_id');
        if (!empty($groups)) {
            mysqli_begin_transaction(DB::$db_connect_id);
            try {
                foreach ($groups as $group_id) {
                    $old_templates = PrintDB::getOldPrintTemplates($group_id);
                    foreach ($old_templates as $old_template) {
                        $paper_size = 'A4-A5';
                        if ($old_template['template'] == 1) {
                            $paper_size = 'A4IN8';
                        }

                        $template = PrintDB::getTemplateByGroupId($group_id, $paper_size);
                        $template_system = PrintDB::getTemplateDataSystem($paper_size);
                        if (!empty($template_system)) {
                            $arr_replaces = [
                                '{__TEN_BAN_IN__}' => $old_template['print_name'],
                                '{__TEN_NGUOI_GUI__}' => $old_template['print_name'],
                                '{__SDT_NGUOI_GUI__}' => $old_template['print_phone'],
                                '{__DIA_CHI_NGUOI_GUI__}' => $old_template['print_address'],
                            ];
                            $data = $template_system['data'];
                            foreach ($arr_replaces as $k => $replace) {
                                $data = str_replace($k, $replace, $data);
                            }

                            if (empty($template)) {
                                DB::insert('prints_templates', [
                                    'group_id' => $group_id,
                                    'template_id' => $template_system['id'],
                                    'data' => $data,
                                    'type' => 1,
                                    'paper_size' => $paper_size,
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            } else {
                                DB::update_id('prints_templates', [
                                    'template_id' => $template_system['id'],
                                    'data' => $data,
                                    'updated_at' => date('Y-m-d H:i:s')
                                ], $template['id']);
                            }
                        }
                        /*if (empty($template)) {
                            $template_system = PrintDB::getTemplateDataSystem($paper_size);
                            if (!empty($template_system)) {
                                $arr_replaces = [
                                    '{__TEN_BAN_IN__}' => $old_template['print_name'],
                                    '{__TEN_NGUOI_GUI__}' => $old_template['print_name'],
                                    '{__SDT_NGUOI_GUI__}' => $old_template['print_phone'],
                                    '{__DIA_CHI_NGUOI_GUI__}' => $old_template['print_address'],
                                ];
                                $data = $template_system['data'];
                                foreach ($arr_replaces as $k => $replace) {
                                    $data = str_replace($k, $replace, $data);
                                }

                                DB::insert('prints_templates', [
                                    'group_id' => $group_id,
                                    'template_id' => $template_system['id'],
                                    'data' => $data,
                                    'type' => 1,
                                    'paper_size' => $paper_size,
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }*/
                    }
                }

                mysqli_commit(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
                Url::js_redirect(true, "Dữ liệu đã được cập nhật...", array('cmd' => "update_template"));
            } catch (Exception $e) {
                mysqli_rollback(DB::$db_connect_id);
                mysqli_close(DB::$db_connect_id);
                echo "Lỗi! Vui lòng thử lại sau."; die;

            }
        }
        // System::debug($groups);
    }

    function draw()
    {
        $data = [];
        $data['title'] = 'Chuyển đổi mẫu in sang phiên bản mới';
        $old_templates = PrintDB::getGroupsHasTemplate();
        $data['old_templates'] = $old_templates;

        $this->parse_layout('update_template', $data);
    }
}