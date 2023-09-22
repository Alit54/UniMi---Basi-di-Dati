<?php
include 'area_riservata_segreteria.php';

$mail = strtolower($_POST['username']);

$key = $_POST['key'];
$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$username = $mail;
$nascita = $_POST['nascita'];
$sesso = $_POST['sesso'];
$indirizzo = $_POST['indirizzo'];

$sql = "UPDATE docente 
        SET username = $2, 
            nome = $3, 
            cognome = $4,
            nascita = $5,
            sesso = $6,
            indirizzo = $7
        WHERE username = $1";

$params = array(
    $key,
    $username,
    $nome,
    $cognome,
    $nascita,
    $sesso,
    $indirizzo
);

$result = pg_query_params($db, $sql, $params);


if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Docente modificato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_segreteria.php");
?>
