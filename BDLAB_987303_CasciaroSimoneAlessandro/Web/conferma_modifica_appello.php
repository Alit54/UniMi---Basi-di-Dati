<?php
include 'area_riservata_docenti.php';

$key = $_POST['key']; // Insegnamento
$key2 = $_POST['key2']; // Data
$data = $_POST['data'] . ' ' . $_POST['ora'];
$luogo = $_POST['luogo'];

$sql = "UPDATE appello 
        SET data = $3, 
            luogo = $4
        WHERE insegnamento = $1 AND data = $2";

$params = array(
    $key,
    $key2,
    $data,
    $luogo
);
$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_D'] = "Appello modificato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_D'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_docenti.php");
?>
