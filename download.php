<?php 
 
$url = 'http://localhost/mmpic/index.php?id=1816&num=14';
//$file = referfile($url);
print_r($_SERVER);
echo 'http://'.$_SERVER['SERVER_NAME'].$_SERVER[“DOCUMENT_ROOT”];;
$file = file_get_contents($url);
//header('content-disposition:attachment;filename=aa.jpg');
//header('content-length:'. strlen($file));
//echo $file; 