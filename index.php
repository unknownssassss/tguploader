<body>
    <div>
        <?php
        if (isset($_POST['link']) && isset($_POST['f'])) {
            include "vendor/autoload.php";
            $client = new GuzzleHttp\Client();
            $client->request(
                'GET',
                urldecode($_POST['link']),
                array('sink' => $_POST['f'])
            );
            return;
        }
        ?>
    </div>
    <form action="" method="post">
        <textarea name="link"></textarea>
        <input type="text" name="f" />
        <input type="submit" value="Download" />
    </form>
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
        }
    }
    ?>

</body>
</html>
