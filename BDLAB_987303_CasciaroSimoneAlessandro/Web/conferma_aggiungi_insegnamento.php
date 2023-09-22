<?php
include 'area_riservata_segreteria.php';

$nome = $_POST['nome'];
$anno = $_POST['anno'];
$CFU = $_POST['CFU'];
$descrizione = $_POST['descrizione'];
$responsabile = $_POST['responsabile'] ? $_POST['responsabile'] : NULL;
$corso = $_POST['corso'];

$sql = 'INSERT INTO insegnamento VALUES (DEFAULT, $1, $2, $3, $4, $5, $6)';

$params = array(
    $nome,
    $anno,
    $CFU,
    $descrizione,
    $responsabile,
    $corso
);

$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Insegnamento creato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}
    header("Location: area_riservata_segreteria.php");
?>