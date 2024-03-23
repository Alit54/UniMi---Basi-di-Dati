<?php
include 'area_riservata_segreteria.php';

$input_username = $_POST['user'];

$sql = "DELETE FROM segreteria WHERE username = $1";
$params = array(
    $input_username  
);

$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Segretario eliminato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_segreteria.php");

?>
