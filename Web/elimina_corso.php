<?php
include_once("area_riservata_segreteria.php");
$nome = $_GET['nome'];


$dati_corso = "SELECT * FROM corso WHERE nome = $1";
$params = array($nome);
$result_eliminazione_corso = pg_query_params($db, $dati_corso, $params);
$row_eliminazione_corso = pg_fetch_assoc($result_eliminazione_corso);
?>

<form action="conferma_elimina_corso.php" method="POST">
    <h2 class="text-center">Sei sicuro di voler eliminare il corso <?php echo $row_eliminazione_corso['nome']; ?>?</h2>
    <p> ATTENZIONE: non sarà possibile rimuovere il corso se ci sono studenti iscritti!
    <div class="form-group"><span>Nome</span>
        <input readonly="" type="text" class="form-control" name="nome" placeholder="Nome" value="<?php echo $row_eliminazione_corso['nome']; ?>" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->