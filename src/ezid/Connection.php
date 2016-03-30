<?php

namespace ezid;

use GuzzleHttp\Client;

class Connection
{
    
    const DEFAULT_URL = 'http://ezid.cdlib.org';
    
    public $url;
    public $username;
    public $password;
    
    public function __construct($opts = null)
    {
        $config = json_decode(file_get_contents("config/ezid.json"), true);
        $this->url = empty($opts["url"]) ? $this::DEFAULT_URL : $opts["url"];
        $this->username = empty($opts["username"]) ? $config["username"] : $opts["username"];
        $this->password = empty($opts["password"]) ? $config["password"] : $opts["password"];
    }

    public function get()
    {
        $client = new Client([
            'base_uri' => $this->url,
        ]);
        
        $response = $client->request('GET', '', ['auth' => [$this->username, $this->password]]);
        
        return $response;
    }

    public function status()
    {
        $client = new Client([
            'base_uri' => $this->url."/status"
        ]);
        
        $response = $client->request('GET');
        return $response;
    }
}
?>