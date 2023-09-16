<?php

class NumberToText
{
    private static $maps = ['không','một','hai','ba','bốn','năm','sáu','bảy','tám','chín' ];
    
    private static function chuc($e) {
        $n = intval($e);

        if ($n >= 20) {
            $dv = self::$maps[$e[1]];
            if ($e[1] == 4) $dv = 'tư';
            else if ($e[1] == 1) $dv = 'mốt';
            else if ($e[1] == 5) $dv = 'lăm';

            return self::$maps[$e[0]] . ' mươi ' . ($e[1] > 0 ? $dv : '');
        } else if ($n >= 10) {
            return 'mười ' . ($n == 10 ? '' : ($e[1] == 0 ? '' : self::$maps[$e[1]]));
        }

        return;
    }

    private static function tram($e) {
        return intval($e)
            ? self::$maps[$e[0]] . ' trăm ' . ($e[1] == 0 && $e[2] != 0 ? 'linh ' . self::$maps[$e[2]] : self::chuc(substr($e, 1)))
            : '';
    }
    private static function dv($e, $i) {
        return self::$maps[$e];
    }

    public static function parse($e)
    {
        $text = Arr::of(preg_split('/(?=(?:\d{3})+(?:\.|$))/', $e))
            ->reverse()
            ->map(function ($e, $i) {
                switch (strlen($e)) {
                    case 1:
                        return self::dv($e, $i);

                    case 2:
                        return self::chuc($e);

                    case 3:
                        return self::tram($e);
                }
            })
            ->map(function ($e, $i) {
                return $e ? $e . ' ' . ['', 'nghìn', 'triệu', 'tỉ'][($i >= 4 ? $i + 1 : $i) % 4] : '';
            })
            ->reverse()
            ->join(' ');
            
        $text = preg_replace('/\s*không*$/i', '', $text);
        
        return trim($text);
    }
}