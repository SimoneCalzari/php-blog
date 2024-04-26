<?php
session_start();
// se l'utente non loggato digita questa pagina nell url viene rimandato al login
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    die();
}
// salvo l user  in una variabile
$user = $_SESSION['user'];
// connessione al db
require_once __DIR__ . '/../utilities/db_conn.php';
// query categorie
$result = $conn->query('SELECT * FROM categories');
$categories = $result->fetch_all(MYSQLI_ASSOC);
// creo un array con solo gli id delle categorie
$cat_ids = array_map(function ($cat) {
    return $cat['id'];
}, $categories);
// query post
$qery = "SELECT posts.* , categories.name  FROM posts INNER JOIN categories ON categories.id = posts.category_id WHERE user_id = " . $user['id'] . ' ORDER BY posts.updated_at DESC';
$result = $conn->query($qery);
$posts = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
// controllo se i post sono aumentati, diminuti o rimasti uguali
// nei primi due casi mostro un messaggio di avvenuta creazione o cancellazione
$post_created = false;
$post_deleted = false;
if (count($posts) > $_SESSION['posts_num']) {
    $_SESSION['posts_num']++;
    $post_created = true;
}
if (count($posts) < $_SESSION['posts_num']) {
    $_SESSION['posts_num']--;
    $post_deleted = true;
}
// salvo in una variabile la categoria corrente se passata, altrimenti sarà vuota e corrisponderà al caso tutte categorie
$curr_category = htmlspecialchars($_GET['category_id'] ?? '');
// controllo se l id passato esiste tra quelli delle categorie, altrimenti restituirò tutti i post
if (in_array($curr_category, $cat_ids)) {
    // filtro per categoria
    $posts = array_filter($posts, function ($post) {
        return $post['category_id'] === $GLOBALS['curr_category'];
    });
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once __DIR__ . '/../partials/boos_font.php' ?>
    <link rel="stylesheet" href="../css/style.css">
    <title>Post Dasboard</title>
</head>

<body class="vh-100 d-flex flex-column">
    <!-- BACK TO TOP BUTTON -->
    <div id="back-to-top" class="bg-primary">
        <i class="fa-solid fa-arrow-up"></i>
    </div>
    <?php require_once __DIR__ . '/../partials/post_header.php' ?>
    <main class="flex-grow-1 overflow-auto h-25" id="back-to-top-target">
        <div class="container-fluid row py-2">
            <!-- RICERCA PER CATEGORIA -->
            <div class="col-2">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="GET">
                    <label for="category" class="fs-5 mb-1 py-2">Filter by category:</label>
                    <select class="form-select mb-3" id="category" name="category_id">
                        <option value="" <?php if ($curr_category === '') : ?> selected <?php endif; ?>>All</option>
                        <?php foreach ($categories as $category) : ?>
                            <option value="<?php echo $category['id'] ?>" <?php if ($curr_category === $category['id']) : ?> selected <?php endif; ?>>
                                <?php echo $category['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-info">Search <i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
            <!-- /RICERCA PER CATEGORIA -->
            <!-- LISTA POST UTENTE -->
            <div class="col-8">
                <!-- INDICAZIONE NUMERO POST O LORO ASSENZA -->
                <?php if (count($posts)) : ?>
                    <p class="fs-5 mb-1 py-2">You have <?php echo count($posts) ?> posts on our blog
                        <?php echo in_array($curr_category, $cat_ids) ? "under the selected category" : '' ?>
                    </p>
                <?php elseif ($curr_category) : ?>
                    <p class="fs-5 mb-1 py-2">Your search under the selected category has produced no results</p>
                <?php else : ?>
                    <p class="fs-5 mb-1 py-2">You have not published yet on our blog</p>
                <?php endif; ?>
                <!-- /INDICAZIONE NUMERO POST O LORO ASSENZA -->
                <!-- MESSAGGI CREAZIONE E CANCELLAZIONE POST -->
                <?php if ($post_created) : ?>
                    <div class="alert alert-success" role="alert">
                        Your post has been successfully created!
                    </div>
                <?php endif;  ?>
                <?php if ($post_deleted) : ?>
                    <div class="alert alert-danger" role="alert">
                        Your post has been successfully deleted!
                    </div>
                <?php endif;  ?>
                <!-- /MESSAGGI CREAZIONE E CANCELLAZIONE POST -->
                <!-- POSTS -->
                <ul class="ps-0">
                    <?php foreach ($posts as $index => $post) : ?>
                        <li class="list-unstyled bg-body-secondary py-2 px-3 border border-2 border-white d-flex justify-content-between align-items-center">
                            <!-- TITOLO POST E CATEGORIA -->
                            <div class="fw-bold fs-5">
                                <?php echo $post['title'] ?> -
                                <span class="bg-info fs-6 px-2 fw-lighter rounded">
                                    <?php echo $post['name'] ?>
                                </span>
                            </div>
                            <!-- /TITOLO POST E CATEGORIA -->
                            <div class="d-flex gap-2">
                                <!-- CANCELLA POST -->
                                <form action="delete.php" method="POST">
                                    <!-- BUTTON CHE APRE LA MODALE -->
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDelete<?php echo $index ?>">
                                        Delete post <i class="fa-solid fa-trash-can ms-1"></i>
                                    </button>
                                    <!-- /BUTTON CHE APRE LA MODALE -->
                                    <!-- MODALE -->
                                    <div class="modal fade" id="modalDelete<?php echo $index ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5">Confirmation deleting post</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete <span class="fw-bold"><?php echo $post['title'] ?></span>?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-danger" name="id" value="<?php echo $post['id'] ?>">Yes</button>
                                                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">No</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /MODALE -->
                                </form>
                                <!-- CANCELLA POST -->
                                <!-- AGGIORNA POST -->
                                <a href="edit.php?id=<?php echo $post['id'] ?>" class="btn btn-info">
                                    Update post <i class="fa-solid fa-pen-to-square ms-1"></i>
                                </a>
                                <!-- /AGGIORNA POST -->
                                <!-- DETTAGLIO POST -->
                                <a href="show.php?id=<?php echo $post['id'] ?>" class="btn btn-success">
                                    Show post <i class="fa-solid fa-circle-info ms-1"></i>
                                </a>
                                <!-- /DETTAGLIO POST -->
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <!-- /POSTS -->
            </div>
            <!-- /LISTA POST UTENTE -->
            <!-- CREA CATEGORIA -->
            <div class="col-2">
                <p class="fs-5 mb-1 py-2">Add a new category: </p>
                <form action="../category/store.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="name">
                        <button class="input-group-text btn btn-success">Create</button>
                    </div>
                </form>
            </div>
            <!-- /CREA CATEGORIA -->
        </div>
    </main>
    <!-- JS BOOTSTRAP -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- /JS BOOTSTRAP -->
    <!-- JS MENU -->
    <script src="../js/menu.js"></script>
    <!-- /JS MENU -->
    <!-- JS BACK TO TOP -->
    <script src="../js/backToTop.js"></script>
    <!-- /JS BACK TO TOP -->
</body>

</html>