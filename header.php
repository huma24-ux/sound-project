<?php
// session_start(); // ✅ Always start the session before using $_SESSION

// ✅ Check if the user is logged in
$name = isset($_SESSION['name']) && !empty($_SESSION['name']) ? $_SESSION['name'] : 'Guest';
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sound Group </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />


    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">



        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="dashboard.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">Sound Group</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?= htmlspecialchars($name); ?></h6>
                        <!-- <span>Admin</span> -->
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="dashboard.php" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="language_view.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Languages</a>
                    <a href="genre_view.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Genere</a>
                    <a href="artist_view.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Artist</a>
                    <a href="album_view.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Album</a>
                    <a href="video_view.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Video</a>
                    <a href="music_view.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Music</a>
                    <a href="review_view.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Review</a>
                    <a href="ratings_view.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Rating</a>
                    <a href="contact.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>contact</a>
                    <!-- <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa fa-laptop me-2"></i>Elements</a>
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="button.html" class="dropdown-item">Buttons</a>
                            <a href="typography.html" class="dropdown-item">Typography</a>
                            <a href="element.html" class="dropdown-item">Other Elements</a>
                        </div>
                    </div>
                    <a href="widget.html" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Widgets</a>
                    <a href="form.html" class="nav-item nav-link"><i class="fa fa-keyboard me-2"></i>Forms</a>
                    <a href="table.html" class="nav-item nav-link"><i class="fa fa-table me-2"></i>Tables</a>
                    <a href="chart.html" class="nav-item nav-link"><i class="fa fa-chart-bar me-2"></i>Charts</a> -->
                    <div class="nav-item dropdown">
                        <!-- <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="far fa-file-alt me-2"></i>Pages</a> -->
                        <div class="dropdown-menu bg-transparent border-0">
                            <a href="signin.html" class="dropdown-item">Sign In</a>
                            <a href="signup.html" class="dropdown-item">Sign Up</a>
                            <a href="404.html" class="dropdown-item">404 Error</a>
                            <a href="blank.html" class="dropdown-item">Blank Page</a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content ">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-1">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <!-- <input class="form-control border-0" type="search" placeholder="Search"> -->

                </form>
                <div class="navbar-nav align-items-center ms-auto">
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <div class="nav-item dropdown">
                            <div class="d-flex align-items-center justify-content-between p-2">
                                <span class="me-3 text-dark fw-bold">Welcome, <?= htmlspecialchars($name); ?> (Admin)</span>
                                <a href="logout.php" class="btn btn-danger btn-sm ms-2">Logout</a>
                            </div>
                        </div>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <div class="nav-item dropdown">
                            <div class="d-flex align-items-center justify-content-between p-2">
                                <span class="me-3 text-dark fw-bold">Welcome, <?= htmlspecialchars($name); ?> (User)</span>
                                <a href="logout.php" class="btn btn-danger btn-sm ms-2">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary btn-sm me-2">Login</a>
                        <a href="register.php" class="btn btn-outline-primary btn-sm">Register</a>
                    <?php endif; ?>
                </div>
            </nav>
            <!-- Navbar End -->