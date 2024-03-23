<?php
include 'area_riservata_segreteria.php';

$mail = strtolower($_POST['username']);

$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$password = md5($nome . $cognome); 
$username = $mail . '@unimi.it';
$nascita = $_POST['nascita'];
$sesso = $_POST['sesso'];
$indirizzo = $_POST['indirizzo'];

$sql = 'INSERT INTO segreteria VALUES ($1, $2, $3, $4, $5, $6, $7)';

$params = array(
    $username,
    $password,
    $nome,
    $cognome,
    $nascita,
    $sesso,
    $indirizzo
);

$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Segretario creato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}
    header("Location: area_riservata_segreteria.php");
?>