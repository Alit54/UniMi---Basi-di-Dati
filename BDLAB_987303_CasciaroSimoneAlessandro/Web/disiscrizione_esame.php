<?php
include_once("area_riservata_studenti.php");
$matricola = $_GET['matricola'];
$insegnamento = $_GET['insegnamento'];
$data = $_GET['data'];

$dati_appello = "DELETE FROM esame WHERE studente = $1 AND insegnamento = $2 AND data = $3";
$params = array($matricola, $insegnamento, $data);
$result_eliminazione_appello = pg_query_params($db, $dati_appello, $params);

if ($result_eliminazione_appello) {
   $_SESSION['Comunicazioni_St'] = "Disiscrizione completata!";
} else {
   $errore = pg_last_error($db);
   $_SESSION['Comunicazioni_St'] = "Errore: " . $errore; 
}

header("Location: visualizza_appelli.php?id=" . $insegnamento);
?>

