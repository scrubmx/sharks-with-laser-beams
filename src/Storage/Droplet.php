<?php

namespace Sharks\Storage;

class Droplet extends Storage
{
    /**
     * @param array $data
     */
    public static function save(array $data)
    {
        static::set('droplets', $data);
    }

    /**
     * @return void
     */
    public static function delete()
    {
        static::set('droplets', []);
    }

    /**
     * @return array
     */
    public static function all()
    {
        return static::get('droplets');
    }

    /**
     * @return array
     */
    public static function ips()
    {
        $droplets = static::all();

        return array_map(function($instance){
            return $instance->ip_address;
        }, $droplets);
    }

    /**
     * @return array
     */
    public static function ids()
    {
        $droplets = static::all();

        return array_map(function($instance){
            return $instance->id;
        }, $droplets);
    }

    /**
     * @return int
     */
    public static function count()
    {
        return count(static::all());
    }

    /**
     * @return string
     */
    public static function getPath()
    {
        return __DIR__ . '/droplets.json';
    }
}