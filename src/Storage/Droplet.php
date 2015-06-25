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
     * @return array
     */
    public static function all()
    {
        return static::get('droplets');
    }

    /**
     * @return array
     */
    public static function ids()
    {
        $droplets = static::all();

        return array_map(function($instance){
            return $instance->droplet->id;
        }, $droplets);
    }

    /**
     * @return string
     */
    public static function getPath()
    {
        return __DIR__ . '/droplets.json';
    }
}