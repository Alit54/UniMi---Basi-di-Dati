<?php
include 'area_riservata_segreteria.php';

$nome = $_POST['nome'];
$durata = $_POST['durata'];
$descrizione = $_POST['descrizione'];

$sql = 'INSERT INTO corso VALUES ($1, $2, $3)';

$params = array(
    $nome,
    $durata,
    $descrizione
);
$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Corso creato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}
    header("Location: area_riservata_segreteria.php");
?>