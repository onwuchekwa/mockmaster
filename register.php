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
            <div class="card card-margin">
                <div class="card-header"><h4>Candidate Registration</h4></div>
                <div class="card-body">
                    <span id="message"></span>
                    <form method="post" id="userRegisterForm">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" id="candidateEmail" name="candidateEmail" class="form-control" maxlength="44" data-parsley-checkemail data-parsley-checkemail-message="Email address already exists" />
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="candidatePassword" name="candidatePassword" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>First name</label>
                            <input type="text" id="candidateFirstname" name="candidateFirstname" class="form-control" maxlength="44" />
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="candidateLastname" name="candidateLastname" class="form-control" maxlength="44" />
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select id="candidateGender" name="candidateGender" class="form-control">
                                <option value="">Select your gender</option>
                                <option value="f">Female</option>
                                <option value="m">Male</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" id="candidateAddress" name="candidateAddress" class="form-control" maxlength="99" />
                        </div>
                        <div class="form-group">
                            <label>Residential Country</label>
                            <input type="text" id="candidateCountry" name="candidateCountry" class="form-control" data-parsley-error-message="You must live in Ghana to register" />
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" id="candidatePhone" name="candidatePhone" class="form-control" maxlength="10" data-parsley-error-message="This values should be numbers only" />
                        </div>
                        <div class="form-group">
                        <input type="hidden" name="page" value="register" />
                        <input type="hidden" name="action" value="register" />
                        <input type="submit" name="userRegister" id="userRegister" value="Register" class="btn btn-info" />
                        </div>
                    </form>
                    <div class="login-link">
                        <a href="login.php" title="Click here to log in">Click here to log in</a>
                    </div>
                </div>
            </div>
        </div>
    <div class="col-md-3"></div>
</div>

<script>
    $(document).ready(function() {
        window.ParsleyValidator.addValidator('checkemail', { 
        //window.Parsley.addValidator('checkemail', { 
            validateString: function(value) {
                return $.ajax({
                    url: "user_ajax_action.php",
                    method: "POST",
                    data: { page: 'register', action: 'checkEmail', email: value },
                    dataType: "json",
                    success: function(data) {
                        return true;
                    }
                });
            }
        });

        $('#userRegisterForm').parsley();

        $('#userRegisterForm').on('submit', function(e) {
            e.preventDefault();
            $('#candidateEmail').attr('required', 'required');
            $('#candidateEmail').attr('data-parsley-type', 'email');
            $('#candidatePassword').attr('required', 'required');
            $('#candidatePassword').attr('data-parsley-minlength', '6');
            $('#confirmPassword').attr('required', 'required');
            $('#confirmPassword').attr('data-parsley-equalto', '#candidatePassword');
            $('#confirmPassword').attr('data-parsley-minlength', '6');
            $('#candidateFirstname').attr('required', 'required');
            $('#candidateFirstname').attr('data-parsley-pattern', '^[a-zA-Z- ]+$');
            $('#candidateLastname').attr('required', 'required');
            $('#candidateLastname').attr('data-parsley-pattern', '^[a-zA-Z- ]+$');
            $('#candidateGender').attr('required', 'required');
            $('#candidateAddress').attr('required', 'required');
            $('#candidateCountry').attr('required', 'required');
            $('#candidateCountry').attr('data-parsley-equalto', 'Ghana');
            $('#candidatePhone').attr('required', 'required');
            $('#candidatePhone').attr('data-parsley-pattern', '^[0-9]+$');

            if($('#userRegisterForm').parsley().isValid() !== false) {
                $.ajax({
                    url: "user_ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#userRegister').attr('disabled', 'disabled');
                        $('#userRegister').val('please wait...')
                    },
                    success: function(data) {
                        if(data.success) {
                            $('#message').html('<span class="alert alert-success d-block">Please check your email</span>');
                            $('#userRegisterForm')[0].reset();
                            $('#userRegisterForm').parsley().reset();
                        }
                        $('#userRegister').attr('disabled', false);
                        $('#userRegister').val('Register');
                    }
                });
            }
        });
    });
</script>

<?php include('master/footer.php') ?>