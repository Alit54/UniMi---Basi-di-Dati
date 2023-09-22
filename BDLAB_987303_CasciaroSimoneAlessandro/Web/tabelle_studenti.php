<?php

$matricola = $_SESSION['matricola'];

echo "<br> <br>
      <B>Dati Utente " . $_SESSION['nome'] . ' ' . $_SESSION['cognome'] . "</B> <br> <br>";

$dati = "SELECT * FROM get_data($1)";
$params = array($matricola);
$results = pg_query_params($db, $dati, $params);

$cfu_avg = pg_fetch_assoc($results);

$cfu = $cfu_avg['cfu'] ? $cfu_avg['cfu'] : "0";
$avg = $cfu_avg['average'] ? number_format($cfu_avg['average'], 2) : "0";
?>

<table class="styled-table" width="100%">
    <tr>
        <td> Numero CFU </td>
        <td> Media </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
    	<td> <?php echo $cfu; ?> </td>
    	<td> <?php echo $avg; ?> </td>
        <td> <a href="carriera_studente.php?matricola=<?php echo $matricola; ?>" class="btn btn-info">Carriera</a> </td>
        <td> <a href="carriera_valida_studente.php?matricola=<?php echo $matricola; ?>" class="btn btn-warning">Carriera Valida</a> </td>
    </tr>
</table>
<br><br>