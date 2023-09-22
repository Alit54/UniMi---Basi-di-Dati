<?php

include_once("area_riservata_studenti.php");

$matricola = $_SESSION['matricola'];

$query_esami_prenotabili = "SELECT * 
							FROM get_carriera_valida($1) c 
                            RIGHT JOIN insegnamento i ON i.id = c.codice
                            WHERE corso = (
								SELECT corso
								FROM studente
								WHERE matricola = $1
							)
                            ORDER BY i.anno, i.nome";
$params = array($matricola);
$esami_prenotabili = pg_query_params($db, $query_esami_prenotabili, $params);
?>

<table class="styled-table" width="100%">
    <thead>
        <tr>
            <th>Esame</th>
            <th>Docente</th>
            <th>Anno Previsto</th>
            <th>Voto</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($esami_prenotabili)){ ?>
        <tr <?php if ($row['voto']) { 
            ?> style="background-color:#a3eba3;" <?php } ?> >
            <td><?php echo $row['nome']; ?></td>
            <td><?php echo $row['responsabile']; ?></td>
            <td><?php echo $row['anno']; ?></td>
            <td><?php $lode = $row["lode"] == 't' ? "L" : "";
                echo $row['voto'] . $lode; ?></td>
            <td>
                <a href="visualizza_appelli.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Visualizza Appelli</a>
                <a href="visualizza_propedeuticita.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Visualizza Propedeuticità</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->