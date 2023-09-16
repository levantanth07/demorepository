<?php

class ArrHelper
{
    /**
     * $data variable
     *
     * @var array
     */
    private $data;

    /**
     * __construct function
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * set function
     *
     * @param array $data
     * @return void
     */
    public function set(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * get function
     *
     * @param string $locations
     * @param mixed $default
     * @return mixed
     */
    public function get(string $keys, $default)
    {
        $data = $this->data;
        $keys = explode('.', $keys);
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                return $default;
            }//end if

            $data = $data[$key];
        }//end foreach

        return $data;
    }

    /**
     * getAll function
     *
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * getInt function
     *
     * @param string $key
     * @param integer $default
     * @return integer
     */
    public function getInt(string $key, $default = 0): int
    {
        return intVal($this->get($key, $default));
    }

    /**
     * getStr function
     *
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getStr(string $key, $default = ''): string
    {
        return trim($this->get($key, $default));
    }

    /**
     * getArr function
     *
     * @param string $key
     * @param array $default
     * @return array
     */
    public function getArr(string $key, $default = []): array
    {
        return $this->get($key, $default);
    }

    /**
     * getFloat function
     *
     * @param string $key
     * @param integer $default
     * @return float
     */
    public function getFloat(string $key, $default = 0): float
    {
        return floatVal($this->get($key, $default));
    }
}
