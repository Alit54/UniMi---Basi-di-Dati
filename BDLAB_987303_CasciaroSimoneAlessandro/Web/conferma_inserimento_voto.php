<?php
include 'area_riservata_docenti.php';

$matricola = $_POST['matricola'];
$insegnamento = $_POST['insegnamento'];
$data = $_POST['data'];
$voto = $_POST['voto'];
$lode = $_POST['lode'];

$sql = "UPDATE esame 
        SET voto = $4, 
            lode = $5
        WHERE insegnamento = $1 AND data = $2 AND studente = $3";

$params = array(
    $insegnamento,
    $data,
    $matricola,
    $voto, 
    $lode
);
$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_D'] = "Voto inserito con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_D'] = "Errore: " . $errore; 
}

    header("Location: lista_studenti_iscritti_appello.php?insegnamento=" . $insegnamento . "&data=" . $data);

?>
