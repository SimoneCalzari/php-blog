<!-- HEADER -->
<header class="bg-primary">
    <div class="container-fluid d-flex justify-content-between">
        <!-- TITOLO -->
        <h1 class="mb-0 py-3 ps-2"><?php echo  $user['username'] . ' Dashboard';  ?></h1>
        <!-- /TITOLO -->
        <!-- MENU -->
        <nav class="d-flex align-items-center">
            <!-- TORNA ALLA DASHBOARD -->
            <a href="index.php" class="btn btn-info me-3">Posts Dashboard <i class="fa-solid fa-table-list ms-1"></i></a>
            <!-- /TORNA ALLA DASHBOARD -->
            <!-- CREA NUOVO POST -->
            <a href="create.php" class="btn btn-success me-2">New post <i class="fa-solid fa-plus ms-1"></i></a>
            <!-- /CREA NUOVO POST -->
            <!-- USER -->
            <div class="fw-bold h-100 d-flex align-items-center px-2 position-relative fs-5 btn btn-primary text-black" id="user">
                <?php echo $user['username'] ?> <i class="fa-solid fa-user ms-2"></i>
                <!-- MENU USER -->
                <nav class="position-absolute top-100 end-0 border border-black rounded bg-primary d-none" id="menu-user" style="z-index: 1000;">
                    <ul class="list-unstyled mb-0">
                        <li class="border-bottom border-black">
                            <a href="http://localhost/Post_Boolean/php-blog/index.php" class="text-black text-decoration-none d-flex py-2 px-3 align-items-center justify-content-end"><span>Home</span><i class="fa-solid fa-house ms-1 pt-1"></i>
                            </a>
                        </li>
                        <li>
                            <a href="http://localhost/Post_Boolean/php-blog/logout.php" class="text-black text-decoration-none d-flex py-2 px-3 align-items-center justify-content-end"><span>Logout</span> <i class="fa-solid fa-right-from-bracket ms-2 pt-1"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /MENU USER -->
            </div>
            <!-- /USER -->
        </nav>
        <!-- /MENU -->
    </div>
</header>
<!-- /HEADER -->