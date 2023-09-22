<?php
include_once("area_riservata_segreteria.php");
$username = $_GET['user'];

$dati_segretario = "SELECT * FROM segreteria WHERE username = $1";
$params = array($username);
$result_modifica_segretario = pg_query_params($db, $dati_segretario, $params);
$row_modifica_segretario = pg_fetch_assoc($result_modifica_segretario);
?>

<form action="conferma_modifica_segretario.php" method="POST">
    <h2 class="text-center"> Form modifica dati Segretario </h2>

    <div class="form-group" hidden=""><span>Key</span>
        <input type="text" class="form-control" name="key" placeholder="" value="<?php echo $row_modifica_segretario['username']; ?>" required="required">
    </div>

    <div class="form-group"><span>Nome</span>
        <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo $row_modifica_segretario['nome']; ?>" required="required">
    </div>
    
    <div class="form-group"><span>Cognome</span>
        <input type="text" class="form-control" name="cognome" placeholder="" value="<?php echo $row_modifica_segretario['cognome']; ?>" required="required">
    </div>

    <div class="form-group"><span>Data di Nascita</span>
        <input type="date" class="form-control" name="nascita" placeholder="" value="<?php echo $row_modifica_segretario['nascita']; ?>" required="required">
    </div>

        <div class="form-group"><span>Username</span>
        <input type="text" class="form-control" name="username" placeholder="" value="<?php echo $row_modifica_segretario['username']; ?>" required="required">
    </div>


    <div class="form-group"><span>Sesso</span>
                <select type="text" class="form-control" name="sesso" placeholder="Sesso" required="required">
                    <optgroup label="Attualmente memorizzato"> 
                    <option value="<?php echo $row_modifica_segretario['sesso']; ?>"><?php echo $row_modifica_segretario['sesso']; ?></option></optgroup> 
                    <optgroup label="Cambia con:"> 
                    <option value="Maschio">Maschio</option>
                    <option value="Femmina">Femmina</option>
                    <option value="<?php echo NULL ?>">Non dichiarato</option></optgroup></select>
            </div>


    <div class="form-group"><span>Indirizzo</span>
        <input type="text" class="form-control" name="indirizzo" placeholder="" value="<?php echo $row_modifica_segretario['indirizzo']; ?>" required="required">
    </div>

    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->