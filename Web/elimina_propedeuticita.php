<?php
include_once("area_riservata_segreteria.php");
$insegnamento = $_GET['insegnamento'];
$requisito = $_GET['requisito'];

?>

<form action="conferma_elimina_propedeuticita.php" method="POST">
    <h2 class="text-center">Sei sicuro di voler eliminare la propedeuticità?</h2>
    <div class="form-group"><span>Insegnamento</span>
        <input readonly="" type="text" class="form-control" name="insegnamento" placeholder="" value="<?php echo $insegnamento; ?>" required="required">
    </div>
    <div class="form-group"><span>Requisito</span>
        <input readonly="" type="text" class="form-control" name="requisito" placeholder="" value="<?php echo $requisito; ?>" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->