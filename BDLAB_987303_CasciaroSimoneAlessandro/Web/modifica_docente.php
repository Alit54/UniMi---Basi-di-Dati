<?php
include_once("area_riservata_segreteria.php");
$username = $_GET['user'];

$dati_docente = "SELECT * FROM docente WHERE username = $1";
$params = array($username);
$result_modifica_docente = pg_query_params($db, $dati_docente, $params);
$row_modifica_docente = pg_fetch_assoc($result_modifica_docente);
?>

<form action="conferma_modifica_docente.php" method="POST">
    <h2 class="text-center"> Form modifica dati Docente </h2>

    <div class="form-group" hidden=""><span>Key</span>
        <input type="text" class="form-control" name="key" placeholder="" value="<?php echo $row_modifica_docente['username']; ?>" required="required">
    </div>

    <div class="form-group"><span>Nome</span>
        <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo $row_modifica_docente['nome']; ?>" required="required">
    </div>
    
    <div class="form-group"><span>Cognome</span>
        <input type="text" class="form-control" name="cognome" placeholder="" value="<?php echo $row_modifica_docente['cognome']; ?>" required="required">
    </div>

    <div class="form-group"><span>Data di Nascita</span>
        <input type="date" class="form-control" name="nascita" placeholder="" value="<?php echo $row_modifica_docente['nascita']; ?>" required="required">
    </div>

        <div class="form-group"><span>Username</span>
        <input type="text" class="form-control" name="username" placeholder="" value="<?php echo $row_modifica_docente['username']; ?>" required="required">
    </div>


    <div class="form-group"><span>Sesso</span>
                <select type="text" class="form-control" name="sesso" placeholder="Sesso" required="required">
                    <optgroup label="Attualmente memorizzato"> 
                    <option value="<?php echo $row_modifica_docente['sesso']; ?>"><?php echo $row_modifica_docente['sesso']; ?></option></optgroup> 
                    <optgroup label="Cambia con:"> 
                    <option value="Maschio">Maschio</option>
                    <option value="Femmina">Femmina</option>
                    <option value="<?php echo NULL ?>">Non dichiarato</option></optgroup></select>
            </div>


    <div class="form-group"><span>Indirizzo</span>
        <input type="text" class="form-control" name="indirizzo" placeholder="" value="<?php echo $row_modifica_docente['indirizzo']; ?>" required="required">
    </div>

    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->