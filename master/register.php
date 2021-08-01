<?php

    //register.php
    $pageName = 'Admin Registration';
    include('header.php');
?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 card-container">
        <span id="message"></span>
        <div class="card card-margin">
            <div class="card-header">Admin Registration</div>
            <div class="card-body">
                <form method="POST" id="adminRegisterForm">
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" id="adminEmail" name="adminEmail" class="form-control" data-parsley-checkemail data-parsley-checkemail-message="Email address already exists" maxlength="45" />
                    </div>
                    <div class="form-group">
                        <label>First name</label>
                        <input type="text" id="adminFirstname" name="adminFirstname" class="form-control" maxlength="45" />
                    </div>
                    <div class="form-group">
                        <label>Last name</label>
                        <input type="text" id="adminLastname" name="adminLastname" class="form-control" maxlength="45" />
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="adminPassword" name="adminPassword" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Confirm password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="page" value="register" />
                        <input type="hidden" name="action" value="register" />
                        <input type="submit" name="adminRegister" id="adminRegister" value="Register" class="btn btn-info" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<script>
    $(document).ready(function(){
        window.ParsleyValidator.addValidator('checkemail', {
            validateString: function(value) {
                return $.ajax({
                    url: "ajax_action.php",
                    method: "POST",
                    data: { page: 'register', action: 'check_email', email: value },
                    dataType: "json",
                    success: function(data) {
                        return true;
                    }
                });
            }
        });

        $('#adminRegisterForm').parsley();

        $('#adminRegisterForm').on('submit', function(e) {
            e.preventDefault();

            $('#adminEmail').attr('required', 'required');
            $('#adminEmail').attr('data-parsley-type', 'email');
            $('#adminFirstname').attr('required', 'required');
            $('#adminFirstname').attr('data-parsley-pattern', '^[a-zA-Z- ]+$');
            $('#adminLastname').attr('required', 'required');
            $('#adminLastname').attr('data-parsley-pattern', '^[a-zA-Z- ]+$');
            $('#adminPassword').attr('required', 'required');  
            $('#adminPassword').attr('data-parsley-minlength', '6');                  
            $('#confirmPassword').attr('required', 'required');
            $('#confirmPassword').attr('data-parsley-equalto', '#adminPassword');
            $('#confirmPassword').attr('data-parsley-minlength', '6');

            if($('#adminRegisterForm').parsley().isValid() !== false) {
                $.ajax({
                    url: "ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#adminRegister').attr('disabled', 'disabled');
                        $('#adminRegister').val('Please wait...');
                    },
                    success: function(data) {
                        if(data.success) {
                            $('#message').html('<span class="alert alert-success d-block">Please check your email</span>');
                            $('#adminRegisterForm')[0].reset();
                            $('#adminRegisterForm').parsley().reset();
                        }
                        $('#adminRegister').attr('disabled', false);
                        $('#adminRegister').val('Register');
                    }
                });
            }
        });
    });
</script>
<?php
    include('footer.php');
?>