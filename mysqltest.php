<?php
echo "testing connection<br>";
$link = mysql_connect('192.168.0.8','ftp','ftp');
if (!$link) { die('Could not connect to MySQL: ' . mysql_error());} echo 'Connection OK';
mysql_close($link);
?>