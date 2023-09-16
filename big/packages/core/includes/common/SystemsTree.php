<?php

require_once ROOT_PATH . 'packages/core/includes/common/Systems.php';

/**
 * Lớp giúp xây dựng cây phân cấp hệ thống với HTML hoặc với mảng
 * Lớp còn có thể được mở rộng để support được nhiều tính huống hơn nữa
 */
class SystemsTree
{   
    const SELECTED_PARENT = 1;
    const SELECTED_CURRENT = 0;

    // Định dạng của group cha
    private static $parrentFormat = '<ul>%s</ul>';

    // Định dạng của phần tử con, ở đây nó sẽ gồm 2 thành phần là thông tin phần tử con 
    // và danh sách phần tử con nếu có. Nó có thể được ghi đè với setter 
    private static $childFormat = '<li>%s%s</li>';

    // Định dạng dữ liệu sẽ hiển thị cho một phần tử con. Nó có thể được ghi đè với setter
    // Layout sau khi biên dịch sẽ được gọi trực tiếp thông qua eval. Vì complier đang dùng eval extract để 
    // bung mảng thành các biến và eval để chạy nó nên {{$id}} sẽ tương đương echo <?php echo $id; ?\>
    private static $layout = '
                <a href="index062019.php?page=groups_system&cmd=move_up&id={{$id}}"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a> 
                <a href="index062019.php?page=groups_system&cmd=move_down&id={{$id}}"><span class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></a> 
                <input  name="selected_ids[]" type="checkbox" value="{{$id}}" id="Category_checkbox_{{$id}}" onclick="select_checkbox(document.ListCategoryForm,\'Category\',this.checked,\'#FFFFEC\',\'#FFF\');" {{$checked}}> <span class="node"> <i class="icon-minus-sign"></i></span>
                <a href="index062019.php?page=groups_system&cmd=edit&id={{$id}}"><strong>{{$name}}</strong></a> - {{$admin_users}}
        ';

    private static $selects = ['*'];

    // Hàm sẽ được gọi trước khi build item. Nó sẽ được gọi với dữ liệu của item hiện tại 
    // Nên khai báo nó nếu cần phải thiết lập một vài thông số mà item không có để build
    private static $preBuildItemCallback = null;


    /**
     * Builds a raw.
     *
     * @param      int     $rootStructureID  The root structure id
     *
     * @return     <type>  The raw.
     */
    public static function buildRaw(int $rootStructureID = null)
    {   
        $rootSystem = Systems::getByIDStructure(is_null($rootStructureID) ? ID_ROOT : $rootStructureID);

        return self::buildRawSystems([$rootSystem]);
    }


    /**
     * Builds raw systems.
     *
     * @param      array  $systems  The systems
     *
     * @return     array  The raw systems.
     */
    private static function buildRawSystems(array $systems)
    {
        foreach($systems as $ID => $system){
            $systems[$ID]['childs'] = $self::buildRawSystems(self::getChilds($system['structure_id']));
        }

        return $systems;
    }

    /**
     * Builds a html.
     *
     * @param      int     $rootStructureID  The root structure id
     *
     * @return     <type>  The html.
     */
    public static function buildHtml(int $rootStructureID = null)
    {   
        $rootSystem = Systems::getByIDStructure(is_null($rootStructureID) ? ID_ROOT : $rootStructureID);

        return self::buildHtmlSystems([$rootSystem]);
    }

    /**
     * Gets the childs.
     *
     * @param      int     $rootStructureID  The root structure id
     *
     * @return     <type>  The childs.
     */
    public static function getChilds(int $rootStructureID = null)
    {   
        return Systems::getDirectSystemsChild($rootStructureID);
    }

    /**
     * Builds html systems.
     *
     * @param      array   $systems  The systems
     *
     * @return     string  The html systems.
     */
    private static function buildHtmlSystems(array $systems)
    {   
        if(!$systems){
            return '';
        }

        $htmlChilds = array_map(function($system){
            if(is_callable(self::$preBuildItemCallback)){
                call_user_func_array(self::$preBuildItemCallback, [&$system]);
            }

            return sprintf(
                self::$childFormat, 
                self::buildHtmlChild($system), 
                self::buildHtmlSystems(self::getChilds($system['structure_id']))
            );
        }, $systems);


        return sprintf(self::$parrentFormat, implode('', $htmlChilds));
    }

