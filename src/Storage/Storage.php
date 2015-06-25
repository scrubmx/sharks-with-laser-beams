<?php

namespace Sharks\Storage;

/**
 * Class Storage
 *
 * @package Sharks\Storage
 */
abstract class Storage
{
    /**
     * @param string $key
     *
     * @return null
     */
    public static function get($key)
    {
        $config = static::getAll();

        return isset($config->$key) ? $config->$key : null;
    }

    /**
     * @param string $key
     *
     * @param $value
     */
    public static function set($key, $value)
    {
        file_put_contents(static::getPath(), json_encode([$key => $value]));
    }

    /**
     * @return string
     */
    abstract static function getPath();

    /**
     * @return mixed
     */
    public static function getAll()
    {
        return json_decode(file_get_contents(static::getPath()));
    }
}
