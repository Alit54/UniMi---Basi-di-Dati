<?php
include_once("functions.php");
include("menu.php");

session_destroy(); // Ripulisce i dati della session e la chiude

pg_close($db);
redirect("./index.php");
?>