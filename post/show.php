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
$post_id = htmlspecialchars($_GET['id']);
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
// se ho un risultato procedo a salvarmi il post in una variabile
$post = $result->fetch_assoc();
// chiudo la connesione al db
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once __DIR__ . '/../partials/boos_font.php' ?>
    <title>Show post</title>
</head>

<body class="vh-100 d-flex flex-column overflow-hidden">
    <?php require_once __DIR__ . '/../partials/post_header.php' ?>
    <main class="flex-grow-1 overflow-auto">
        <div class="w-50 mx-auto bg-body-secondary my-4 px-4 pb-4 rounded-5">
            <header class="d-flex justify-content-between align-items-center">
                <h2 class="text-center py-3">
                    <?php echo $post['title'] ?>
                    <span class="bg-info fs-6 px-2 rounded">
                        <?php echo $post['name'] ?>
                    </span>
                </h2>
                <a href="edit.php?id=<?php echo $post['id'] ?>" class="btn btn-info">
                    Update<i class="fa-solid fa-pen-to-square ms-1"></i>
                </a>
            </header>
            <?php if ($post['image']) : ?>
                <img src="../uploads/<?php echo $post['image'] ?>" alt="<?php echo $post['title'] ?>" class="w-50 mx-auto d-block mb-2">
            <?php endif; ?>
            <h5>Content</h5>
            <p><?php echo $post['content'] ?></p>
            <div class="row">
                <p class="col-6"><span class="fw-bold fs-6">Created: </span><?php echo $post['created_at'] ?></p>
                <p class="col-6"><span class="fw-bold fs-6">Last update: </span><?php echo $post['updated_at'] ?></p>
            </div>
        </div>
    </main>
    <!-- JS MENU -->
    <script src="../js/menu.js"></script>
    <!-- /JS MENU -->
</body>

</html>