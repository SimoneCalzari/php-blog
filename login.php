<?php
session_start();
$user = $_SESSION['user'] ?? null;
// se l'utente è già loggato lo rimando alla dashboard di gestione dei post
if ($user) {
    header("Location: ./post/index.php");
    die();
}
// controllo se sono stati inviati user e password
if (isset($_POST['user']) && isset($_POST['psw'])) {
    $user_name = htmlspecialchars($_POST['user']);
    $psw = htmlspecialchars($_POST['psw']);
    // connessione al db
    require_once __DIR__ . '/utilities/db_conn.php';

    // prep and bind per prendere l'eventuale utente
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $username = $user_name;
    // eseguo lo statement
    $stmt->execute();
    // prendo i risultati 
    $result = $stmt->get_result();
    // caso utente esiste
    if ($result->num_rows > 0) {
        // salvo la row dell'utente come un array associativo
        $user_db = $result->fetch_assoc();
        $stmt->close();

        if (password_verify($psw, $user_db['password'])) {
            // salvo l utente in sessione
            $_SESSION['user'] = $user_db;
            // salvo il suo numero di post per avere un riferimento nel caso ne aggiunga o tolta altri
            $result = $conn->query('SELECT * FROM posts WHERE user_id = ' . $user_db['id']);
            $_SESSION['posts_num'] = $result->num_rows;
            $conn->close();
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
    <?php require_once __DIR__ . '/partials/public_header.php' ?>
    <main class="flex-grow-1 d-flex bg-body-secondary">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="m-auto border border-2 border-primary p-4 rounded-4 bg-white">
            <h4 class="text-primary mb-3">Log into your account!</h4>
            <!-- USERNAME -->
            <div class="mb-2">
                <label for="user" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control" id="user" name="user" required value="<?php echo $user_name ?? '' ?>">
            </div>
            <!-- /USERNAME -->
            <!-- PASSWORD -->
            <div class="mb-1">
                <label for="psw" class="form-label fw-bold">Password</label>
                <div class="position-relative">
                    <input type="password" class="form-control" id="psw" name="psw" required value="<?php echo $psw ?? '' ?>">
                    <!-- MOSTRA E NASCONDI PSW -->
                    <i class="fa-solid fa-eye-slash text-primary position-absolute fs-5" style="top: 30%; right: 5%;" role="button"></i>
                    <i class="fa-solid fa-eye text-primary position-absolute fs-5 d-none" style="top: 30%; right: 5%;" role="button"></i>
                    <!-- /MOSTRA E NASCONDI PSW -->
                </div>
            </div>
            <!-- /PASSWORD -->
            <!-- MESSAGGI D ERRORE -->
            <?php if ($error_msg ?? false) : ?>
                <p style="font-size: 14px;" class="text-danger fw-bold mb-1">User or password not valid</p>
            <?php endif; ?>
            <!-- /MESSAGGI D ERRORE -->
            <!-- BUTTON E VAI ALLA REGISTRAZIONE -->
            <p class="mt-2">No account yet? <a href="register.php">Create one now!</a></p>
            <button class="btn btn-primary mx-auto d-block px-4">Login</button>
            <!-- /BUTTON E VAI ALLA REGISTRAZIONE -->
        </form>
    </main>
    <!-- OCCHIOLINO MOSTRA E NASCONID PSW -->
    <script src="js/seePassword.js"></script>
    <!-- /OCCHIOLINO MOSTRA E NASCONID PSW -->
</body>

</html>