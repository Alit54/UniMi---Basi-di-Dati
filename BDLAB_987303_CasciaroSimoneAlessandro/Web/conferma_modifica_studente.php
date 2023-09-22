<?php
include 'area_riservata_segreteria.php';

$mail = strtolower($_POST['username']);

$matricola = $_POST['matricola'];
$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$password = md5($nome . $cognome); 
$username = $mail;
$nascita = $_POST['nascita'];
$sesso = $_POST['sesso'];
$indirizzo = $_POST['indirizzo'];
$iscrizione = $_POST['iscrizione'];
$corso = $_POST['corso'];

$sql = "UPDATE studente 
        SET username = $2, 
            nome = $3, 
            cognome = $4,
            nascita = $5,
            sesso = $6,
            indirizzo = $7,
            iscrizione = $8
        WHERE matricola = $1";

$params = array(
    $matricola,
    $username,
    $nome,
    $cognome,
    $nascita,
    $sesso,
    $indirizzo,
    $iscrizione,
);

$result = pg_query_params($db, $sql, $params);


if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Utente modificato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_segreteria.php");

?>
