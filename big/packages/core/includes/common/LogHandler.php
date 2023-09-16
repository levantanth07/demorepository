<?php
class LogHandler
{
    const LOCAL_MYSQL_LOG_API = 'http://mysqlwritelog.local/account-log';
    const ACCOUNT_LOG_TABLE = 'account_log';

    public static function sendLogToQueue($data)
    {
        $host = $_SERVER['HTTP_HOST'];
        $dataStr = json_encode($data);
        $ch = curl_init(self::LOCAL_MYSQL_LOG_API);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $host,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($dataStr))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    public static function sendSqlToQueue($data)
    {
        $sql = self::buildLogSql($data);

        System::is_local() ? DB::query($sql) : self::sendLogToQueue(['sql_string' => $sql]);
    }

    public static function buildLogSql($arrData)
    {
        extract($arrData);
        $sql = "INSERT INTO " . self::ACCOUNT_LOG_TABLE . " (`account_id`, `log_type`, `content`, `time`, `ip`, `group_id`) 
            VALUES ('$account_id', '$log_type', '$content', '$time', '$ip', '$group_id')";
        return $sql;
    }
}