<?php
include_once("area_riservata_studenti.php");

echo "<B>Lista e Gestione Corsi</B> <br> <br>";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = $search ? "WHERE nome ILIKE '%$search%'" : '';
$lista_corsi = "SELECT * FROM corso $search_condition ORDER BY nome ASC";
$results = pg_query($db, $lista_corsi);
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
                        echo '<a href="lista_corsi.php" class="btn btn-primary">Indietro</a>';
                    }
                    ?>
                </form>
            </div>
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
            <th>Tipologia</th>
            <th>Descrizione</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($results)){ ?>
        <tr>
            <td><?php echo $row['nome']; ?></td>
            <td><?php echo $row['durata'] == 2 ? "Magistrale" : "Triennale"; ?></td>
            <td><?php echo $row['descrizione']; ?></td>
            <td>
                <a href="info_corso.php?nome=<?php echo $row['nome']; ?>" class="btn btn-primary">Info</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->