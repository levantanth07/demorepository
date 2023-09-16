<?php

class FilterCondition
{   
    private static $timeFrom = '';
    private static $timeTo = '';

    /**
     * Initializes the object.
     */
    public static function init()
    {   
        $timeFrom = URL::getString('date_from');
        self::$timeFrom = $timeFrom ? Date_Time::to_time($timeFrom) : time();

        $timeTo = URL::getString('date_to');
        self::$timeTo = $timeTo ? Date_Time::to_time($timeTo) : time();
    }

    /**
     * { function_description }
     *
     * @return     bool  ( description_of_the_return_value )
     */
    public static function validateTimeRange()
    {   
        if(self::$timeFrom > self::$timeTo || self::$timeTo > time()){
            Form::set_flash_message(CostForm::FLASH_MESSAGE_KEY, 'Thời gian bắt đầu và kết thúc không hợp lệ');
            return false;
        }

        if(self::$timeTo - self::$timeFrom > 31 * 86400){
            Form::set_flash_message(CostForm::FLASH_MESSAGE_KEY, 'Vui lòng chọn khoảng thời gian tối đa là một tháng.');
            return false;
        }

        if(date('Ym', self::$timeTo) - date('Ym', self::$timeFrom)!== 0){
            Form::set_flash_message(CostForm::FLASH_MESSAGE_KEY, 'Vui lòng chọn khoảng thời gian trong một tháng.');
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