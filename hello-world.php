
<?php
// A simple web site in Cloud9 that runs through Apache
// Press the 'Run' button on the top to start the web server,
// then click the URL that is emitted to the Output tab of the console

$meta = array(
    "creator" => "Random Citizen",
    "title" => "Random Thoughts"
);

$data = <<<EOT
datacite.creator: {$meta['creator']}
datacite.title: {$meta['title']}

EOT;

echo $data;

echo uniqid();


$identifier = "doi:10.5072/FK2".uniqid();

echo preg_quote($identifier);
