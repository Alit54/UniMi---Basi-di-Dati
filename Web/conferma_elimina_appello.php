<?php
include 'area_riservata_docenti.php';

$insegnamento = $_POST['insegnamento'];
$data = $_POST['data'];

$result = pg_query($db, 'SET SEARCH_PATH TO universita');

$sql = "DELETE FROM appello WHERE insegnamento = $1 AND data = $2";
$params = array(
    $insegnamento,
    $data
);

$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_D'] = "Appello eliminato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_D'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_docenti.php");
?>
