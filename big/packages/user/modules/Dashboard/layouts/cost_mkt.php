
<?php
$title = 'Báo cáo Chi phí quảng cáo theo hệ thống';
?>
<style type="text/css">
#table-wraper {overflow: auto; max-height: 600px;}
#table-wraper thead{position: sticky; top:  0; z-index: 2;}
div#table-wraper td:nth-child(-n + 2), div#table-wraper th[col='stt'], div#table-wraper th[col='name'] {position: sticky; left: 1px; z-index:  1}
div#table-wraper td:nth-child(2), div#table-wraper th[col='name'] {left: 48px; }
div#table-wraper td:nth-child(2) {font-weight: bold; text-align: left;}

.hilight-cell{background: #67c69e !important; font-weight: bold;}
div#table-wraper th {background: #037db4; color: #fff }
div#table-wraper tr:nth-child(odd) td {background: #f6f6f6; }
div#table-wraper tr:nth-child(even) td {background: #fff; }
div#table-wraper table {border-collapse: separate; }
div#table-wraper td, div#table-wraper th {border-color: #d1d1d1; border-left: 0; border-top: 0; text-align:  center}
#title-report {display: none;}
button.btn.btn-default.multiselect-clear-filter {padding: 9px; }
i.glyphicon.glyphicon-remove-circle {top: 0px; }
loadding {background: url('/assets/standard/svgs/loading-black.svg'); background-repeat: no-repeat; background-position: center; background-size: 30px; display: block; width: 100%; height: 100px; }
.error{border-color: red;}
b#system-name {display: block; }
div#table-wraper tbody tr:last-child{font-weight: bold;}

ul#custom_columns_wrapper, ul#custom_columns_wrapper ul {
    list-style: none;
    margin: 0 20px;
    padding: 0;
    display: flex;
    background: #fff;
}


ul#custom_columns_wrapper input{
    margin-right: 10px
}

ul#custom_columns_wrapper li {
    padding: 3px 10px;
    margin: 3px;
    border: 1px solid #ececec;
    border-radius: 3px
}

ul#custom_columns_wrapper .block_name{text-align: center; display: flex; width: 100%; flex-grow: 1; align-items: center; justify-content: center; flex-direction: column; }
ul#custom_columns_wrapper span.block_name input {margin: 10px  0 5px 0!important; }
div#modal_custom_columns_wrapper .modal-dialog {width: 100% !important; }
div#modal_custom_columns_wrapper .modal-header {display: flex; }
div#modal_custom_columns_wrapper .modal-title {flex-grow: 1; }
div#modal_custom_columns_wrapper .modal-body {overflow-x: auto; }
</style>


