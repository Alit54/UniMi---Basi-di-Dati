<?php
function open_pg_connection() {
	include_once('config.php');
 
    $connection = "host=".myhost." dbname=".mydb." user=".myuser." password=".mypsw;
    return pg_connect ($connection);
}

function redirect($url) {
    header("Location: " . $url); 
}
?>