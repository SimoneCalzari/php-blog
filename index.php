<?php
session_start();
// se l utente è loggato lo salvo in una variabile, altrimenti questa sarà nulla
$user = $_SESSION['user']  ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once __DIR__ . '/partials/boos_font.php' ?>
    <title>PHP MY BLOG</title>
</head>

<body class="vh-100 d-flex flex-column">
    <?php require_once __DIR__ . '/partials/public_header.php' ?>
    <!-- JS MENU -->
    <script src="js/menu.js"></script>
    <!-- /JS MENU -->
</body>

</html>