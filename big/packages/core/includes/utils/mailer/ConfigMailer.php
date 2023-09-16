<?php

class ConfigMailer
{
    static $host;
    static $smtpAuth;
    static $username;
    static $password;
    static $smtpsSecure;
    static $port;
    static $charset;

    function __construct()
    {
        self::$host = 'smtp.mailgun.org';
        self::$smtpAuth = true;
        self::$username = MAILER_USERNAME;
        self::$password = MAILER_PASSWORD;
        self::$smtpsSecure = 'STARTTLS';
        self::$port = 587;
        self::$charset = 'UTF-8';
    }
}
