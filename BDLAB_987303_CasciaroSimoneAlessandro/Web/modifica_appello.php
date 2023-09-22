<?php
include_once("area_riservata_docenti.php");
$insegnamento = $_GET['insegnamento'];
$data = $_GET['data'];
$username_session = $_SESSION['username'];

// Query per ottenere il responsabile dell'insegnamento di cui si vuole modificare l'appello
$query_responsabile_insegnamento = "SELECT responsabile FROM insegnamento WHERE id = $1";
$params = array($insegnamento);
$responsabile_insegnamento = pg_query_params($db, $query_responsabile_insegnamento, $params);
$nome_responsabile = pg_fetch_assoc($responsabile_insegnamento);
$controllo_responsabile = $nome_responsabile['responsabile'];


// Mostra il risultato solo se il docente loggato è uguale al risultato della Query precedente
if ($controllo_responsabile == $username_session) {


$dati_appello = "SELECT * FROM appello WHERE insegnamento = $1 AND data = $2";
$params = array($insegnamento, $data);
$result_modifica_appello = pg_query_params($db, $dati_appello, $params);
$row_modifica_appello = pg_fetch_assoc($result_modifica_appello);
?>

<form action="conferma_modifica_appello.php" method="POST">
    <h2 class="text-center"> Form modifica dati Appello </h2>
    
    <div class="form-group" hidden=""><span>Insegnamento</span>
        <input type="text" class="form-control" name="key" placeholder="" value="<?php echo $insegnamento; ?>" required="required">
    </div>

    <div class="form-group" hidden=""><span>Data</span>
        <input type="text" class="form-control" name="key2" placeholder="" value="<?php echo $data; ?>" required="required">
    </div>

    <div class="form-group"><span>Data</span>
        <input type="date" class="form-control" name="data" placeholder="" value="<?php echo date("Y-m-d", strtotime($row_modifica_appello['data'])); ?>" required="required">
    </div>

    <div class="form-group"><span>Ora</span>
        <input type="time" class="form-control" name="ora" placeholder="" value="<?php echo date("H:i", strtotime($row_modifica_appello['data'])); ?>" required="required">
    </div>

    <div class="form-group"><span>Luogo</span>
        <input type="text" class="form-control" name="luogo" placeholder="Luogo" value="<?php echo $row_modifica_appello['luogo']; ?>" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
<?php } else {  echo 'Accesso negato'; } ?>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->