<?php
session_start();
// se l'utente non loggato digita questa pagina nell url viene rimandato al login
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    die();
}
// salvo l user in una variabile
$user = $_SESSION['user'];
// mi connetto al db per prendere le categorie e mostrarle come opzione nel form
require_once __DIR__ . '/../utilities/db_conn.php';
$result = $conn->query('SELECT * FROM categories');
$categories = $result->fetch_all(MYSQLI_ASSOC);
// entriamo nel caso che il form sia stato inviato
if (isset($_POST['submit'])) {
    // sanifico valori ricevuti
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $category_id = htmlspecialchars($_POST['category_id']);
    $img = $_FILES['image'] ?? null;
    // prepare and bind statement
    // se passo un file ed è un immagine
    if ($img && str_contains($img['type'], 'image/')) {
        // carico immagine nei miei uploads
        $img_name_db = rand(1, 100000) . $img['name'];
        move_uploaded_file($img['tmp_name'], '../uploads/' . $img_name_db);
        $stmt = $conn->prepare('INSERT INTO posts  (title, content, user_id, category_id, image) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('ssiis', $title_q, $content_q, $user_id_q, $category_id_q, $image_name);
    }
    // se non passo nessun file
    else {
        $stmt = $conn->prepare('INSERT INTO posts  (title, content, user_id, category_id) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssii', $title_q, $content_q, $user_id_q, $category_id_q);
    }
    $title_q = $title;
    $content_q = $content;
    $category_id_q = $category_id;
    $user_id_q = $user['id'];
    $image_name = $img_name_db;
    // eseguo lo statement
    $stmt->execute();
    // chiudo lo statement e la connesione al db
    $stmt->close();
    $conn->close();
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
    <main class="flex-grow-1 overflow-auto h-25 bg-body-secondary">
        <div class="container py-5">
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="bg-white px-5 py-4" enctype="multipart/form-data">
                <h2>Create new post</h2>
                <!-- TITOLO -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <!-- /TITOLO -->
                <!-- CONTENUTO -->
                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="8" required></textarea>
                </div>
                <!-- /CONTENUTO -->
                <!-- CATEGORY -->
                <label for="category" class="mb-2 fw-bold">Choose a category:</label>
                <select class="form-select mb-3" id="category" name="category_id">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category['id'] ?>">
                            <?php echo $category['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <!-- /CATEGORY -->
                <!-- UPLOAD IMMAGINE -->
                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Add an image:</label>
                    <div class="d-flex">
                        <input class="form-control rounded-end-0" type="file" id="image" name="image">
                        <div class="border border-start-0 rounded-end-2 d-flex justify-content-center align-items-center px-3 bg-body-secondary" role="button" id="empty-input"><i class="fa-solid fa-xmark fs-4"></i></div>
                    </div>
                </div>
                <!-- /UPLOAD IMMAGINE -->
                <!-- PREVIEW IMMAGINE -->
                <h6 class="fw-bold">Image preview:</h6>
                <div class="border border-2 rounded-2 mb-4 overflow-hidden" style="width: 15%; aspect-ratio: 1/1;" id="img-preview">
                    <div class="h-100 d-flex justify-content-center align-items-center" id="icon-preview">
                        <i class="fa-solid fa-image fs-1"></i>
                    </div>
                    <img src="" alt="" id="preview" class="w-100 object-fit-cover h-100 d-none">
                </div>
                <!-- /PREVIEW IMMAGINE -->
                <!-- BUTTON INVIO -->
                <button class="btn btn-success" name="submit" value="1">Create post</button>
                <!-- /BUTTON INVIO -->
            </form>
        </div>
    </main>
    <!-- JS PREVIEW IMMAGINE CARICARE -->
    <script src="../js/previews.js"></script>
    <!-- /JS PREVIEW IMMAGINE CARICARE -->
</body>

</html>