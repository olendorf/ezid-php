
<?php
// A simple web site in Cloud9 that runs through Apache
// Press the 'Run' button on the top to start the web server,
// then click the URL that is emitted to the Output tab of the console

$meta = array(
    "creator" => "Random Citizen",
    "title" => "Random Thoughts"
);

$data = <<<EOT
success: doi:10.5072/FK256FC28FB1E16B
datacite.publisher: Random Houses
_profile: datacite
_export: yes
datacite.creator: Random Citizen
datacite.publicationyear: 2015
_datacenter: CDL.CDL
_updated: 1459366139
_target: http://ezid.cdlib.org/id/doi:10.5072/FK256FC28FB1E16B
datacite.title: Random Thoughts
_ownergroup: ss1
_owner: ss1
_shadowedby: ark:/b5072/fk256fc28fb1e16b
_created: 1459366139
_status: publicationyear
EOT;


$data_array = explode("\n", $data);

$data_hash = array();

foreach($data_array as $line)
{
    $temp = explode(": ", $line);
    $data_hash[$temp[0]] = $temp[1];
}

$key = '_status';
if(substr($key, 0, 1) == '_')
{
    echo 'it worked';
}
else
{
    echo 'it failed';
}

?>
