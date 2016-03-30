<?php

namespace spec\ezid;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConnectionSpec extends ObjectBehavior
{
    const DEFAULT_URL = 'https://ezid.cdlib.org';
    
    ##
    # requestb.in urls are ephemeral. If this is set to one, and its not working
    # register a new one. Also httpbin.org provides a more stable url and API
    # if preferred.
    // private $dev_url = "http://requestb.in/szst68sz";
    private $dev_url = "http://httpbin.org/";
    
    
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
        $this->doi_shoulder->shouldEqual($config["doi_shoulder"]);
        $this->ark_shoulder->shouldEqual($config["ark_shoulder"]);
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
        $this->beConstructedWith(array("url" => $this->dev_url));
        $this->url->shouldEqual($this->dev_url);
    }
    
    ////////////////////////////////////////////////////////////////////
    //  Testing an external API is problematic at best. Here I am relying 
    //  on a generic testing service httpbin.org and also ezid's own testing
    //  services. The tests may fail if either of those services happen to 
    //  be down. If tests are failing, I suggest using http://hurl.it to
    //  verify the services are running.
    
    // Some of the tests rely on the EZID authentication. You will need
    // to add those to your config to run the tests.
    //////////////////////////////////////////////////////////////// 
    
    /**
     * Sanity check
     */ 
    function it_should_make_a_get_request()
    {
        $this->beConstructedWith(array("url" => $this->dev_url));
        $this->get()->getStatusCode()->shouldEqual(200);
    }
    
    function it_should_get_ezid_status()
    {
        $response = $this->status();
        $response->getBody()->getContents()->shouldEqual('success: EZID is up');
        $response->getStatusCode()->shouldEqual(200);
    }
    
    function it_should_create_a_doi()
    {
        $identifier = "doi:10.5072/FK2".uniqid();
        
        $response = $this->create($identifier, $this->meta());
        $response_body = $response->getWrappedObject();
        $response->GetStatusCode()->shouldEqual(201);
        $response->GetReasonPhrase()->shouldEqual('CREATED');
    }
    
    function it_should_mint_a_doi()
    {
        $response = $this->mint('doi', $this->meta());
        $response_body = $response->getWrappedObject();
        echo "\r\nBody: ".$response_body->getBody()."\r\n";
        $response->GetStatusCode()->shouldEqual(201);
        $response->GetReasonPhrase()->shouldEqual('CREATED');
    }
    
    function meta()
    {
        return [
            "creator" => 'Random Citizen',
            'title' => 'Random Thoughts',
            'publisher' => 'Random Houses',
            'publicationyear' => '2015'
        ];
    }
}

?>
