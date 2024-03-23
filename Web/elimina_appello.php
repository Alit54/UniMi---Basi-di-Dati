<?php
include_once("area_riservata_docenti.php");
$insegnamento = $_GET['insegnamento'];
$data = $_GET['data'];
$username_session = $_SESSION['username'];


// Query che restituisce il responsabile dell'insegnamento
$query_responsabile_insegnamento = "SELECT responsabile FROM insegnamento WHERE id = $1";
$params = array($insegnamento);
$responsabile_insegnamento = pg_query_params($db, $query_responsabile_insegnamento, $params);
$nome_responsabile = pg_fetch_assoc($responsabile_insegnamento);
$controllo_responsabile = $nome_responsabile['responsabile'];


// Mostra i risultati solo se il docente loggato è uguale al risultato della Query precedente
if ($controllo_responsabile == $username_session) {


?>

<form action="conferma_elimina_appello.php" method="POST">
    <h2 class="text-center">Sei sicuro di voler eliminare l'appello del <?php echo date("d/m/Y H:i", strtotime($data)); ?>?</h2>
    <div class="form-group" hidden=""><span>Data</span>
        <input type="text" class="form-control" name="data" placeholder="Nome" value="<?php echo $data; ?>" required="required">
    </div>
    <div class="form-group" hidden=""><span>Insegnamento</span>
        <input type="text" class="form-control" name="insegnamento" placeholder="Nome" value="<?php echo $insegnamento; ?>" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
<?php } else {  echo 'Accesso negato'; } ?>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->