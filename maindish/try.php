<?php
    $db = mysqli_connect('localhost','root','','dashboards')  ;

    //error msg if connection fail
    if(!$db){
        echo 'Connection  error: '. mysqli_connect_error();
    }    
?>
   <!--$hashed_password = password_hash($password,PASSWORD_DEFAULT);-->