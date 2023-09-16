<?php
function get_testing_accounts()
{
    $testingAccounts = [
        'guest', 'kkk.khoand', 'mintice', 'palshop', 'admin.thanhhuyen', 'pal.huyenkute', 'pal.Huyennt125',
        'pal.support1', 'PAL.khoand', 'admin.minhhuyen'
    ];

    if (defined('TESTING_ACCOUNTS')) {
        $testingAccounts = array_merge($testingAccounts, TESTING_ACCOUNTS);
    }

    return $testingAccounts;
}

function get_testing_groups()
{
    $testingGroups = [15821, 2417, 14385, 17059];

    if (defined('TESTING_GROUPS')) {
        $testingGroups = array_merge($testingGroups, TESTING_GROUPS);
    }

    return $testingGroups;
}

function th_debug($data, $exit = true)
{
    if (System::is_local() || is_testing_account()) {
        echo '<pre>';
        if(is_array($data))
        array_map(function($var){ print_r($var); }, $data);
        else print_r($data);
        echo '</pre>';
        $exit && exit;
    }
}

function th_dump($data, $exit = true)
{
    if (System::is_local() || is_testing_account()) {
        var_dump($data);
        if ($exit) {
            die;
        }
    }
}

function is_testing_account(string $userID = '')
{
    return in_array(Session::get('user_id') || $userID, get_testing_accounts());
}

function is_testing_group()
{
    return in_array(Session::get('group_id'), get_testing_groups());
}

function is_logged_in()
{
    return !empty(Session::get('user_id')) && Session::get('user_id') != 'guest';
}

function generate_random_str($length = 8)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

/**
 * Write log
 */
function write_log($content, $path = '')
{
    if ($path === '' && defined('ROOT_PATH')) {
        $path = ROOT_PATH . 'cache/log.log';
    }

    if (file_exists(ROOT_PATH . 'cache')) {
        file_put_contents($path, $content . "\n", FILE_APPEND);
    }
}

/**
 * Disables the modules when overload.
 */
function disable_modules_when_overload()
{
    if(!defined('OVERLOAD_FEATURES')){
        return;
    }

    $rawTime = Portal::get_setting('temp_off', false, '#default');

    if(!$time = strtotime($rawTime)){
        return;
    }

    if($time < time()){
        return;
    }
    $page = URL::getString('page');
    foreach (OVERLOAD_FEATURES as $feature) {

        // bỏ qua nếu không phải page này
        if($feature['page'] != $page){
            continue;
        }

        // params rỗng và là page hiện tại thì thoát
        if(!$feature['params']){
            return fm_modules_when_overload($feature['id'], $rawTime);
        }

        foreach($feature['params'] as $param){
            // param rỗng và là page hiện tại thì thoát
            if(!$param){
                return fm_modules_when_overload($feature['id'], $rawTime);
            }

            // khớp params thì thoát
            if(!array_diff($param, $_REQUEST)){
                // ngược lại thoát
                return fm_modules_when_overload($feature['id'], $rawTime);
            }
        }
    }
}

/**
 * { function_description }
 *
 * @param      <type>  $id     The identifier
 * @param      <type>  $time   The time
 */
function fm_modules_when_overload($id, $time)
{
    Form::set_flash_message('__OVERLOAD_FEATURES__', $id);
    Form::set_flash_message('__OVERLOAD_FEATURES_TIME__', $time);
}
