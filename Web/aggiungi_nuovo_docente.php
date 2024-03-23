<?php
include_once("area_riservata_segreteria.php");

?>

<form action="conferma_aggiungi_docente.php" method="POST">
    <h2 class="text-center"> Form inserimento dati Nuovo Docente </h2>
    
    <div class="form-group"><span>Nome</span>
        <input type="text" class="form-control" name="nome" placeholder="" value="" required="required">
    </div>
    
    <div class="form-group"><span>Cognome</span>
        <input type="text" class="form-control" name="cognome" placeholder="" value="" required="required">
    </div>

    <div class="form-group"><span>Data di Nascita</span>
        <input type="date" class="form-control" name="nascita" placeholder="" value="" required="required">
    </div>

        <div class="form-group"><span>Username</span>
        <input type="text" class="form-control" name="username" placeholder="" value="" required="required">
    </div>


    <div class="form-group"><span>Sesso</span>
                <select type="text" class="form-control" name="sesso" placeholder="Sesso" required="required">
                    <option value="Maschio">Maschio</option>
                    <option value="Femmina">Femmina</option>
                    <option value="<?php echo NULL ?>">Non dichiarato</option><select>
            </div>


    <div class="form-group"><span>Indirizzo</span>
        <input type="text" class="form-control" name="indirizzo" placeholder="" value="" required="required">
    </div>


    
    <?php  $query_insegnamenti = "SELECT id, nome FROM insegnamento WHERE responsabile IS NULL ORDER BY nome ASC";
           $menu_insegnamenti = pg_query($db, $query_insegnamenti); ?>
    
    <div class="form-group"><span>Primo Insegnamento</span>
                <select type="text" class="form-control" name="insegnamento" placeholder="Insegnamento" required="required">
      <?php while ($row = pg_fetch_assoc($menu_insegnamenti)){ ?>      <option value="<?php echo $row['id'] ?>"><?php echo $row['nome'] ?></option>
      <?php } ?>              
                    </select>
            </div>



    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->