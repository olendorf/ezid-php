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
    
    ///////////////////////////////////////////////////////////////////////
    // This block tests features associated with configuration and
    // construction
    /////////////////////////////////////////////////////////////////////
    function it_should_initialize_from_config()
    {
        $config = json_decode(file_get_contents("config/ezid.json"), true);
        $this->username->shouldEqual($config["username"]);
        $this->password->shouldEqual($config["password"]);
    }
    
    function it_should_override_authentation_credentials_config_with_constructor_options()
    {
        $opts = array("username"=>"john_doe", "password"=>"foobaz");
        $this->beConstructedWith($opts);
        $this->username->shouldEqual($opts["username"]);
        $this->password->shouldEqual($opts["password"]);
    }
    
    function it_should_override_url_with_constructor_options()
    {
        $url = 'example.com';
        $this->beConstructedWith(array("url" => $url));
        $this->url->shouldEqual($url);
    }
}
