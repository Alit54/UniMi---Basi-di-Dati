<?php
include("menu.php");
      
      if ($_SESSION["ruolo"] == "studente") {
        // Credenziali corrette, reindirizza all'area riservata relativa agli studenti
        header("Location: area_riservata_studenti.php"); }
     else if ($_SESSION["ruolo"] == "docente") {
        // Credenziali corrette, reindirizza all'area riservata relativa ai docenti
        header("Location: area_riservata_docenti.php");
    }


echo $_SESSION['Comunicazioni_Sg'];
$_SESSION['Comunicazioni_Sg'] = '';
?>