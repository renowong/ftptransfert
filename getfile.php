<?
include("global_vars.php");

$id = $_GET["id"];
$name = getname($id);
echo $name."<br>";

getfile($id,$name);

function getfile($id,$name){
$ftp_user_name = DBUSER;
$ftp_user_pass = DBUSER;
$ftp_server = HOST;
$local_file = "/tmp/".$name;
$server_file = $id;

// set up a connection to ftp server
$conn_id = ftp_connect($ftp_server);
 
// login with username and password
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
 
// turn passive mode on
ftp_pasv($conn_id, TRUE);
 
//// get contents of the current directory
//$contents = ftp_nlist($conn_id, ".");
//
//// output $contents
//var_dump($contents);

// try to download $server_file and save to $local_file
if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
    header('Content-Length: '. filesize($local_file));
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($local_file).'"');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    readfile($local_file); // send the file
    exit;  // make sure no extraneous characters get appended
}

// close the connection
ftp_close($conn_id);

}

function getname($id){
    $mysqli = new mysqli(HOST, DBUSER, DBPASSWORD, DB);
    $query = "SELECT `name` FROM `ftptransfert`.`files` WHERE `md5`='$id'";
    if ($result = $mysqli->query($query)) {
        $row = $result->fetch_assoc();
        $name = $row["name"];
    }
    $result->free();
    $mysqli->close();
    return $name;
}

?>