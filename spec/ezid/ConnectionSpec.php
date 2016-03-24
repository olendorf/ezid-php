<?php

namespace spec\ezid;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConnectionSpec extends ObjectBehavior
{
    const DEFAULT_URL = 'ezid.cdlib.org';
    
    function it_is_initializable()
    {
        $this->shouldHaveType('ezid\Connection');
    }
    
    function it_has_the_right_default_url()
    {
        $this->url->shouldEqual($this::DEFAULT_URL);
    }
}
