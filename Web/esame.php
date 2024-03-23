<?php
include_once("area_riservata_docenti.php");

$insegnamento = $_GET['insegnamento'];


$nome_appello = "SELECT nome FROM insegnamento WHERE id = $1";
$param = array($insegnamento);
$result = pg_query_params($db, $nome_appello, $param);
$row = pg_fetch_assoc($result);

echo "<br> <br>
      <B>Lista e Gestione Appelli " . $row['nome'] . "</B> <br> <br>";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = $search ? "AND data::date = '$search'" : '';
$lista_appelli = "SELECT * FROM appello WHERE insegnamento = $1 $search_condition ORDER BY data DESC";
$params = array($insegnamento);
$results = pg_query_params($db, $lista_appelli, $params);
?>

<table class="styled-table" width="100%">
    <tr>
        <td Style="text-align: left;">
            <div class="search-box">
                <form action="" method="GET">
                    <input type="hidden" name="insegnamento" value="<?php echo $insegnamento ?>">
                    <label for="search">Cerca:</label>
                    <input type="date" id="search" name="search" placeholder="Inserire la data" value="<?php echo $search ?>">
                    <button type="submit">Cerca</button>
                    <?php
                    if (!empty($search)) {
                        echo '<a href="esame.php?insegnamento='. $insegnamento . '" class="btn btn-primary">Indietro</a>';
                    }
                    ?>
                </form>
            </div>
        </td>
        <td align="center">
            <a href="aggiungi_nuovo_appello.php<?php echo "?insegnamento=" . $insegnamento; ?>" class="btn btn-success">Aggiungi Nuovo Appello</a>
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
            <th>Data</th>
            <th>Luogo</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($results)){ ?>
        <tr>
            <td><?php echo date("d/m/Y H:i", strtotime($row['data'])); ?></td>
            <td><?php echo $row['luogo']; ?></td>
            <td>
                <a href="modifica_appello.php?insegnamento=<?php echo $row['insegnamento'] . "&data=" . $row['data']; ?>" class="btn btn-primary">Modifica</a>
                <a href="elimina_appello.php?insegnamento=<?php echo $row['insegnamento'] . "&data=" . $row['data']; ?>" class="btn btn-danger">Elimina</a>
                <a href="lista_studenti_iscritti_appello.php?insegnamento=<?php echo $row['insegnamento'] . "&data=" . $row['data']; ?>" class="btn btn-warning">Lista Studenti</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->