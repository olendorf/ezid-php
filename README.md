# EZID-PHP 

EZID-PHP is a simple wrapper around the Guzzle HTTP client intended to simplify 
interaction with the EZID DOI service. It is configurable to use your own 
authentication and shoulders. Its still early in development. Feel free to 
use it, but please be be aware there are probably issues and bugs. 
If you find them report them or fix them!


## Installation

The recommended way to install Ezid-php is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of Guzzle:

```bash
composer.phar require olendorf/ezid-php
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

You can then later update Guzzle using composer:

 ```bash
composer.phar update
 ```
 
**OR**

Edit your compuser.json to include the following

```json
{
   
   "require": {
      "olendorf/ezid-php": ">=0.0.0"
    }
}
```

## Configuring

Copy and rename ```src/ezid/ezid.json.example``` to ```src/ezid/ezid.json```. Then
edit it to reflect your credentials and shoulders. If upi do not wish to use the config
file just delete it (or don't copy and rename in the first place). You can always override 
the values as you wish.

## Usage



### Creating The Connection

```php
/**
 * Using ezid.json configuration
 */
$client = new ezid\Connection();

/**
 * Overriding or not using ezid.json
 */
$config = array(
                 "username"=>"RandomCitizen",
                 "password"=>"foobar123",
                 "doi_shoulder"=>"doi:10.5072/FK2",
                 "ark_shoulder"=>""ark:/99999/fk4"
               );
$client = new ezid\Connection($config);


```

### Creating and minting identifiers

```php
 // Getting Server Status
 $response = $this->status();
 
 echo $response->getBody()->getContents(); // success: EZID is up
 
 // Creating an identifier
 $meta = [
            "creator" => 'Random Citizen',
            'title' => 'Random Thoughts',
            'publisher' => 'Random Houses',
            'publicationyear' => '2015',
            'resourcetype' => 'Text'
        ];
 $identifier = $client->doi_shoulder.uniqid(); // Just using uniqid() to generate a  unique string.
 $response = $client->create($identifier, $meta);
 
 echo $response->GetStatusCode();  // 201
 
 // Minting an identifier
        
 $response = $client->mint('doi', $meta);  //uses the shoulder specified in config or on creation of the client.
 
 echo $response->GetStatusCode();  // 201
```

### Retrieving Metadata

```php
 
 $response = $client->get_identifier_metadata($identifier);  // will get the meta sent in create()
 echo (string)$response->getBody();  // Key value pair formatted string with metadata
      // datacite.creator: Random Citizen
      // datacite.title : Random Thoughts
      // ...
      
 // You can extract this using parse_response_metadata()
 
 $meta_array = $client->parse_response_metadata((string)$response->getBody()); // Guzzle returns a stream, cast it to a string
 
 print_r($meta_array);  
    // (
    //    [datacite.creator] => 'Random Citizen',
    //    [datacite.title] => 'Random Thoughts',
    //    ...
    //  )
    
    
```

### Modifying the metadata

```php 
 $new_meta = [
            "creator" => 'Anonymous Resident',
            'resourcetype' => ''
            ];
 $response = $client->modify_identifier_metadata($identifier, $new_meta);
 echo $response->GetStatusCode()  // 200
```

### Deleting an identifier (must be reserved)

```php
 $response = $client->delete_identifier($identifier);
 echo $response->GetStatusCode()  // 200
```






## Contributing

Contributions are welcome. Either submit an issue, or fork the repo and then 
submit a Pull request. 

