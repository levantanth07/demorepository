<?php
/**
 * { function_description }
 */
function refuse_if_coccoc_browser()
{
    $yourbrowser = $_SERVER['HTTP_USER_AGENT'];
    $yourbrowser .= isset($_SERVER['HTTP_SEC_CH_UA']) ? $_SERVER['HTTP_SEC_CH_UA'] : "";

    if (preg_match('/coc_coc_browser|coccoc/i', $yourbrowser)) {
        die('Phầm mềm Tuha đã tạm dừng hoạt động trên trình duyệt Cốc cốc. Kính mong Qúy khách hàng thông cảm vì sự bất tiện này!');
    }
}

/**
 * Determines if local.
 *
 * @return     bool  True if local, False otherwise.
 */
function is_local()
{
    return defined('ENVIRONMENT') && ENVIRONMENT !== 'production';
}

/**
 * dump and die
 *
 * @param      <type>  ...$data  The data
 */
function dd(...$values)
{
    dump(...$values);
    exit;
}

/**
 * { function_description }
 *
 * @param      <type>  ...$values  The values
 */
function dump(...$values)
{
    echo '<pre style="background: #f9f9f9; line-height: 1; padding: 15px; margin: 15px; border-radius: 3px;">';
    foreach ($values as $value) {
        if (is_null($value)) {
            echo 'null';
        }

        else if (is_bool($value)) {
            echo $value ? 'true' : 'false';
        }

        else if (is_string($value)) {
            echo sprintf('"%s"', htmlentities($value));
        }

        else if ($value instanceof \Exception || $value instanceof \Error) {
            $messages = [sprintf(
                "%s MESSAGE: %s\n",
                $value instanceof \Exception ? 'EXCEPTION' : 'ERROR',
                $value->getMessage()
            )];

            foreach ($value->getTrace() as $key => $trace) {
                $messages[] = sprintf(
                    "#%d %s:%d [%s%s%s]\n",
                    ++$key,
                    $trace['file'] ?? '',
                    $trace['line'] ?? '',
                    $trace['class'] ?? '',
                    $trace['type'] ?? '',
                    $trace['function'] ?? ''
                );
            }
            echo implode("\n", $messages);
        } else {
            print_r($value);
        }
    }
    echo '</pre>';
}

/**
 * Determines if https protocol.
 *
 * @return     bool  True if https protocol, False otherwise.
 */
function is_https_protocol()
{
    return defined('PROTOCOL') && PROTOCOL === 'https';
}

/**
 * { function_description }
 *
 * @param      <type>  $value  The value
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function array_wrap($value)
{
    if (is_null($value)) {
        return [];
    }

    return is_array($value) ? $value : [$value];
}

/**
 * Determines if set date.
 *
 * @return     bool  True if set date, False otherwise.
 */
function is_set_date(string $date = null)
{
    return !empty($date) && $date != NULL_TIME && $date != NULL_DATE; 
}

/**
 * { function_description }
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function now() 
{
    return date('Y-m-d H:i:s');
}