<?php
session_start();
$user = $_SESSION['user'] ?? null;
// se l'utente è già loggato lo rimando alla dashboard di gestione dei post
if ($user) {
    header("Location: ./post/index.php");
    die();
}

function handlingUser($string)
{
    // array dove salverò eventuali messaggi d'errore
    $errorMessages = [];
    // controllo che l'input sia almeno 5 caratteri
    if (strlen($string) < 5) {
        // pusho primo messaggio d errore
        array_push($errorMessages, "Your username should be 5 characters minimum");
    }
    // controllo che l'input non contenga caratteri speciali
    if (!ctype_alnum($string)) {
        // pusho secondo messaggio d errore
        array_push($errorMessages, "Your username should contain only letters or numbers");
    }
    // controllo se questo username è già presente nel db
    // connessione al db
    require __DIR__ . '/utilities/db_conn.php';
    // prep and bind per prendere l'eventuale utente
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $username = $string;
    // eseguo lo statement
    $stmt->execute();
    // controllo se esiste l username
    $result = $stmt->get_result();
    if ($result->num_rows  > 0) {
        // esiste già questo username, restituisco messaggio d errore
        array_push($errorMessages, "Your username already exists, pick another one");
    }
    $stmt->close();
    $conn->close();
    // se l'user supera i test avrò array vuoto !!!
    return $errorMessages;
}

function handlingPassword($string)
{
    // array dove salverò eventuali messaggi d'errore
    $errorMessages = [];
    // controllo che l'input sia almeno 6 caratteri
    if (strlen($string) < 6) {
        // pusho primo messaggio d errore
        array_push($errorMessages, "Your password should be 6 characters minimum");
    }
    // controllo che l'input non contenga caratteri speciali
    if (!ctype_alnum($string)) {
        // pusho secondo messaggio d errore
        array_push($errorMessages, "Your password should contain only letters or numbers");
    }
    // se la password supera i test avrò array vuoto !!!
    return $errorMessages;
}

function registeredYesNo($username, $password)
{
    $errors = [];
    // eventuali errori connessi all'username
    $errorsUser = handlingUser($username);
    array_push($errors, $errorsUser);
    // eventuali errori connessi alla password
    $errorsPassword = handlingPassword($password);
    array_push($errors, $errorsPassword);
    // controllo se ci son stati errori in generale
    $anyErrors = false;
    foreach ($errors as $error) {
        if (count($error) > 0) {
            $anyErrors = true;
        }
    }
    // se non ho errori procedo alla registrazione
    if (!$anyErrors) {
        // connessione al db
        require __DIR__ . '/utilities/db_conn.php';
        // prep and bind per inserire l utente in tabella
        $stmt = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->bind_param('ss', $user_name, $psw);
        // hasho password
        $psw = password_hash($password, PASSWORD_DEFAULT);
        $user_name = $username;
        // eseguo lo statement
        $stmt->execute();
        $stmt->close();
        // prendo l user appena registrato dal db per salvarlo in sessione
        $user = $conn->query("SELECT * FROM users WHERE username = '$username'")->fetch_assoc();
        $_SESSION['user'] = $user;
        // setto il numero di post in sessione a zero per la questione messaggi di cancellazione o pubblicazione post
        $_SESSION['posts_num'] = 0;
        $conn->close();
        header("Location: ./post/index.php");
        die();
    }
    // altrimenti restituisco gli errori che poi stamperò in pagina
    return $errors;
}
// nel caso siano stati inviati password e username
if (isset($_POST['user']) && isset($_POST['psw'])) {
    // salvo i dati in due variabili
    $username = htmlspecialchars(trim($_POST['user']));
    $psw = htmlspecialchars(trim($_POST['psw']));
    // eseguo la funzione che gestice la registrazione
    // se ho successo verrò rendirizzato all area personale, altrimenti avrò messaggi d errore
    // questi messaggi li salvo in una variabile per stamparli in pagina per l utente
    $errorsUser = registeredYesNo($username, $psw)[0];
    $errorsPassword = registeredYesNo($username, $psw)[1];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once __DIR__ . '/partials/boos_font.php' ?>
    <title>Register</title>
</head>

<body class="vh-100 d-flex flex-column">
    <?php require_once __DIR__ . '/partials/public_header.php' ?>
    <main class="flex-grow-1 d-flex bg-body-secondary">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="m-auto border border-2 border-primary p-4 rounded-4 bg-white">
            <h4 class="text-primary mb-3">Join our blog today!</h4>
            <div class="mb-2">
                <label for="user" class="form-label fw-bold">Choose your username:</label>
                <input type="text" class="form-control" id="user" name="user" value="<?php echo $username ?? '' ?>" required>
            </div>
            <?php if (isset($errorsUser)) : ?>
                <ul class=" list-unstyled mb-1">
                    <?php foreach ($errorsUser as $error) : ?>
                        <li style="font-size: 14px;" class="text-danger fw-bold"><?php echo $error ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div class="mb-1">
                <label for="psw" class="form-label fw-bold">Choose your password</label>
                <input type="password" class="form-control" id="psw" name="psw" value="<?php echo $psw ?? '' ?>" required>
            </div>
            <?php if (isset($errorsPassword)) : ?>
                <ul class=" list-unstyled mb-1">
                    <?php foreach ($errorsPassword as $error) : ?>
                        <li style="font-size: 14px;" class="text-danger fw-bold"><?php echo $error ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <p class=" mt-2">Already a member? <a href="login.php">Login in here!</a></p>
            <button class="btn btn-primary mx-auto d-block px-4">Register</button>
        </form>
    </main>
</body>

</html>