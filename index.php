<?
include("global_vars.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
       <title>FAAA upload</title>
</head>
<body>
<table align="center">
       <tr>
              <td>
                     <img src="logo.jpg"/>
              </td>
              <td>
                     <h2>Faa'a Upload</h2>
              </td>
       </tr>
</table>
<form action="index.php" method="POST" enctype="multipart/form-data">
<table align="center">
<tr>
<td align="right"><p>
S&eacute;lectionner votre fichier (30 Mo maximum!):
</p>
</td>
<td>
<input name="userfile" type="file" size="50">
</td>
</tr>
</table>
<table align="center">
<tr>
<td align="center">
<input type="submit" name="submit" value="Transf&eacute;rer" />
</td>
</tr>
 
</table>
</form>

</body>
</html>
<?
echo "<hr>";

if(isset($_POST["submit"])){
set_time_limit(300);//for setting 
 
$paths="/";
 
$filep=$_FILES['userfile']['tmp_name'];
 
$ftp_server=HOST;
 
$ftp_user_name=DBUSER;
 
$ftp_user_pass=DBPASSWORD;
 
$truename=$_FILES['userfile']['name'];
 
$name=md5($truename.date('Y-m-d'));
//print $truename;

updatedb($name,$truename);
 
// set up a connection to ftp server
$conn_id = ftp_connect($ftp_server);
 
// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
 
// check connection and login result
if ((!$conn_id) || (!$login_result)) {
       echo "Erreur de connexion FTP!";
       echo "Attempted to connect to $ftp_server for user $ftp_user_name....";
       exit;
   } else {
       //echo "Connected to $ftp_server, for user $ftp_user_name".".....";
   }
 
// turn passive mode on
ftp_pasv($conn_id, TRUE);
 
// upload the file to the path specified
$upload = ftp_put($conn_id, $paths.'/'.$name, $filep, FTP_BINARY);
 
// check the upload status
if (!$upload) {
       echo "<p>Erreur en cours de chargement!</p>";
   } else {
       //echo "Uploaded file with name $name to $ftp_server <br>";
       echo "Votre lien est : <h3><a href='http://".HOST."/getfile.php?id=$name' target='_blank'>http://".HOST."/getfile.php?id=$name</a></h3>";
       echo "<p>Copiez ce lien et envoyez le &agrave; votre correspondant.</p>";
       //echo "Devel link : <h3><a href='getfile.php?id=$name' target='_blank'>http://localhost/ftptransfert/getfile.php?id=$name</a></h3>";
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