<?php

class WeakPasswordDetector
{
    private static $weakPasswords = [
        'abc@123'
    ];

    private static $withOuts = [
        'abc@12'
    ];

    /**
     * Kiểm tra một mật khẩu có thuộc vào danh sách mật khẩu yếu.
     *
     * @param      string  $password         The password
     * @param      bool    $toLowerPassword  The force to lower before test
     *
     * @return     boolean
     */
    public static function detect(string $password, bool $toLowerPassword = true, string $username = '')
    {
        if($username) {
            self::$withOuts[] = $username;
        }

        if($toLowerPassword === true) {
            $password = mb_strtolower($password);
        }

        return self::isPasswordInWeaks($password) 
            || self::isPasswordInWithOuts($password);
    }

    /**
     * Determines whether the specified password is weak password.
     *
     * @param      string  $password  The password
     *
     * @return     bool    True if the specified password is weak password, False otherwise.
     */
    public static function isPasswordInWeaks(string $password)
    {
        return in_array($password, self::$weakPasswords);
    } 

    /**
     * Determines whether the specified password is password in with outs.
     *
     * @param      string  $password  The password
     *
     * @return     bool    True if the specified password is password in with outs, False otherwise.
     */
    public static function isPasswordInWithOuts(string $password)
    {
        foreach (self::$withOuts as $keyword) {
            if(preg_match('#' . $keyword . '#i', $password)) {
                return true;
            }
        }
    }
}