<?php

namespace ezid;

use GuzzleHttp\Client;

/**
 * ezid/Connection implements the EZID API.
 * 
 * ezid/Connection is a class that facilitates using the EZID API, documented
 * at {@link http://ezid.lib.purdue.edu/doc/apidoc.html}. 
 * 
 * @package ezid
 * @author Robert Olendorf <drolendorf@gmail.com>
 * @version 0.0.1
 * @access public
 * @license https://opensource.org/licenses/MIT
 */
class Connection
{
    
    // 
    const DEFAULT_URL = 'https://ezid.cdlib.org';
    
    /** @var string The url to the EZID provider */
    public $url;
    
    /** @var string The user name for the EZID account */
    public $username;
    
    /** @var string The password for the EZID account */
    public $password;
    
    /** @var string The doi shoulder assigned to EZID account */
    public $doi_shoulder;
    
    /** @var string The ark shoulder assigned to the EZID account */
    public $ark_shoulder;
    
    /**
     * @param Array<mixed> $opts [
     *                              username=>string,  
     *                              password=>string,
     *                              doi_shoulder=string,
     *                              ark_shoulder=string
     *                            ]
     *   An array of optional parameters used to override those provided
     *   in config/ezid.json. If there is no config/ezid.json, these options
     *   are not optional. 
     */
    public function __construct($opts = null)
    {
        // Read in the json config if it exists. 
        if(file_exists("config/ezid.json"))
        {
            $config = json_decode(file_get_contents("config/ezid.json"), true);
        }
        
        // Override the default url. You would want to do this if you want to
        // hit requestb.in for instance to test your requests.
        $this->url = empty($opts["url"]) ? $this::DEFAULT_URL : $opts["url"];
        
        // Override values in config/ezid.json if specified in construction. This will throw
        // errors if the config doesn't exist and no value was specified in the 
        // $opts array.
        $this->username = empty($opts["username"]) ? $config["username"] : $opts["username"];
        $this->password = empty($opts["password"]) ? $config["password"] : $opts["password"];
        $this->doi_shoulder = empty($opts["doi_shoulder"]) ? $config["doi_shoulder"] : $opts["doi_shoulder"];
        $this->ark_shoulder = empty($opts["ark_shoulder"]) ? $config["ark_shoulder"] : $opts["ark_shoulder"];
    }

    /**
     * Returns the status of the EZID server
     * 
     * @return GuzzleHttp\Psr7\Response The http response in the form of a Guzzle response
     */
    public function status()
    {
        $client = new Client([
            'base_uri' => $this->url."/status"
        ]);
        
        $response = $client->request('GET');
        return $response;
    }
    
    /**
     * Creates a DOI or ARK with the given identifier
     * 
     * @param string $identifier The desired DOI or ARK identifier
     * @param mixed[] $meta Associative array of the metadata. See EZID for more.
     * 
     * @return GuzzleHttp\Psr7\Response The http response in the form of a Guzzle response
     */
    public function create($identifier, $meta)
    {
        $str = $this->format_metadata($meta);
        
        $client = new Client([
            'auth' => [$this->username, $this->password],
            'base_uri' => $this->url."/id/".$identifier,
            'headers' => [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Content-Length' => strlen($str)
            ],
            'body' => $str
        ]);
        
        return $client->request('PUT');
    }
    
     /**
     * Mints a DOI or ARK with the given shoulder
     * 
     * @param string $shoulder The desired DOI or ARK shoulder
     * @param mixed[] $meta Associative array of the metadata. See EZID for more.
     * 
     * @return GuzzleHttp\Psr7\Response The http response in the form of a Guzzle response
     */
    public function mint($shoulder, $meta)
    {
        $str = $this->format_metadata($meta);
        
        
        if($shoulder == 'ark')
        {
            $shoulder = $this->ark_shoulder;
        }
        else
        {
            $shoulder = $this->doi_shoulder;
        }
        
        $client = new Client([
            'auth' => [$this->username, $this->password],
            'base_uri' => $this->url."/shoulder/".$shoulder,
            'headers' => [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Content-Length' => strlen($str)
            ],
            'body' => $str
        ]);
        
        return $client->request('POST');
    }
    
     /**
     * Gets the metadata for a given identifier. EZID returns the metadata
     * as a structured string. Use parse_response_metadata($metadata) to
     * parse the metadata string into an associative array.
     * 
     * 
     * @param string $identifier The desired DOI or ARK identifier
     * 
     * @return GuzzleHttp\Psr7\Response The http response in the form of a Guzzle response
     */
    public function get_identifier_metadata($identifier)
    {
        $client = new Client([
            'base_uri' => $this->url."/id/".$identifier
        ]);
        
        return $client->request('GET');
        
    }
    
     /**
     * Parses the string returned by EZID and returns an associative array.
     * 
     * 
     * @param string $identifier The desired DOI or ARK identifier
     * 
     * @return mixed[] An associative array of the metadata.
     */
    public function parse_response_metadata($metadata)
    {
        $split_array = explode("\n", $metadata);

        $meta_array = array();
        
        foreach($split_array as $line)
        {
            if(!empty($line))
            {
                $temp = explode(": ", $line);
                $meta_array[$temp[0]] = $temp[1];
            }
        }
        return $meta_array;
    }
    
    /**
     * Formats an associative array of metdata into the string
     * required by EZID.
     * 
     * @param mixed[] Associative array of metdata.
     * 
     * @return string The EZID formatted string.
     */
    private function format_metadata($meta)
    {
    return <<<EOD
datacite.creator: {$meta['creator']}
datacite.title: {$meta['title']}
datacite.publisher: {$meta['publisher']}
datacite.publicationyear: {$meta['publicationyear']}
EOD;
    }

 
}
?>