<?php
session_start();
// se l utente è loggato lo salvo in una variabile, altrimenti questa sarà nulla
$user = $_SESSION['user']  ?? null;
// connessione al db
require_once __DIR__ . '/utilities/db_conn.php';
// query categorie
$result = $conn->query('SELECT * FROM categories ORDER BY name ASC');
$categories = $result->fetch_all(MYSQLI_ASSOC);
// creo un array con solo gli id delle categorie
$cat_ids = array_map(function ($cat) {
    return $cat['id'];
}, $categories);
// query post
$qery = 'SELECT posts.* , categories.name, users.username FROM users JOIN posts ON users.id = posts.user_id  JOIN categories ON categories.id = posts.category_id ORDER BY posts.updated_at DESC';
$result = $conn->query($qery);
$posts = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
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
    <?php require_once __DIR__ . '/partials/boos_font.php' ?>
    <link rel="stylesheet" href="css/style.css">
    <title>PHP MY BLOG</title>
</head>

<body class="vh-100 d-flex flex-column">
    <!-- BACK TO TOP BUTTON -->
    <div id="back-to-top" class="bg-primary">
        <i class="fa-solid fa-arrow-up"></i>
    </div>
    <!-- /BACK TO TOP BUTTON -->
    <?php require_once __DIR__ . '/partials/public_header.php' ?>
    <main class="flex-grow-1 h-25">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <!-- RICERCA PER CATEGORIA -->
                <div class="col-2 ps-4 pt-3">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="GET">
                        <label for="category" class="fs-5 fw-bold mb-1 py-2">Filter by category:</label>
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
                <!-- POSTS -->
                <div class="col-10 pt-3 h-100 overflow-auto" id="back-to-top-target">
                    <?php if ($curr_category) : ?>
                        <p class="text-center fw-bold fs-4">Your research has produced <?php echo count($posts) ? count($posts) : 'no' ?> results</p>
                    <?php else : ?>
                        <p class="text-center fw-bold fs-4">We have <?php echo count($posts) ? count($posts) : '0' ?> posts on our blog at the moment</p>
                    <?php endif;  ?>
                    <div class="container">
                        <div class="row">
                            <?php foreach ($posts as $post) : ?>
                                <!-- POST -->
                                <div class="col-6 px-4 mb-5">
                                    <div class="bg-body-secondary  px-4 pb-4 rounded-5 h-100">
                                        <header class="d-flex justify-content-between align-items-center">
                                            <h3 class="text-center py-3">
                                                <?php echo strtoupper($post['title']) ?> by <?php echo $post['username'] ?>
                                            </h3>
                                            <span class="bg-info fs-6 fw-bold px-2 rounded">
                                                <?php echo $post['name'] ?>
                                            </span>
                                        </header>
                                        <?php if ($post['image']) : ?>
                                            <img src="uploads/<?php echo $post['image'] ?>" alt="<?php echo $post['title'] ?>" class="w-50 mx-auto d-block mb-2">
                                        <?php endif; ?>
                                        <p class="mt-3 mb-2"><?php echo $post['content'] ?></p>
                                        <p><span class="fw-bold fs-6">Last update: </span><?php echo $post['updated_at'] ?></p>
                                    </div>
                                </div>
                                <!-- /POST -->
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
                <!-- /POSTS -->
            </div>
        </div>
    </main>
    <!-- JS MENU -->
    <script src="js/menu.js"></script>
    <!-- /JS MENU -->
    <!-- JS BACK TO TOP -->
    <script src="js/backToTop.js"></script>
    <!-- /JS BACK TO TOP -->
</body>

</html>