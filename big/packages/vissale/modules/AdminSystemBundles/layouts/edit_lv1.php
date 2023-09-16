<?php
$miBundles = MiString::array2js($this->map['bundles']);
$bundleStatuses = MiString::array2js($this->map['filter_status_list']);
$filterStatus = $this->map['filter_status'];
?>

<script>
    var miBundles = <?php echo $miBundles; ?>;
    const filterStatus = '<?php echo $filterStatus; ?>';
    const bundleStatuses = <?php echo $bundleStatuses; ?>;
    const currentUrl = window.location.href;
    const l2BundleUrl = '<?php echo Url::build('admin_system_bundles') . '&lv=2'; ?>&lv=2&filter_parent_id=';
</script>

<style>
    .d-flex {
        display: flex;
    }
</style>

<div class="container-fluid" style="padding: 30px 0">
    <div class="row">
        <div class="col-xs-12">
            <form name="EditAdminSystemBundlesForm" id="EditAdminSystemBundlesForm" method="post" action="<?= Url::build('admin_system_bundles'); ?>" enctype="multipart/form-data" class="form-inline">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong style="line-height: 30px">[[|title|]]</strong>
                        <div class="pull-right">
                            <button type="button" type="button" data-toggle="modal" data-action="create" class="jsElShowBundleModal btn btn-primary" data-target="#addModal">
                                <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                Thêm mới
                            </button>
                        </div>
                    </div>
                    <div class="panel-body mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input name="keysearch" value="[[|keysearch|]]" type="text" placeholder="Tìm kiếm theo tên nhóm cấp 1" class="form-control">
                                <input type="hidden" name="lv" value="1">
                            </div>
                            <div class="col-md-4">
                                <label for="filter_status">Trạng thái</label>
                                <select name="filter_status" class="form-control" id="filter_status"></select>
                            </div>
                            <div class="col-md-4 text-right">
                                <a class="btn btn-link" href="<?=Url::build('admin_system_bundles');?>">Reset Tìm kiếm</a>
                                <button class="btn btn-primary ml-5" type="submit">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body form-inline">
                        <?= Form::$current->is_error() && Form::$current->error_messages(); ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <td class="col-xs-2"><label>ID</label>
                    </div>
                    <td class="col-xs-4"><label>Nhóm sản phẩm cấp 1</label>
                </div>
                <td class="col-xs-3"><label>Hiệu lực</label>
        </div>
        <td class="col-xs-3"><label>Tiện ích</label>
    </div>
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
                <h5 class="modal-title" id="editBundleModal">Chỉnh sửa nhóm sản phẩm cấp 1</h5>
                <button type="button" class="close jsElAcitonCloseEditModal" data-action="create" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <b>Tên nhóm sản phẩm cấp 1</b>
                </p>
                <input type="hidden" id="jsElEditBundleId" name="bundle_id" require>
                <p>
                    <input type="text" name="name" id="jsElEditBundleName" class="form-control" placeholder="Nhập tên nhóm sản phẩm" require />
                </p>
                <p>
                    <label>
                        <input type="checkbox" name="status" id="jsElEditBundleStatus" value="1" />
                        Hiệu lực
                    </label>
                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary jsElAcitonCloseEditModal" data-dismiss="modal">Đóng</button>
                <button type="button" id="jsElSubmitBundleModal" class="btn btn-primary">
                    <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                    Lưu
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addBundleModal" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBundleModal">Thêm nhóm sản phẩm cấp 1</h5>
                <button type="button" class="close jsElActionCloseAddModal" data-action="create" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <b>Tên nhóm sản phẩm cấp 1</b>
                </p>
                <div id="jsElRootBundles">
                    <div class="d-flex mb-3" id="jsElNodeBundleDefault">
                        <input type="text" class="form-control jsElBundleNames" placeholder="Nhập tên nhóm sản phẩm" require />
                        <button type="button" class="btn btn-danger jsElActionRemoveNode" data-target="Default">
                            <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <button id="jsElAddNode" class="btn btn-primary">
                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                    Thêm nhóm
                </button>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary jsElActionCloseAddModal" data-dismiss="modal">Đóng</button>
                <button type="button" id="jsElActionSubmitCreate" class="btn btn-success">
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
            html += fetchRowBundle(bundleId, bundleName, bundleStatus);
        } //end for

        elBundles.html(html);
    }

    function fetchRowBundle(bundleId, bundleName, bundleStatus) {
        return `
            <tr id="jsElBundle${bundleId}" class="jsElRowBundle">
                <td class="col-xs-2">${bundleId}</td>
                <td class="col-xs-4" id="jsElBundle${bundleId}Name">
                    <a href="${l2BundleUrl + bundleId}" target="_blank">${bundleName}</a>
                </td>
                <td class="col-xs-3" id="jsElBundle${bundleId}Status">${bundleStatus}</td>
                <td class="col-xs-3">
                    <button type="button" data-toggle="modal" data-action="edit" class="jsElShowBundleModal" 
                        data-bundle="${bundleId}" data-target="#editModal">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="jsElDeleteBundle" data-bundle="${bundleId}">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function clearEditModal() {
        return fillDataEditModal(null, null, true);
    }

    function fillDataEditModal(
        bundleId = null,
        bundleName = null,
        bundleStatus = true
    ) {
        $('#jsElEditBundleId').val(bundleId);
        $('#jsElEditBundleName').val(bundleName);
        $('#jsElEditBundleStatus').prop('checked', bundleStatus);
    }

    function changeDisplayData(bundleId, bundleName, bundleStatusName) {
        $(`#jsElBundle${bundleId}Name`).html(bundleName);
        $(`#jsElBundle${bundleId}Status`).html(bundleStatusName);
    }

    function unsetAndRemoveRow(bundleId) {
        $(`#jsElBundle${bundleId}`).remove();
    }

    function changeLocalData(bundleId, bundleName, bundleStatus) {
        miBundles[bundleId].name = bundleName;
        miBundles[bundleId].status = bundleStatus;
    }

    function addLocalData(bundleId, bundleName, bundleStatus) {
        miBundles[bundleId] = {
            id: bundleId,
            name: bundleName,
            status: bundleStatus,
        };
    }

    function appendElBundles(_html) {
        let elBundles = $('#jsElBundles');
        elBundles.html(_html + elBundles.html());
    }

    function initEditBundleModal() {
        const self = $(this);
        let bundleId = self.data('bundle');
        if (!bundleId) {
            return;
        } //end if

        let _bundle = miBundles[bundleId];
        let _status = false;
        if (parseInt(_bundle.status) > 0) {
            _status = true;
        } //end if

        return fillDataEditModal(_bundle.id, _bundle.name, _status);
    }

    function handleSubmitModal() {
        let bundleId = $('#jsElEditBundleId').val();
        let bundleName = $('#jsElEditBundleName').val();
        if (!bundleName) {
            return alert('Vui lòng điền tên nhóm sản phẩm');
        } //end if

        let bundleStatus = $('#jsElEditBundleStatus').prop('checked') ? 1 : 0;
        let bundleStatusName = bundleStatuses[bundleStatus];
        let url = currentUrl + '&action=edit';
        let data = {
            id: bundleId,
            name: bundleName,
            status: bundleStatus,
        };

        $.ajax({
            url,
            data,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    changeLocalData(bundleId, bundleName, bundleStatus);
                    changeDisplayData(bundleId, bundleName, bundleStatusName);
                } else {
                    alert(data.msg)
                } //end if
            },
            complete: function() {}
        })

        clearEditModal();
        return $('#editModal').modal('hide');
    }

    function handleCreateBundle(bundleName) {
        if (!bundleName) {
            return alert('Vui lòng điền tên nhóm sản phẩm');
        } //end if

        let url = currentUrl + '&action=create';
        let bundleStatus = 1;
        let bundleStatusName = bundleStatuses[bundleStatus];
        let data = {
            name: bundleName,
            status: bundleStatus,
        };

        $.ajax({
            url,
            data,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    bundleId = data.data;
                    addLocalData(bundleId, bundleName, 1);
                    let _html = fetchRowBundle(bundleId, bundleName, bundleStatusName);
                    appendElBundles(_html);
                    refreshBundleNode();
                } else {
                    alert(data.msg)
                } //end if
            },
            complete: function() {}
        })

        return $('#addModal').modal('hide');
    }

    function handleDeleteBundle() {
        const self = $(this);
        let bundleId = self.data('bundle');
        if (!bundleId) {
            return;
        } //end if

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
                } //end if
            },
            complete: function() {}
        })
    }

    function fetchBundleNode(nodeId) {
        return `
            <div class="d-flex mb-3" id="jsElNodeBundle${nodeId}" >
                <input type="text" class="form-control jsElBundleNames" placeholder="Nhập tên nhóm sản phẩm" require/>
                <button type="button" class="btn btn-danger jsElActionRemoveNode" data-target="${nodeId}">
                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                </button>
            </div>
        `;
    }

    function appendBundleNode(_html) {
        let elBundles = $('#jsElRootBundles');
        elBundles.append(_html);
    }

    function refreshBundleNode() {
        let nodeName = (Math.random() + 1).toString(36).substring(7);
        let _html = fetchBundleNode(nodeName);
        return $('#jsElRootBundles').html(_html);
    }

    function handleAddNode() {
        let nodeName = (Math.random() + 1).toString(36).substring(7);
        let html = fetchBundleNode(nodeName);
        return appendBundleNode(html);
    }

    function removeNode() {
        let target = $(this).data('target');
        return $(`#jsElNodeBundle${target}`).remove();
    }

    function handleCreateBundles() {
        const elBundleNames = $('.jsElBundleNames');
        const bundleNames = elBundleNames.map((_, el) => el.value).get()
        for (bundleName of bundleNames) {
            if (bundleName.length > 0) {
                handleCreateBundle(bundleName);
            } //end if
        } //end forof
    }

    // function renderStatusOptions(elName) {
    //     let el = $(elName);
    //     let html = "<option value='-1' selected>--- Tất cả --- </option>";
    //     for (const key in bundleStatuses) {
    //         let name = bundleStatuses[key];
    //         html += `<option value='${key}'>${name}</option>`;
    //     } //end for

    //     el.html(html);
    // }

    $(document).ready(function() {
        renderBundles();
        // renderStatusOptions('#jsElFilterStatus');
        // $('#jsElFilterStatus').val(filterStatus).change();

        /**
         * Create bundle
         */
        $('#jsElRootBundles').on('click', '.jsElActionRemoveNode', removeNode);
        $('#jsElAddNode').on('click', handleAddNode);
        $('#jsElActionSubmitCreate').on('click', handleCreateBundles);
        $('.jsElActionCloseAddModal').on('click', clearEditModal);

        /**
         * Edit bundle
         */
        $(document).on('click', '.jsElShowBundleModal', initEditBundleModal);
        $('.jsElAcitonCloseEditModal').on('click', clearEditModal);
        $('.jsElActionCloseAddModal').on('click', clearEditModal);
        $('#jsElSubmitBundleModal').on('click', handleSubmitModal);

        /**
         * Delete bundle
         */
        $(document).on('click', '.jsElDeleteBundle', handleDeleteBundle);
    });
</script>