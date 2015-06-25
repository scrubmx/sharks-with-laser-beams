<?php

namespace Sharks\Support\Debug;

use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class Dumper
{
    /**
     * Dump a value with elegance.
     *
     * @param  mixed  $value
     *
     * @return void
     */
    public function dump($value)
    {
        $dumper = new CliDumper();

        $dumper->dump((new VarCloner)->cloneVar($value));
    }
}
