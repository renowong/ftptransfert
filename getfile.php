<?
include("global_vars.php");

$id = $_GET["id"];
$name = getname($id);

getfile($id,$name);

function getfile($id,$name){
$ftp_user_name = DBUSER;
$ftp_user_pass = DBPASSWORD;
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
    $file_extension = strtolower(substr(strrchr($local_file,"."),1));

    switch ($file_extension) {
               case "pdf": $ctype="application/pdf"; break;
               case "exe": $ctype="application/octet-stream"; break;
               case "zip": $ctype="application/zip"; break;
               case "doc": $ctype="application/msword"; break;
               case "xls": $ctype="application/vnd.ms-excel"; break;
               case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
               case "gif": $ctype="image/gif"; break;
               case "png": $ctype="image/png"; break;
               case "jpe": case "jpeg":
               case "jpg": $ctype="image/jpg"; break;
               case "odt": $ctype="application/vnd.oasis.opendocument.text"; break;
               case "ods": $ctype="application/vnd.oasis.opendocument.spreadsheet"; break; 
               case "odp": $ctype="application/vnd.oasis.opendocument.presentation"; break; 
               case "odg": $ctype="application/vnd.oasis.opendocument.graphics"; break; 
               default: $ctype="application/force-download";
           }

           if (!file_exists($local_file)) {
               die("NO FILE HERE");
           }
           
        header("Pragma: public");
           header("Expires: 0");
           header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
           header("Cache-Control: private",false);
           header("Content-Type: $ctype");
           header("Content-Disposition: attachment; filename=\"".basename($local_file)."\";");
           header("Content-Transfer-Encoding: binary");
           header("Content-Length: ".@filesize($local_file));
           set_time_limit(0);
           @readfile("$local_file") or die("File not found.");
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