<?php

namespace Sharks\Storage;

/**
 * Class Config
 *
 * @package Sharks\Support
 */
class Config extends Storage
{
    /**
     * @return string
     */
    public static function getPath()
    {
        return __DIR__ . '/../../config.json';
    }
}