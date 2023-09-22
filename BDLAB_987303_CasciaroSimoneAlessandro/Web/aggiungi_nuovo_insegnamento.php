<?php
include_once("area_riservata_segreteria.php");

?>

<form action="conferma_aggiungi_insegnamento.php" method="POST">
    <h2 class="text-center"> Form inserimento dati Nuovo Insegnamento </h2>
    
    <div class="form-group"><span>Nome</span>
        <input type="text" class="form-control" name="nome" placeholder="" value="" required="required">
    </div>

    <div class="form-group"><span>Anno Previsto</span>
        <input type="number" min="1" max="3" class="form-control" name="anno" placeholder="" value="1" required="required">
    </div>

    <div class="form-group"><span>CFU</span>
        <input type="number" min="1" class="form-control" name="CFU" placeholder="" value="3" required="required">
    </div>

    <?php 
    // Query che restituisce tutti i docenti con meno di 3 insegnamenti
     $query_menu_insegnamenti = "SELECT d.username, d.nome || ' ' || d.cognome AS docente 
                                FROM docente d
                                LEFT JOIN insegnamento i ON d.username = i.responsabile
                                GROUP BY d.username
                                HAVING count(*) < 3
                                ORDER BY docente ASC";
           $menu_insegnamenti = pg_query($db, $query_menu_insegnamenti); ?>

    <div class="form-group"><span>Responsabile</span>
                <select type="text" class="form-control" name="responsabile" placeholder="Responsabile">
        <option value="<?php echo NULL; ?>"> Nessun docente </option> 
      <?php while ($row = pg_fetch_assoc($menu_insegnamenti)){ ?>      <option value="<?php echo $row['username'] ?>"><?php echo $row['docente'] ?></option>
      <?php } ?>              
                    </select>
    </div>

     <?php  $query_menu_corsi = "SELECT nome FROM CORSO ORDER BY nome ASC";
           $menu_corsi = pg_query($db, $query_menu_corsi); ?>
    
    <div class="form-group"><span>Corso</span>
                <select type="text" class="form-control" name="corso" placeholder="Corso" required="required">
      <?php while ($row = pg_fetch_assoc($menu_corsi)){ ?>      <option value="<?php echo $row['nome'] ?>"><?php echo $row['nome'] ?></option>
      <?php } ?>              
                    </select>
            </div>

    <div class="form-group"><span>Descrizione</span>
        <input type="text" class="form-control" name="descrizione" placeholder="" value="" required="required">
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->