<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Untree.co">
    <link rel="shortcut icon" href="favicon.png">

    <meta name="description" content="<?php echo htmlspecialchars($site_description); ?>" />
    <meta name="keywords" content="bootstrap, bootstrap5" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/tiny-slider.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/glightbox.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/flatpickr.min.css">

    <title><?php echo htmlspecialchars($site_title); ?></title>
    <style>
        .featured-img {
            width: 100%;
            height: 200px; /* Sesuaikan tinggi sesuai kebutuhan */
            overflow: hidden;
            position: relative;
        }
        .featured-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .more-blog-post-img, .popular-post-img {
            width: 100%;
            height: 150px; /* Sesuaikan tinggi sesuai kebutuhan */
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .popular-article-content h4 {
            font-size: 14px; /* Ukuran font lebih kecil */
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .search-result-wrap .blog-entry-search-item img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        object-position: center;
        }
        
        /* File: css/admin_style.css */


    </style>
</head>
<body>

    <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
            <div class="site-mobile-menu-close">
                <span class="icofont-close js-menu-toggle"></span>
            </div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>

    <nav class="site-nav">
        <div class="container">
            <div class="menu-bg-wrap">
                <div class="site-navigation">
                    <div class="row g-0 align-items-center">
                        <div class="col-2">
                            <a href="index.php" class="logo m-0 float-start"><?php echo htmlspecialchars($site_title); ?><span class="text-primary">.</span></a>
                        </div>
                        <div class="col-8 text-center">
                            <form action="search.php" method="get" class="search-form d-inline-block d-lg-none">
                                <input type="text" class="form-control" name="query" placeholder="Search...">
                                <span class="bi-search"></span>
                            </form>

                            <ul class="js-clone-nav d-none d-lg-inline-block text-start site-menu mx-auto">
                                <li class="active"><a href="index.php">Home</a></li>

                            </ul>
                        </div>
                        <div class="col-2 text-end">
                            <a href="#" class="burger ms-auto float-end site-menu-toggle js-menu-toggle d-inline-block d-lg-none light">
                                <span></span>
                            </a>
                            <form action="search.php" method="get" class="search-form d-none d-lg-inline-block">
                                <input type="text" class="form-control" name="query" placeholder="Search...">
                                <span class="bi-search"></span>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

<!-- Content continues... -->