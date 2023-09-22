<?php

session_start();
$matricola_get = $_GET['matricola'];
$matricola_session = $_SESSION['matricola'];


if ($_SESSION['ruolo'] == "segreteria") { include_once("area_riservata_segreteria.php"); }
if ($_SESSION['ruolo'] == "studente") { 

    include_once("area_riservata_studenti.php"); 

    // Uno studente può visualizzare solo la sua carriera
    if($matricola_get != $matricola_session) {
     
        die("Accesso negato");
    }

}

$dati_studente = "SELECT * FROM get_carriera($1)";
$params = array($matricola_get);
$result_carriera_studente = pg_query_params($db, $dati_studente, $params);
?>

<?php
if (pg_num_rows($result_carriera_studente) === 0) {
    echo "<p>Nessun risultato trovato</p>";
} else { ?>
<table class="styled-table" width="100%">
    <thead>
        <tr>
            <th>Insegnamento</th>
            <th>Voto</th>
            <th>Data</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        while ($row_carriera_studente = pg_fetch_assoc($result_carriera_studente)){
            $lode = $row_carriera_studente["lode"] == 't' ? "L" : "";
        ?>
        <tr>
            <td><?php echo $row_carriera_studente['nome']; ?></td>
            <td><?php echo $row_carriera_studente['voto'] . $lode; ?></td>
            <td><?php echo date("d/m/Y", strtotime($row_carriera_studente['data'])); ?></td>
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