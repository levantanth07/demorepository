<?php

class FilterCondition
{   
    private static $timeFrom = '';
    private static $timeTo = '';

    private static $displayTimeFrom = '';
    private static $displayTimeTo = '';

    /**
     * Initializes the object.
     */
    public static function init()
    {   
        self::$timeFrom = URL::getDateTimeFmt('date_from', 'd/m/Y', 'Y-m-d');
        self::$timeTo = URL::getDateTimeFmt('date_to', 'd/m/Y', 'Y-m-d');

        self::$displayTimeFrom = URL::getDateTimeFmt('date_from', 'd/m/Y', 'd/m/Y', date('m/d/Y'));
        self::$displayTimeTo = URL::getDateTimeFmt('date_to', 'd/m/Y', 'd/m/Y', date('m/d/Y'));
    }

    /**
     * { function_description }
     *
     * @return     bool  ( description_of_the_return_value )
     */
    public static function validateTimeRange()
    {   
        $from = strtotime(self::$timeFrom);
        $to = strtotime(self::$timeTo);

        if($from > $to || $to > time()){
            Form::set_flash_message(CostMktForm::FLASH_MESSAGE_KEY, 'Thời gian bắt đầu và kết thúc không hợp lệ');
            return false;
        }

        if($to - $from > 31 * 86400){
            Form::set_flash_message(CostMktForm::FLASH_MESSAGE_KEY, 'Vui lòng chọn khoảng thời gian tối đa là một tháng.');
            return false;
        }

        return true;
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