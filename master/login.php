<?php

    // login.php

    include('Examination.php');

    $exam = new Examination;

    $exam->adminSessionPublic();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login | MockMasters</title>

        <link rel="stylesheet" href="/mockmasters/vendors/bootstrap/css/bootstrap.min.css" media="screen">
        <link rel="stylesheet" href="/mockmasters/css/styles.css">
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
            </header>

            <main>
                <div class="container">
                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-6 card-container">
                            <span id="message">
                                <?php
                                    if(isset($_GET['verified'])) {
                                        echo '
                                            <span class="alert alert-success d-block">
                                                Your email has been verified. You can proceed and log in with the credentials in your email. Your are requested to change your password when logged in.
                                            </span>
                                        ';
                                    }

                                    if(isset($_GET['alreadyverified'])) {
                                        echo '
                                            <span class="alert alert-info d-block">
                                                Your email has already been verified. You can log in with the credentials.
                                            </span>
                                        ';
                                    }
                                ?>
                            </span>
                            <div class="card card-margin">
                                <div class="card-header">Admin Login</div>
                                <div class="card-body">
                                    <form method="POST" id="adminLoginForm">
                                        <div class="form-group">
                                            <label>Email address</label>
                                            <input type="email" id="adminEmail" name="adminEmail" class="form-control" />
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" id="adminPassword" name="adminPassword" class="form-control" />
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="hidden" name="page" value="login" />
                                            <input type="hidden" name="action" value="login" />
                                            <input type="submit" name="adminLogin" id="adminLogin" value="Login" class="btn btn-info" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </main>
        </div>

        <script src="/mockmasters/vendors/jquery/jquery.js"></script>
        <script src="/mockmasters/vendors/parsley/parsley.min.js"></script>
        <script src="/mockmasters/vendors/popper/popper.min.js"></script>
        <script src="/mockmasters/vendors/bootstrap/js/bootstrap.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#adminLoginForm').parsley();

                $('#adminLoginForm').on('submit', function(e) {
                    e.preventDefault();

                    $('#adminEmail').attr('required', 'required');
                    $('#adminEmail').attr('data-parsley-type', 'email');
                    $('#adminPassword').attr('required', 'required');

                    if($('#adminLoginForm').parsley().validate()) {
                        $.ajax({
                            url: "ajax_action.php",
                            method: "POST",
                            data: $(this).serialize(),
                            dataType: "json",
                            beforeSend: function() {
                                $('#adminLogin').attr('disabled', 'disabled');
                                $('#adminLogin').val('Please wait...');
                            },
                            success: function(data) {
                                if(data.success) {
                                    location.href = 'index.php';
                                } else {
                                    $('#message').html('<span class="alert alert-danger d-block">' + data.error + '</span>');
                                }
                                $('#adminLogin').attr('disabled', false);
                                $('#adminLogin').val('Login');
                            }
                        });
                    }
                });
            });
        </script>
    </body>
</html>