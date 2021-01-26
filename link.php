<?php
    include "config.php";
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass);
    mysqli_query($conn , "set names utf8");
    $sql = 'SELECT yuan_url, 
            xian_url
            FROM short_url
            WHERE xian_url="'.$_GET['url'].'"';
     
    mysqli_select_db( $conn, 'short_url' );
    $retval = mysqli_query( $conn, $sql );
    while($row = mysqli_fetch_array($retval, MYSQLI_ASSOC)){
        $mysqldata = "{$row['yuan_url']}";
    }
    if($mysqldata==""){
        header("Location: https://".$domain);
    }else{
        $mysqldata=base64_decode($mysqldata);
        header("Location: $mysqldata");
    }
?>