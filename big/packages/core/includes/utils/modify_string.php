<?php
class ModifyPhoneNumber
{
    static function hidePhoneNumber($phoneNumber, $length){
        $phoneNumber = trim($phoneNumber);
        if(strlen($phoneNumber) == 0) return '';
        if (empty($length)) $length = 3;
        if(strlen($phoneNumber) > $length) {
            $phoneNumberCensored = substr($phoneNumber, 0, strlen($phoneNumber) - $length) . str_repeat("*", $length);
        } else {
            $phoneNumberCensored = str_repeat("*", strlen($phoneNumber));
        }
        return $phoneNumberCensored;
    }
}