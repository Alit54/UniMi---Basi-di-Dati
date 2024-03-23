<?php
include_once("area_riservata_segreteria.php");
$nome = $_GET['nome'];

$dati_corso = "SELECT * FROM corso WHERE nome = $1";
$params = array($nome);
$result_modifica_corso = pg_query_params($db, $dati_corso, $params);
$row_modifica_corso = pg_fetch_assoc($result_modifica_corso);
?>

<form action="conferma_modifica_corso.php" method="POST">
    <h2 class="text-center"> Form modifica dati Corso </h2>

     <div class="form-group" hidden=""><span>key</span>
        <input type="text" class="form-control" name="key" placeholder="" value="<?php echo $nome; ?>" required="required">
    </div>

    <div class="form-group"><span>Nome</span>
        <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo $row_modifica_corso['nome']; ?>" required="required">
    </div>

     <div class="form-group"><span>Tipologia</span>
                <select type="text" class="form-control" name="durata" placeholder="Sesso" required="required">
                    <option value=3>Triennale</option>
                    <option value=2>Magistrale</option>
                </select>
        </div>

        <div class="form-group"><span>Descrizione</span>
        <input type="text" class="form-control" name="descrizione" placeholder="" value="<?php echo $row_modifica_corso['descrizione']; ?>" required="required">
    </div>

    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->