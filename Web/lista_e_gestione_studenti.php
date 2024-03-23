<?php
include_once("area_riservata_segreteria.php");

// Controllo CheckBox
$MostraInattivi = isset($_GET['mostraTabella']);



echo "<br> <br>
      <B>Lista e Gestione Studenti</B> <br> <br>";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = $search ? "WHERE nome ILIKE '%$search%' OR cognome ILIKE '%$search%' OR CAST(matricola AS VARCHAR) ILIKE '%$search%'" : '';
$lista_studenti = "SELECT * FROM studente $search_condition ORDER BY cognome, nome, matricola ASC";
$results = pg_query($db, $lista_studenti);
?>

<table class="styled-table" width="100%">
    <tr>
        <td Style="text-align: left;">
            <div class="search-box">
                <form action="" method="GET">
                    <label for="search">Cerca:</label>
                    <input type="text" id="search" name="search" placeholder="Inserire nome, cognome o matricola" value="<?php echo $search; ?>">
                    <button type="submit">Cerca</button>
                    <?php
                    if (!empty($_GET['search'])) {
                        echo '<a href="lista_e_gestione_studenti.php" class="btn btn-primary">Indietro</a>';
                    }
                    ?>
                    <label for="mostraTabella">Mostra Inattivi:</label>
<input type="checkbox" name="mostraTabella" id="mostraTabella" <?php echo $MostraInattivi ? 'checked' : ''; ?> onchange="this.form.submit()">
                </form>
            </div>
        </td>
        <td align="center">
            <a href="aggiungi_nuovo_studente.php" class="btn btn-success">Aggiungi Nuovo Studente</a>
        </td>
    </tr>
</table>

<?php
if (!$MostraInattivi) {
if (pg_num_rows($results) === 0) {
    if ($search) {
        echo "<p>Nessun risultato trovato per la ricerca: '$search'</p>";
    } else {
        echo "<p>Nessun risultato trovato</p>";   
    }
} else {  // tabella con gli utenti Attivi 
    ?>
<table class="styled-table" width="100%">
    <thead>
        <tr>
            <th>Matricola</th>
            <th>Cognome</th>
            <th>Nome</th>
            <th>e-Mail</th>
            <th>Corso</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($results)){ ?>
        <tr>
            <td><?php echo $row['matricola']; ?></td>
            <td><?php echo $row['cognome']; ?></td>
            <td><?php echo $row['nome']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['corso']; ?></td>
            <td>
                <a href="modifica_studente.php?matricola=<?php echo $row['matricola']; ?>" class="btn btn-primary">Modifica</a>
                <a href="elimina_studente.php?matricola=<?php echo $row['matricola']; ?>" class="btn btn-danger">Elimina</a>
                <a href="carriera_studente.php?matricola=<?php echo $row['matricola']; ?>" class="btn btn-info">Carriera</a>
                <a href="carriera_valida_studente.php?matricola=<?php echo $row['matricola']; ?>" class="btn btn-warning">Carriera Valida</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php
} } else { 
$lista_studenti_attivi_e_inattivi = "SELECT *, NULL, NULL, True 
                                    FROM studente $search_condition
                                    UNION
                                    SELECT *, False
                                    FROM studente_storico $search_condition
                                    ORDER BY cognome, nome, matricola"; 
$risultati_attivi_e_inattivi = pg_query($db, $lista_studenti_attivi_e_inattivi);
// Tabella con gli utenti sia Attivi che Inattivi
   ?>
<table class="styled-table" width="100%">
    <thead>
        <tr>
            <th>Matricola</th>
            <th>Cognome</th>
            <th>Nome</th>
            <th>e-Mail</th>
            <th>Corso</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($attivi_e_inattivi = pg_fetch_assoc($risultati_attivi_e_inattivi)){ ?>
        <tr <?php if ($attivi_e_inattivi['bool'] == 'f') { ?> style="background-color:#eba3a3;" <?php } ?> >
            <td><?php echo $attivi_e_inattivi['matricola']; ?></td>
            <td><?php echo $attivi_e_inattivi['cognome']; ?></td>
            <td><?php echo $attivi_e_inattivi['nome']; ?></td>
            <td><?php echo $attivi_e_inattivi['username']; ?></td>
            <td><?php echo $attivi_e_inattivi['corso']; ?></td>
            <td>
                <?php if ($attivi_e_inattivi['bool'] == 't') { ?>
                <a href="modifica_studente.php?matricola=<?php echo $attivi_e_inattivi['matricola']; ?>" class="btn btn-primary">Modifica</a>
                <a href="elimina_studente.php?matricola=<?php echo $attivi_e_inattivi['matricola']; ?>" class="btn btn-danger">Elimina</a> <?php } ?>
                <a href="carriera_studente.php?matricola=<?php echo $attivi_e_inattivi['matricola']; ?>" class="btn btn-info">Carriera</a>
                <a href="carriera_valida_studente.php?matricola=<?php echo $attivi_e_inattivi['matricola']; ?>" class="btn btn-warning">Carriera Valida</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php
}
?>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->