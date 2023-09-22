<?php
//ini_set("display_errors", "On");
//ini_set("error_reporting", E_ALL);

// Startiamo la SESSION (memoria cache del browser), ci servirà dopo per memorizzare username e ruolo
session_start();

include("functions.php");

$db = open_pg_connection();

// A connessione stabilita posso verificare le credenziali immesse nel form di Login
$input_username = $_POST['username'];
$input_password = $_POST['password'];
$input_ruolo = $_POST['ruolo'];

$result = pg_query($db, 'SET SEARCH_PATH TO universita');

$sql = "SELECT * FROM $input_ruolo WHERE username = $1 AND password = $2";
$params = array(
    $input_username,
    md5($input_password)
);

$result = pg_prepare($db, "login", $sql);
$result = pg_execute($db, "login", $params);

$_SESSION['username'] = $input_username;
$_SESSION['password'] = $input_password;

if (pg_num_rows($result) == 1) {

    $row = pg_fetch_assoc($result);

    // Memorizzo l'username e il ruolo nella sessione
    $_SESSION["username"] = $row["username"];
    $_SESSION["nome"] = $row["nome"];
    $_SESSION["cognome"] = $row["cognome"];
    $_SESSION["ruolo"] = $input_ruolo;
    $_SESSION["messaggio_password_modificata"] = "";

    // Controllo il ruolo per reindirizzare all'area riservata corrispondente
    
    //Credenziali corrette, reindirizza all'area riservata specifica
    if ($_SESSION["ruolo"] == "docente") {
        $_SESSION["Comunicazioni_D"] = "";
        header("Location: area_riservata_docenti.php");
    } else if ($_SESSION["ruolo"] == "studente") {
        $_SESSION["Comunicazioni_St"] = "";
        $_SESSION["matricola"] = $row["matricola"];
        header("Location: area_riservata_studenti.php");
    }
    else if ($_SESSION["ruolo"] == "segreteria") {
        $_SESSION["Comunicazioni_Sg"] = "";
        header("Location: area_riservata_segreteria.php");
    }

    
} else {
    // Credenziali errate, reindirizza nuovamente alla pagina di login con un messaggio di errore
    header("Location: index-relog.php");
}
?>
