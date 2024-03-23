<?php
include("menu.php");
      
      if ($_SESSION["ruolo"] == "segreteria") {
        // Reindirizza all'area riservata relativa alla segreteria
        header("Location: area_riservata_segreteria.php");
    } else if ($_SESSION["ruolo"] == "studente") {
        // Reindirizza all'area riservata relativa agli studenti
        header("Location: area_riservata_studenti.php");
    }

echo $_SESSION['Comunicazioni_D'];
$_SESSION['Comunicazioni_D'] = '';
?>