<?php

namespace ezid;

class Connection
{
    
    const DEFAULT_URL = 'ezid.cdlib.org';
    
    public $url;
    
    public function __construct()
    {
        $this->url = $this::DEFAULT_URL;
    }
}
