<?php

/**
 * This class describes an arr.
 */
class Arr
{
    
    /**
     * Constructs a new instance.
     *
     * @param      <type>  $arr    The arr
     */
    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }
    
    /**
     * { function_description }
     *
     * @param      array  $arr    The arr
     *
     * @return     self
     */
    public static function of(array $arr)
    {
        return new self($arr);
    }
    
    /**
     * Lặp qua từng phần tử của mảng
     *
     * @param      Closure  $callback  The callback. $callback($value, $key, $curent_array)
     *
     * @return     self
     */
    public function map(Closure $callback)
    {
        foreach ($this->arr as $key => $val) {
            $this->arr[$key] = call_user_func_array($callback, [$val, $key, $this]);
        }
        
        return $this;
    }
    
    /**
     * Lọc các phần tử mà callback trả về true. Phương thức nhận vào 1 callback($value, $key, $curent_array)
     *
     * @param      Closure  $callback  The callback
     *
     * @return     self     ( description_of_the_return_value )
     */
    public function filter(Closure $callback)
    {
        foreach ($this->arr as $key => $val) {
            if (!call_user_func_array($callback, [$val, $key, $this])) {
                unset($this->arr[$key]);
            }
        }
        
        return $this;
    }

    /**
     * Lọc các phần tử mà callback trả về true. Phương thức nhận vào 1 callback($value, $key, $curent_array)
     *
     * @param      Closure  $callback  The callback
     *
     * @return     self     ( description_of_the_return_value )
     */
    public function filterKey(Closure $callback)
    {
        $keys = [];
        foreach ($this->arr as $key => $val) {
            if (call_user_func_array($callback, [$val, $key, $this])) {
                $keys[] = $key;
            }
        }
        
        return Arr::wrap($keys);
    }

    /**
     * Gộp mảng vào mảng hiện tại
     *
     * @param      array  $array  The array
     *
     * @return     self   ( description_of_the_return_value )
     */
    public function merge(array ...$array)
    {
        foreach ($array as $value) {
            $this->arr += $value;
        }

        return $this;
    }

    /**
     * Trả về TRUE nếu tất cả các phần tử trong mảng làm cho callback trả về TRUE.
     *
     * @return     bool  ( description_of_the_return_value )
     */
    public function every(Closure $callback)
    {
        foreach ($this->arr as $key => $val) {
            if (!call_user_func_array($callback, [$val, $key, $this])) {
                return false;
            }
        }

        return true;
    }

    /**
     * TRUE nếu tồn tại giá trị trong mảng, ngược lại FALSE.
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function exists($value)
    {
        return in_array($value, $this->arr);
    }

    /**
     * Trả lại index đầu tiên tìm thấy giá trị, ngược lại FALSE
     *
     * @param      <type>  $value  The value
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function indexOf($value)
    {
        return array_search($value, $this->arr);
    }

    /**
     * Nối các phần tử của mảng lại theo chuỗi phân cách
     *
     * @param      string  $separator  The separator
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function join(string $separator = ',')
    {
        return implode($separator, $this->arr);
    }

    /**
     * Trả lại instance của Arr với keys của mảng
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function keys()
    {
        return Arr::of(array_keys($this->arr));
    }

    /**
     * Trả lại instance của Arr với values của mảng
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function values()
    {
        return Arr::of(array_values($this->arr));
    }

    /**
     * Trả lại phần tử cuối cùng của mảng, nếu mảng rỗng trả lại null
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function pop()
    {
        return $this->wrap(array_pop($this->arr));
    }

    /**
     * { function_description }
     *
     * @param      <type>  $value  The value
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function wrap($val)
    {
        return is_array($val) ? Arr::of($val) : $val;
    }

    /**
     * Thêm một hoặc nhiều phần tử vào cuối mảng
     *
     * @param      <type>  ...$values  The values
     *
     * @return     self    ( description_of_the_return_value )
     */
    public function push($values)
    {
        array_push($this->arr, ...$values);

        return $this;
    }

    /**
     * { function_description }
     *
     * @param      <type>  $callback  The callback
     * @param      array   $init      The initialize
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function reduce($callback, $init = [])
    {
        $results = $init;
        foreach ($this->arr as $key => $val) {
            $results = call_user_func_array($callback, [$results, $val, $key, $this]);
        }

        return $this->wrap($results);
    }

    /**
     * Nhóm lại theo khóa
     *
     * @param      string  $key      The key
     * @param      bool    $keepKey  The keep key
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function groupByKey(string $key, bool $keepKey = false)
    {
        return $this->reduce(function($res, $row) use($key, $keepKey) {
            $keyValue = $row[$key] ?? null;

            if (is_null($keyValue)) {
                return $res;
            }

            if (!isset($res[$keyValue])) {
                $res[$keyValue] = [];
            }

            // Không giữ lại khóa thì xóa khóa khỏi mảng
            if (!$keepKey) {
                unset($row[$key]);
            }

            $res[$keyValue][] = $row;
            
            return $res;
        }, []);
    }

    /**
     * { function_description }
     *
     * @param      string  $key      The key
     * @param      bool    $keepKey  The keep key
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function keyBy(string $key, bool $keepKey = false)
    {
        return $this->reduce(function ($res, $row) use ($key, $keepKey) {
            if (is_null($keyValue = $row[$key] ?? null)) {
                return $res;
            }

            // Không giữ lại khóa thì xóa khóa khỏi mảng
            if (!$keepKey) {
                unset($row[$key]);
            }

            $res[$keyValue] = $row;
            
            return $res;
        }, []);
    }

    /**
     * { function_description }
     *
     * @param      bool  $preserve_keys  The preserve keys
     *
     * @return     self  ( description_of_the_return_value )
     */
    public function reverse(bool $preserve_keys = false)
    {
        $this->arr = array_reverse($this->arr, $preserve_keys);

        return $this;
    }

    /**
     * Lấy phần tử đầu tiên
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function shift()
    {
        return $this->wrap(array_shift($this->arr));
    }

    /**
     * Copy một phần mảng
     *
     * @param      int       $offset         The offset
     * @param      int|null  $length         The length
     * @param      bool      $preserve_keys  The preserve keys
     *
     * @return     <type>    ( description_of_the_return_value )
     */
    public function slice(int $offset, ?int $length = null, bool $preserve_keys = false)
    {
        return $this->wrap(array_slice($this->arr, $offset, $length, $preserve_keys));
    }

    /**
     * Trả vể true nếu một trong các item làm callback trả về true
     *
     * @param      Closure  $callback  The callback
     *
     * @return     bool
     */
    public function some(Closure $callback)
    {
        foreach ($this->arr as $key => $val) {
            if (call_user_func_array($callback, [$val, $key, $this])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tham khaỏ array_rand()
     *
     * @param      int     $num    The number
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function rand(int $num = 1)
    {
        return $this->wrap(array_rand($input, $num));
    }

    /**
     * Trộn mảng để tạo sự ngâu nhiên
     *
     * @param      array  $array    The array
     * @param      bool   $keepKey  The keep key
     *
     * @return     array  ( description_of_the_return_value )
     */
    public function shuffle($keepKey = false)
    {
        if (!$keepKey) {
            shuffle($this->arr);

            return $this;
        }
        
        $keys = array_keys($this->arr);
        shuffle($keys);

        return Arr::of($keys)->combine($this->arr);
    }

    /**
     * Tham khao array_column
     *
     * @param      int|null|string  $column_key  The column key
     * @param      int|null|string  $index_key   The index key
     *
     * @return     <type>           ( description_of_the_return_value )
     */
    public function column($column_key, $index_key = null)
    {
        return Arr::of(array_column($this->arr, $column_key, $index_key));
    }

    /**
     * Tham khao array_unique
     *
     * @return     <type>
     */
    public function unique(int $flags = SORT_STRING)
    {
        $this->arr = array_unique($this->arr, $flags);

        return $this;
    }

    /**
     * Tham khao array_combine
     *
     * @param      array   $keys    The keys
     * @param      array   $values  The values
     *
     * @return     <type>
     */
    public function combine(array $values)
    {
        return Arr::of(array_combine($this->arr, $values));
    }

    /**
     * Đảo ngược key và values
     *
     * @return     self
     */
    public function flip()
    {
        array_flip($this->arr);

        return $this;
    }

    /**
     * Tham khảo hàm sort()
     *
     * @param      int   $flags  The flags
     */
    public function sort(int $flags = SORT_REGULAR)
    {
        sort($this->arr, $flags);

        return $this;
    }

    /**
     * Tham khảo hàm usort()
     *
     * @param      Closure  $callback  The callback
     *
     * @return     self     ( description_of_the_return_value )
     */
    public function usort(Closure $callback)
    {
        usort($this->arr, $callback);

        return $this;
    }

    /**
     * Tham khảo array_splice
     *
     * @param      array     $array        The array
     * @param      int       $offset       The offset
     * @param      int|null  $length       The length
     * @param      mixed     $replacement  The replacement
     *
     * @return     self      ( description_of_the_return_value )
     */
    public function splice(int $offset, ?int $length = null, $replacement = null)
    {
        array_slice($this->arr, $offset, $length, $replacement);

        return $this;
    }

    /**
     * Thêmn các phần tử vào đầu mảng. Tham khảo array_unshift
     *
     * @param      <type>  ...$values  The values
     *
     * @return     self    ( description_of_the_return_value )
     */
    public function unshift(...$values)
    {
        array_unshift($this->arr, ...$values);

        return $this;
    }

    
    /**
     * Returns an array representation of the object.
     *
     * @return     <type>  Array representation of the object.
     */
    public function toArray()
    {
        return $this->arr;
    }

    /**
     * { function_description }
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function count()
    {
        return count($this->arr);
    }

    /**
     * Gets the specified key.
     *
     * @param      <type>  $key    The key
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function get($key)
    {
        if (is_numeric($key) && isset($this->arr[$key])) {
            return $this->wrap($this->arr[$key]);
        }

        $keys = explode('.', $key);
        $result = $this->arr;
        foreach ($keys as $__key ) {
            if (!isset($result[$__key])) {
                return null;
            }

            $result = $result[$__key];
        }

        return $this->wrap($result);
    }

    /**
     * Thực thi callback lên value và trả lại value mà không quan tâm kết quả callback
     *
     * @param      <type>    $value     The value
     * @param      callable  $callback  The callback
     *
     * @return     <type>    ( description_of_the_return_value )
     */
    public function tap(Closure $callback)
    {
        $callback($this->arr);

        return $this->arr;
    }

    /**
     * Thực thi callback lên value và trả lại kết quả callback
     *
     * @param      <type>    $value     The value
     * @param      callable  $callback  The callback
     *
     * @return     <type>    ( description_of_the_return_value )
     */
    public function pipe(Closure $callback)
    {
        return $this->wrap($callback($this->arr));
    }
}

if (!function_exists('from')) {
    function from(array $arr)
    {
        return Arr::of($arr);
    }
}

if (!function_exists('tap')) {
    function tap($value, Closure $callback)
    {   
        $callback($value);

        return $value;
    }
}

if (!function_exists('pipe')) {
    function pipe($value, Closure $callback)
    {   
        return $callback($value);
    }
}