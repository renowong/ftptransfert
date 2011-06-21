<?
include("global_vars.php");
if(!isset($_POST["submit"])){?>
 
<form action="index.php" method="POST" enctype="multipart/form-data">
<table align="center">
<tr>
<td align="right">
Selectionner votre fichier:
</td>
<td>
<input name="userfile" type="file" size="50">
</td>
</tr>
</table>
<table align="center">
<tr>
<td align="center">
<input type="submit" name="submit" value="Transferer" />
</td>
</tr>
 
</table>
</form>
<?}
else 
{
 
set_time_limit(300);//for setting 
 
$paths="/";
 
$filep=$_FILES['userfile']['tmp_name'];
 
$ftp_server=HOST;
 
$ftp_user_name=DBUSER;
 
$ftp_user_pass=DBPASSWORD;
 
$truename=$_FILES['userfile']['name'];
 
$name=md5($truename);
//print $truename;

updatedb($name,$truename);
 
// set up a connection to ftp server
$conn_id = ftp_connect($ftp_server);
 
// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
 
// check connection and login result
if ((!$conn_id) || (!$login_result)) {
       echo "FTP connection has encountered an error!";
       echo "Attempted to connect to $ftp_server for user $ftp_user_name....";
       exit;
   } else {
       //echo "Connected to $ftp_server, for user $ftp_user_name".".....";
   }
 
// upload the file to the path specified
$upload = ftp_put($conn_id, $paths.'/'.$name, $filep, FTP_BINARY);
 
// check the upload status
if (!$upload) {
       echo "FTP upload has encountered an error!";
   } else {
       echo "Uploaded file with name $name to $ftp_server <br>";
       echo "Votre lien est : <h3><a href='http://192.168.0.8/getfile.php?id=$name' target='_blank'>http://192.168.0.8/getfile.php?id=$name</a></h3>";
       echo "Devel link : <h3><a href='getfile.php?id=$name' target='_blank'>http://localhost/ftptransfert/getfile.php?id=$name</a></h3>";
   }
 
// close the FTP connection
ftp_close($conn_id);	
 
 

}

function updatedb($md5,$name){
    $mysqli = new mysqli(HOST, DBUSER, DBPASSWORD, DB);
    $query = "INSERT INTO `ftptransfert`.`files` (`ID` ,`md5` ,`name`) VALUES (NULL ,  '$md5',  '$name')";
    $mysqli->query($query);
    $mysqli->close();
}
?>