<?php
include("menu.php");

// Riportiamo le due password del form precedente
$nuova_password = $_POST['nuova_password'];
$nuova_password_conferma = $_POST['nuova_password_conferma'];


// Mandiamo avanti le operazioni solo se le password coincidono
if ($nuova_password == $nuova_password_conferma) {

$username = $_SESSION["username"];
if ($_SESSION["ruolo"] == "studente") {
          $sql= "UPDATE studente SET password = $1 WHERE username = $2"; 
        }
    else if ($_SESSION["ruolo"] == "segreteria") {
          $sql= "UPDATE segreteria SET password = $1 WHERE username = $2"; 
        }
    else if ($_SESSION["ruolo"] == "docente") {
          $sql= "UPDATE docente SET password = $1 WHERE username = $2"; 
        } 
$params = array(
	md5($nuova_password_conferma),
    $username
);

$result = pg_query_params($db, $sql, $params);
if ($result) {
	$_SESSION['messaggio_password_modificata'] = 'Password modificata con successo';
} else {
	$_SESSION['messaggio_password_modificata'] = 'Password non modificata';
}

} 
else 
	{ $_SESSION['messaggio_password_modificata'] = 'Le due Password non coincidono'; }
// echo "pg_num_rows(result)";

header('Location: cambio_password.php');
?>