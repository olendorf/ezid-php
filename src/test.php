<?php

require 'src/ezid/Connection.php';

$client = new ezid\Connection();

echo $client->username;

$config = array(
                 "username"=>"RandomCitizen",
                 "password"=>"foobar123",
                 "doi_shoulder"=>"doi:10.5072/FK2",
                 "ark_shoulder"=>"ark:/99999/fk4"
               );
$client2 = new ezid\Connection($config);

echo  $client2->username;
?>