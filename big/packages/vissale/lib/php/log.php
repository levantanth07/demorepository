<?php

require_once ROOT_PATH.'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;



function storeLog($id, $table, $dataStore){
    if(Session::is_set('debuger_id')) return;

    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    $new_token = LOG_TOKEN;
    $data = [
        'id' => $id,
        'table' => $table,
        'data'  => $dataStore
    ];
    try {
        $response = $client->request('POST', "store",
            [
                'json' => $data,
                'headers' => [
                    'Authorization' => "Bearer {$new_token}",
                    'Accept'     => 'application/json',
                    "Content-Type" => "application/json"
                ]
            ]
        );
        $body = $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody, true);
    } catch (RequestException $e) {
        // create a log channel
        $log = new Logger('mongo-store');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}

function getLog($id, $table, $take)
{
    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    $new_token = LOG_TOKEN;
    try {
        $response = $client->request('GET', "get-log?table=$table&id=$id&take=$take",
            [
                'headers' => [
                    'Authorization' => "Bearer {$new_token}",
                    'Accept'     => 'application/json'
                ]
            ]
        );
        $body = $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody, true);
    } catch (RequestException $e) {
        // create a log channel
        $log = new Logger('mongo-get');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}

function getSecurityLog($payload)
{
    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    $new_token = LOG_TOKEN;
    try {
        $response = $client->request('POST', "get-security-log",
            [
                'json' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$new_token}",
                    'Accept'     => 'application/json'
                ]
            ]
        );
        $body = $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody, true);
    } catch (RequestException $e) {
        $log = new Logger('mongo-get');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));

        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}
function storeSecurityLog($payload){
    if(Session::is_set('debuger_id')) return;
    
    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    $new_token = LOG_TOKEN;
    try {
        $response = $client->request('POST', "store-security-log",
            [
                'json' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$new_token}",
                    'Accept'     => 'application/json',
                    "Content-Type" => "application/json"
                ]
            ]
        );
        $body = $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody, true);
    } catch (RequestException $e) {
        $log = new Logger('mongo-store');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}

function getAccountLog($payload)
{
    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    $new_token = LOG_TOKEN;
    try {
        $response = $client->request('POST', "get-account-log",
            [
                'json' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$new_token}",
                    'Accept'     => 'application/json'
                ]
            ]
        );
        $body = $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody, true);
    } catch (RequestException $e) {
        // create a log channel
        $log = new Logger('mongo-get');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}

function storeAccountLog($payload){
    if(Session::is_set('debuger_id')) return;

    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    $new_token = LOG_TOKEN;
//  Payload: "account_id":"electric","log_type":1,"content": "Đăng nhập tài khoản","time": 1625719812,"ip": "127.0.0.1","group_id": 1135,"module_id": 0
    try {
        $response = $client->request('POST', "store-account-log",
            [
                'json' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$new_token}",
                    'Accept'     => 'application/json',
                    "Content-Type" => "application/json"
                ]
            ]
        );
        $body = $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody, true);
    } catch (RequestException $e) {
        // create a log channel
        $log = new Logger('mongo-store');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}

function updateAccountLog($payload){
    if(Session::is_set('debuger_id')) return;

    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    $new_token = LOG_TOKEN;
//  Payload: "account_id":"electric","log_type":1,"content": "Đăng nhập tài khoản","time": 1625719812,"ip": "127.0.0.1","group_id": 1135,"module_id": 0
    try {
        $response = $client->request('POST', "update-account-log",
            [
                'json' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$new_token}",
                    'Accept'     => 'application/json',
                    "Content-Type" => "application/json"
                ]
            ]
        );
        $body = $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody, true);
    } catch (RequestException $e) {
        // create a log channel
        $log = new Logger('mongo-store');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}


function storeManyLog(array $rows){
    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    try {
        $response = $client->request('POST', "store-many", [
                'json' => $rows,
                'headers' => [
                    'Authorization' => "Bearer " . LOG_TOKEN,
                    'Accept'     => 'application/json',
                    "Content-Type" => "application/json"
                ]
            ]
        );

        return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
        // create a log channel
        $log = new Logger('mongo-store');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}


function getManyLog($IDs, $table, $take){
    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    try {
        $url = "get-many-log?table=$table&ids=$IDs&take=$take";
        $options = [
            'headers' => [
                'Authorization' => "Bearer " . LOG_TOKEN,
                'Accept'     => 'application/json',
                "Content-Type" => "application/json"
            ]
        ];

        $response = $client->request('GET', $url, $options);

        return json_decode($response->getBody(), true);
        
    } catch (RequestException $e) {
        // create a log channel
        $log = new Logger('mongo-store');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}

function storeAccountPhotoLog($payload){
    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    $new_token = LOG_TOKEN;
    try {
        $response = $client->request('POST', "store-account-photo-log",
            [
                'json' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$new_token}",
                    'Accept'     => 'application/json',
                    "Content-Type" => "application/json"
                ]
            ]
        );
        $body = $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody, true);
    } catch (RequestException $e) {
        $log = new Logger('mongo-store');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}

function getAccountPhotoLog($payload){
    $client = new Client([
        'base_uri' => API_LOG,
        'timeout'  => 5.0,
    ]);
    $new_token = LOG_TOKEN;
    try {
        $response = $client->request('GET', "get-log-account-photo-log",
            [
                'json' => $payload,
                'headers' => [
                    'Authorization' => "Bearer {$new_token}",
                    'Accept'     => 'application/json',
                    "Content-Type" => "application/json"
                ]
            ]
        );
        $body = $response->getBody();
        $stringBody = (string) $body;
        return json_decode($stringBody, true);
    } catch (RequestException $e) {
        $log = new Logger('mongo-store');
        $log->pushHandler(new StreamHandler(ROOT_PATH .'cache/mongodb.log', Logger::DEBUG));
        $log->warning(Psr7\str($e->getRequest()));
        if ($e->hasResponse()) {
            $log->warning(Psr7\str($e->getResponse()));
        }
        return false;
    }
}