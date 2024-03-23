<?php
include_once("area_riservata_studenti.php");

$matricola = $_SESSION['matricola'];
$insegnamento = $_GET['id'];

// Query per selezionare il corso dello studente
$query_corso_studente = "SELECT corso
                         FROM studente
                         WHERE matricola = $1";
$params = array($matricola);
$query_corso = pg_query_params($db, $query_corso_studente, $params);
$query_corso_estratto = pg_fetch_assoc($query_corso);
$risultato_query_corso_studente = $query_corso_estratto['corso'];

// Query per selezionare il corso dell'insegnamento
$query_corso_insegnamento = "SELECT corso
                         FROM insegnamento
                         WHERE id = $1";
$params = array($insegnamento);
$query_insegnamento = pg_query_params($db, $query_corso_insegnamento, $params);
$query_insegnamento_estratto = pg_fetch_assoc($query_insegnamento);
$risultato_query_insegnamento = $query_insegnamento_estratto['corso'];

// Visualizza la tabella solo se le due Query precedenti restituiscono lo stesso corso
if ($risultato_query_insegnamento == $risultato_query_corso_studente){

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = $search ? "AND data::date = '$search'" : '';
$nome_appello = "SELECT *
                FROM appello a
                INNER JOIN insegnamento i ON a.insegnamento = i.id
                WHERE a.insegnamento = $1 $search_condition 
                ORDER BY data DESC";
$param = array($insegnamento);
$result_appello = pg_query_params($db, $nome_appello, $param);

// Query per ottenere il nome dell'insegnamento a cui l'appello fa riferimento
$nome_insegnamento = "SELECT nome
        FROM insegnamento
        WHERE id = $1";
$params = array($insegnamento);
$result_nome_insegnamento = pg_query_params($db, $nome_insegnamento, $params);
$row_nome_insegnamento = pg_fetch_assoc($result_nome_insegnamento);


echo $_SESSION['Comunicazioni_St'] . "<br>";
$_SESSION['Comunicazioni_St'] = "";

echo "
      <B>Visualizza Appelli " . $row_nome_insegnamento['nome'] . "</B> <br> <br>"; 

?>

<table class="styled-table" width="100%">
    <tr>
        <td Style="text-align: left;">
            <div class="search-box">
                <form action="" method="GET">
                    <input type="hidden" name="id" value="<?php echo $insegnamento ?>">
                    <label for="search">Cerca:</label>
                    <input type="date" id="search" name="search" placeholder="Inserire la data" value="<?php echo $search ?>">
                    <button type="submit">Cerca</button>
                    <?php
                    if (!empty($search)) {
                        echo '<a href="visualizza_appelli.php?id='. $insegnamento . '" class="btn btn-primary">Indietro</a>';
                    }
                    ?>
                </form>
            </div>
        </td>
    </tr>
</table>

<?php 
if (pg_num_rows($result_appello) === 0) {
    echo "<p>Nessun risultato trovato</p>";   
} else {  // tabella con gli utenti Attivi 

?>


<table class="styled-table" width="100%">
    <thead>
        <tr>
            <th>Data</th>
            <th>Luogo</th>
            <th>Voto</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row_appello = pg_fetch_assoc($result_appello)){ 
            $verifica_iscrizione_appello = "SELECT * FROM esame WHERE studente = $3 AND data = $2 AND insegnamento = $1";
            $param = array($insegnamento, $row_appello['data'], $matricola);
            $result = pg_query_params($db, $verifica_iscrizione_appello, $param);
            $appello = pg_fetch_assoc($result);
        ?>
        <tr>
            <td><?php echo date("d/m/Y H:i", strtotime($row_appello['data'])); ?></td>
            <td><?php echo $row_appello['luogo']; ?></td>
            <td>
<!-- Se lo studente è già iscritto all'esame, compare il tasto "Disiscriviti". Altrimenti "Iscriviti"-->
                <?php if (pg_num_rows($result) == 1) {
                    $lode = $appello["lode"] == 't' ? "L" : "";
                    echo $appello['voto'] . $lode;?>
                
            </td>
            <td>
                 <a href="disiscrizione_esame.php?insegnamento=<?php echo $insegnamento . "&data=" . $row_appello['data'] . "&matricola=" . $matricola; ?>" class="btn btn-danger">Disiscriviti</a> <?php } else { ?>
            </td>
            <td>
                <a href="iscrizione_esame.php?insegnamento=<?php echo $insegnamento . "&data=" . $row_appello['data'] . "&matricola=" . $matricola;
            ?>" class="btn btn-primary">Iscriviti</a>
            </td> <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php }}  else {  echo 'Accesso Negato'; } ?>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->