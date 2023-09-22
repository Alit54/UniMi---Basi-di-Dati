<?php
require("config.php");
include_once("functions.php");
ini_set("display_errors", "On");
ini_set("error_reporting", E_ALL);
session_start();
if (!isset($_SESSION["username"]) || $_SESSION["username"] == "" ){  
  redirect("./index.php");
}

$nome_utente = $_SESSION['username'];

$db = open_pg_connection();
$result = pg_query($db, 'SET SEARCH_PATH TO universita');
  ?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <title>Area Personale</title>
  <style>
    .profile-image {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 10px;
    }

    .styled-table td,
    .styled-table th {
        padding: 10px;
        text-align: center; 
    }

.flex-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
    margin-bottom: 10px;
}
  </style>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
</head>
<body>
<br>
<br>
  <div class="container-fluid">
    <div class="row">
      <div class="col-3">
        <div class="text-center">
          <?php
        if ($_SESSION["ruolo"] == "segreteria") { ?>
          <img src="immagini/Segreteria.jpg" alt="immagine profilo" class="profile-image">
          <h4><?php  echo $_SESSION["nome"] . ' ' . $_SESSION["cognome"] ?></h4>
          <p class="font-weight-light">
        <?php echo 'Area personale Segreteria';
        } else if ($_SESSION["ruolo"] == "studente") { ?>
          <img src="immagini/Studente.jpg" alt="immagine profilo" class="profile-image">
          <h4><?php  echo $_SESSION["nome"] . ' ' . $_SESSION["cognome"] ?></h4>
          <p class="font-weight-light"> 
        <?php echo 'Area personale Studente';
        } else if ($_SESSION["ruolo"] == "docente") { ?>
          <img src="immagini/Docente.jpg" alt="immagine profilo" class="profile-image">
          <h4><?php  echo $_SESSION["nome"] . ' ' . $_SESSION["cognome"] ?></h4>
          <p class="font-weight-light">
        <?php echo 'Area personale Docente';
        }
        ?>
        </p>
        </div>
        <div class="list-group">
    <?php
    if ($_SESSION["ruolo"] == "studente") {
          echo '<a href="area_riservata_studenti.php" class="list-group-item list-group-item-action"><i class="bi bi-house"></i> Home</a>'; 
          echo '<a href="esami_prenotabili.php" class="list-group-item list-group-item-action"><i class="bi bi-calendar"></i>  Esami Prenotabili</a>';
          echo '<a href="lista_corsi.php" class="list-group-item list-group-item-action"><i class="bi bi-journal-text"></i>  Lista Corsi di Laurea</a>';
        }
    else if ($_SESSION["ruolo"] == "segreteria") {
          echo '<a href="area_riservata_segreteria.php" class="list-group-item list-group-item-action"><i class="bi bi-house-door"></i> Home</a>'; 
          echo '<a href="lista_e_gestione_studenti.php" class="list-group-item list-group-item-action"><i class="bi bi-person-lines-fill"></i> Lista Studenti</a>';
          echo '<a href="lista_e_gestione_docente.php" class="list-group-item list-group-item-action"><i class="bi bi-person-badge-fill"></i> Lista Docenti</a>';

          if($_SESSION["username"] == "admin") {
            echo '<a href="lista_e_gestione_segretari.php" class="list-group-item list-group-item-action"><i class="bi bi-file-earmark-person-fill"></i></i> Lista Segretari</a>'; }

         echo '<a href="lista_e_gestione_insegnamenti.php" class="list-group-item list-group-item-action"><i class="bi bi-easel"></i></i> Lista Insegnamenti</a>';

          echo '<a href="lista_e_gestione_corsi.php" class="list-group-item list-group-item-action"><i class="bi bi-journal-text"></i> Lista Corsi</a>';
        }
    else if ($_SESSION["ruolo"] == "docente") {
          echo '<a href="area_riservata_docenti.php" class="list-group-item list-group-item-action"><i class="bi bi-house"></i> Home</a>';
           $query_esame = "SELECT * FROM insegnamento WHERE responsabile = $1 ORDER BY nome"; 
           $params = array($nome_utente);
           $result = pg_query_params($db, $query_esame, $params);
           while($row = pg_fetch_assoc($result)) {
            echo '<a href="esame.php' . '?insegnamento=' . $row["id"] . '" class="list-group-item list-group-item-action"><i class="bi bi-book"></i> ' . $row["nome"] . '</a>';
           }
        }  ?>
          <a href="cambio_password.php" class="list-group-item list-group-item-action"><i class="bi bi-key"></i>  Cambio Password</a>
          <a href="logout.php" class="list-group-item list-group-item-action"><i class="bi bi-box-arrow-right"></i>  Logout</a>
        </div>
      </div>
      <div class="col-9">
        <?php
        if ($_SESSION["ruolo"] == "segreteria") {
          include_once("tabelle_segreteria.php");
        } else if ($_SESSION["ruolo"] == "studente") {
          include_once("tabelle_studenti.php");
        } else if ($_SESSION["ruolo"] == "docente") {
          include_once("tabelle_docenti.php");
        }
        ?>
    

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>