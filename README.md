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
edit it to reflect your credentials and shoulders. If do not wish to use the config
file just delete it (or don't copy and rename in the first place).

## Usage

## Creating The Connection

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

## Working With Identifiers

*NOTE: Ezid-php uses Guzzle. Refer to https://github.com/guzzle/guzzle for more documentation on working with guzzle responses.

```php
 $response = $this->status();
 
 echo $response->getBody()->getContents();
 
 \\ success: EZID is up
```





## Contributing

Contributions are welcome. Either submit an issue, or fork the repo and then 
submit a Pull request. 

