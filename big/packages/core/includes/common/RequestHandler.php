<?php

class RequestHandler {
    /**
     * Sends a json.
     *
     * @param      <type>  $data   The data
     */
    public static function sendJson($data)
    {   
         @ob_clean();
        header('Content-Type: application/json');
        die(json_encode($data));
    }


    /**
     * Sends a json status.
     *
     * @param      <type>  $data    The data
     * @param      int     $status  The status
     */
    public static function sendJsonStatus($data, $status = 1)
    {   
        $status = $status == 1 ? 'success' : 'error';

        if(is_array($data)){
            self::sendJson(array_merge(['status' => $status], $data));
        }

        self::sendJson(['status' => $status, 'message' => $data]);
    }

    /**
     * Sends a json error.
     *
     * @param      <type>  $data   The data
     */
    public static function sendJsonError($data)
    {
        self::sendJsonStatus($data, 0);
    }


    /**
     * Sends a json success.
     *
     * @param      <type>  $data   The data
     */
    public static function sendJsonSuccess($data)
    {
        self::sendJsonStatus($data, 1);
    }

    /**
     * Sends an exception.
     *
     * @param      Exception  $e      { parameter_description }
     */
    public static function sendException($e)
    {
        if(!is_local()) {
            self::sendJsonError('Lỗi hệ thống !');
        }

        self::sendJsonError([
            'msg' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }

    /**
     * Shows the error.
     *
     * @param      string  $message  The message
     */
    public static function showError(string $message)
    {
        echo sprintf('<div class="alert alert-danger" role="alert">%s</div>', $message);
        die();
    }
}