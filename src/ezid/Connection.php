<?php

namespace ezid;

class Connection
{
    
    const DEFAULT_URL = 'ezid.cdlib.org';
    
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
}
