<?php
include_once("area_riservata_segreteria.php");
$matricola = $_GET['matricola'];

$dati_studente = "SELECT * FROM studente WHERE matricola = $1";
$params = array($matricola);
$result_eliminazione_studente = pg_query_params($db, $dati_studente, $params);
$row_eliminazione_studente = pg_fetch_assoc($result_eliminazione_studente);
?>

<form action="conferma_elimina_studente.php" method="POST">
    <h2 class="text-center">Sei sicuro di voler eliminare lo studente <?php echo $row_eliminazione_studente['nome'] . ' ' . $row_eliminazione_studente['cognome']; ?> ?</h2>
    <div class="form-group"><span>Inattività</span>
        <input type="date" class="form-control" name="inattivita" placeholder="Nome utente" value="<?php echo date("Y-m-d"); ?>" required="required">
    </div>
    <div class="form-group"><span>Motivazione</span>
        <select type="text" class="form-control" name="motivazione" placeholder="Ruolo" required="required">
            <option value="<?php echo NULL ?>">Scegli un'opzione</option>
            <option value="Rinuncia">Rinuncia agli Studi</option>
            <option value="Laurea">Laurea</option>
        </select> <!-- Chiusura mancante del tag </select> -->
    </div>

    <div class="form-group"><span>Matricola</span>
        <input readonly="" type="text" class="form-control" name="matricola" placeholder="Matricola" value="<?php echo $row_eliminazione_studente['matricola']; ?>" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->