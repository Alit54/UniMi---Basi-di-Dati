<?php
include_once("area_riservata_segreteria.php");
$username = $_GET['user'];


$dati_segretario = "SELECT * FROM segreteria WHERE username = $1";
$params = array($username);
$result_eliminazione_segretario = pg_query_params($db, $dati_segretario, $params);
$row_eliminazione_segretario = pg_fetch_assoc($result_eliminazione_segretario);
?>

<form action="conferma_elimina_segretario.php" method="POST">
    <h2 class="text-center">Sei sicuro di voler eliminare il segretario <?php echo $row_eliminazione_segretario['nome'] . ' ' . $row_eliminazione_segretario['cognome']; ?> ?</h2>

    <div class="form-group"><span>Username</span>
        <input readonly="" type="text" class="form-control" name="user" placeholder="Username" value="<?php echo $row_eliminazione_segretario['username']; ?>" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->