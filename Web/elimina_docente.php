<?php
include_once("area_riservata_segreteria.php");
$username = $_GET['user'];


$dati_docente = "SELECT * FROM docente WHERE username = $1";
$params = array($username);
$result_eliminazione_docente = pg_query_params($db, $dati_docente, $params);
$row_eliminazione_docente = pg_fetch_assoc($result_eliminazione_docente);
?>

<form action="conferma_elimina_docente.php" method="POST">
    <h2 class="text-center">Sei sicuro di voler eliminare il docente <?php echo $row_eliminazione_docente['nome'] . ' ' . $row_eliminazione_docente['cognome']; ?> ?</h2>

    <p> ATTENZIONE: eliminare un docente lascerà tutti i suoi insegnamenti senza docente!

    <div class="form-group"><span>Username</span>
        <input readonly="" type="text" class="form-control" name="user" placeholder="Username" value="<?php echo $row_eliminazione_docente['username']; ?>" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->