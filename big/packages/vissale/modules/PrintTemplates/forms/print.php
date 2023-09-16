<?php

class PrintForm extends Form
{

    function __construct()
    {
        Form::Form('PrintForm');
        $this->link_js('assets/vissale/bootstrap-dialog/bootstrap-dialog.min.js');
        $this->link_css('assets/vissale/bootstrap-dialog/bootstrap-dialog.min.css');
        $this->link_js('assets/standard/ckeditor/ckeditor.js');
        $this->link_js('assets/standard/ckeditor/config.js');
        $this->link_js('assets/standard/ckeditor/plugins/templateconsignment/plugin.js');
        $this->link_js('assets/standard/ckeditor/plugins/quicktable/plugin.js');
        $this->link_js('assets/standard/ckeditor/plugins/tableresize/plugin.js');
    }

    function on_submit()
    {
        $user_id = get_user_id();
        $group_id = Session::get('group_id');
        $type = Url::post('type_id');
        $cond = [];
        $rows = [
            'data' => Url::post('data'),
            'type' => $type,
            'group_id' => $group_id,
            'user_modified' => $user_id
        ];
        $cond[] = "type = $type";
        if (!User::is_admin()) {
            $cond[] = "AND group_id = $group_id";
        }

        if (Url::post('paper_size')) {
            $rows['paper_size'] = Url::post('paper_size');
            $cond[] = "AND paper_size = '". Url::post('paper_size') ."'";
        }

        if (Url::post('is_system')) {
            $rows['is_system'] = 1;
            $cond[] = "AND is_system = 1";
        } else {
            if (Url::post('template_id')) {
                $rows['template_id'] = Url::post('template_id');
            }
        }

        $is_default = 2;
        if (Url::post('is_default')) {
            $rows['is_default'] = Url::post('is_default');
        }

        $location = 'print-templates';
        $cond = implode(" ", $cond);
        $original_template = PrintDB::checkExistsPrintTemplate($cond);
        DB::update('prints_templates', ["is_default" => 2], "group_id = $group_id");
        try {
            if ($original_template) {
                $rows['updated_at'] = date('Y-m-d H:i:s');
                DB::update_id('prints_templates', $rows, $original_template['id']);
            } else {
                $rows['user_id'] = $user_id;
                DB::insert('prints_templates', $rows);
            }

            Url::js_redirect($location,'Bạn đã cập nhật mẫu in thành công.', array(
                'type' => Url::get('type'),
                'paper_size' => Url::post('paper_size')
            ));
        } catch (Exception $e) {
            Url::js_redirect($location,'Có lỗi xảy ra. Bạn vui lòng thử lại sau.', array(
                'type' => Url::get('type'),
                'paper_size' => Url::post('paper_size')
            ));
        }
    }

    function draw()
    {
        $data = [];
        $data['title'] = 'Mẫu in';
        $group_id = Session::get('group_id');
        $cond = [];
        $cond_system = [];
        $cond[] = "group_id = $group_id";
        $print_types = prints_type();
        if (!Url::get('type')) {
            $_REQUEST['type'] = 'DON_HANG';
        }

        $type = Url::get('type');
        $data['print_types'] = $print_types;
        $data['type'] = $type;
        $type_id = $print_types[$type]['id'];
        $cond[] = "AND type = $type_id";
        $cond_system[] = "type = $type_id";

        $data['type_id'] = $type_id;
        $print_groups = prints_group();
        $print_constants = prints_constant();
        $constants = [];
        foreach ($print_constants as $key => $value) {
            if (in_array($type, $value['types'])) {
                $constants[$key] = $value;
            }
        }

        $print_paper_sizes = prints_paper_sizes();
        $paper_sizes = [];
        foreach ($print_paper_sizes as $key => $value) {
            if (in_array($type, $value['types'])) {
                $paper_sizes[$key] = $value;
            }
        }

        $data['paper_sizes'] = $paper_sizes;
        if (!empty($paper_sizes) && !Url::get('paper_size')) {
            $_REQUEST['paper_size'] = key($paper_sizes);
        }

        if (!empty($_REQUEST['paper_size'])) {
            $cond[] = "AND paper_size = '". $_REQUEST['paper_size'] ."'";
            $cond_system['paper_size'] = "AND paper_size = '". $_REQUEST['paper_size'] ."'";
        }

        $cond_system[] = "AND is_system = 1";
        $template_id = '';
        $template_system_obj = PrintDB::checkExistsPrintTemplate(implode(" ", $cond_system));
        if (empty($template_system_obj)) {
            unset($cond_system['paper_size']);
            $template_system_obj = PrintDB::checkExistsPrintTemplate(implode(" ", $cond_system));
        }

        $template_system = '';
        if (!empty($template_system_obj)) {
            $template_system = $template_system_obj['data'];
            $template_id = $template_system_obj['id'];
        }

        $data['constants'] = $constants;
        $data['print_groups'] = $print_groups;
        $nomalItems = [];
        $intervalItems = [];
        foreach ($constants as $key => $value) {
            foreach ($value['variables'] as $k => $v) {
                $k_item = str_replace(['{', '}'], '', $k);
                if (empty($v['is_interval'])) {
                    $nomalItems[$k_item] = $this->compress_html($v['content']);
                } else {
                    foreach ($v['data'] as $k_i => $v_interval) {
                        $intervalItems[$k_i][$k_item] = $v_interval;
                    }
                }
            }
        }

        $template = $template_system;
        $template_obj = PrintDB::checkExistsPrintTemplate(implode(" ", $cond));
        $is_default = 2;
        if (!empty($template_obj)) {
            $template = $template_obj['data'];
            $is_default = $template_obj['is_default'];
        }

        $data['is_default'] = $is_default;
        $page_variables = [
            'consignmentExamples' => [
                'nomalItems' => $nomalItems,
                'intervalItems' => $intervalItems
            ],
            'templates' => $this->compress_html($template_system)
        ];
        $data['page_variables'] = json_encode($page_variables, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $data['template'] = $this->compress_html($template);
        $data['template_id'] = $template_id;

        $this->parse_layout('print', $data);
    }

    function compress_html($string) {
        return preg_replace(
            array(
                '/ {2,}/',
                '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'
            ),
            array(
                ' ',
                ''
            ),
            $string
        );
    }
}