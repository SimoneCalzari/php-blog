<?php
session_start();
// se l'utente non loggato digita questa pagina nell url viene rimandato al login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    die();
}
// salvo l user in una variabile
$user = $_SESSION['user'];
// sanifico il dato ricevuto dell'id del post
$post_id = htmlspecialchars($_POST['id']);
// se l'id non è numerico rimando alla pagina coi post
if (!is_numeric($post_id)) {
    header("Location: index.php");
    die();
}
// mi connetto al db 
require_once __DIR__ . '/../utilities/db_conn.php';
// verifico che l id sia relativo ad un post dell user loggato
$result = $conn->query('SELECT * FROM posts WHERE user_id = ' . $user['id'] . " AND id = $post_id");
// se non ottengo risultati rimando alla dashboard coi posts
if ($result->num_rows === 0) {
    $conn->close();
    header("Location: index.php");
    die();
}
// il post esiste ed è dell utente loggato allora procedo alla cancellazione
$conn->query("DELETE FROM posts WHERE id = $post_id");
// chiudo la connesione al db e rimando alla pagina coi post
$conn->close();
header("Location: index.php");
die();
