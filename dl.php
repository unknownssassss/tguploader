<?php
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
            $scan = scandir(".");
            foreach ($scan as $file) {
                if (is_file($file)) {
                    echo "<pre>";
                    echo $file ." = ".formatbytes(filesize($file))."<br>";
                    echo "</pre>";
                }
            }
die;
        }
    }
    if(isset($_FILES['fileToUpload'])){
        $inputfile = microtime(true)."_".basename($_FILES['fileToUpload']['name']);
if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$inputfile)){
 $outputimage = "output.png";
        exec("ffmpeg -i $inputfile -ss 00:00:05.000 -v frames 1 $outputimage",$output);
print_r($output);
return;
}
echo "Error";
return;
    }
    ?>
<html>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        Select Video to upload:
        <input type="file" name="fileToUpload" id="fileToUpload" accept="video/mp4">
        <input type="submit" value="Upload" name="submit">
    </form>
</body>
</html>
