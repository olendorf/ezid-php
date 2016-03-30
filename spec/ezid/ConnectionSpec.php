<?php

namespace spec\ezid;

include 'src/ezid/Connection.php';

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ezid;

class ConnectionSpec extends ObjectBehavior
{
    const DEFAULT_URL = 'https://ezid.cdlib.org';
    
    const DOI_REGEX = '/\b(doi:10[.][0-9]{4,}(?:[.][0-9]+)*\/(?:(?!["&\'<>])[[:graph:]])+)\b/i';
    
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
        $response_object = $response->getWrappedObject();
        $response->GetStatusCode()->shouldEqual(201);
        $response->GetReasonPhrase()->shouldEqual('CREATED');
        
        preg_match($this::DOI_REGEX, $response_object->getBody(), $matches);
        expect(strtoupper($matches[0]))->toEqual(strtoupper($identifier));
    }
    
    function it_should_mint_a_doi()
    {
        $response = $this->mint('doi', $this->meta());
        $response_object = $response->getWrappedObject();
        $response->GetStatusCode()->shouldEqual(201);
        $response->GetReasonPhrase()->shouldEqual('CREATED');
        
        preg_match($this::DOI_REGEX, $response_object->getBody(), $matches);
        
        // Test that an actual doi was minted using rgex.
        expect(preg_match($this::DOI_REGEX, $response_object->getBody(), $matches))->toEqual(1);
    }
    
    function it_should_get_identifier_metadata()
    {
        $identifier = "doi:10.5072/FK2".uniqid();
        $this->existing_identifier($identifier, $this->meta());
        
        $response = $this->get_identifier_metadata($identifier);
        
        $response_object = $response->getWrappedObject();
        
        $metadata = ezid\Connection::parse_response_metadata((string)$response_object->getBody());
        
        expect($metadata["datacite.creator"])->toEqual($this->meta()["creator"]);
        
    }
    
    function it_should_modify_identifier_metadata()
    {
        
        $identifier = "doi:10.5072/FK2".uniqid();
        $this->existing_identifier($identifier, $this->meta());
        
        $new_meta = [
            "creator" => 'Anonymous Resident',
            'resourcetype' => ''
            ];
            
        $response = $this->modify_identifier_metadata($identifier, $new_meta);
        
        $response->GetStatusCode()->shouldEqual(200);
        
        // Get the metadata and make sure that it was updated.
        $get_response = $this->get_identifier_metadata($identifier);
        
        $response_object = $get_response->getWrappedObject();
        
        $metadata = ezid\Connection::parse_response_metadata((string)$response_object->getBody());
        
        expect($metadata['datacite.creator'])->toEqual($new_meta['creator']);
        expect(empty($metadata['datacite.resourcetype']))->toBe(true);
    }
    
    function it_should_delete_reserved_identifiers()
    {
        $identifier = "doi:10.5072/FK2".uniqid();
        $meta = $this->meta();
        $meta['_status'] = 'reserved';
        
        $this->existing_identifier($identifier, $meta);
       
        // Get the metadata and make sure that it was updated.
        $get_response = $this->get_identifier_metadata($identifier);
        
        $response_object = $get_response->getWrappedObject();
        
        $metadata = ezid\Connection::parse_response_metadata((string)$response_object->getBody());
        
        $response = $this->delete_identifier($identifier);
        
        $response->GetStatusCode()->shouldEqual(200);
    }
    
    
    ///////////////////////////////////////////////
    //////////////////////////////////////////////
    //
    // Helper functions
    //////////////////////////////////////////////
    
    function existing_identifier($identifier, $meta)
    {
        $client = new ezid\Connection();
        return $client->create($identifier, $meta);
    }
    
    function meta()
    {
        return [
            "creator" => 'Random Citizen',
            'title' => 'Random Thoughts',
            'publisher' => 'Random Houses',
            'publicationyear' => '2015',
            'resourcetype' => 'Text'
        ];
    }
}

?>
