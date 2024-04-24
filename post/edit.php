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
// quando arrivo dall dashboard avrò l id con get, quando submitto il form mi verrà passato l'id con pulsante d invio
$post_id = htmlspecialchars($_GET['id'] ?? $_POST['submit']);
// se l'id non è numerico rimando alla pagina coi post
if (!is_numeric($post_id)) {
    header("Location: index.php");
    die();
}

// mi connetto al db 
require_once __DIR__ . '/../utilities/db_conn.php';
// verifico che l id sia relativo ad un post dell user loggato
$result = $conn->query('SELECT posts.* , categories.name  FROM posts INNER JOIN categories ON categories.id = posts.category_id WHERE user_id = ' . $user['id'] . " AND posts.id = $post_id");
// se non ottengo risultati rimando alla dashboard coi posts
if ($result->num_rows === 0) {
    $conn->close();
    header("Location: index.php");
    die();
}
// se ho un risultato procedo a salvarmi il post in una variabile, cosi da poter mostrare i valori attuali che poi l utente potrà aggiornare
$post = $result->fetch_assoc();
// prendo tutte le categorie da mostrare nella select
$result = $conn->query('SELECT * FROM categories');
$categories = $result->fetch_all(MYSQLI_ASSOC);
// se l'utente ha aggiornato i dati partiamo a salvare le eventuali modifiche
if (isset($_POST['submit'])) {
    // sanifico valori ricevuti
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $category_id = htmlspecialchars($_POST['category_id']);
    // prepare and bind statement
    $stmt = $conn->prepare('UPDATE posts SET title = ?, content = ?,  category_id = ? WHERE id = ?');
    $stmt->bind_param('ssii', $title_q, $content_q, $category_id_q, $post_id_q);
    $title_q = $title;
    $content_q = $content;
    $category_id_q = $category_id;
    $post_id_q = $post_id;
    // eseguo lo statement
    $stmt->execute();
    // chiudo lo statement e la connesione al db
    $stmt->close();
    $conn->close();
    // rimando alla pagina dettaglio del post per vederne le modifiche
    header("Location: show.php?id=$post_id");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once __DIR__ . '/../partials/boos_font.php' ?>
    <title>Edit post</title>
</head>

<body class="vh-100 d-flex flex-column">
    <?php require_once __DIR__ . '/../partials/post_header.php' ?>
    <main class="flex-grow-1 overflow-auto h-25 bg-body-secondary">
        <div class="container py-5">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="bg-white px-5 py-4">
                <h2>Edit your post</h2>
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?php echo $title ?? $post['title'] ?>">
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="6" required><?php echo $content ?? $post['content'] ?>
                    </textarea>
                </div>
                <label for="category" class="mb-2 fw-bold">Choose a category:</label>
                <select class="form-select mb-4" id="category" name="category_id">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category['id'] ?>" <?php if ($category_id ?? $post['category_id'] === $category['id']) : ?> selected <?php endif; ?>>
                            <?php echo $category['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-success" name="submit" value="<?php echo $post['id'] ?>">Edit post</button>
            </form>
        </div>
    </main>
</body>

</html>