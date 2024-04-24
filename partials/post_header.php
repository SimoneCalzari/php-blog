<!-- HEADER -->
<header class="bg-primary text-center py-2">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1><?php echo "Welcome, " . $user['username'];  ?></h1>
        <nav>
            <a href="index.php" class="btn btn-info me-2">Post Dashboard</a>
            <a href="create.php" class="btn btn-success me-2">New post</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </nav>
    </div>
</header>
<!-- /HEADER -->