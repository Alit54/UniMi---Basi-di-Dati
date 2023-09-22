<?php
include_once("area_riservata_segreteria.php");
?>

<form action="conferma_aggiungi_corso.php" method="POST">
    <h2 class="text-center"> Form inserimento dati Nuovo Corso </h2>
    
    <div class="form-group"><span>Nome</span>
        <input type="text" class="form-control" name="nome" placeholder="" value="" required="required">
    </div>

    <div class="form-group"><span>Tipologia</span>
                <select type="text" class="form-control" name="durata" placeholder="tipologia" required="required">
                    <option value=3>Triennale</option>
                    <option value=2>Magistrale</option>
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