<?php
session_start();
// se l'utente non loggato digita questa pagina nell url viene rimandato al login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    die();
}
// salvo l user in una variabile
$user = $_SESSION['user'];
// mi connetto al db per prendere le categorie e mostrarle come opzione nel form
$host = 'localhost';
$db_user = 'root';
$db_psw = 'root';
$db_name = 'php_blog';
$conn = new mysqli($host, $db_user, $db_psw, $db_name);
$result = $conn->query('SELECT * FROM categories');
$categories = $result->fetch_all(MYSQLI_ASSOC);
// entriamo nel caso che il form sia stato inviato
if (isset($_POST['submit'])) {
    // sanifico valori ricevuti
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $category_id = htmlspecialchars($_POST['category_id']);
    // prepare and bind statement
    $stmt = $conn->prepare('INSERT INTO posts  (title, content, user_id, category_id) VALUES (?, ?, ?,?)');
    $stmt->bind_param('ssii', $title_q, $content_q, $user_id_q, $category_id_q);
    $title_q = $title;
    $content_q = $content;
    $category_id_q = $category_id;
    $user_id_q = $user['id'];
    // eseguo lo statement
    $stmt->execute();
    // rimando alla pagina admin coi post per veder quello nuovo creato
    header("Location: index.php");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once __DIR__ . '/../partials/boos_font.php' ?>
    <title>Create post</title>
</head>

<body class="vh-100 d-flex flex-column">
    <?php require_once __DIR__ . '/../partials/post_header.php' ?>
    <main class="flex-grow-1 d-flex overflow-auto h-25 bg-body-secondary">
        <div class="container py-3">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="bg-white px-5 py-4">
                <h2>Create new post</h2>
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Content</label>
                    <input type="text" class="form-control" id="content" name="content" required>
                </div>
                <label for="category" class="mb-2 fw-bold">Choose a category:</label>
                <select class="form-select mb-4" id="category" name="category_id">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category['id'] ?>">
                            <?php echo $category['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-success" name="submit" value="1">Create post</button>
            </form>
        </div>
    </main>
</body>

</html>