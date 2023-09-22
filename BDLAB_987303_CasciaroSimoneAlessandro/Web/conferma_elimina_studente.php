<?php
include 'area_riservata_segreteria.php';

$input_matricola = $_POST['matricola'];
$input_inattivita = $_POST['inattivita'];
$input_motivazione = $_POST['motivazione'];

$result = pg_query($db, 'SET SEARCH_PATH TO universita');

$sql = "SELECT delete_studente($1, $2, $3)";
$params = array(
    $input_matricola,
     $input_inattivita,
      $input_motivazione   
);

$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Utente eliminato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_segreteria.php");
?>
