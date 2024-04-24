<?php
// controllo se sono stati inviati user e password
if (isset($_POST['user']) && isset($_POST['psw'])) {
    $user = htmlspecialchars($_POST['user']);
    $psw = htmlspecialchars($_POST['psw']);
    // connessione al db
    $host = 'localhost';
    $db_user = 'root';
    $db_psw = 'root';
    $db_name = 'php_blog';
    $conn = new mysqli($host, $db_user, $db_psw, $db_name);

    // prep and bind per prendere l'eventuale utente
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $username = $user;
    // eseguo lo statement
    $stmt->execute();
    // prendo i risultati 
    $result = $stmt->get_result();
    // caso utente esiste
    if ($result->num_rows > 0) {
        // salvo la row dell'utente come un array associativo
        $user_db = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        if (password_verify($psw, $user_db['password'])) {
            header("Location: ./post/index.php");
            die();
        }
    }
    // caso utente non esiste o password sbagliata, mostro messaggio d'errore
    $error_msg = true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once __DIR__ . '/partials/boos_font.php' ?>
    <title>Login</title>
</head>

<body class="vh-100 d-flex flex-column">
    <header class="bg-primary text-center py-2">
        <h1>PHP MY BLOG - LOGIN</h1>
    </header>
    <main class="flex-grow-1 d-flex bg-body-secondary">
        <form action="login.php" method="POST" class="m-auto border border-2 border-primary p-4 rounded-4 bg-white">
            <div class="mb-3">
                <label for="user" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control" id="user" name="user" required value="<?php echo $user ?? '' ?>">
            </div>
            <div class="mb-4">
                <label for="psw" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" id="psw" name="psw" required value="<?php echo $psw ?? '' ?>">
            </div>
            <?php if ($error_msg ?? false) : ?>
                <p class="text-danger mb-4">User or password not found</p>
            <?php endif; ?>
            <button class="btn btn-primary mx-auto d-block px-4">Login</button>
        </form>
    </main>
</body>

</html>