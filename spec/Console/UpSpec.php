<?php

namespace spec\Sharks\Console;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sharks\Providers\DigitalOcean;

class UpSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new DigitalOcean);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sharks\Console\Up');
    }

    public function it_is_named_correctly()
    {
        $this->getName()->shouldReturn('up');
    }
}
