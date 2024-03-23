<?php
include 'area_riservata_segreteria.php';

$input_id = $_POST['id'];


$sql = "DELETE FROM insegnamento WHERE id = $1";
$params = array(
    $input_id  
);

$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Insegnamento eliminato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_segreteria.php");
?>
