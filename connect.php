<?php

    $servername ='us-cdbr-iron-east-01.cleardb.net';
    $username = 'b6820daedec610';
    $password = '341af09b';
    $database = 'heroku_e2582eb78b0ce2a';
    $dbport = 3306;

//###########################Create connection##################################
try
{
    $db= new PDO("mysql:host=$servername;dbname=$database", $username, $password);
}
catch(PDOException $e)
{
    echo $e;
}

?>