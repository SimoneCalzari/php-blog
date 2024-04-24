<!-- HEADER -->
<header class="bg-primary text-center py-2">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <h1><?php echo "Welcome, " . $user['username'];  ?></h1>
        <nav>
            <a href="" class="btn btn-dark me-2">New post</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </nav>
    </div>
</header>
<!-- /HEADER -->