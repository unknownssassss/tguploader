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
        var_dump($_FILES);
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
