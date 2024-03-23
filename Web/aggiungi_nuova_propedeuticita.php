<?php
include_once("area_riservata_segreteria.php");

$insegnamento = $_GET['insegnamento'];

$sql = "SELECT corso FROM insegnamento WHERE id = $1";
$params = array($insegnamento);

$result = pg_query_params($db, $sql, $params);
$row = pg_fetch_assoc($result);

?>

<form action="conferma_aggiungi_propedeuticita.php" method="POST">
    <h2 class="text-center"> Form inserimento dati Nuova Propedeuticità </h2>
    
     <?php  $query_propedeuticita = "SELECT id, nome 
                                    FROM insegnamento 
                                    WHERE corso = $1
                                    EXCEPT
                                    SELECT id, nome
                                    FROM insegnamento
                                    WHERE id = $2
                                    ORDER BY nome ASC";
            $params = array($row['corso'], $insegnamento);
           $menu_propedeuticita = pg_query_params($db, $query_propedeuticita, $params); ?>
    
    <div class="form-group"><span>Requisito</span>
                <select type="text" class="form-control" name="requisito" placeholder="" required="required">
      <?php while ($row = pg_fetch_assoc($menu_propedeuticita)){ ?>      <option value="<?php echo $row['id'] ?>"><?php echo $row['nome'] ?></option>
      <?php } ?>              
                    <select>
            </div>

    <div class="form-group" hidden=""><span>Insegnamento</span>
        <input type="text" class="form-control" name="insegnamento" placeholder="" value="<?php echo $insegnamento; ?>" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->