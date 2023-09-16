<?php

class FilterCondition
{   
    const ORDER_RATING_DESC = 'desc';
    const ORDER_RATING_ASC = 'asc';

    private static $timeFrom = '';
    private static $timeTo = '';
    private static $isActive = 1;
    private static $users = [];
    private static $times = [];
    private static $dates = [];


    /**
     * Initializes the object.
     */
    public static function init()
    {   
        $timeFrom = URL::getString('date_from');
        self::$timeFrom = date('Y-m-d', $timeFrom ? Date_Time::to_time($timeFrom) : time());

        $timeTo = URL::getString('date_to');
        self::$timeTo = date('Y-m-d', $timeTo ? Date_Time::to_time($timeTo) : time());

        self::$isActive = URL::getUInt('is_active', 0);

        $period = new DatePeriod(new DateTime(self::$timeFrom), new DateInterval('P1D'), new DateTime(self::$timeTo));
        foreach ($period as $key => $value) {
            self::$dates[] = $value->format('Y-m-d');      
        }
        self::$dates[] = self::$timeTo;

        self::$users = URL::getArray('users', [0]);
        self::$times = URL::getArray('select_times', [0]);
    }

    /**
     * { function_description }
     *
     * @return     bool  ( description_of_the_return_value )
     */
    public static function validateTimeRange()
    {
        $timeTo = strtotime(self::$timeTo);
        $timeFrom = strtotime(self::$timeFrom);

        if($timeFrom > $timeTo || $timeTo > time()){
            Form::set_flash_message(AdvNewDashboardForm::FLASH_MESSAGE_KEY, 'Thời gian bắt đầu và kết thúc không hợp lệ');
            return false;
        }

        if($timeTo - $timeFrom > 31 * 86400){
            Form::set_flash_message(AdvNewDashboardForm::FLASH_MESSAGE_KEY, 'Vui lòng chọn khoảng thời gian tối đa là một tháng.');
            return false;
        }

        return true;
    }
    
    public static function validateDayRange()
    {
        $timeTo = strtotime(self::$timeTo);
        $timeFrom = strtotime(self::$timeFrom);

        if($timeFrom > $timeTo || $timeTo > time()){
            Form::set_flash_message(AdvDayDashboardForm::FLASH_MESSAGE_KEY, 'Thời gian bắt đầu và kết thúc không hợp lệ');
            return false;
        }

        if($timeTo - $timeFrom > 31 * 86400){
            Form::set_flash_message(AdvDayDashboardForm::FLASH_MESSAGE_KEY, 'Vui lòng chọn khoảng thời gian tối đa là một tháng.');
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
