<?php
try{
$exec = exec('mediainfo --help', $output);
print_r($output);
}catch(exception $e){
echo "boom";
}
