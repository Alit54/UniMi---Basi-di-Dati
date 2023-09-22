<?php
include_once("area_riservata_studenti.php");

$id = $_GET['id'];
$matricola = $_SESSION['matricola'];

$dati_insegnamento = "SELECT nome FROM insegnamento WHERE id = $1";
$params = array($id);
$nome_insegnamento = pg_query_params($db, $dati_insegnamento, $params);
$nome = pg_fetch_assoc($nome_insegnamento);

echo "<br> <br>
      <B>Lista Propedeuticità " . $nome["nome"] . "</B> <br> <br>";

$lista_insegnamento = "SELECT p.requisito, i.nome, c.voto, c.lode
                        FROM propedeuticita p
                        INNER JOIN insegnamento i ON p.requisito = i.id 
                        LEFT JOIN get_carriera_valida($2) c ON i.id = c.codice
                        WHERE insegnamento = $1
                        ORDER BY requisito ASC";

$params = array($id, $matricola);
$results = pg_query_params($db, $lista_insegnamento, $params);
?>

<?php
if (pg_num_rows($results) === 0) {
    echo "<p>Nessun risultato trovato</p>";
} else {     
?>

<table class="styled-table" width="100%">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Voto</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($results)){ ?>
        <tr <?php if ($row['voto']) { ?> style="background-color:#a3eba3" <?php } ?>>
            <td><?php echo $row['nome']; ?></td>
            <td><?php $lode = $row['lode'] == "t" ? "L" : ""; echo $row['voto'] . $lode; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?> 
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->