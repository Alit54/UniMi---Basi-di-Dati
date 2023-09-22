<?php
include_once("area_riservata_segreteria.php");

$id = $_GET['id'];
$dati_insegnamento = "SELECT nome FROM insegnamento WHERE id = $1";
$params = array($id);
$nome_insegnamento = pg_query_params($db, $dati_insegnamento, $params);
$nome = pg_fetch_assoc($nome_insegnamento);

echo "<br> <br>
      <B>Lista e Gestione Propedeuticità " . $nome["nome"] . "</B> <br> <br>";

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = $search ? "AND nome ILIKE '%$search%'" : '';
$lista_insegnamento = "SELECT p.requisito, i.nome
                        FROM propedeuticita p
                        INNER JOIN insegnamento i ON p.requisito = i.id 
                        WHERE insegnamento = $1 $search_condition
                        ORDER BY requisito ASC";

$results = pg_query_params($db, $lista_insegnamento, $params);
?>

<table class="styled-table" width="100%">
    <tr>
        <td Style="text-align: left;">
        </td>
        <td Style="text-align: right; padding-right: 150px;">
            <a href="aggiungi_nuova_propedeuticita.php?insegnamento=<?php echo $id; ?>" class="btn btn-success">Aggiungi Nuova Propedeuticità</a>
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
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($results)){ ?>
        <tr>
            <td><?php echo $row['nome']; ?></td>
            <td>
                <a href="elimina_propedeuticita.php?insegnamento=<?php echo $id; ?>&requisito=<?php echo $row['requisito']; ?>" class="btn btn-danger">Elimina</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?> 
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->