<!-- Modal -->
<div class="modal fade" id="modal_custom_columns_wrapper" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Tùy chỉnh cột hiển thị</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <ul id="custom_columns_wrapper"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="reset-btn">
                    Reset về mặc định
                </button>
                <button type="button" class="btn btn-primary" id="save-btn">
                    Lưu
                </button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    const COOKIE_KEY = '<?=CostMktForm::COOKIE_KEY?>';
    const DEFAULT_COLUMNS_CONFIG =  <?=json_encode($this->map['columns_config'])?>

    /**
     * Builds tag attributes.
     *     {name: 'aaa', class: ['bbb', 'fff'], id: 'ccc'}
     * =>  name="aaa" class="bbb fff" id="ccc"
     *
     * @param      {<type>}  attrs   The attributes
     * @return     {<type>}  The tag attributes.
     */
    function buildTagAttrs(attrs) {
        return Object.keys(attrs)
            .map(function (propName) {
                switch(true){
                    case Array.isArray(attrs[propName]):
                        return `${propName}="` + attrs[propName].join(' ') + `"`
        
                    case typeof attrs[propName] === 'function':
                        return `${propName}="` + attrs[propName]() + `"`

                    case typeof attrs[propName] === 'boolean':
                        return attrs[propName] ? `${propName}` : ''
        
                    default:
                        return `${propName}="` + attrs[propName].toString() + '"';
                }
            })
            .join(' ')
            .trim();
    }

    /**
     * Builds a raw tag. Tạo một tag ở dạng text
     *
     * @param      {<type>}   tagName       The tag name
     * @param      {<type>}   attrs         The attributes
     * @param      {<type>}   html          The html
     * @param      {<type>}   dontCloseTag  The don't close tag
     * @return     {boolean}  The raw tag.
     */
    function buildRawTag(tagName, attrs, html, dontCloseTag) {
        if (!tagName) return html;

        const attrStr = buildTagAttrs(attrs);

        return dontCloseTag
            ? `<${tagName}${attrStr ? ' ' + attrStr : ''}>`
            : `<${tagName}${attrStr ? ' ' + attrStr : ''}>${html}</${tagName}>`;
    }

    /**
     * Builds a tag. Tạo một tag là instance của Element DOM
     *
     * @param      {<type>}  tagName       The tag name
     * @param      {<type>}  attrs         The attributes
     * @param      {<type>}  html          The html
     * @param      {<type>}  dontCloseTag  The don't close tag
     * @return     {<type>}  The tag.
     */
    function buildTag(tagName, attrs, html, dontCloseTag) {
        return buildElement(buildRawTag(tagName, attrs, html, dontCloseTag));
    }

    /**
     * Builds an element.
     *
     * @param      {<type>}  textHtml  The text html
     * @return     {<type>}  The element.
     */
    function buildElement(textHtml) {
        const __div__ = document.createElement(`div`);
        __div__.innerHTML = textHtml;

        return __div__.firstChild;
    }

    /**
     * Builds a tree array.
     *
     * @param      {<type>}  arrayConfig  The array configuration
     * @return     {<type>}  The tree array.
     */
    function buildTreeArray(arrayConfig) {
        return arrayConfig.reduce((result, _config) => (result += buildTree(_config)), '');
    }

    /**
     * Builds a tree.
     *
     * @param      {<type>}  treeConfig  The tree configuration
     * @return     {<type>}  The tree.
     */
    function buildTree(treeConfig) {

        // Trường hợp nhận vào 1 chuỗi thì trả lại chuỗi mà không xử lí tiếp
        if (typeof treeConfig == 'string') {
            return treeConfig;
        }

        let _html = '',
            dontCloseTag = false;

        // Trường hợp nhận vào 1 mảng thì mặc định coi nó là 1 danh sách các el
        if (Array.isArray(treeConfig.html)) {
            _html = buildTreeArray(treeConfig.html);
        }

        // Trường hợp nếu nhận vào 1 object thì coi như phần tử hiện tại chỉ có duy nhất 1 phần tử con
        else if (typeof treeConfig.html == 'object') {
            _html = buildTree(treeConfig.html);
        }

        // Trường hợp html là chuỗi thì không xử lý html mà coi html là 1 thẻ hợp lệ
        else if (typeof treeConfig.html == 'string') {
            _html = treeConfig.html;
        }

        // Trường hợp html là boolean thì coi như phần tử không có innerHTML do đó nó không có closeTag
        else if (typeof treeConfig.html == 'boolean') {
            dontCloseTag = !treeConfig.html;
        }

        // Throw lỗi nếu không thuộc các trường hợp trên
        else {
            throw Error('config invalid !');
        }

        const _attrs = Object.assign({}, treeConfig.attrs);
        const rawTag = buildRawTag(treeConfig.tag, _attrs, _html, dontCloseTag);

        return rawTag;
    }

    /**
     * Mount
     *
     * @param      {<type>}    target       The target
     * @param      {<type>}    mountHTML    The mount html
     * @param      {Function}  beforeMount  The before mount
     * @param      {Function}  mounted      The mounted
     * @return     {<type>}    { description_of_the_return_value }
     */
    function mount(target, mountHTML, beforeMount, mounted) {
        const mountPoint = document.querySelector(target);
        if (mountPoint) {
            typeof beforeMount === 'function' && beforeMount(mountHTML);
            mountPoint.replaceWith(mountHTML);
            typeof mounted === 'function' && mounted(mountHTML);

            return mountHTML;
        }

        console.log(`mount point "${target}" not found`);
    }

    /**
     * Chuẩn bị danh sách custom để hiển thị
     * CÓ thể giá trị đã cookie không giống giá trị thực tế.
     * Vì vậy cần có bước điền hoặc xóa các field không đúng hoặc thiếu
     *
     * @param      {<type>}  current     The current
     * @param      {<type>}  defaultCfg  The default configuration
     * @return     {<type>}  { description_of_the_return_value }
     */
    function prepareCustom(current, defaultCfg){
        
        /**
         * Xóa các thuộc tính có trong current mà không có trong default
         */
        function clearCurrent(current, defaultCfg){
            Object.keys(current).map(key => {
                if(key !== 'checked' && !defaultCfg.hasOwnProperty(key)){
                    delete current[key]
                }

                typeof current[key] === 'object' && clearCurrent(current[key], defaultCfg[key])
            })
        }

        /**
         * Thêm các thuộc tính có trong default mà không có trong current
         */
        function fillCurrent(current, defaultCfg){
            Object.keys(defaultCfg).map(key => {
                if(!current.hasOwnProperty(key)){
                    current[key] = defaultCfg[key]
                }

                typeof current[key] === 'object' && fillCurrent(current[key], defaultCfg[key])
            })
        }
        
        clearCurrent(current, defaultCfg)
        fillCurrent(current, defaultCfg)

        return current
    }

    /**
     * Loads a customized.
     *
     * @return     {Object}  { description_of_the_return_value }
     */
    function loadCustomized(defaultCfg){
        try{
            let cookieValue = JSON.parse($.cookie(COOKIE_KEY));
            
            if(cookieValue){
                return prepareCustom(cookieValue, defaultCfg);
            }
        }catch(e){}

        return  defaultCfg;
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  config  The configuration
     */
    function generateConfig(key, config, parent){
        const currentTree = {
            tag: 'li',
            attrs: {key: key},
            html: [
                {
                    tag: 'span',
                    attrs: {class: 'block_name'},
                    html:[
                        {
                            tag: 'input', 
                            attrs: {
                                type: "checkbox", 
                                value: key, 
                                checked: config.hasOwnProperty('checked') ? !!config.checked : true,
                            }, 
                            html: false 
                        },
                        config.name
                    ]
                }
            ]
        }

        if(config.childs && Object.keys(config.childs).length){
            currentTree.html.push({
                tag: 'ul',
                html: [...Object.keys(config.childs).map(_key => generateConfig(_key, config.childs[_key], config))]
            })
        }

        return currentTree;
    }

    const customized = loadCustomized(DEFAULT_COLUMNS_CONFIG);

    const defaultColumnConfigKeys = Object.keys(customized.childs);
    // Xây dựng cây html sẽ được dùng để render 
    const treeConfig = {
        tag: 'ul',
        attrs: {id: 'custom_columns_wrapper'},
        html: [
            ...defaultColumnConfigKeys.map(key => generateConfig(key, customized.childs[key]))
        ]
    }

    const mounted = function(DOM){
        DOM = $(DOM);

        /**
         * Gets the deep object property.
         *
         * @param      {<type>}  obj     The object
         * @param      {<type>}  path    The path
         */
        function getDeepObjectProp(obj, path){
            if(!obj.childs){
                return;
            }

            let currentProp = obj.childs[path.shift()];

            if(!path.length){
                return currentProp;
            }

            return currentProp ? getDeepObjectProp(currentProp, path) : undefined;
        }

        /**
         * Gets the deep child object.
         *
         * @param      {<type>}  obj     The object
         * @param      {<type>}  path    The path
         * @return     {<type>}  The deep child object.
         */
        function getDeepChildObject(obj, path){
            if(!path.length){
                return obj;
            }

            let currentObj = obj.childs[path.shift()];
                
            if(!currentObj){
                return undefined;
            }
            
            return getDeepChildObject(currentObj, path);
        }

        /**
         * Cập nhật checked cho các node
         *
         * @param      {<type>}  options  The options
         * @return     {<type>}  { description_of_the_return_value }
         */
        function updateChecked(options){
            const {root, path, value} = options;

            if(!root.childs){
                return;
            }

            let currentProp = root.childs[path.shift()];
            if(!currentProp){
                return;
            }

            currentProp.checked = value;

            return  updateChecked({root: currentProp, path: path, value: value});
        }

        /**
         * Cập nhật checked cho tất cả node con và node hiện tại
         *
         * @param      {<type>}  options  The options
         */
        function updateAllChilds(options){
            const {root, value, skipCurrent} = options;

            if(!root){
                return;
            }

            if(!skipCurrent){
                root.checked = value;
            }   

            const childKeys = Object.keys(root.childs || {});
            childKeys.map(childKey => {
                const options = {root: root.childs[childKey], value: value, skipCurrent: false};
                return updateAllChilds(options)
            })
        }

        /**
         * { function_description }
         *
         * @param      {<type>}  paths   The paths
         * @param      {<type>}  keys    The keys
         */
        function updatePosition(options){
            const {root, paths, keys} = options;
            const childObject = getDeepChildObject(root, paths);

            if(!childObject || !childObject.childs){
                return;
            }

            childObject.childs = keys.reduce((res, key) => {
                res[key] = childObject.childs[key]
                return res;
            }, {})
        }

        /**
         * { function_description }
         *
         * @param      {<type>}  data    The data
         */
        function save(data){
             $.cookie(COOKIE_KEY,  JSON.stringify(data), { expires: 365 });
        }

        DOM.find('input[type="checkbox"]').change(function(currentInput){
            const parentInput = $(this)
                    .parents('ul')
                    .siblings('span')
                    .children('input[type="checkbox"]');
            const pathProps = parentInput.get().map(parentInput => parentInput.value);

            // Cập nhật thay đổi cho tất cả các parent (Chỉ khi là checked)
            if(this.checked){

                updateChecked({root: customized, path: [...pathProps, this.value], value: this.checked})
                parentInput.prop('checked', this.checked)
            }

            // Cập nhật thay đổi cho tất cả các child
            $(this)
                .parent('span')
                .siblings('ul')
                .find('input[type="checkbox"]')
                .prop('checked', this.checked)

            const obj = getDeepChildObject(customized, [...pathProps, this.value])
            console.log([...pathProps, this.value])
            updateAllChilds({root: obj, value: this.checked, skipCurrent: false})

            save(customized);
        })

        // sortable block
        $( "#custom_columns_wrapper, #custom_columns_wrapper ul").sortable({
            axis: 'x',
            update: function (event, {item}) {
                const ul = item.parent('ul');
                const keys = ul.children('li').map((i, e) => e.getAttribute('key')).get()
                const paths = ul.parents('li').map((i, e) => e.getAttribute('key')).get()

                // Cập nhật lại vị trí các rows
                updatePosition({root: customized, paths: paths.reverse(), keys: keys})
                
                save(customized);
            }
        });

        $('#reset-btn').click(function(e) {
            const treeConfig = {
                tag: 'ul',
                attrs: {id: 'custom_columns_wrapper'},
                html: [
                    ...Object.keys(DEFAULT_COLUMNS_CONFIG.childs).map(key => generateConfig(key, DEFAULT_COLUMNS_CONFIG.childs[key]))
                ]
            }

            mount('#custom_columns_wrapper', buildElement(buildTree(treeConfig)), null, mounted)

            save(DEFAULT_COLUMNS_CONFIG);
        })

        $( "#custom_columns_wrapper li" ).disableSelection();
    }

    mount('#custom_columns_wrapper', buildElement(buildTree(treeConfig)), null, mounted)


</script>
<div id="product-system-wrapper" class="container-fluid" style="padding: 30px">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-dark">
            <li class="breadcrumb-item"><a href="/">QLBH</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('report')?>">Báo cáo</a></li>
            <li class="breadcrumb-item"><a href="<?=Url::build('dashboard')?>">Dashboard</a></li>
            <li class="breadcrumb-item">
                <?=$title?> - <a href="https://big.shopal.vn/bai-viet/huong-dan-su-dung/bao-cao-chi-phi-quang-cao-theo-he-thong/"
                                 target="_blank"
                                 class="btn btn-default"
                                 style="padding: 0px 2px;">
                                 <i class="fa fa-question-circle"></i>
                                 Hướng dẫn
                              </a>

            </li>
            <li class="pull-right">
                <div class="pull-right">

                </div>
            </li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading form-inline">
                    <div class="nav">
                        <div class="form-group">
                            <input type="text" id="date_from" name="date_from" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <input type="text" id="date_to" name="date_to" class="form-control" />
                        </div>
                        <div class="form-group">
                        <?=$this->systemsSelectbox?>
                        </div>
                        <div class="form-group">
                        <JSHELPER id="select-groups"></JSHELPER>
                        </div>
                        <div class="form-group">
                        <button class="btn btn-primary" type="button" name="view_report">Xem báo cáo</button>
                        </div>
                        <div class="form-group pull-right">
                            <button type="button" class="btn btn-success" id="export-report">Xuất excel</button>
                            <button type="button" class="btn btn-default" id="print-report"><i class="fa fa-print"></i> In Báo cáo</button>
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal_custom_columns_wrapper">
                                <i class="fa fa-gear"></i> Tùy chỉnh cột
                            </button>
                        </div>
                    </div>

                </div>
                

                <div class="panel-body form-inline" id="report">
                    <div class="text-warning">
                        <strong>Chú ý:</strong><br>
                        - Cột tổng: Bao gồm các đơn thuộc loại đơn Sale (số mới), Tối ưu, CSKH.<br>
                        - Số liệu chỉ tính cho các đơn do marketing đổ về.<br>
                        - Các cột Đơn, Điểm sẽ tính theo ngày Xác nhận chốt đơn theo Loại đơn tương ứng (trừ đơn không tính doanh thu)
                    </div>
                    <style type="text/css">
                        @media print {
                            #title-report{text-align: center !important;}
                            .text-center {text-align: center !important; }
                            table{border-collapse: collapse;}
                            table th, table td {border:1px solid #666;padding: 5px;}
                        }
                    </style>
                    <h2 id="title-report" class="text-center">Báo cáo chi phí quảng cáo hệ thống <b id="system-name"></b></h2>
                    <center id="d" style="display: none; margin-bottom: 15px;"><b>Từ ngày <span id="d_from"></span> đến ngày <span id="d_to"></span></b></center>
                    <div id="table-wraper">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    const MAX_SYSTEM_SELECTED = <?=CostMktForm::MAX_SYSTEM_SELECTED?>;
    const MODULE_ID = <?=Module::block_id()?>;
    let ONLOAD = -1;

    const MONTH_EL = $('select[name="month"]')
    const YEAR_EL = $('select[name="year"]')

    MONTH_EL.val(new Date().getMonth() + 1)
    YEAR_EL.val(new Date().getFullYear())

    /**
     * Loads groups.
     *
     * @param      {<type>}  systemID  The system id
     */
    const loadGroups = async function(systemID){
        try{
            $("#ms-select-groups").multiselect('destroy');
            const response = await fetchGroupsBySystemID(systemID);
            const rawSelectGroup = JSHELPER.render.select({
                data: {0: 'Tất cả nhóm', ...response},
                HTML_COLUMN: 'name',
                selectAttrs: {
                    class: 'form-control d-none',
                    name: "select-groups[]",
                    multiple: true,
                    id: 'ms-select-groups',
                    style: 'display: none'
                },
            })._mountHTML

            $('JSHELPER#select-groups').html(rawSelectGroup);

            onMountedSelectGroups()

        }catch(e){
            console.log(e)
        }
    }

    /**
     * Shows the loadding.
     */
    const showLoadding = function()
    {
        $('#table-wraper').html('<loadding></loading>')
    }

    /**
     * Loads a report.
     *
     * @return     {<type>}  { description_of_the_return_value }
     */
    const loadReport = async function(){
        showLoadding();
        try{
            const groupIDs = $('#ms-select-groups').val();

            const [cost, profit] = await Promise.all([
                $.post('/form.php', {
                    do: 'cost_mkt',
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    groups: groupIDs,
                    act: 'load_report',
                    block_id: MODULE_ID
                }),
                $.post('/form.php', {
                    do: 'cost',
                    date_from: $('#date_from').val(),
                    date_to: $('#date_to').val(),
                    groups: groupIDs,
                    act: 'json',
                    block_id: MODULE_ID
                }),     
            ])

            $('#table-wraper').html(cost);

            let date = new Date();
            let currentDate = date.getDate() + '/' + (date.getMonth() + 1);
            let dayIndex = [].slice.call(document.querySelectorAll('th')).reduce(function(o, e, i){
                return e.innerText == currentDate && (o = i), o
            }, null)
            document.querySelectorAll('td:nth-child(' + (dayIndex + 1) + ')').forEach(e => e.classList.add('hilight-cell'))

            $.each([...groupIDs, 'total'], function(key, groupID){
                let val = profit.loi_nhuan ? (profit.loi_nhuan[groupID] ||0) : 0;
                $('td[profit="' + groupID + '"]').text(val)
            })

            window.__REPORT_PROFIT = profit.loi_nhuan
        }catch(e){
            alert('Server Error!')
            console.log(e)
        }
    }

    /**
     * Fetches a groups by system id.
     *
     * @param      {<type>}  systemID  The system id
     * @return     {<type>}  The groups by system id.
     */
    const fetchGroupsBySystemID = async function(systemID){
        return await $.post('/form.php', {
            do: 'cost_mkt',
            systemID: systemID,
            act: 'load_groups_by_system_id',
            block_id: MODULE_ID
        })
    }

    /**
     * Called on mounted team.
     *
     * @param      {<type>}  el      { parameter_description }
     */
    const onMountedSelectGroups = function(el){
        $('#ms-select-groups').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '150px',
            maxHeight: 200,
            onChange: function(option, checked) {
                const val = parseInt(option.val());

                if(val == 0){
                    return $('#ms-select-groups').multiselect(checked ? 'selectAll' : 'deselectAll', false).multiselect('updateButtonText')
                }
            }
        })

        // Tự động select hết
        $('#ms-select-groups')
            .multiselect('selectAll', false)
            .multiselect('updateButtonText')
    }

    /**
     * { function_description }
     */
    const exportExcel = async function()
    {   
        try{
            const data = {
                do: 'cost_mkt',
                report_expired: __REPORT_EXPIRED,
                report_hash: __REPORT_HASH,
                report_data: JSON.stringify(__REPORT_DATA),
                report_profit: JSON.stringify(__REPORT_PROFIT),
                act: 'export_report',
                block_id: MODULE_ID
            };
            
            const body = Object.entries(data).reduce(function(res, e){
                const [key, val] = e;
                
                if(!Array.isArray(val)){
                    res.push(encodeURIComponent(key) + '=' + encodeURIComponent(val));
                }else{
                    res.push(...(val.map(e => encodeURIComponent(key) + '[]=' + encodeURIComponent(e))))
                }

                return res;
            }, []).join('&')

            const response = await fetch('/form.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                },
                body: body
            })

            // const text = await response.text();
            // console.log(text)
            const blob = await response.blob();

            var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'Báo cáo CPQC hệ thống ' + $('#system-name').text() + '.xlsx';
                link.click();
        }catch(e){
            alert('Server Error!')
            console.log(e)
        }
    }

    /**
     * Prints a report.
     */
    const printReport = function()
    {   
        const TABLE = $("#report");
        if(!TABLE){
            return;
        }

        const newWindown = window.open("");
        newWindown.document.write(TABLE[0].outerHTML);
        newWindown.print();
        newWindown.close();
    }

    let DATE_FROM = moment('<?=$this->map['date_from']?>');
    let DATE_TO = moment('<?=$this->map['date_to']?>');
    /**
     * { function_description }
     *
     * @param      {<type>}  e       { parameter_description }
     * @return     {<type>}  { description_of_the_return_value }
     */
    const dateFromChange = function(e)
    {   
        DATE_FROM = e.date;

        return validateDateTime();
    }

    /**
     * { function_description }
     *
     * @param      {<type>}  e       { parameter_description }
     * @return     {<type>}  { description_of_the_return_value }
     */
    const dateToChange = function(e)
    {
        DATE_TO = e.date;

        return validateDateTime();
    }

    /**
     * { function_description }
     *
     * @param      {boolean}  bool    The bool
     */
    const dateUI = function(bool)
    {
        $('#date_from')[bool ? 'removeClass' : 'addClass']('error')
        $('#date_to')[bool ? 'removeClass' : 'addClass']('error')

        $('[name="view_report"]').prop('disabled', !bool);
    }

    /**
     * { function_description }
     */
    const validateDateTime = function()
    { 
        if(DATE_TO.format('MM') - DATE_FROM.format('MM')){
            alert('Vui lòng chọn khoảng thời gian trong 1 tháng !');
            return dateUI(false);
        }

        if(DATE_TO.diff(DATE_FROM, 'months')){
            alert('Vui lòng chọn khoảng thời gian tối đa 1 tháng !');
            return dateUI(false);
        }

        if(DATE_TO.isBefore(DATE_FROM)){
            alert('Thời gian bắt đầu và kết thúc không hợp lệ !');
            return dateUI(false);
        }

        return dateUI(true);
    }

    $(document).ready(function() {
        $('#date_from').datetimepicker({
            format: 'DD/MM/YYYY',
            defaultDate: DATE_FROM
        }).on('dp.change', dateFromChange);

        $('#date_to').datetimepicker({
            format: 'DD/MM/YYYY',
            defaultDate: DATE_TO
        }).on('dp.change', dateToChange);

        $('#system_group_id').multiselect({
            enableFiltering: true,
            filterBehavior: 'text',
            enableCaseInsensitiveFiltering: true,
            maxHeight: 200,
            enableClickableOptGroups : true, 
            onChange: function(option, checked) {
                const systemIDs = $('#system_group_id').val().map(e => parseInt(e) || 0)

                if(systemIDs.length > MAX_SYSTEM_SELECTED){
                    alert('Vui lòng chọn tối đa ' + MAX_SYSTEM_SELECTED + ' hệ thống !');
                    return;
                }

                // Hiện title
                $('#title-report').show()
                let systemNames = $('#system_group_id option:selected')
                    .get()
                    .map(e => e.innerText.replace(/\s*--\s*/g, ''))
                    .join(',')
                $('#system-name').text(systemNames)

                // Xóa table
                $('#table-wraper').html('<p class="text-center" style="padding: 40px">Vui lòng chọn shop và bấm vào nút <b>xem báo cáo</b></p>')

                loadGroups(systemIDs)
            }
        })
        .multiselect('select', <?=$this->map['default_system_id']?>, true)
        .multiselect('updateButtonText')

        $('[name="view_report"], #save-btn').click(function(){
            $('#d_from').text(DATE_FROM.format('DD/MM/20YY'));
            $('#d_to').text(DATE_TO.format('DD/MM/20YY'));
            $('#d').show();

            $('#modal_custom_columns_wrapper').modal('hide')
            loadReport();
        })

        $('#export-report').click(exportExcel)

        $('#print-report').click(printReport)
    });
</script>
