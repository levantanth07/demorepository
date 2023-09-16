<?php
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Exception\ClientException;

class BiggameAPI
{
    /**
     * Constructs a new instance.
     */
    private function __construct(){}


    /**
     * Lấy instance của class
     *
     * @throws     Exception  (description)
     *
     * @return     static
     */
    public static function instance()
    {
        if(!defined('TUHA_BIGGAME_ENDPOINT')){
            throw new Exception('Vui lòng thiết lập "TUHA_BIGGAME_ENDPOINT"');
        }

        return new static;
    }

    /**
     * Sends list carts.
     *
     * @param      array  $listCarts  The list carts
     *
     * @return     string
     */
    public function sendListCarts(array $listCarts) : string
    {
        try {
            if (defined('BIGGAME_SYNC') && BIGGAME_SYNC === 1) {
                $headers = [
                    'Content-Type' => 'application/json'
                ];
                $client = new Client([
                    'headers' => $headers
                ]);
                $listCarts = array_values($listCarts);
                if (!empty($listCarts)) {
                    foreach ($listCarts as $key => $listCart) {
                        $listCarts[$key]['id'] = (int) $listCart['id'];
                        $listCarts[$key]['user_confirmed'] = (int) $listCart['user_confirmed'];
                        $listCarts[$key]['total_price'] = (int) $listCart['total_price'];
                        $listCarts[$key]['group_id'] = (int) $listCart['group_id'];
                        $listCarts[$key]['status_id'] = (int) $listCart['status_id'];
                        $listCarts[$key]['no_revenue'] = (int) $listCart['no_revenue'];
                        $listCarts[$key]['level'] = (int) $listCart['level'];
                    }
                }
                
                $response = $client->request('POST', TUHA_BIGGAME_ENDPOINT , [
                    'json' => ['orders' => array_values($listCarts)]
                ]);
                return Message::toString($response);
            }
            return 'BIGGAME SYNC OFF';
        } catch (ClientException $e) {
            return Message::toString($e->getResponse());
        }
    }
}
