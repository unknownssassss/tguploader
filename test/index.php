<?php
if(file_exists("../vendor/autoload.php")){
require "../vendor/autoload.php";
use \danog\MadelineProto\Tools;
var_dump(get_class_methods('Tools'));
echo "has";
}else{
echo "nis";
}
