<?php
session_start();
// se l'utente non loggato digita questa pagina nell url viene rimandato al login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    die();
}
// salvo l user in una variabile
$user = $_SESSION['user'];
// salvo la nuova categoria in una variabile dopo averla sanitizzata e tolto gli spazi bianchi
$name = trim($_POST['name']);
// se la stringa è vuota non la considero valida
if (strlen($name) < 1) {
    header("Location: ../post/index.php");
    die();
}
// se è valida procedo al salvataggio
// connessione al db
require_once __DIR__ . '/../utilities/db_conn.php';
$conn->query("INSERT INTO categories (name) VALUES ('$name')");
$conn->close();
header("Location: ../post/index.php");
die();
