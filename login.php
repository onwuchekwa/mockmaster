<?php 
    // register.php
    include('master/Examination.php');
    $exam = new Examination;
    $exam->candidateSessionPublic();
    $pageName = "Candidate Registration";
    include('header.php');
?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 card-container">
        <span id="message">
        <?php
            if(isset($_GET['verified'])) {
                echo '
                    <span class="alert alert-success d-block">
                        Your email has been verified. You can proceed and log in with the credentials in your email.
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
            <div class="card-header">Candidate Login</div>
            <div class="card-body">
                <form method="POST" id="candidateLoginForm">
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" id="candidateEmail" name="candidateEmail" class="form-control" />
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="candidatePassword" name="candidatePassword" class="form-control" />
                    </div>
                    
                    <div class="form-group">
                        <input type="hidden" name="page" value="login" />
                        <input type="hidden" name="action" value="login" />
                        <input type="submit" name="candidateLogin" id="candidateLogin" value="Login" class="btn btn-info" />
                    </div>
                </form>
                <div class="login-link">
                    <a href="register.php" title="Click here to register">Click here to register</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<script>
    $(document).ready(function() {
        $('#candidateLoginForm').parsley();
        $('#candidateLoginForm').on('submit', function(e) {
            e.preventDefault();
            $('#candidateEmail').attr('required', 'required');
            $('#candidateEmail').attr('data-parsley-type', 'email');
            $('#candidatePassword').attr('required', 'required');

            if($('#candidateLoginForm').parsley().validate()) {
                $.ajax({
                    url: "user_ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#candidateLogin').attr('disabled', 'disabled');
                        $('#candidateLogin').val('Please wait...');
                    },
                    success: function(data) {
                        if(data.success) {
                            location.href = 'index.php';
                        } else {
                            $('#message').html('<span class="alert alert-danger d-block">' + data.error + '</span>');
                        }
                        $('#candidateLogin').attr('disabled', false);
                        $('#candidateLogin').val('Login');
                    }
                });
            }
        });
    });
</script>

<?php include('../mockmasters/master/footer.php') ?>