<?php
include 'area_riservata_segreteria.php';

$input_nome = $_POST['nome'];

$sql = "DELETE FROM corso WHERE nome = $1";
$params = array(
    $input_nome
);

$result = pg_query_params($db, $sql, $params);

if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Corso eliminato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_segreteria.php");

?>
