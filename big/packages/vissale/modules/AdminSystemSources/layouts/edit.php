<?php
$sources = MiString::array2js($this->map['sources']);
$statusList = MiString::array2js($this->map['filter_active_list']);
$filterActive = $this->map['filter_active'];
?>

<script>
    var sources = <?php echo $sources; ?>;
    const filterActive = '<?php echo $filterActive; ?>';
    const statusList = <?php echo $statusList; ?>;
    const currentUrl = window.location.href;
</script>

<style>
    .d-flex {
        display: flex;
    }
</style>

<div class="container-fluid" style="padding: 30px 0">
    <div class="row">
        <div class="col-xs-12">
            <form name="EditAdminSystemSourcesForm" id="EditAdminSystemSourcesForm" method="post" action="<?= Url::build('admin_system_sources'); ?>" enctype="multipart/form-data" class="form-inline">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong style="line-height: 30px">Quản lý nguồn marketing</strong>
                        <div class="pull-right">
                            <button type="button" type="button" data-toggle="modal" data-action="create" class="jsElShowEditModal btn btn-primary" data-target="#addModal">
                                <i class="fa fa-plus-square-o" aria-hidden="true"></i> Thêm mới
                            </button>
                        </div>
                    </div>
                    <div class="panel-body mb-3">
                        <div class="row">
                            <div class="col-md-4 mt-3">
                                <input name="keysearch" value="[[|keysearch|]]" id="keysearch" type="text" placeholder="Nhập tên nguồn marketing" class="form-control">
                                <input type="hidden" name="lv" value="1">
                            </div>
                            <div class="col-md-4 mt-3">
                                <label for="filter_active">Trạng thái</label>
                                <select name="filter_active" id="filter_active" class="form-control"></select>
                            </div>
                            <div class="col-md-4 mt-3 text-right">
                                <a href="<?= Url::build('admin_system_sources'); ?>" class="btn btn-link">
                                    <i class="fa fa-refresh"></i> <span>Reset bộ lọc</span>
                                </a>
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
                                    <td class="col-xs-4"><label>Nguồn marketing</label>
                                    <td class="col-xs-3"><label>Hiệu lực</label>
                                    <td class="col-xs-3"><label>Tiện ích</label>
                                </tr>
                            </thead>
                            <tbody id="jsElItems"></tbody>
                        </table>
                        <div>[[|paging|]]</div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editItemModal" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModal">Chỉnh sửa nguồn marketing</h5>
                <button type="button" class="close jsElAcitonCloseEditModal" data-action="create" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <b>Tên nguồn marketing</b>
                </p>
                <input type="hidden" id="jsElEditItemId" name="bundle_id" require>
                <p>
                    <input type="text" name="name" id="jsElEditItemName" class="form-control" placeholder="Nhập tên nguồn marketing" require />
                </p>
                <p>
                    <label>
                        <input type="checkbox" name="is_active" id="jsElEditItemStatus" value="1" />
                        Hiệu lực
                    </label>
                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary jsElAcitonCloseEditModal" data-dismiss="modal">Đóng</button>
                <button type="button" id="jsElActionSubmitEdit" class="btn btn-primary">
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
                <h5 class="modal-title" id="addBundleModal">Thêm nguồn marketing</h5>
                <button type="button" class="close jsElActionCloseAddModal" data-action="create" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <b>Tên nguồn marketing</b>
                </p>
                <div id="jsElRootItems">
                    <div class="d-flex mb-3" id="jsElNodeItemDefault">
                        <input type="text" class="form-control jsElItemNames" placeholder="Nhập tên nguồn marketing" />
                        <button type="button" class="btn btn-danger jsElActionRemoveNode" data-target="Default">
                            <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <button id="jsElAddNode" class="btn btn-primary">
                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                    Thêm nguồn
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
    function renderItems() {
        let elItems = $('#jsElItems');
        let html = '';
        for (const itemId in sources) {
            let source = sources[itemId];
            let itemName = source.name;
            let itemStatus = statusList[source.is_active];
            html += fetchRowBundle(itemId, itemName, itemStatus);
        } //end for

        elItems.html(html);
    }

    function fetchRowBundle(itemId, bundleName, bundleStatus) {
        return `
            <tr id="jsElItem${itemId}" class="jsElRowBundle">
                <td class="col-xs-2">${itemId}</td>
                <td class="col-xs-4" id="jsElItem${itemId}Name">${bundleName}</td>
                <td class="col-xs-3" id="jsElItem${itemId}Status">${bundleStatus}</td>
                <td class="col-xs-3">
                    <button type="button" data-toggle="modal" data-action="edit" 
                        class="jsElShowEditModal" data-source="${itemId}" data-target="#editModal">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="jsElDeleteItem" data-source="${itemId}">
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
        itemId = null,
        itemName = null,
        itemStatus = true
    ) {
        $('#jsElEditItemId').val(itemId);
        $('#jsElEditItemName').val(itemName);
        $('#jsElEditItemStatus').prop('checked', itemStatus);
    }

    function changeDisplayData(itemId, itemName, itemStatus) {
        $(`#jsElItem${itemId}Name`).html(itemName);
        $(`#jsElItem${itemId}Status`).html(itemStatus);
    }

    function unsetAndRemoveRow(itemId) {
        $(`#jsElItem${itemId}`).remove();
    }

    function changeLocalData(itemId, itemName, itemStatus) {
        sources[itemId].name = itemName;
        sources[itemId].status = itemStatus;
    }

    function addLocalData(itemId, itemName, itemStatus) {
        sources[itemId] = {
            id: itemId,
            name: itemName,
            status: itemStatus,
        };
    }

    function appendElItems(_html) {
        let elBundles = $('#jsElItems');
        elBundles.html(_html + elBundles.html());
    }

    function initEditModal() {
        const self = $(this);
        let itemId = self.data('source');
        if (!itemId) {
            return;
        } //end if

        let source = sources[itemId];
        let itemName = source.name;
        let itemStatus = false;
        if (parseInt(source.is_active) > 0) {
            itemStatus = true;
        } //end if

        return fillDataEditModal(itemId, itemName, itemStatus);
    }

    function handleUpdate() {
        let itemId = $('#jsElEditItemId').val();
        let itemName = $('#jsElEditItemName').val();
        if (!itemName) {
            return alert('Vui lòng điền tên nguồn marketing');
        } //end if

        let itemStatus = $('#jsElEditItemStatus').prop('checked') ? 1 : 0;
        let itemStatusName = statusList[itemStatus];
        let url = currentUrl + '&action=edit';
        let data = {
            id: itemId,
            name: itemName,
            is_active: itemStatus,
        };

        $.ajax({
            url,
            data,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    changeLocalData(itemId, itemName, itemStatus);
                    changeDisplayData(itemId, itemName, itemStatusName);
                } else {
                    alert(data.msg)
                } //end if
            },
            complete: function() {}
        })

        clearEditModal();
        return $('#editModal').modal('hide');
    }

    function ajaxCreate(itemName) {
        if (!itemName) {
            return alert('Vui lòng điền tên nguồn marketing');
        } //end if

        let url = currentUrl + '&action=create';
        let itemStatus = 1;
        let itemStatusName = statusList[itemStatus];
        let data = {
            name: itemName,
            status: itemStatus,
        };

        $.ajax({
            url,
            data,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    itemId = data.data;
                    addLocalData(itemId, itemName, 1);
                    let _html = fetchRowBundle(itemId, itemName, itemStatusName);
                    appendElItems(_html);
                    refreshNodes();
                } else {
                    alert(data.msg)
                } //end if
            },
            complete: function() {}
        })

        return $('#addModal').modal('hide');
    }

    function ajaxDelete() {
        const self = $(this);
        let sourceId = self.data('source');
        if (!sourceId) {
            return;
        } //end if

        let url = currentUrl + '&action=delete';
        let data = {
            id: sourceId,
        };

        $.ajax({
            url,
            data,
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    return unsetAndRemoveRow(sourceId);
                } else {
                    alert(data.msg)
                } //end if
            },
            complete: function() {}
        })
    }

    function fetchNode(nodeId) {
        return `
            <div class="d-flex mb-3" id="jsElNodeItem${nodeId}" >
                <input type="text" class="form-control jsElItemNames" placeholder="Nhập tên nguồn marketing" require/>
                <button type="button" class="btn btn-danger jsElActionRemoveNode" data-target="${nodeId}">
                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                </button>
            </div>
        `;
    }

    function appendNodes(_html) {
        $('#jsElRootItems').append(_html);
    }

    function refreshNodes() {
        let nodeName = (Math.random() + 1).toString(36).substring(7);
        let _html = fetchNode(nodeName);
        return $('#jsElRootItems').html(_html);
    }

    function handleAddNode() {
        let nodeName = (Math.random() + 1).toString(36).substring(7);
        let html = fetchNode(nodeName);
        return appendNodes(html);
    }

    function removeNode() {
        let target = $(this).data('target');
        return $(`#jsElNodeItem${target}`).remove();
    }

    function handleCreate() {
        const elItemNames = $('.jsElItemNames');
        const itemNames = elItemNames.map((_, el) => el.value).get()
        for (itemName of itemNames) {
            if (itemName.length > 0) {
                ajaxCreate(itemName);
            } //end if
        } //end forof
    }

    function renderStatusOptions(elName) {
        let el = $(elName);
        let html = "<option value='-1' selected>--- Tất cả --- </option>";
        for (const key in statusList) {
            let name = statusList[key];
            html += `<option value='${key}'>${name}</option>`;
        } //end for

        el.html(html);
    }

    $(document).ready(function() {
        renderItems();
        // renderStatusOptions('#jsElfilterActive');

        // $('#jsElfilterActive').val(filterActive).change();

        /**
         * Create Source
         */
        $('#jsElRootItems').on('click', '.jsElActionRemoveNode', removeNode);
        $('#jsElAddNode').on('click', handleAddNode);
        $('#jsElActionSubmitCreate').on('click', handleCreate);
        $('.jsElActionCloseAddModal').on('click', clearEditModal);

        /**
         * Edit Source
         */
        $(document).on('click', '.jsElShowEditModal', initEditModal);
        $('.jsElAcitonCloseEditModal').on('click', clearEditModal);
        $('.jsElActionCloseAddModal').on('click', clearEditModal);
        $('#jsElActionSubmitEdit').on('click', handleUpdate);

        /**
         * Delete Source
         */
        $(document).on('click', '.jsElDeleteItem', ajaxDelete);
    });
</script>