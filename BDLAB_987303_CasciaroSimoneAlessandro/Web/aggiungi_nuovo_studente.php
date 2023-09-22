<?php
include_once("area_riservata_segreteria.php");
?>

<form action="conferma_aggiungi_studente.php" method="POST">
    <h2 class="text-center"> Form inserimento dati Nuovo Studente </h2>
    
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

   
   <div class="form-group"><span>Data Iscrizione</span>
        <input type="date" class="form-control" name="iscrizione" placeholder="" value="" required="required">
    </div>

    
    <?php  $query_menu_corsi = "SELECT nome FROM CORSO ORDER BY nome ASC";
           $menu_corsi = pg_query($db, $query_menu_corsi); ?>
    
    <div class="form-group"><span>Corso</span>
                <select type="text" class="form-control" name="corso" placeholder="Corso" required="required">
      <?php while ($row = pg_fetch_assoc($menu_corsi)){ ?>      <option value="<?php echo $row['nome'] ?>"><?php echo $row['nome'] ?></option>
      <?php } ?>              
                    <select>
            </div>



    
    <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block">Conferma</button>
    </div>
</form>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->