<?php
    include('Examination.php');

    $exam = new Examination;

    $exam->adminSessionPrivate();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageName ?> | MockMasters</title>

        <link rel="stylesheet" href="/vendors/bootstrap/css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="/vendors/DataTables/DataTables/css/dataTables.bootstrap4.min.css" media="screen">
        <link rel="stylesheet" href="/css/bootstrap-datetimepicker.css">
        <link rel="stylesheet" href="/css/TimeCircles.css">
        <link rel="stylesheet" href="/css/styles.css">        

        <script src="/vendors/jquery/jquery.js"></script>
        <script src="/vendors/DataTables/DataTables/js/jquery.dataTables.min.js"></script>
        <script src="/vendors/DataTables/DataTables/js/dataTables.bootstrap4.min.js"></script>
        <script src="/vendors/parsley/parsley.min.js"></script>
        <script src="/vendors/popper/popper.min.js"></script>
        <script src="/js/bootstrap-datetimepicker.js"></script>
        <script src="/js/TimeCircles.js"></script>
        <script src="/vendors/bootstrap/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="content-wrapper">
            <header>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="jumbotron jumbotron-fluid text-center">
                            <div class="container">
                                <h1 class="display-4">MockMasters</h1>
                                <p class="lead">The online examination center</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
                            <a class="navbar-brand" href="index.php">Admin Side</a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="exam.php">Exam</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="domain.php">Question</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="register.php">New Admin</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="user.php">Candidate</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="change-password.php">Change Password</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="logout.php">Logout</a>
                                    </li>   
                                </ul>
                            </div>  
                        </nav>
                    </div>
                </div>
            </header>

            <main>
                <div class="container">