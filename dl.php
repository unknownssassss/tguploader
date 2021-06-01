<?php
ini_set('memory_limit', '-1');
function showall($path){
$scandir = scandir($path);
foreach($scandir as $d){
if(is_file($d)){
echo "<pre>";
                    echo $d." = ".formatbytes(filesize($d))."<br>";
                    echo "</pre>";
}else{
if(is_dir($d)){
showall($d);
}
}
}
}
function formatBytes($bytes, $precision = 2) {

        $units = ['B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    if (isset($_GET['type'])) {

        if ($_GET['type'] == "scan") {
showall(".");
          /*  $scan = scandir(".");
            foreach ($scan as $file) {
                if (is_file($file)) {
                    echo "<pre>";
                    echo $file ." = ".formatbytes(filesize($file))."<br>";
                    echo "</pre>";
                }
            }*/
die;
        }
    }
    
