<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageName ?> | MockMasters</title>

        <link rel="stylesheet" href="/mockmasters/vendors/bootstrap/css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="/mockmasters/vendors/DataTables/DataTables/css/dataTables.bootstrap4.min.css" media="screen">
        <link rel="stylesheet" href="/mockmasters/css/bootstrap-datetimepicker.css">
        <link rel="stylesheet" href="/mockmasters/css/TimeCircles.css">
        <link rel="stylesheet" href="/mockmasters/css/styles.css">        

        <script src="/mockmasters/vendors/jquery/jquery.js"></script>
        <script src="/mockmasters/vendors/DataTables/DataTables/js/jquery.dataTables.min.js"></script>
        <script src="/mockmasters/vendors/DataTables/DataTables/js/dataTables.bootstrap4.min.js"></script>
        <script src="/mockmasters/vendors/parsley/parsley.min.js"></script>
        <script src="/mockmasters/vendors/popper/popper.min.js"></script>
        <script src="/mockmasters/js/bootstrap-datetimepicker.js"></script>
        <script src="/mockmasters/js/TimeCircles.js"></script>
        <script src="/mockmasters/vendors/bootstrap/js/bootstrap.min.js"></script>
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
                                   
                <?php if(isset($_SESSION['candidateId'])) { ?>
                <div class="row">
                    <div class="col-sm-12">
                        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
                            <a class="navbar-brand" href="index.php">Candidate Side</a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="enroll-exam.php">Enrolled Exam</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="profile.php">Profile</a>
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
                <?php } ?>             
            </header>

            <main>
                <div class="container">