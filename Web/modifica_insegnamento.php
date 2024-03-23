<?php
include_once("area_riservata_segreteria.php");
$id = $_GET['id'];

$dati_insegnamento = "SELECT * FROM insegnamento WHERE id = $1";
$params = array($id);
$result_modifica_insegnamento = pg_query_params($db, $dati_insegnamento, $params);
$row_modifica_insegnamento = pg_fetch_assoc($result_modifica_insegnamento);
?>

<form action="conferma_modifica_insegnamento.php" method="POST">
    <h2 class="text-center"> Form modifica dati Insegnamento </h2>

    <div class="form-group" hidden=""><span>ID</span>
        <input type="text" class="form-control" name="id" placeholder="" value="<?php echo $row_modifica_insegnamento['id']; ?>" required="required">
    </div>

    <div class="form-group"><span>Nome</span>
        <input type="text" class="form-control" name="nome" placeholder="" value="<?php echo $row_modifica_insegnamento['nome']; ?>" required="required">
    </div>
    
    <div class="form-group"><span>Anno Previsto</span>
        <input type="number" min="1" max="3" class="form-control" name="anno" placeholder="" value="<?php echo $row_modifica_insegnamento['anno']; ?>" required="required">
    </div>

    <div class="form-group"><span>CFU</span>
        <input type="number" min="1" class="form-control" name="CFU" placeholder="" value="<?php echo $row_modifica_insegnamento['cfu']; ?>" required="required">
    </div>


     <?php  
     // Query per ottenere tutti i docenti che hanno meno di 3 insegnamenti
     $query_menu_insegnamenti = "SELECT d.username, d.nome || ' ' || d.cognome AS docente 
                                FROM docente d
                                LEFT JOIN insegnamento i ON d.username = i.responsabile
                                GROUP BY d.username
                                HAVING count(*) < 3
                                ORDER BY docente ASC";
           $menu_insegnamenti = pg_query($db, $query_menu_insegnamenti); ?>

    <div class="form-group"><span>Responsabile</span>
                <select type="text" class="form-control" name="responsabile" placeholder="Responsabile">
                <option value="<?php echo $row_modifica_insegnamento['responsabile'];?>">Non cambiare </option>
        <option value="<?php echo NULL; ?>"> Nessun docente </option> 
      <?php while ($row = pg_fetch_assoc($menu_insegnamenti)){ ?>      <option value="<?php echo $row['username'] ?>"><?php echo $row['docente'] ?></option>
      <?php } ?>              
                    <select>
    </div>

    <div class="form-group"><span>Descrizione</span>
        <input type="text" class="form-control" name="descrizione" placeholder="" value="<?php echo $row_modifica_insegnamento['descrizione']; ?>" required="required">
    </div>

    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->