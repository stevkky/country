<?php
@session_start();
@session_destroy();
unset($_SESSION['email']);
unset($_SESSION['user_type']);
header("Location:Login.php")
?>  