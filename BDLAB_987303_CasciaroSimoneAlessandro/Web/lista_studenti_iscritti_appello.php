<?php
include_once("area_riservata_docenti.php");

$insegnamento = $_GET['insegnamento'];
$data = $_GET['data'];
$username_session = $_SESSION['username'];

echo $_SESSION['Comunicazioni_D'] . "<br>";
$_SESSION['Comunicazioni_D'] = '';

// Query che restituisce il responsabile dell'insegnamento
$query_responsabile_insegnamento = "SELECT responsabile FROM insegnamento WHERE id = $1";
$params = array($insegnamento);
$responsabile_insegnamento = pg_query_params($db, $query_responsabile_insegnamento, $params);
$nome_responsabile = pg_fetch_assoc($responsabile_insegnamento);
$controllo_responsabile = $nome_responsabile['responsabile'];

// Mostra il risultato solo se il docente loggato è lo stesso della query precedente
if ($controllo_responsabile == $username_session) {


echo "<B>Lista Studenti</B> <br> <br>";

$lista_studenti = "SELECT matricola, cognome, nome, voto, lode
                FROM esame e
                INNER JOIN studente s ON e.studente = s.matricola
                WHERE insegnamento = $1 AND data = $2
                UNION
                SELECT matricola, cognome, nome, voto, lode
                FROM esame_storico ee
                INNER JOIN studente_storico ss ON ee.studente = ss.matricola
                WHERE insegnamento = $1 AND data = $2
                ORDER BY cognome, nome ASC";

$params = array($insegnamento, $data);
$results = pg_query_params($db, $lista_studenti, $params);


if (pg_num_rows($results) === 0) {
        echo "<p>Nessun risultato trovato</p>";   
} else {
    ?>
<table class="styled-table" width="100%">
    <thead>
        <tr>
            <th>Matricola</th>
            <th>Cognome</th>
            <th>Nome</th>
            <th>Voto</th>
            <th>Lode</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($results)){ ?>
        <tr>
            <td><?php echo $row['matricola']; ?></td>
            <td><?php echo $row['cognome']; ?></td>
            <td><?php echo $row['nome']; ?></td>
            <?php if($row['voto']) { ?>
                <td><?php echo $row['voto']; ?></td>
                <td><?php $lode = $row['lode'] == 't' ? 'Lode' : ''; echo $lode; ?></td>
            <?php } else { ?>
            <form action="conferma_inserimento_voto.php" method="POST">
            <td><input type="number" min="0" max="30" id="voto" name="voto" value="<?php echo $row['voto']; ?>">
                <input hidden="" type="text" id="insegnamento" name="insegnamento" value="<?php echo $insegnamento; ?>">
                <input hidden="" type="text" id="matricola" name="matricola" value="<?php echo $row['matricola']; ?>">
                <input hidden="" type="text" id="data" name="data" value="<?php echo $data; ?>"></td>
            <td><select id="lode" name="lode">
                <?php if ($row['lode'] == "t") { ?>
                 <option id="lode" name="lode" value="1">Lode</option>
                 <option id="lode" name="lode" value="0">Non Lode</option>
                 <?php } else { ?>
                 <option id="lode" name="lode" value="0">Non Lode</option>
                 <option id="lode" name="lode" value="1">Lode</option>
                 <?php } ?> 
        </td>
            <td>
                <button type="submit" class="btn btn-primary">Conferma</button>
            </td>
        <?php } ?>
        </tr>
    </form>
        <?php } ?>
    </tbody>
</table>
<?php }} else {  echo 'Accesso negato'; } ?>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->