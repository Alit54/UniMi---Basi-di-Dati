<?php
include_once("area_riservata_segreteria.php");
$id = $_GET['id'];

$dati_insegnamento = "SELECT * FROM insegnamento WHERE id = $1";
$params = array($id);
$result_eliminazione_insegnamento = pg_query_params($db, $dati_insegnamento, $params);
$row_eliminazione_insegnamento = pg_fetch_assoc($result_eliminazione_insegnamento);
?>

<form action="conferma_elimina_insegnamento.php" method="POST">
    <h2 class="text-center">Sei sicuro di voler eliminare l'insegnamento <?php echo $row_eliminazione_insegnamento['nome']?>?</h2>
    <p> ATTENZIONE: l'eliminazione non sarà possibile se alcuni studenti sono iscritti agli appelli di questo esame!
    <div class="form-group"><span>ID</span>
        <input readonly="" type="text" class="form-control" name="id" placeholder="Matricola" value="<?php echo $row_eliminazione_insegnamento['id']; ?>" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->