<?php
include_once("area_riservata_segreteria.php");
$matricola = $_GET['matricola'];

$dati_studente = "SELECT * FROM studente WHERE matricola = $1";
$params = array($matricola);
$result_modifica_studente = pg_query_params($db, $dati_studente, $params);
$row_modifica_studente = pg_fetch_assoc($result_modifica_studente);
?>

<form action="conferma_modifica_studente.php" method="POST">
    <h2 class="text-center"> Form modifica dati Studente </h2>
    
    <div class="form-group" hidden=""><span>Matricola</span>
        <input type="text" class="form-control" name="matricola" placeholder="" value="<?php echo $row_modifica_studente['matricola']; ?>" required="required">
    </div>

    <div class="form-group"><span>Nome</span>
        <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo $row_modifica_studente['nome']; ?>" required="required">
    </div>
    
    <div class="form-group"><span>Cognome</span>
        <input type="text" class="form-control" name="cognome" placeholder="" value="<?php echo $row_modifica_studente['cognome']; ?>" required="required">
    </div>

    <div class="form-group"><span>Data di Nascita</span>
        <input type="date" class="form-control" name="nascita" placeholder="" value="<?php echo $row_modifica_studente['nascita']; ?>" required="required">
    </div>

        <div class="form-group"><span>Username</span>
        <input type="text" class="form-control" name="username" placeholder="" value="<?php echo $row_modifica_studente['username']; ?>" required="required">
    </div>


    <div class="form-group"><span>Sesso</span>
                <select type="text" class="form-control" name="sesso" placeholder="Sesso" required="required">
                    <optgroup label="Attualmente memorizzato"> 
                    <option value="<?php echo $row_modifica_studente['sesso']; ?>"><?php echo $row_modifica_studente['sesso']; ?></option></optgroup> 
                    <optgroup label="Cambia con:"> 
                    <option value="Maschio">Maschio</option>
                    <option value="Femmina">Femmina</option>
                    <option value="<?php echo NULL ?>">Non dichiarato</option>
                    </optgroup>
                </select>
            </div>


    <div class="form-group"><span>Indirizzo</span>
        <input type="text" class="form-control" name="indirizzo" placeholder="" value="<?php echo $row_modifica_studente['indirizzo']; ?>" required="required">
    </div>

   
   <div class="form-group"><span>Data Iscrizione</span>
        <input type="date" class="form-control" name="iscrizione" placeholder="" value="<?php echo $row_modifica_studente['iscrizione']; ?>" required="required">
    </div>

    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->