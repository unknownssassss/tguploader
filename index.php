<body>
    <div>
        <?php
        if (isset($_POST['link']) && isset($_POST['f'])) {
           var_dump($_POST);
            return;
        }
        ?>
    </div>
    <form action="" method="post">
      <textarea name="link"></textarea>  
      <input type="text" name="f" />
      <input type="submit" value="Download"/>
    </form>
</body>
</html>
/*if(isset($_GET['type'])){
if($_GET['type'] == "scan"){
echo "<pre>";
print_r(scandir("."));
echo "</pre>";
}
if($_GET['type'] == "dl" && isset($_GET['url']) && isset($_GET['f'])){
include "vendor/autoload.php";
$client = new GuzzleHttp\Client();
$client->request(
  'GET',
  urldecode($_GET['url']),
  array('sink' => $_GET['f'])
);
}
}
*/
