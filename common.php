<?php 

    $username = "user"; 
    $password = "Mysqlt0p0t!"; 
    $host = "localhost"; 
    $dbname = "mysql"; 

    // use UTF-8 character encoding
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
     
    try 
    { 
        // opens connection to database using the PDO library 
        $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); 
    } 
    catch(PDOException $ex) 
    { 
        // only output error message for testing --> $ex->getMessage()
        //die("Failed to connect to the database: " . $ex->getMessage()); 
        die("Failed to connect to the database.\n Please contact Asio Security if the problem persists.");
    } 
     
    // configures PDO to throw an exception when it encounters errors 
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
     
    // configures PDO to return database rows from database using an
    // associative array. the array will have string indexes representing
    // the names of the columns in database. 
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
     
    // undos magic quotes (outdated php)
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
    { 
        function undo_magic_quotes_gpc(&$array) 
        { 
            foreach($array as &$value) 
            { 
                if(is_array($value)) 
                { 
                    undo_magic_quotes_gpc($value); 
                } 
                else 
                { 
                    $value = stripslashes($value); 
                } 
            } 
        } 
     
        undo_magic_quotes_gpc($_POST); 
        undo_magic_quotes_gpc($_GET); 
        undo_magic_quotes_gpc($_COOKIE); 
    } 
     
    // tells web browser utf-8 
    header('Content-Type: text/html; charset=utf-8'); 
     

    session_start(); 


