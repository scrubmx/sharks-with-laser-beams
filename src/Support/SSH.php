<?php

namespace Sharks\Support;

class SSH
{
    /**
     * @param $keyPath
     *
     * @return mixed
     */
    public static function getFingerPrint($keyPath)
    {
        $ssh_key = explode(' ', exec("ssh-keygen -lf {$keyPath}"));

        return $ssh_key[1];
    }
}