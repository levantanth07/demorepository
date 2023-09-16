<?php 
    $miBundles = MiString::array2js($this->map['bundles']);
    $parentBundles = MiString::array2js($this->map['parent_bundles']);
    $bundleStatuses = MiString::array2js($this->map['filter_status_list']);
    $filterStatus = $this->map['filter_status'];
    $filterParentId = $this->map['filter_parent_id'];
?>

<link href="assets/lib/select2/select2.min.css?v=15022020" rel="stylesheet" />
<script src="assets/lib/select2/select2.js"></script>
<script>
    var miBundles = <?php echo $miBundles; ?>;
    const parentBundles = <?php echo $parentBundles; ?>;
    const filterStatus = '<?php echo $filterStatus; ?>';
    const filterParentId = '<?php echo $filterParentId; ?>';
    const bundleStatuses = <?php echo $bundleStatuses; ?>;
    const currentUrl = window.location.href;
</script>

<style>
    .d-flex {
        display: flex;
    }

    .w-100 {
        width: 100% !important;
    }
</style>

<div class="container-fluid" style="padding: 30px 0">
    <div class="row">
        <div class="col-xs-12">
            <form name="EditAdminSystemBundlesForm"
                id="EditAdminSystemBundlesForm"
                method="post"
                action="<?=Url::build('admin_system_bundles') . '&lv=2';?>"
                enctype="multipart/form-data"
                class="form-inline">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong style="line-height: 30px">[[|title|]]</strong>
                        <div class="pull-right">
                            <button type="button" type="button" data-toggle="modal" data-action="create" 
                                class="jsElShowBundleModal btn-primary btn" data-target="#editModal">
                                <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                Thêm mới
                            </button>
                        </div>
                    </div>
                    <div class="panel-heading mb-3">
                        <div class="row mb-5">
                            <div class="col-md-3">
                                <label for="">Tên nhóm cấp 2</label>
                                <input name="keysearch" value="[[|keysearch|]]" type="text" placeholder="Tìm kiếm theo tên nhóm cấp 2" class="form-control" >
                                <input type="hidden" name="lv" value="2">
                            </div>
                            <div class="col-md-6">
                                <label for="jsElFilterParents">Nhóm cấp 1</label>
                                <select id="jsElFilterParents" name="filter_parent_id"  class="form-control w-100" require></select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_status">Trạng thái</label>
                                <select name="filter_status" class="form-control w-100" id="filter_status"></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body form-inline">
                        <?= Form::$current->is_error() && Form::$current->error_messages(); ?>
                        <table class="table table-hover" >
                            <thead>
                                <tr>
                                    <td class="col-xs-2"><label>ID</label></div>
                                    <td class="col-xs-3"><label>Nhóm sản phẩm cấp 2</label></div>
                                    <td class="col-xs-3"><label>Nhóm sản phẩm cấp 1</label></div>
                                    <td class="col-xs-2"><label>Hiệu lực</label></div>
                                    <td class="col-xs-2"><label>Tiện ích</label></div>
                                </tr>
                            </thead>
                            <tbody id="jsElBundles"></tbody>
                        </table>
                        <div>[[|paging|]]</div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editBundleModal" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBundleModal"></h5>
                <button type="button" class="close jsElActionCloseEditModal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <b>Tên nhóm sản phẩm cấp 2</b>
                </p>
                <input type="hidden" id="jsElEditBundleId" require>
                <input type="hidden" id="jsElAction" require>
                <p>
                    <input type="text" id="jsElEditBundleName" class="form-control" require/>
                </p>
                <p>
                    <b>Chọn nhóm sản phẩm cấp 1</b>
                </p>
                <p>
                    <select id="jsElEditParentBundles" class="form-control  w-100" require></select>
                </p>
                <p>
                    <label>
                        <input type="checkbox" id="jsElEditBundleStatus" value="1"/>  Hiệu lực
                    </label>
                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary jsElActionCloseEditModal" data-dismiss="modal">Đóng</button>
                <button type="button" id="jsElSubmitBundleModal" class="btn btn-success">
                    <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                    Lưu
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function renderBundles() {
        let elBundles = $('#jsElBundles');
        let html = '';
        for (const bundleId in miBundles) {
            let _bundle = miBundles[bundleId];
            let bundleName = _bundle.name;
            let bundleStatus = bundleStatuses[_bundle.status];
            let bundleParent = _bundle.parent_name;
            let bundleParentId = _bundle.parent_id;
            html += fetchRowBundle(bundleId, bundleName, bundleStatus, bundleParent, bundleParentId);
        }//end for

        elBundles.html(html);
    }
    function renderParentBundleOptions(elName) {
        let elEditParentBundles = $(elName);
        let html = " <option value='' selected>--- Tất cả --- </option>";
        for (const parentBundleId in parentBundles) {
            let _bundle = parentBundles[parentBundleId];
            let bundleName = _bundle.name;
            html += fetchOptions(parentBundleId, bundleName);
        }//end for

        elEditParentBundles.html(html);
    }

    function renderStatusOptions(elName) {
        let el = $(elName);
        let html = "<option value='-1' selected>--- Tất cả --- </option>";
        for (const key in bundleStatuses) {
            let name = bundleStatuses[key];
            html += `<option value='${key}'>${name}</option>`;
        }//end for

        el.html(html);
    }

    
    function fetchOptions(bundleId, bundleName) {
        return `
            <option value='${bundleId}'>${bundleName}</option>
        `;
    }

    function fetchRowBundle(bundleId, bundleName, bundleStatus, bundleParentName, bundleParentId) {
        return `
            <tr id="jsElBundle${bundleId}" class="jsElRowBundle">
                <td class="col-xs-2">${bundleId}</td>
                <td class="col-xs-3" id="jsElBundle${bundleId}Name">${bundleName}</td>
                <td class="col-xs-3" id="jsElBundle${bundleId}Parent">${bundleParentName}</td>
                <td class="col-xs-2" id="jsElBundle${bundleId}Status">${bundleStatus}</td>
                <td class="col-xs-2">
                    <button type="button" data-toggle="modal" data-action="edit" class="jsElShowBundleModal" 
                        data-bundle="${bundleId}" data-target="#editModal">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="jsElDeleteBundle" data-parent="${bundleParentId}" data-bundle="${bundleId}">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function clearEditModal() {
        return fillDataEditModal(null, null, true, null, null);
    }

    function fillDataEditModal(
        bundleId = null,
        bundleName = null,
        bundleStatus = true,
        bundleParentId = null,
        action = null
    ) {
        $('#jsElEditBundleId').val(bundleId);
        $('#jsElEditBundleName').val(bundleName);
        $('#jsElEditBundleStatus').prop('checked', bundleStatus);
        $('#jsElEditParentBundles').val(bundleParentId).change();
        $('#jsElAction').val(action);
    }

    function changeDisplayData(bundleId, bundleName, bundleStatusName, parentName, parentId) {
        $(`#jsElBundle${bundleId}Name`).html(bundleName);
        $(`#jsElBundle${bundleId}Status`).html(bundleStatusName);
        $(`#jsElBundle${bundleId}Parent`).html(parentName);
        $(`#jsElBundle${bundleId} .jsElDeleteBundle`).data('parent', parentId);
    }

    function unsetAndRemoveRow(bundleId) {
        $(`#jsElBundle${bundleId}`).remove();
    }

    function changeLocalData(bundleId, bundleName, bundleStatus, parentName, parentId) {
        miBundles[bundleId].name = bundleName;
        miBundles[bundleId].status = bundleStatus;
        miBundles[bundleId].parent_name = parentName;
        miBundles[bundleId].parent_id = parentId;
    }

    function addLocalData(bundleId, bundleName, bundleStatus, parentName, parentId)
    {
        miBundles[bundleId] = {
            id: bundleId,
            name: bundleName,
            status: bundleStatus,
            parent_name: parentName,
            parent_id: parentId,
        };
    }

    function initEditBundleModal() {
        const self = $(this);
        let action = self.data('action');
        let elEditBundleModal = $('#editBundleModal');
        if (action === 'create') {
            elEditBundleModal.html('Thêm nhóm sản phẩm cấp 2');
            return fillDataEditModal(null, null, true, null, action);
        }//end if

        elEditBundleModal.html('Chỉnh sửa nhóm sản phẩm cấp 2');
        let bundleId = self.data('bundle');
        if (!bundleId) {
            return;
        }//end if

        let bundle = miBundles[bundleId];
        let status = false;
        if (parseInt(bundle.status) > 0) {
            status = true;
        }//end if

        return fillDataEditModal(bundle.id, bundle.name, status, bundle.parent_id, action);
    }

    function appendElBundles(_html) {
        let elBundles = $('#jsElBundles');
        elBundles.html( _html + elBundles.html());
    }

    function handleSubmitModal() {
        let bundleId = $('#jsElEditBundleId').val();
        let bundleName = $('#jsElEditBundleName').val();
        if (!bundleName) {
            return alert('Vui lòng điền tên nhóm sản phẩm');
        }//end if

        let bundleParentId = parseInt($('#jsElEditParentBundles').val());
        if (!bundleParentId) {
            return alert('Vui lòng lựa chọn nhóm sản phẩm cấp 1');
        }//end if

        let bundleStatus = $('#jsElEditBundleStatus').prop('checked') ? 1 : 0;
        let bundleStatusName = bundleStatuses[bundleStatus];
        let bundleParentName = parentBundles[bundleParentId].name;
        let action = $('#jsElAction').val();

        let url = currentUrl + '&action=' + action;
        let data = {
            id: bundleId,
            name: bundleName,
            status: bundleStatus,
            parent_id: bundleParentId,
        };

        $.ajax({
            url,
            data,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    if (action == 'create') {
                        bundleId = data.data;
                        addLocalData(bundleId, bundleName, bundleStatus, bundleParentName, bundleParentId);
                        let _html = fetchRowBundle(bundleId, bundleName, bundleStatusName, bundleParentName, bundleParentId);
                        appendElBundles(_html);
                    } else {
                        changeLocalData(bundleId, bundleName, bundleStatus, bundleParentName, bundleParentId);
                        changeDisplayData(bundleId, bundleName, bundleStatusName, bundleParentName, bundleParentId);
                    }
                } else {
                    alert(data.msg)
                }//end if
            },
            complete: function() {
            }
        });
        
        clearEditModal();
        return $('#editModal').modal('hide');
    }


    function handleDeleteBundle() {
        const self = $(this);
        let bundleId = self.data('bundle');
        if (!bundleId) {
            return;
        }//end if

        let url = currentUrl + '&action=delete';
        let data = {
            id: bundleId,
        };
        
        $.ajax({
            url,
            data,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    return unsetAndRemoveRow(bundleId);
                } else {
                    alert(data.msg)
                }//end if
            },
            complete: function() {
            }
        });
    }

    $(document).ready(function () {
        renderBundles();
        renderParentBundleOptions('#jsElEditParentBundles');
        renderParentBundleOptions('#jsElFilterParents');
        // renderStatusOptions('#jsElFilterStatus');
        $('#jsElEditParentBundles').select2({
            dropdownParent: $("#editModal"),
            dropdownAutoWidth : true,
            width: '100%',
        });

        $('#jsElFilterParents').select2({
            dropdownAutoWidth : true,
            width: '100%',
        });

        $('#jsElFilterParents').val(filterParentId).change();
        // $('#jsElFilterStatus').val(filterStatus).change();

        /**
         * Edit bundle
         */
        $(document).on('click', '.jsElShowBundleModal', initEditBundleModal);
        $('.jsElActionCloseEditModal').on('click', clearEditModal);
        $('.jsElActionCloseAddModal').on('click', clearEditModal);
        $('#jsElSubmitBundleModal').on('click', handleSubmitModal);

        /**
         * Delete bundle
         */
        $(document).on('click', '.jsElDeleteBundle', handleDeleteBundle);
    });
</script>