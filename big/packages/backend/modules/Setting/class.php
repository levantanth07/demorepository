<?php
class Setting extends Module
{
    function __construct($row)
    {   
        if($this->canAccessOverloadFeatureManager()){
            Module::Module($row);
            require_once('db.php');
            require_once 'forms/overload_feature_manager.php';
            return $this->add_form(new OverloadFeatureManagerForm());
        }
                
        if(User::can_admin(MODULE_SETTING,ANY_CATEGORY))
        {
            Module::Module($row);
            require_once('db.php');
            switch(Url::get('cmd'))
            {
                case 'seo':
                    $this->seo_config();
                    break;
                case 'front_end':
                    $this->front_back();
                    break;
                case 'unlink':
                    $this->delete_file();
                    break;
                default:
                    $this->account_setting();
                    break;
            }
        }
        else
        {
            Url::access_denied();
        }
    }
    

    /**
     * Determines ability to access overload feature manager.
     *
     * @return     bool  True if able to access overload feature manager, False otherwise.
     */
    private function canAccessOverloadFeatureManager()
    {
        return defined('OVERLOAD_FEATURE_MANAGER') 
           && in_array(Session::get('user_id'), OVERLOAD_FEATURE_MANAGER) 
           && URL::getString('cmd') === 'overload_feature_manager';
    }

    function delete_file()
    {
        if(Url::get('link') and file_exists(Url::get('link')) and User::can_delete(false,ANY_CATEGORY))
        {
            @unlink(Url::get('link'));
        }
        echo '<script>window.close();</script>';
    }
    function seo_config()
    {
        require_once 'forms/seo.php';
        $this->add_form(new SettingForm());
    }
    function front_back()
    {
        require_once 'forms/front.php';
        $this->add_form(new FrontEndForm());
    }
    function account_setting()
    {
        require_once 'forms/account_setting.php';
        $this->add_form(new AccountSettingForm());
    }
}
?>