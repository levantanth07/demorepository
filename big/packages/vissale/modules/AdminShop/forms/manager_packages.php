<?php

class ManagerPackageForm extends Form
{

    function __construct()
    {
        Form::Form('ManagerPackageForm');

        if (URL::get('do') == 'save_new' && !empty($_POST)) {
            $row = [];
            $data['success'] = false;
            $except = ['form_block_id'];
            foreach ($_POST as $key => $value) {
                if (!in_array($key, $except)) {
                    $row[$key] = $value;
                    if ($key == 'price') {
                        $row[$key] = str_replace(',', '', $value);
                    }

                    if ($key == 'percent_discount' || $key == 'weight') {
                        $row[$key] = !empty($value) ? $value : 0;
                    }
                }
            }

            try {
                $id = DB::insert('acc_packages', $row);
                $data['success'] = true;
            } catch (Exception $e) {
                
            }

            echo json_encode($data); die();
        }

        if (URL::get('do') == 'save_edit' && !empty($_POST) && Url::post('id')) {
            $row = [];
            $data['success'] = false;
            $id = Url::post('id');
            $except = ['form_block_id', 'id'];
            foreach ($_POST as $key => $value) {
                if (!in_array($key, $except)) {
                    $row[$key] = $value;
                    if ($key == 'price') {
                        $row[$key] = str_replace(',', '', $value);
                    }
                }
            }

            try {
                DB::update_id('acc_packages', $row, $id);
                $data['success'] = true;
            } catch (Exception $e) {
                
            }

            echo json_encode($data); die();
        }

        if (URL::get('do') == 'delete' && Url::post('id')) {
            $data['success'] = false;
            try {
                DB::delete_id('acc_packages', Url::post('id'));
                $data['success'] = true;
            } catch (Exception $e) {
                
            }

            echo json_encode($data); die();
        }

        if (URL::get('do') == 'modal_edit_address' && Url::get('id')) {
            $html = '';
            $item = DB::fetch("SELECT * FROM acc_packages WHERE id=" . Url::get('id'));
            if (!empty($item)) {
                $html = $this->showModalEdit($item);
            }

            echo $html; die();
        }
    }

    function draw()
    {
        if(!can_tuha_administrator()){
            Url::access_denied();
        }
        $data = [];
        $data['title'] = 'Quản lý gói cước';
        $packages = AdminShopDB::getPackages();
        $data['acc_packages'] = $packages;

        $this->parse_layout('manager_packages', $data);
    }

    function showModalEdit($item)
    {
        $html = '
            <div class="modal fade" id="modal-edit-package" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <form action="" id="form-edit-package" name="form_add_address">
                        <input type="hidden" name="id" value="'. $item['id'] .'">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Sửa gói cước</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Tên gói cước (*)</label>
                                    <input type="text" class="form-control name" name="name" value="'. $item['name'] .'" placeholder="Tên gói cước (*)" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Cước phí/tháng (*)</label>
                                    <input type="text" class="form-control price" name="price" value="'. number_format($item['price']) .'" placeholder="Cước phí/tháng (*)" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Số tháng (*)</label>
                                    <input type="number" class="form-control number_months" value="'. $item['number_months'] .'" name="number_months" placeholder="Số tháng (*)" required>
                                </div>
                                <div class="form-group">
                                    <label for="">% chiết khấu</label>
                                    <input type="number" class="form-control percent_discount" name="percent_discount" value="'. $item['percent_discount'] .'" placeholder="% chiết khấu">
                                </div>
                                <div class="form-group">
                                    <label for="">Số user tối đa (*)</label>
                                    <input type="text" class="form-control max_user" name="max_user" value="'. $item['max_user'] .'" placeholder="Số user tối đa (*)" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Số page tối đa (*)</label>
                                    <input type="text" class="form-control max_page" name="max_page" value="'. $item['max_page'] .'" placeholder="Số page tối đa (*)" required>
                                </div>
                                <div class="form-group">
                                    <label for="">Thứ tự hiển thị</label>
                                    <input type="number" class="form-control" id="weight" name="weight" value="'. $item['weight'] .'" placeholder="Thứ tự">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Trở lại</button>
                                <button type="submit" class="btn btn-primary">Hoàn thành</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        ';

        return $html;
    }
}
