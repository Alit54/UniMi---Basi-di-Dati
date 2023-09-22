<?php
include_once("area_riservata_studenti.php");

$nome = $_GET['nome'];

$query_esami_prenotabili = "SELECT * 
							FROM get_info_corso($1)
                            ORDER BY anno, nome";
$params = array($nome);
$esami_prenotabili = pg_query_params($db, $query_esami_prenotabili, $params);
?>

<table class="styled-table" width="100%">
    <thead>
        <tr>
            <th>Esame</th>
            <th>Docente</th>
            <th>Anno Previsto</th>
            <th>Descrizione</th>
            <th>Azioni</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = pg_fetch_assoc($esami_prenotabili)){ ?>
        <tr>
            <td><?php echo $row['nome']; ?></td>
            <td><?php echo $row['responsabile']; ?></td>
            <td><?php echo $row['anno']; ?></td>
            <td><?php echo $row['descrizione']; ?></td>
            <td>
                <a href="visualizza_propedeuticita.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Visualizza Propedeuticità</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
    </div>
    </div>
  </div> <!-- questi tre div chiudono row, col-3 e col-9 che derivano da menu.php -->