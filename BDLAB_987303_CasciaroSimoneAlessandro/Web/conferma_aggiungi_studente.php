<?php
include 'area_riservata_segreteria.php';

// La matricola viene assegnata di default dalla funzione get_new_matricola()
$matricola = pg_query($db, 'SELECT * FROM get_new_matricola()');
$mat = pg_fetch_assoc($matricola);

$mail = strtolower($_POST['username']);

$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$password = md5($nome . $cognome); 
$username = $mail . '@studenti.unimi.it';
$nascita = $_POST['nascita'];
$sesso = $_POST['sesso'];
$indirizzo = $_POST['indirizzo'];
$iscrizione = $_POST['iscrizione'];
$corso = $_POST['corso'];

$sql = 'INSERT INTO studente VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)';

$params = array(
    $mat['get_new_matricola'],
    $username,
    $password,
    $nome,
    $cognome,
    $nascita,
    $sesso,
    $indirizzo,
    $iscrizione,
    $corso
);

$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Studente creato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}
    header("Location: area_riservata_segreteria.php");
?>