    /**
     * Builds a tuha child tree.
     *
     * @param      <type>  $variable  The variable
     *
     * @return     <type>  The tuha child tree.
     */
    private static function buildHtmlChild($variable)
    {
        static $complied = null;

        if(is_null($complied)){
            $complied = preg_replace('#\{\{(.+?)\}\}#', '<?php echo $1;?>', self::$layout);
        }

        ob_start();
        extract($variable);
        eval('?>' . $complied);

        return ob_get_clean();
    }

    
    /**
     * Sets the pre build item callback.
     *
     * @param      Clousoure  $callback  The callback
     */
    public static function setPreBuildItemCallback(Closure $callback)
    {
        self::$preBuildItemCallback = $callback;
    }

    /**
     * Sets the parrent format.
     *
     * @param      string  $format  The format
     */
    public static function setParrentFormat(string $format)
    {
        self::$parrentFormat = $format;
    }

    /**
     * Sets the child format.
     *
     * @param      string  $format  The format
     */
    public static function setChildFormat(string $format)
    {
        self::$childFormat = $format;
    }

    /**
     * Sets the layout.
     *
     * @param      string  $layout  The layout
     */
    public static function setLayout(string $layout)
    {
        self::$layout = $layout;
    }

    /**
     * Sets the selects.
     *
     * @param      array  $selects  The selects
     */
    public static function setSelects(array $selects)
    {
        self::$selects = $selects;
    } 

    /**
     * Tạo selectbox html
     *
     * @param      int     $rootStructureID  The root structure id
     * @param      int     $selected         The selected
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function selectBox(int $rootStructureID = null, array $options = [])
    {   
        $defaultOptions = ['selected' => 0, 'selectedType' => 0, 'props' => [], 'default' => ''];
        
        extract(array_merge($defaultOptions, $options));

        $rootSystem = Systems::getByIDStructure(is_null($rootStructureID) ? ID_ROOT : $rootStructureID);

        $optionTags = [];
        if($default){
            $optionTags[] = $default;
        }
        $optionTags = array_merge($optionTags, self::buildSelectBox([$rootSystem], $selected, $selectedType));

        return sprintf(
            '<select%s>%s</select>',
            self::buildTagProps($props),
            implode('', $optionTags)
        );
    }

    /**
     * Builds a select box.
     *
     * @param      <type>  $systems  The systems
     *
     * @return     array   The select box.
     */
    public static function buildSelectBox(array $systems, $selected = 0, int $selectedType = 0)
    {
        if(!$systems){
            return [];
        }

        return array_reduce($systems, function($ret, $system) use($selected, $selectedType){
            $allChilds = self::getChilds($system['structure_id']);

            $fmt = '<option value="%d" %s>%s%s</option>';
            $indent = str_pad('', Systems::getIDStructureLevel($system['structure_id']) * 4, ' -- ');

            // build option tag 
            $selectedProp = self::isSelected($system, $selected, $allChilds, $selectedType) ? 'selected' : '';
            $ret[] = sprintf($fmt, $system['id'], $selectedProp, $indent, $system['name'] );
            
            // Gọi đệ quy với system id hiện tại
            $ret = array_merge($ret, self::buildSelectBox($allChilds, $selected, $selectedType));

            return $ret;
        }, []);
    }

    /**
     * Determines if selected.
     *
     * @param      array   $currentSystem  The current system
     * @param      <type>  $selected       The selected
     * @param      array   $childs         The childs
     * @param      int     $selectedType   The selected type
     */
    private static function isSelected(array $currentSystem, $selected, array $childs, int $selectedType)
    {
        if(!is_array($selected)){
            return self::isSelectedID($currentSystem, $selected, $childs, $selectedType);
        }

        foreach ($selected as $selectedID) {
            if(self::isSelectedID($currentSystem, $selectedID, $childs, $selectedType)){
                return true;
            }
        }
    }

    /**
     * Determines if selected id.
     *
     * @param      array      $currentSystem  The current system
     * @param      array|int  $selectedID     The selected id
     * @param      array      $childs         The childs
     * @param      int        $selectedType   The selected type
     *
     * @return     bool       True if selected id, False otherwise.
     */
    private static function isSelectedID(array $currentSystem, int $selectedID, array $childs, int $selectedType)
    {
        
        if($selectedType == self::SELECTED_PARENT && !empty($childs[$selectedID])){
            return true;
        }

        if($selectedType == self::SELECTED_CURRENT && $selectedID == $currentSystem['id']){
            return true;
        }
    }

    /**
     * Builds tag properties.
     *
     * @param      array   $props  The properties
     *
     * @return     string  The tag properties.
     */
    public static function buildTagProps(array $props)
    {   
        if(!$props){
            return;
        }

        $props = array_map(function($name) use($props) {
            return sprintf('%s="%s"', $name, $props[$name]);
        }, array_keys($props));

        return ' ' . implode(' ', $props);
    }
}