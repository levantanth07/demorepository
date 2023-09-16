<?php
class OverloadFeatureManagerForm extends Form
{   
    const FLASH_MESSAGE_KEY = 'OverloadFeatureManagerForm';

    protected $map = [];


    public function __construct()
    {
        Form::Form('OverloadFeatureManagerForm');

    }

    public function on_submit()
    {      
        if(!$time = URL::getString('time')){
            return;
        }

        Portal::set_setting('temp_off', $time, '#default');
        Form::set_flash_message(self::FLASH_MESSAGE_KEY, 'Cài đặt thành công !');
    }

    public function draw()
    {
        $this->map['time'] = $this->getConfig();
        $this->map['rows'] = OVERLOAD_FEATURES;
        $this->parse_layout('overload_feature_manager',$this->map);
    }

    /**
     * Gets the configuration.
     *
     * @return     <type>  The configuration.
     */
    private function getConfig()
    {
        if($OVERLOAD_FEATURES = Portal::get_setting('temp_off', false, '#default')){
            return $OVERLOAD_FEATURES;
        } 

        return $this->getDefaultConfig();
    }

    /**
     * Gets the default configuration.
     *
     * @return     <type>  The default configuration.
     */
    private function getDefaultConfig()
    {
        return date('H:i:s d-m-Y');
    }
}
?>