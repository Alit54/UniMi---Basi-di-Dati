<?php
include_once("area_riservata_segreteria.php");

echo "<br> <br>
      <B>Lista e Gestione Docenti</B> <br> <br>";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = $search ? "WHERE nome ILIKE '%$search%' OR cognome ILIKE '%$search%'" : '';
$lista_docenti = "SELECT * FROM docente $search_condition ORDER BY cognome, nome ASC";
$results = pg_query($db, $lista_docenti);
?>

<table class="styled-table" width="100%">
    <tr>
        <td Style="text-align: left;">
            <div class="search-box">
                <form action="" method="GET">
                    <label for="search">Cerca:</label>
                    <input type="text" id="search" name="search" placeholder="Inserire nome o cognome" value="<?php echo $search; ?>">
                    <button type="submit">Cerca</button>
                    <?php
                    if (!empty($_GET['search'])) {
                        echo '<a href="lista_e_gestione_docente.php" class="btn btn-primary">Indietro</a>';
                    }
                    ?>
                </form>
            </div>
        </td>
        <td align="center">
            <a href="aggiungi_nuovo_docente.php" class="btn btn-success">Aggiungi Nuovo Docente</a>
        </td>
    </tr>
</table>

<?php
if (pg_num_rows($results) === 0) {
    if ($search) {
        echo "<p>Nessun risultato trovato per la ricerca: '$search'</p>";
    } else {
        echo "<p>Nessun risultato trovato</p>";   
    }
} else { 
    ?>
<table class="styled-table" width="100%">
    <thead>
        <tr>
            <th>Cognome</th>
            <th>Nome</th>
            <th>e-Mail</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($results)){ ?>
        <tr>
            <td><?php echo $row['cognome']; ?></td>
            <td><?php echo $row['nome']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td>
                <a href="modifica_docente.php?user=<?php echo $row['username']; ?>" class="btn btn-primary">Modifica</a>
                <a href="elimina_docente.php?user=<?php echo $row['username']; ?>" class="btn btn-danger">Elimina</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->