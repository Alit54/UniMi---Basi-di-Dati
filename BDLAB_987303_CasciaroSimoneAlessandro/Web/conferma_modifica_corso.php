<?php
include 'area_riservata_segreteria.php';

$key = $_POST['key'];
$nome = $_POST['nome'];
$durata = $_POST['durata'];
$descrizione = $_POST['descrizione'];

$sql = "UPDATE corso 
        SET nome = $2, 
            durata = $3, 
            descrizione = $4
        WHERE nome = $1";

$params = array(
    $key,
    $nome,
    $durata,
    $descrizione
);
$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Corso modificato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_segreteria.php");
?>
