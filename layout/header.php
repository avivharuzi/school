<!DOCTYPE html>
<html lang="en-us">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="A system for managing college courses for students">
        <meta name="keywords" content="system, management, content, students, courses">
        <meta name="author" content="Aviv Haruzi">
        <title><?php if (isset($title)) { echo $title; } ?></title>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="css/libs/jquery-ui.min.css">
        <link rel="stylesheet" type="text/css" href="css/libs/sweetalert2.min.css">
        <link rel="stylesheet" type="text/css" href="css/libs/theme-default-validator.min.css">
        <link rel="stylesheet" type="text/css" href="css/libs/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="css/main/style.css">   
        <link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico">
        <link rel="icon" type="image/png" href="images/icons/icon.png">
    </head>
    <body>
        <?php if (isset($_SESSION["isLoggedIn"]) === true) { ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top mb-5">
            <div class="container">
                <a class="navbar-brand" href="index.php">Coding School</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="school.php">School</a></li>
                        <?php if ($_SESSION["role"] !== "sales") { ?>
                        <li class="nav-item"><a class="nav-link" href="administration.php">Administration</a></li>
                        <?php } ?>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        <span class="navbar-text mr-3"><img class="rounded-circle" src="<?php echo $administrator->getImage(); ?>" alt="profile" width="48" height="48"></span>
                        <span class="navbar-text mr-3"><?php echo $administrator->getFullName(); ?><br><?php echo ucwords($_SESSION["role"]); ?></span>
                        <li class="navbar-text"><a class="nav-link" href="logout.php"><i class="fa fa-sign-out mr-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid" id="mainContainer">
        <?php } ?>