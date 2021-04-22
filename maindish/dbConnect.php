<?php
require('f3/base.php');
$f3=Base::instance();
            
$f3->config(__DIR__.'/config.ini');

$db=new DB\SQL("mysql:dbname={$f3->get('DBNAME')};host={$f3->get('DBHOST')};port={$f3->get('DBPORT')}",
$f3->get('DBUSER'),
$f3->get('DBPASSWORD'),
array( \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION ));

    //error msg if connection fail
    /*if(!$conDB){
        echo 'Connection  error: '. mysqli_connect_error();
    }*/    
?>