<?php
session_start();
// se l'utente non loggato digita questa pagina nell url viene rimandato al login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    die();
}
// salvo l user in una variabile
$user = $_SESSION['user'];
// connessione al db
require_once __DIR__ . '/../utilities/db_conn.php';
// query post
$qery = "SELECT posts.* , categories.name  FROM posts INNER JOIN categories ON categories.id = posts.category_id WHERE user_id = " . $user['id'] . ' ORDER BY posts.updated_at DESC';
$result = $conn->query($qery);
$posts = $result->fetch_all(MYSQLI_ASSOC);
// var_dump($posts);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once __DIR__ . '/../partials/boos_font.php' ?>
    <title>Post Dasboard</title>
</head>

<body class="vh-100 d-flex flex-column">
    <?php require_once __DIR__ . '/../partials/post_header.php' ?>
    <main class="flex-grow-1 overflow-auto h-25">
        <div class="container">
            <!-- INDICAZIONE NUMERO POST O LORO ASSENZA -->
            <?php if (count($posts)) : ?>
                <p class="fs-5 mb-1 py-2">You have <?php echo count($posts) ?> posts on our blog</p>
            <?php else : ?>
                <p class="fs-5 mb-1 py-2">You have not pubblished any post yet</p>
            <?php endif; ?>
            <!-- /INDICAZIONE NUMERO POST O LORO ASSENZA -->
            <!-- POSTS -->
            <ul class="ps-0">
                <?php foreach ($posts as $post) : ?>
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
                                <button class="btn btn-danger" name="id" value="<?php echo $post['id'] ?>">Delete post <i class="fa-solid fa-trash-can ms-1"></i></button>
                            </form>
                            <!-- CANCELLA POST -->
                            <!-- AGGIORNA POST -->
                            <form action="edit.php" method="GET">
                                <button class="btn btn-info" name="id" value="<?php echo $post['id'] ?>">Update post <i class="fa-solid fa-pen-to-square ms-1"></i></button>
                            </form>
                            <!-- /AGGIORNA POST -->
                            <!-- DETTAGLIO POST -->
                            <form action="show.php" method="GET">
                                <button class="btn btn-success" name="id" value="<?php echo $post['id'] ?>">Show post <i class="fa-solid fa-circle-info ms-1"></i></button>
                            </form>
                            <!-- /DETTAGLIO POST -->
                        </div>

                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- /POSTS -->
        </div>
    </main>
</body>

</html>