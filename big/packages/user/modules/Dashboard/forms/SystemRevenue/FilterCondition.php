<?php

class FilterCondition
{   
    const ORDER_RATING_DESC = 'desc';
    const ORDER_RATING_ASC = 'asc';

    private static $timeFrom = '';
    private static $timeTo = '';
    private static $status = XAC_NHAN;
    private static $orderRating = FilterCondition::ORDER_RATING_DESC;
    private static $sortByLevel = 0;
    private static $levels = [1 => 'root', 'cha', 'F0', 'F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'F7'];
    private static $selectedSystemID;

    /**
     * Initializes the object.
     */
    public static function init()
    {   
        $timeFrom = URL::getString('date_from');
        self::$timeFrom = date('Y-m-d', $timeFrom ? Date_Time::to_time($timeFrom) : time());

        $timeTo = URL::getString('date_to');
        self::$timeTo = date('Y-m-d', $timeTo ? Date_Time::to_time($timeTo) : time());

        self::$status = URL::getUInt('status_id', XAC_NHAN);
        self::$orderRating = URL::getString('order_rating', FilterCondition::ORDER_RATING_DESC);
        self::$sortByLevel = URL::getUInt('order_level', 1);
        self::$selectedSystemID = URL::getArray('system_group_id', [0]);
    }

    /**
     * Getter cho các thuộc tính bao gồm cả private
     * Cách dùng: FilterCondition::getTimeFrom() -> FilterCondition::$timeFrom
     *
     * @param      string  $name   The name
     * @param      <type>  $args   The arguments
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function __callStatic(string $name, $args)
    {
        if(preg_match('#get([A-z]+)#', $name, $matches) && isset(self::${lcfirst($matches[1])})){
            return self::${lcfirst($matches[1])};
        }

        return null;
    }
}