<?php
include_once("area_riservata_segreteria.php");


echo "<br> <br>
      <B>Lista e Gestione Insegnamenti</B> <br> <br>";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = $search ? "WHERE nome ILIKE '%$search%'" : '';
$lista_insegnamento = "SELECT * FROM insegnamento $search_condition ORDER BY responsabile IS NULL DESC, nome ASC";
$results = pg_query($db, $lista_insegnamento);
?>

<table class="styled-table" width="100%">
    <tr>
        <td Style="text-align: left;">
            <div class="search-box">
                <form action="" method="GET">
                    <label for="search">Cerca:</label>
                    <input type="text" id="search" name="search" placeholder="Inserire nome" value="<?php echo $search; ?>">
                    <button type="submit">Cerca</button>
                    <?php
                    if (!empty($_GET['search'])) {
                        echo '<a href="lista_e_gestione_insegnamenti.php" class="btn btn-primary">Indietro</a>';
                    }
                    ?>
                </form>
            </div>
        </td>
        <td align="center">
            <a href="aggiungi_nuovo_insegnamento.php" class="btn btn-success">Aggiungi Nuovo Insegnamento</a>
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
            <th>Nome</th>
            <th>Responsabile</th>
            <th>Corso</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($results)){ ?>
        <tr <?php if (is_null($row['responsabile'])) { ?> style="background-color:#eba3a3;" <?php } ?>>
            <td><?php echo $row['nome']; ?></td>
            <td><?php echo $row['responsabile']; ?></td>
            <td><?php echo $row['corso']; ?></td>
            <td>
                <a href="modifica_insegnamento.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Modifica</a>
                <a href="elimina_insegnamento.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Elimina</a>
                <a href="lista_e_gestione_propedeuticita.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Propedeuticità</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?> 
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->