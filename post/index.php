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
$host = 'localhost';
$db_user = 'root';
$db_psw = 'root';
$db_name = 'php_blog';
$conn = new mysqli($host, $db_user, $db_psw, $db_name);
// query post
$qery = "SELECT posts.* , categories.name  FROM posts INNER JOIN categories ON categories.id = posts.category_id WHERE user_id = " . $user['id'];
$result = $conn->query($qery);
$posts = $result->fetch_all(MYSQLI_ASSOC);
// var_dump($posts);
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
    <main class="flex-grow-1 d-flex overflow-auto h-25">
        <div class="container py-5">
            <!-- POSTS -->
            <ul>
                <?php foreach ($posts as $post) : ?>
                    <li class="list-unstyled bg-body-secondary py-2 px-3 border border-white d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5"><?php echo $post['title'] ?></span>
                        <div class="d-flex gap-2">
                            <form action="delete.php" method="POST">
                                <button class="btn btn-danger" name="id" value="<?php echo $post['id'] ?>">Delete post</button>
                            </form>
                            <form action="edit.php" method="GET">
                                <button class="btn btn-info" name="id" value="<?php echo $post['id'] ?>">Update post</button>
                            </form>
                            <form action="show.php" method="GET">
                                <button class="btn btn-success" name="id" value="<?php echo $post['id'] ?>">Show post</button>
                            </form>
                        </div>

                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- /POSTS -->
        </div>
    </main>
</body>

</html>