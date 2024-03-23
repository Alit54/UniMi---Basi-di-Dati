<?php
include_once("area_riservata_studenti.php");
$matricola = $_GET['matricola'];
$insegnamento = $_GET['insegnamento'];
$data = $_GET['data'];

$dati_appello = "INSERT INTO esame VALUES ($1, $2, $3, NULL, NULL)";
$params = array($data, $insegnamento, $matricola);
$result_inserimento_appello = pg_query_params($db, $dati_appello, $params);

if ($result_inserimento_appello) {
   $_SESSION['Comunicazioni_St'] = "Iscrizione completata!";
} else {
   $errore = pg_last_error($db);
   $_SESSION['Comunicazioni_St'] = "Errore: " . $errore; 
}


   header("Location: visualizza_appelli.php?id=" . $insegnamento);
?>

