<?php
include("menu.php"); // La session viene startata nel menù
      
    if ($_SESSION["ruolo"] == "segreteria") {
        // Reindirizza all'area riservata relativa alla segreteria
        header("Location: area_riservata_segreteria.php"); }
    else if ($_SESSION["ruolo"] == "docente") {
        // Reindirizza all'area riservata relativa ai docenti
        header("Location: area_riservata_docenti.php");
    }

echo $_SESSION['Comunicazioni_St'];
$_SESSION['Comunicazioni_St'] = '';
?>