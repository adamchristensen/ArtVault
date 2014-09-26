<?php
function db_connect(){
    $dbhost = "localhost"; $dbuser = "root"; $dbpw = ""; $db = "headyartvault";
    $mysqli = new mysqli($dbhost,$dbuser,$dbpw,$db);
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        error_log( "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
        exit(false);
        //return(false);
    }
    return($mysqli);
}
function add_art($artist_id, $artwork_name, $artwork_description, $price, $for_sale, $glass_type, $user_id){
    $mysqli=db_connect();
    $query = "INSERT INTO headyartvault.`art`(`artist_id_fk`, `artwork_name`, `artwork_description`, `price`, `for_sale`, `glass_type`, `last_action`, `last_action_by`, `last_action_date`) VALUES('".$artist_id."', '".$artwork_name."', '".$artwork_description."', '".$price."', '".$for_sale."', '".$glass_type."', 'Add Art', '".$user_id."', NOW())";
    $result = $mysqli->query($query);
    if($result === false){
        error_log("Failed to insert art into database");
        return(false);
    }else{
        activity_log('Added art: '.$artwork_name, $user_id);
        return(true);
    }
}
function create_admin($user_email, $password){
    $encrypted_password = hash('sha256', $password);
    $mysqli=db_connect();
    $query = "INSERT INTO headyartvault.`user`(`user_email`, `user_password`, `last_action`, `last_action_date`, `user_type`) VALUES('".$user_email."', '".$encrypted_password."', 'Create Admin', NOW(), '1')";
    $result = $mysqli->query($query);
    if($result === false){
        error_log("Failed to insert admin into database");
        return(false);
    }else{
        activity_log('Created admin: '.$user_email, $user_id);
        return(true);
    }
}
function create_artist($artist_name, $user_id){
    $mysqli=db_connect();
    $query = "INSERT INTO headyartvault.`artist`(`artist_name`, `last_action`, `last_action_by`, `last_action_date`) VALUES('".$artist_name."', 'Create Artist', '".$user_id."', NOW())";
    $result = $mysqli->query($query);
    if($result === false){
        error_log("Failed to insert artist into database");
        return(false);
    }else{
        activity_log('Created artist: '.$artist_name, $user_id);
        return(true);
    }
}
function activity_log($action, $user_id){
    $mysqli=db_connect();
    $query = "INSERT INTO headyartvault.`log`(`activity`, `user_id`, `date`) VALUES('".$action."', '".$user_id."', NOW())";
    $result = $mysqli->query($query);
    if($result === false){
        error_log("Failed to insert log into database");
        return(false);
    }else{
        return(true);
    }
}
function upload_media($files, $artwork_id){
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["file"]["name"]);
    $extension = end($temp);

    if ((($_FILES["file"]["type"] == "image/gif")
         || ($_FILES["file"]["type"] == "image/jpeg")
         || ($_FILES["file"]["type"] == "image/jpg")
         || ($_FILES["file"]["type"] == "image/pjpeg")
         || ($_FILES["file"]["type"] == "image/x-png")
         || ($_FILES["file"]["type"] == "image/png"))
        && ($_FILES["file"]["size"] < 20000)
        && in_array($extension, $allowedExts)) {
        if ($_FILES["file"]["error"] > 0) {
            echo "Return Code: " . $files["file"]["error"] . "<br>";
        } else {
            error_log( "Upload: " . $files["file"]["name"] . "<br>");
            error_log( "Type: " . $files["file"]["type"] . "<br>");
            error_log( "Size: " . ($files["file"]["size"] / 1024) . " kB<br>");
            error_log( "Temp file: " . $files["file"]["tmp_name"] . "<br>");
            if (file_exists("upload/".$artwork_id."/" . $files["file"]["name"])) {
                error_log( $files["file"]["name"] . " already exists. ");
            } else {
                move_uploaded_file($files["file"]["tmp_name"],
                                   "upload/".$artwork_id."/" . $files["file"]["name"]);
                error_log( "Stored in: " . "upload/".$artwork_id."/" . $files["file"]["name"]);
            }
            return(true);
        }
    } else {
        error_log( "Invalid file");
        return(false);
    }
}
?>
