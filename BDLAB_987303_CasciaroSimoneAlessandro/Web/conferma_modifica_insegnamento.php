<?php
include 'area_riservata_segreteria.php';


$id = $_POST['id'];
$nome = $_POST['nome'];
$anno = $_POST['anno'];
$CFU = $_POST['CFU'];
$descrizione = $_POST['descrizione'];
$responsabile = $_POST['responsabile'] ? $_POST['responsabile'] : NULL;
$corso = $_POST['corso'];

$sql = "UPDATE insegnamento 
        SET nome = $2, 
            anno = $3, 
            CFU = $4,
            descrizione = $5,
            responsabile = $6
        WHERE id = $1";

$params = array(
    $id,
    $nome,
    $anno,
    $CFU,
    $descrizione,
    $responsabile
);

$result = pg_query_params($db, $sql, $params);


if ($result) {
    $_SESSION['Comunicazioni_Sg'] = "Insegnamento modificato con successo!";
} else { 
    $errore = pg_last_error($db);
    $_SESSION['Comunicazioni_Sg'] = "Errore: " . $errore; 
}

    header("Location: area_riservata_segreteria.php");
?>
