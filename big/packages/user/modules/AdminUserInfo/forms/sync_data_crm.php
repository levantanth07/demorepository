<?php
use GuzzleHttp\Client;
class SyncDataCrm extends Form
{
    function __construct()
    {
        Form::Form('SyncDataCrm');
    }
    function draw()
    {
        $urlStart = API_HTTP_START_CRM;
        $urlStop = API_HTTP_STOP_CRM;
        $urlCrm = API_HTTP_CRM;
        $groupId = DB::escape(Url::get('groupId'));
        $group = DB::fetch('select is_crm from `groups` where id="'.$groupId.'"');
        if (!empty($group['is_crm'])) {
            $client = new Client();
            $data = $groupId;
            try {
                $start = $client->post($urlStart,array());
                $response = $client->post(
                    $urlCrm,
                        array(
                            'json' => array(
                                'group_id'   => $data,
                                'provider' => API_PROVIDER,
                                'token' => TOKEN_CRM
                            ),
                            'verify' => false,
                            'allow_redirects' => [
                                'max'             => 10,      
                                'strict'          => true,     
                                'referer'         => true,     
                                'track_redirects' => true
                            ],
                        )
                );
                $stop = $client->post($urlStop,array());
                echo "TRUE";
            } catch (Exception $e) {
                
            }
        } else {
            echo "FALSE";
        }
    }
}
?>