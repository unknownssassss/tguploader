<?php
function ex($cmd){
exec($cmd,$m);
return $m;
}
