<?php
include 'area_riservata_segreteria.php';

$insegnamento = $_POST['insegnamento'];
$requisito = $_POST['requisito'];

$sql = 'INSERT INTO propedeuticita VALUES ($1, $2)';

$params = array(
    $insegnamento,
    $requisito
);
$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Propedeuticità creata con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}
    header("Location: area_riservata_segreteria.php");
?>