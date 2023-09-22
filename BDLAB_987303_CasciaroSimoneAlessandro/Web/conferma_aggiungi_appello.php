<?php
include 'area_riservata_docenti.php';

$insegnamento = $_POST['insegnamento'];
$data = $_POST['data'] . ' ' . $_POST['ora'];
$luogo = $_POST['luogo'];

$sql = 'INSERT INTO appello VALUES ($1, $2, $3)';

$params = array(
    $data,
    $insegnamento,
    $luogo
);
$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_D'] = "Appello creato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_D'] = "Errore: " . $errore; 
}
    header("Location: area_riservata_docenti.php");
?>