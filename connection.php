<?php
    
    $host = '127.0.0.1';
    $user ='root';
    $password = '';
    $dbname = 'Exercise_lesson_Alihaha';

    $conn = new mysqli($host, $user, $password, $dbname);

    if($conn->connect_error){
        die("Connection failed." .$conn->connect_error);
    }

    $conn -> set_charset("utf8mb4");


?>