<?php

require_once ROOT_PATH . 'packages/core/includes/utils/htmlpurifier/HTMLPurifier.standalone.php';

class DataFilter
{
    public static function filterString($str) {
        return filter_var($str, FILTER_SANITIZE_STRING);
    }

    public static function filterOut($str, $filter = true) {
        return $filter ? htmlentities($str) : $str;
    }

    public static function filterIn($str, $filter = true) {
        return $filter ? strip_tags($str) : $str;
    }

    public static function removeHtmlTags(&$data, $elements = [])
    {
        if (is_array($data)) {
            $arrayKeys = array_keys($data);
            $keys = !empty($elements) ? $elements : $arrayKeys;
            foreach ($keys as $key) {
                if (in_array($key, $arrayKeys)) {
                    $data[$key] = strip_tags($data[$key]);
                }
            }
        } else {
            $data = strip_tags($data);
        }
    }

    public static function removeXSSinHtml($str) {
        $output = "";
        if($str && $str != ""){
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.ForbiddenElements', array('script','style','applet'));
            $purifier = new HTMLPurifier($config);
            $output = $purifier->purify(htmlspecialchars_decode($str));
        }
        return $output;
    }

    // Thay thế <xxx hoăc xxx> thành < xxx và xxx > với xxx là kí tự bất kì ngoại trừ space 
    public static function breakHtmlTags($str){
        return preg_replace(['#<([^\s]+)#', '#([^\s]+)>#'], ['< $1', '$1 >', ], $str);
    }

    public static function removeDuplicatedSpaces($str){
        return preg_replace('/\s+/', ' ', trim($str));
    }

    /**
     * removeXSSinArray
     *
     * @param array $array
     * @param array $excetions
     * @return array
     */
    public static function removeXSSinArray(array $array = [], array $excetions = []): array {
        foreach ($array as $key => $value) {
            if (!in_array($key, $excetions)) {
                $array[$key] = self::removeXSSinHtml($value);
            }//end if
        }//end foreach

        return $array;
    }

    /**
     * Xử lý Xss chuỗi id
     * "1, 2, 3, ..., n"
     * 
     * @param string|null $listIds
     * @return string
     */
    public static function removeXSSinListIds($listIds): string
    {
        if (!$listIds) {
            return $listIds;
        }//end if

        $_listIds = explode(',', $listIds);
        $_listIds = self::removeXSSinArray($_listIds);
        $listIds = implode(',', $_listIds);
        return $listIds;
    }

    /**
     * Xử lý dũ liệu trống trong chuỗi id
     * "1,, 3, ..., n"
     * 
     * @param string|null $listIds
     * @return array
     */
    public static function removeEmptyValueListIds($listIds): array
    {
        if (!$listIds) {
            return [];
        }//end if

        $_listIds = explode(',', $listIds);
        return array_filter($_listIds, function($value) { return !is_null($value) && trim($value) !== ''; });
    }

}
