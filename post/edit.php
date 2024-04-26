<?php
session_start();
// se l'utente non loggato digita questa pagina nell url viene rimandato al login
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
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
    $img = $_FILES['image'] ?? null;
    // prepare and bind statement
    //  se passo un file ed è un immagine
    if ($img && str_contains($img['type'], 'image/')) {
        // se ho già un immagine cancello quella precedente
        if ($post['image']) {
            unlink('../uploads/' . $post['image']);
        }
        // carico immagine nei miei uploads
        $img_name_db = rand(1, 100000) . $img['name'];
        move_uploaded_file($img['tmp_name'], '../uploads/' . $img_name_db);
        $stmt = $conn->prepare('UPDATE posts SET title = ?, content = ?,  category_id = ?, image = ? WHERE id = ?');
        $stmt->bind_param('ssisi', $title_q, $content_q, $category_id_q, $img_q, $post_id_q);
    }
    // se non passo nessun file mantengo quello che c era prima, che fosse null o un immagine
    else {
        $stmt = $conn->prepare('UPDATE posts SET title = ?, content = ?,  category_id = ? WHERE id = ?');
        $stmt->bind_param('ssii', $title_q, $content_q, $category_id_q, $post_id_q);
    }
    $title_q = $title;
    $content_q = $content;
    $category_id_q = $category_id;
    $post_id_q = $post_id;
    $img_q = $img_name_db;
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
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST" class="bg-white px-5 py-4" enctype="multipart/form-data">
                <header class="d-flex justify-content-between align-items-center">
                    <h2>Edit your post</h2>
                    <a href="show.php?id=<?php echo $post['id'] ?>" class="btn btn-success">
                        Post details <i class="fa-solid fa-circle-info ms-1"></i>
                    </a>
                </header>
                <!-- TITOLO -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?php echo $title ?? $post['title'] ?>">
                </div>
                <!-- /TITOLO -->
                <!-- CONTENUTO -->
                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="6" required><?php echo $content ?? $post['content'] ?>
                    </textarea>
                </div>
                <!-- /CONTENUTO -->
                <!-- CATEGORY -->
                <label for="category" class="mb-2 fw-bold">Choose a category:</label>
                <select class="form-select mb-3" id="category" name="category_id">
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category['id'] ?>" <?php if ($category_id ?? $post['category_id'] === $category['id']) : ?> selected <?php endif; ?>>
                            <?php echo $category['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <!-- /CATEGORY -->
                <!-- UPLOAD IMMAGINE -->
                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">Update image (don't fill this field if you like your current image):</label>
                    <div class="d-flex">
                        <input class="form-control rounded-end-0" type="file" id="image" name="image">
                        <div class="border border-start-0 rounded-end-2 d-flex justify-content-center align-items-center px-3 bg-body-secondary" role="button" id="empty-input"><i class="fa-solid fa-xmark fs-4"></i></div>
                    </div>
                </div>
                <!-- /UPLOAD IMMAGINE -->
                <!-- PREVIEW IMMAGINE NUOVA E VECCHIA -->
                <div class="d-flex gap-4">
                    <div style="width: 15%;">
                        <h6 class="fw-bold">New image preview:</h6>
                        <div class="border border-2 rounded-2 mb-4 overflow-hidden" style="aspect-ratio: 1/1;" id="img-preview">
                            <div class="h-100 d-flex justify-content-center align-items-center" id="icon-preview">
                                <i class="fa-solid fa-image fs-1"></i>
                            </div>
                            <img src="" alt="" id="preview" class="w-100 object-fit-cover h-100 d-none">
                        </div>
                    </div>
                    <div style="width: 15%;">
                        <h6 class="fw-bold">Current image preview:</h6>
                        <?php if ($post['image']) : ?>
                            <div class="border border-2 rounded-2 mb-4 overflow-hidden" style="aspect-ratio: 1/1;">
                                <img src="../uploads/<?php echo $post['image'] ?>" alt="" class="w-100 object-fit-cover h-100">
                            </div>
                        <?php else : ?>
                            <p>Your post has no image at the moment</p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- PREVIEW IMMAGINE NUOVA E VECCHIA -->
                <!-- BUTTON INVIO -->
                <button class="btn btn-success" name="submit" value="<?php echo $post['id'] ?>">Edit post</button>
                <!-- /BUTTON INVIO -->
            </form>
        </div>
    </main>
    <!-- JS MENU -->
    <script src="../js/menu.js"></script>
    <!-- /JS MENU -->
    <!-- JS PREVIEW IMMAGINE CARICARE -->
    <script src="../js/previews.js"></script>
    <!-- /JS PREVIEW IMMAGINE CARICARE -->
</body>

</html>