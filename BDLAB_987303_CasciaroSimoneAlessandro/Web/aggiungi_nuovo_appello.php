<?php
include_once("area_riservata_docenti.php");

$insegnamento = $_GET['insegnamento'];
$username_session = $_SESSION['username'];

// Query che restituisce il responsabile dell'insegnamento
$query_responsabile_insegnamento = "SELECT responsabile FROM insegnamento WHERE id = $1";
$params = array($insegnamento);
$responsabile_insegnamento = pg_query_params($db, $query_responsabile_insegnamento, $params);
$nome_responsabile = pg_fetch_assoc($responsabile_insegnamento);
$controllo_responsabile = $nome_responsabile['responsabile'];


// Mostra i risultati solo se il docente loggato è lo stesso della Query precedente
if ($controllo_responsabile == $username_session) {

?>

<form action="conferma_aggiungi_appello.php" method="POST">
    <h2 class="text-center"> Form inserimento dati Nuovo Appello </h2>
    
    <div class="form-group" hidden=""><span>Insegnamento</span>
        <input type="text" class="form-control" name="insegnamento" placeholder="" value="<?php echo $insegnamento; ?>" required="required">
    </div>

    <div class="form-group"><span>Data</span>
        <input type="date" class="form-control" name="data" placeholder="" value="" required="required">
    </div>

    <div class="form-group"><span>Ora</span>
        <input type="time" class="form-control" name="ora" placeholder="" value="" required="required">
    </div>

    <div class="form-group"><span>Luogo</span>
        <input type="text" class="form-control" name="luogo" placeholder="Luogo" value="" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
<?php } else  { die('Accesso negato'); } ?>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->